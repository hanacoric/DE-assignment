<template>
  <div class="final-score-box">
    <h1>Game Over!</h1>
    <p>You completed {{ rounds }} rounds!</p>
    <p class="score">Final Score: {{ score }} / {{ rounds * 3 }}</p>
    <button @click="$emit('restart')" class="play-again">Play Again</button>
  </div>
</template>



<script>
export default {
  name: 'FinalScore',
  props: {
    score: Number,
    rounds: Number
  }
}
</script>


<style>
@import url('https://fonts.googleapis.com/css2?family=Lilita+One&display=swap');

body {
  height: 100vh;
  margin: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  font-family: 'Lilita One', cursive;
  background: linear-gradient(145deg, #ffeaff, #ffd6fa, #ffccf9);
  background-size: 400% 400%;
  animation: sparkleBackground 12s ease infinite;
}

.final-score-box {
  background-color: #fff0fb;
  padding: 40px;
  border-radius: 20px;
  text-align: center;
  box-shadow: 0 0 20px rgba(255, 182, 193, 0.4);
  animation: fadeIn 1s ease;
  width: 100%;
  max-width: 400px;
}

.final-score-box h1 {
  color: #c600a1;
  font-size: 32px;
  margin-bottom: 16px;
}

.final-score-box p {
  font-size: 18px;
  color: #7a007a;
  margin: 10px 0;
}

.score {
  font-size: 22px;
  font-weight: bold;
  color: #9d009d;
}

.play-again {
  margin-top: 20px;
  background: linear-gradient(to right, #ff85c1, #da70d6);
  color: white;
  font-size: 16px;
  padding: 12px 24px;
  border: none;
  border-radius: 16px;
  cursor: pointer;
  font-family: 'Lilita One', cursive;
  position: relative;
  overflow: hidden;
  transition: transform 0.2s ease;
}

.play-again:hover {
  transform: scale(1.05);
  box-shadow: 0 0 12px #ffb3f6;
}

/* Background animation */
@keyframes sparkleBackground {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}

@keyframes fadeIn {
  from { opacity: 0; transform: scale(0.95); }
  to { opacity: 1; transform: scale(1); }
}

</style>


