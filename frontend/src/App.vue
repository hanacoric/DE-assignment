<template>
  <div class="app-wrapper">
    <StartGame v-if="!gameStarted && !showFinal" @started="handleGameStart" />
    <GuessSong
      v-if="gameStarted && !showFinal"
      :genre="genre"
      :deezerGenreId="deezerGenreId"
      :gameSessionId="gameSessionId"
      :snippetDuration="snippetDuration"
      @gameFinished="handleGameFinished"
    />



    <FinalScore
      v-if="showFinal"
      :score="finalScore"
      :rounds="maxRounds"
      @restart="restartGame"
    />
  </div>
</template>

<script>
import StartGame from './components/StartGame.vue';
import GuessSong from './components/GuessSong.vue';
import FinalScore from './components/FinalScore.vue';

export default {
  components: { StartGame, GuessSong, FinalScore },
  data() {
    return {
      genre: null,
      deezerGenreId: null,
      gameSessionId: null,
      snippetDuration: 5, // default duration
      gameStarted: false,
      showFinal: false,
      finalScore: 0,
      maxRounds: 5,
    };
  },

  methods: {
    handleGameStart({ genre, deezerGenreId, gameSessionId, snippetDuration }) {
      this.genre = genre;
      this.deezerGenreId = deezerGenreId;
      this.gameSessionId = gameSessionId;
      this.snippetDuration = snippetDuration;
      this.gameStarted = true;
    },
    handleGameFinished({ score, rounds }) {
      this.finalScore = score;
      this.maxRounds = rounds;
      this.showFinal = true;
      this.gameStarted = false;
    },
    restartGame() {
      this.genre = null;
      this.gameSessionId = null;
      this.finalScore = 0;
      this.gameStarted = false;
      this.showFinal = false;
    }
  }
};
</script>


<style>

#app {
  display: flex !important;
  flex-direction: column;
  min-height: 100vh;
}

html, body {
  height: 100%;
  margin: 0;
}

.app-wrapper {
  height: 100vh;
  display: flex;
  flex-direction: column;
}
/* Center FinalScore specifically */
.app-wrapper:has(> .final-score-box) {
  align-items: center;
  justify-content: center;
}
</style>

