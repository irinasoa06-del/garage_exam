<script setup>
import { RouterView } from 'vue-router'
</script>

<template>
  <div class="app-container">
    <header>
      <!-- Navbar global component if needed, or handled in views -->
    </header>

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
</style>
