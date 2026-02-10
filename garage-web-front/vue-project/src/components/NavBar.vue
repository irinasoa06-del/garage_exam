<template>
  <nav class="navbar">
    <div class="nav-content">
      <router-link to="/" class="brand">
        <i class="fas fa-car brand-icon"></i> 
        <span class="brand-text">Garage Auto</span>
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
  background: rgba(12, 46, 61, 0.95);
  backdrop-filter: blur(10px);
  -webkit-backdrop-filter: blur(10px);
  border-bottom: 2px solid var(--primary);
  padding: 0 1.5rem;
  height: 64px;
  display: flex;
  align-items: center;
  position: sticky;
  top: 0;
  z-index: 100;
  box-shadow: 0 4px 20px rgba(24, 103, 132, 0.3);
  transition: var(--transition);
}

.navbar::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 2px;
  background: var(--primary-gradient);
  opacity: 1;
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
  font-weight: 800;
  font-size: 1.5rem;
  color: var(--primary);
  text-decoration: none;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  transition: var(--transition);
  text-shadow: 0 2px 10px rgba(231, 76, 60, 0.3);
}

.brand:hover {
  transform: scale(1.05);
  color: var(--primary-light);
}

.brand-icon {
  font-size: 2rem;
  filter: drop-shadow(0 2px 8px rgba(231, 76, 60, 0.5));
  animation: pulse-glow 3s infinite;
}

@keyframes pulse-glow {
  0%, 100% {
    filter: drop-shadow(0 2px 8px rgba(231, 76, 60, 0.5));
  }
  50% {
    filter: drop-shadow(0 4px 16px rgba(231, 76, 60, 0.8));
  }
}

.brand-text {
  position: relative;
  letter-spacing: 0.05em;
}

.links {
  display: flex;
  gap: 0.5rem;
  align-items: center;
}

.nav-link {
  color: var(--text-muted);
  text-decoration: none;
  padding: 0.625rem 1.25rem;
  border-radius: var(--radius-md);
  font-weight: 600;
  font-size: 0.875rem;
  transition: var(--transition);
  position: relative;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.nav-link::before {
  content: '';
  position: absolute;
  bottom: 0;
  left: 50%;
  width: 0;
  height: 2px;
  background: var(--primary-gradient);
  transform: translateX(-50%);
  transition: var(--transition);
}

.nav-link:hover {
  background-color: rgba(231, 76, 60, 0.1);
  color: var(--primary);
}

.nav-link:hover::before {
  width: 80%;
}

.nav-link.active {
  background: linear-gradient(135deg, rgba(231, 76, 60, 0.2) 0%, rgba(211, 84, 0, 0.2) 100%);
  color: var(--primary);
  font-weight: 700;
}

.nav-link.active::before {
  width: 80%;
}

.nav-link.logout {
  color: var(--primary-light);
}

.nav-link.logout:hover {
  background-color: rgba(231, 76, 60, 0.15);
  color: #FFFFFF;
}

.nav-link.logout:hover::before {
  background: linear-gradient(135deg, #EC7063 0%, #E74C3C 100%);
}
</style>
