<template>
  <div class="guess-game-wrapper">
    <div class="guess-game">
      <template v-if="song">
        <h2 class="text-xl font-bold mb-2">Listen and Guess!</h2>

        <label class="block mb-2 font-semibold">Choose your snippet length:</label>
        <select v-model="snippetDuration" class="mb-4 p-2 border rounded">
          <option :value="10">10 seconds (Hard)</option>
          <option :value="30">30 seconds (Medium)</option>
          <option :value="60">1 minute (Easy)</option>
        </select>

        <audio
          ref="audioPlayer"
          :src="audioUrl"
          controls
          @play="handlePlay"
          @timeupdate="checkTime"
          class="mb-4"
        />

        <form @submit.prevent="submitGuess" class="form-grid">
          <input v-model="guess.guessed_title" type="text" placeholder="Song title" class="input" />
          <input v-model="guess.guessed_artist" type="text" placeholder="Artist" class="input" />
          <input v-model="guess.guessed_album" type="text" placeholder="Album" class="input" />
          <input v-model="guess.guessed_year" type="number" placeholder="Year" class="input" />
          <button type="submit" class="submit-btn">Submit Guess</button>
        </form>

        <div v-if="result" class="mt-4">
          <p>You got {{ result.points_awarded }} point(s)!</p>
          <p><strong>Correct Answers:</strong></p>
          <div class="correct-answers-grid">
            <div class="answer-block">
              <strong>Title:</strong> {{ result.correct.title }}
            </div>
            <div class="answer-block">
              <strong>Artist:</strong> {{ result.correct.artist }}
            </div>
            <div class="answer-block">
              <strong>Album:</strong> {{ result.correct.album }}
            </div>
            <div class="answer-block">
              <strong>Year:</strong> {{ result.correct.release_year }}
            </div>
          </div>


        </div>

        <button
          v-if="result && round < maxRounds"
          @click="nextSong"
          class="mt-4 bg-green-500 text-white px-4 py-2 rounded"
        >
          Next Song
        </button>


      </template>

      <template v-else>
        <p class="text-center text-purple-800 text-lg">🎧 Loading a song...</p>
      </template>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'GuessSong',
  props: ['genre', 'gameSessionId'],
  data() {
    return {
      song: null,
      guess: {
        guessed_title: '',
        guessed_artist: '',
        guessed_album: '',
        guessed_year: ''
      },
      result: null,
      snippetDuration: 10,
      round: 1,
      maxRounds: 5,
      finished: false,
      finalScore: 0,
      usedSongIds: []
    };
  },
  computed: {
    audioUrl() {
      return `http://127.0.0.1:8000/${this.song?.audio_snippet}`;
    }
  },
  mounted() {
    this.fetchRandomSong();
  },
  methods: {
    async fetchRandomSong() {
      try {
        let attempts = 0;
        let newSong = null;

        while (attempts < 5) {
          const res = await axios.get('http://127.0.0.1:8000/api/songs/random', {
            params: {
              genre: this.genre,
              exclude: this.usedSongIds.join(',')
            }
          });

          newSong = res.data;

          if (!newSong || this.usedSongIds.includes(newSong.id)) {
            attempts++;
            continue;
          }

          break;
        }

        if (!newSong || !newSong.audio_snippet) {
          alert("No more songs found for this genre.");
          return;
        }

        // Stop currently playing song
        if (this.song) {
          const audio = this.$refs.audioPlayer;
          if (audio) {
            audio.pause();
            audio.currentTime = 0;
          }
        }

        this.song = newSong;
        this.usedSongIds.push(newSong.id);

        this.$nextTick(() => {
          const audio = this.$refs.audioPlayer;
          if (audio) {
            audio.currentTime = 0;
            audio.play();
          }
        });

      } catch (err) {
        console.error('Error fetching song:', err);
        alert('Failed to load song. Please try again.');
      }
    },

    handlePlay() {
      const audio = this.$refs.audioPlayer;
      if (audio) {
        audio.currentTime = 0;
        setTimeout(() => {
          if (!audio.paused && audio.currentTime >= this.snippetDuration) {
            audio.pause();
          }
        }, this.snippetDuration * 1000);
      }
    },

    checkTime() {
      const audio = this.$refs.audioPlayer;
      if (audio && audio.currentTime >= this.snippetDuration) {
        audio.pause();
        audio.currentTime = 0;
      }
    },

    async nextSong() {
      this.round++;
      this.guess = {
        guessed_title: '',
        guessed_artist: '',
        guessed_album: '',
        guessed_year: ''
      };
      this.result = null;

      if (this.round > this.maxRounds) return;

      await this.fetchRandomSong();
    },

    async submitGuess() {
      try {
        const res = await axios.post('http://127.0.0.1:8000/api/guess', {
          game_session_id: this.gameSessionId,
          song_id: this.song.id,
          snippet_duration: this.snippetDuration,
          ...this.guess
        });

        this.result = res.data;

        if (this.round === this.maxRounds) {
          this.finished = true;

          const scoreRes = await axios.get(
            `http://127.0.0.1:8000/api/game/score/${this.gameSessionId}`
          );
          this.finalScore = scoreRes.data.score;

          this.$emit('gameFinished', {
            score: this.finalScore,
            rounds: this.maxRounds
          });
        }
      } catch (err) {
        console.error('Error submitting guess:', err);
        alert("Couldn't submit guess. Please try again.");
      }
    }
  }
};
</script>


<style>
@import url('https://fonts.googleapis.com/css2?family=Lilita+One&display=swap');

body {
  margin: 0;
  padding: 0;
  background: linear-gradient(145deg, #ffeaff, #ffd6fa, #ffccf9);
  background-size: 400% 400%;
  animation: sparkleBackground 12s ease infinite;
  font-family: 'Lilita One', cursive;
  overflow-x: hidden;
}

.guess-game-wrapper {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1.5rem;
}

.guess-game {
  max-width: 700px;
  width: 100%;
  padding: 32px;
  background-color: #fff0fb;
  border-radius: 20px;
  box-shadow: 0 0 20px rgba(255, 182, 193, 0.4);
  animation: fadeIn 0.8s ease;
  text-align: center;
}

.guess-game h2 {
  font-size: 26px;
  margin-bottom: 20px;
  color: #c600a1;
  animation: popText 0.6s ease;
}

.guess-game label {
  font-size: 16px;
  color: #7a007a;
  margin-bottom: 12px;
  display: block;
}

select {
  padding: 10px;
  font-size: 14px;
  border: 2px solid #e287d6;
  border-radius: 10px;
  background-color: #ffeafd;
  color: #4a004a;
  box-shadow: 0 0 6px rgba(255, 192, 203, 0.3);
  width: 100%;
  margin-bottom: 20px;
  outline: none;
}

audio {
  width: 100%;
  margin-bottom: 20px;
  border-radius: 10px;
  box-shadow: 0 0 8px rgba(255, 182, 193, 0.4);
}

.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
  margin-bottom: 20px;
}

.input {
  padding: 10px;
  font-size: 14px;
  border: 2px solid #d08adb;
  border-radius: 10px;
  background-color: #fff5fc;
  color: #5a005a;
  box-shadow: 0 0 4px rgba(255, 204, 255, 0.2);
  width: 100%;
}

.input:focus {
  border-color: #cc00aa;
  box-shadow: 0 0 8px #ffb3f6;
  outline: none;
}

.form-grid button {
  grid-column: span 2;
  margin-top: 10px;
}

.submit-btn,
.guess-game button {
  background: linear-gradient(to right, #ff85c1, #da70d6);
  color: white;
  font-size: 14px;
  padding: 10px 20px;
  border: none;
  border-radius: 12px;
  cursor: pointer;
  font-family: 'Lilita One', cursive;
  position: relative;
  overflow: hidden;
  transition: transform 0.2s ease;
}

.submit-btn::after,
.guess-game button::after {
  content: '';
  position: absolute;
  top: 0;
  left: -75%;
  width: 50%;
  height: 100%;
  background: rgba(255, 255, 255, 0.3);
  transform: skewX(-25deg);
  animation: shimmer 2.5s infinite;
}

.submit-btn:hover,
.guess-game button:hover {
  transform: scale(1.03);
  box-shadow: 0 0 12px #ffb3f6;
}

.guess-game .mt-4 {
  margin-top: 20px;
  color: #c600a1;
  font-size: 14px;

}

.correct-answers-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px 20px;
  margin-top: 16px;
  width: 100%;
}

.answer-block {
  background-color: #ffe3f9;
  padding: 10px 14px;
  border-radius: 10px;
  box-shadow: 0 0 6px rgba(255, 179, 245, 0.2);
  text-align: center;

  font-size: 14px;
}

/* Animations */
@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes popText {
  0% { transform: scale(0.95); opacity: 0; }
  100% { transform: scale(1); opacity: 1; }
}

@keyframes shimmer {
  0% { left: -75%; }
  100% { left: 125%; }
}

@keyframes sparkleBackground {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}

@media (max-width: 600px) {
  .form-grid,
  .correct-answers-grid {
    grid-template-columns: 1fr;
  }

  .guess-game {
    padding: 24px;
  }

  .submit-btn,
  .guess-game button {
    width: 100%;
  }
}
</style>

