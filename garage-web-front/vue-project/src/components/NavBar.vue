<template>
  <nav class="navbar">
    <div class="nav-content">
      <router-link to="/" class="brand">
        <span class="brand-icon">🚗</span> Garage Manager
      </router-link>
      <div class="links">
        <template v-if="isLoggedIn">
          <template v-if="isAdmin">
            <router-link to="/admin/dashboard" class="nav-link" active-class="active">Dashboard</router-link>
            <router-link to="/admin/interventions" class="nav-link" active-class="active">Interventions</router-link>
          </template>
          <!-- Common links for all logged in users could go here -->
          <a href="#" @click.prevent="logout" class="nav-link logout">Déconnexion</a>
        </template>
        <template v-else>
          <router-link v-if="!isLoginPage" to="/login" class="btn btn-primary btn-sm">Espace Garage</router-link>
        </template>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';

const router = useRouter();
const route = useRoute();
const isAdmin = ref(false);
const isLoggedIn = ref(false);
const isLoginPage = ref(false);

function checkAuth() {
  isLoggedIn.value = !!localStorage.getItem('auth_token');
  isAdmin.value = localStorage.getItem('isAdmin') === 'true';
  isLoginPage.value = route.path === '/login';
}

onMounted(() => {
  checkAuth();
});

watch(
  () => route.path,
  () => {
    checkAuth();
  }
);

function logout() {
  localStorage.removeItem('isAdmin');
  localStorage.removeItem('auth_token');
  localStorage.removeItem('user_role');
  isLoggedIn.value = false;
  isAdmin.value = false;
  router.push('/');
}
</script>

<style scoped>
.navbar {
  background-color: var(--bg-surface);
  border-bottom: 1px solid var(--border-color);
  padding: 0 1.5rem;
  height: 64px;
  display: flex;
  align-items: center;
  position: sticky;
  top: 0;
  z-index: 100;
  box-shadow: var(--shadow-sm);
}

.nav-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  width: 100%;
  max-width: 1200px;
  margin: 0 auto;
}

.brand {
  font-weight: 700;
  font-size: 1.25rem;
  color: var(--primary);
  text-decoration: none;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.brand-icon {
  font-size: 1.5rem;
}

.links {
  display: flex;
  gap: 0.5rem;
  align-items: center;
}

.nav-link {
  color: var(--text-muted);
  text-decoration: none;
  padding: 0.5rem 1rem;
  border-radius: var(--radius-md);
  font-weight: 500;
  font-size: 0.875rem;
  transition: all 0.2s;
}

.nav-link:hover {
  background-color: var(--bg-app);
  color: var(--primary);
}

.nav-link.active {
  background-color: #eff6ff;
  color: var(--primary);
  font-weight: 600;
}

.nav-link.logout {
  color: var(--danger);
}

.nav-link.logout:hover {
  background-color: #fef2f2;
}
</style>
