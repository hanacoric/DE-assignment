<template>
  <div>
    <h2>Ping Test</h2>
    <p v-if="message"> {{ message }}</p>
    <p v-else>Loading...</p>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'PingTest',
  data() {
    return {
      message: null,
    };
  },
  mounted() {
    axios.get('http://127.0.0.1:8000/api/ping')

      .then(response => {
        this.message = response.data.message;
      })
      .catch(error => {
        console.error('Error:', error);
        this.message = 'Failed to reach backend.';
      });

  },
};
</script>
