<template>
  <nav>
    <div class="nav-content">
      <router-link to="/" class="brand">Garage Info</router-link>
      <div class="links">
        <template v-if="isAdmin">
          <router-link to="/admin/dashboard">Dashboard</router-link>
          <router-link to="/admin/interventions">Interventions</router-link>
          <a href="#" @click.prevent="logout">Déconnexion</a>
        </template>
        <template v-else>
          <router-link to="/login">Espace Garage</router-link>
        </template>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { computed } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const isAdmin = computed(() => localStorage.getItem('isAdmin') === 'true');

function logout() {
  localStorage.removeItem('isAdmin');
  router.push('/');
  // Force reload to update nav state simply
  window.location.reload(); 
}
</script>

<style scoped>
nav {
  background-color: #333;
  color: white;
  padding: 1rem;
}
.nav-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  max-width: 1200px;
  margin: 0 auto;
}
.brand {
  font-weight: bold;
  font-size: 1.2rem;
  color: white;
  text-decoration: none;
}
.links a {
  color: white;
  margin-left: 1rem;
  text-decoration: none;
}
.links a:hover {
  text-decoration: underline;
}
</style>
