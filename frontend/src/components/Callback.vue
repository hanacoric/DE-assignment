<template>
  <div class="callback-page">
    <p>Connecting to Spotify...</p>
  </div>
</template>

<script>
export default {
  name: 'Callback',
  async mounted() {
    const code = new URLSearchParams(window.location.search).get('code');

    if (code) {
      try {
        const res = await fetch(`http://127.0.0.1:8000/spotify/callback?code=${code}`);
        const data = await res.json();
        console.log('Spotify Access Token:', data.access_token);

        // Save token (you can use localStorage, Vuex, Pinia, etc.)
        localStorage.setItem('spotify_access_token', data.access_token);

        // Optional: redirect user back to home or game
        this.$router.push('/');
      } catch (err) {
        console.error('Failed to fetch token:', err);
      }
    }
  }
};
</script>

<style scoped>
.callback-page {
  text-align: center;
  margin-top: 100px;
  font-size: 18px;
}
</style>
