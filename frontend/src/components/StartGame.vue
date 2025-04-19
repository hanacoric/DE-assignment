<template>
  <div class="start-game-box animate-pop">
    <h1>Music Guessing Game</h1>

    <label for="genre">Choose a Genre</label>
    <select id="genre" v-model="selectedGenre">
      <option disabled value="">Select a genre</option>
      <option v-for="genre in genres" :key="genre" :value="genre">
        {{ genre }}
      </option>
    </select>

    <button @click="startGame">Start Game</button>

    <p v-if="gameSessionId" class="started-text">
      Game started! Session ID: {{ gameSessionId }}
    </p>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'StartGame',
  data() {
    return {
      selectedGenre: '',
      genres: [
        'Alternative Rock',
        'Psychedelic Rock',
        'Classic Rock',
        'Grunge',
        'Metal',
        'Nu Metal',
        'New Wave',
        'Shoegaze',
        'Indie Rock',
        'Hip Hop'
      ],
        genreMap: {
          'Grunge': 152,
          'Metal': 464,
          'Hip Hop': 116,
          'Shoegaze': 85,
          'New Wave': 132,
          'Classic Rock': 77,
          'Alternative Rock': 1521,
          'Indie Rock': 999,
          'Nu Metal': 888
        },
      gameSessionId: null,
    };
  },
  methods: {
    async startGame() {
      if (!this.selectedGenre) {
        alert("Please select a genre.");
        return;
      }

      try {
        const res = await axios.post('http://127.0.0.1:8000/api/game/start', {
          genre: this.selectedGenre,
        });

        this.gameSessionId = res.data.game_session_id;


        this.$emit('started', {
          genre: this.selectedGenre,
          deezerGenreId: this.genreMap[this.selectedGenre],
          gameSessionId: this.gameSessionId,
        });

      } catch (err) {
        console.error("Failed to start game:", err);
        alert("Something went wrong.");
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
  overflow: hidden;
}

.spotify-login-btn {
  width: 100%;
  margin-bottom: 20px;
  padding: 12px;
  font-size: 16px;
  background-color: #1db954;
  color: white;
  border: none;
  border-radius: 10px;
  cursor: pointer;
  font-weight: bold;
  transition: background-color 0.3s ease;
}

.spotify-login-btn:hover {
  background-color: #1aa34a;
}


.start-game-box {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background-color: #fff0fb;
  padding: 50px;
  border-radius: 32px;
  box-shadow: 0 0 30px rgba(255, 105, 180, 0.4),
  0 0 60px rgba(255, 182, 193, 0.3);
  text-align: center;
  width: 100%;
  max-width: 500px;
  animation: fadeInCenter 0.6s ease forwards;

}

.start-game-box h1 {
  font-size: 36px;
  color: #d240af;
  margin-bottom: 30px;
  animation: popText 1.2s ease forwards;
}

.start-game-box label {
  display: block;
  font-size: 20px;
  margin-bottom: 12px;
  color: #800080;
}

.start-game-box select {
  width: 100%;
  padding: 14px;
  font-size: 16px;
  margin-bottom: 28px;
  border: 2px solid #e287d6;
  border-radius: 16px;
  background-color: #ffeafd;
  color: #4a004a;
  outline: none;
  box-shadow: 0 0 10px rgba(255, 192, 203, 0.4);
  transition: box-shadow 0.3s ease;
}

.start-game-box select:focus {
  border-color: #cc00aa;
  box-shadow: 0 0 12px #ffb3f6;
}

.start-game-box button {
  width: 100%;
  padding: 16px;
  font-size: 18px;
  background: linear-gradient(135deg, #ff85c1, #da70d6);
  color: white;
  border: none;
  border-radius: 16px;
  font-weight: bold;
  cursor: pointer;
  position: relative;
  overflow: hidden;
  z-index: 1;
  transition: transform 0.3s ease;
}

.start-game-box button::after {
  content: '';
  position: absolute;
  top: 0;
  left: -75%;
  width: 50%;
  height: 100%;
  background: rgba(255, 255, 255, 0.3);
  transform: skewX(-25deg);
  z-index: 2;
  animation: shimmer 2.5s infinite;
}

.start-game-box button:hover {
  transform: scale(1.05);
  box-shadow: 0 0 20px #ffb3f6;
}

.started-text {
  margin-top: 24px;
  color: #c600a1;
  font-weight: bold;
  font-size: 18px;
  animation: fadeIn 0.5s ease;
}

/* Animations */

@keyframes fadeInCenter {
  0% {
    opacity: 0;
    transform: translate(-50%, -50%) scale(0.8);
  }
  100% {
    opacity: 1;
    transform: translate(-50%, -50%) scale(1);
  }
}



@keyframes popText {
  0% { transform: scale(0.8); opacity: 0; }
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


</style>
