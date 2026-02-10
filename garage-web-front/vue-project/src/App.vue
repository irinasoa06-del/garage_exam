<script setup>
import { RouterView } from 'vue-router'
import NavBar from './components/NavBar.vue'
</script>

<template>
  <div class="app-container">
    <NavBar />
    <main>
      <RouterView />
    </main>
  </div>
</template>

<style>
/* Global styles */
body {
  margin: 0;
  font-family: 'Inter', sans-serif;
  background-color: #f8f9fa;
  color: #343a40;
}

main {
  min-height: calc(100vh - 64px);
}
</style>
