<template>
  <div class="login-page">
    <div class="card login-card">
      <div class="login-header">
        <h1>Espace Garage</h1>
        <p class="text-muted">Connectez-vous pour gérer votre atelier</p>
      </div>

      <form @submit.prevent="login">
        <div class="form-group">
          <label>Adresse Email</label>
          <input type="email" v-model="email" required placeholder="admin@garage.com">
        </div>
        <div class="form-group">
          <label>Mot de passe</label>
          <input type="password" v-model="password" required placeholder="••••••••">
        </div>
        
        <button type="submit" class="btn btn-primary btn-block" :disabled="loading">
          {{ loading ? 'Connexion en cours...' : 'Se connecter' }}
        </button>
      </form>

      <div v-if="error" class="error-badge">
        {{ error }}
      </div>
      
      <div class="login-footer">
        <router-link to="/" class="text-sm">Retour à l'accueil</router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import api from '../services/api';

const email = ref('');
const password = ref('');
const error = ref('');
const loading = ref(false);
const router = useRouter();

async function login() {
  error.value = '';
  loading.value = true;
  try {
    const response = await api.post('login', {
      email: email.value,
      password: password.value
    });
    
    let data = response.data;
    if (typeof data === 'string') {
      data = data.replace(/^\uFEFF/, '').replace(/^[\s\u200B-\u200D\uFEFF]+/, '').trim();
      try {
        data = JSON.parse(data);
      } catch (e) {
        throw new Error('Erreur de formatage serveur');
      }
    }
    
    if (data && data.token && data.user) {
      localStorage.setItem('auth_token', data.token);
      localStorage.setItem('isAdmin', data.user.role === 'admin' ? 'true' : 'false');
      
      if (data.user.role === 'admin') {
        router.push('/admin/dashboard');
      } else {
        router.push('/');
      }
    } else {
      error.value = 'Réponse invalide du serveur';
    }
    
  } catch (err) {
    console.error('Login Error:', err);
    error.value = err.response?.data?.message || err.message || 'Identifiants incorrects';
  } finally {
    loading.value = false;
  }
}
</script>

<style scoped>
.login-page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: var(--bg-app);
}

.login-card {
  width: 100%;
  max-width: 420px;
  padding: 2.5rem;
}

.login-header {
  text-align: center;
  margin-bottom: 2rem;
}

.brand-icon {
  font-size: 3rem;
  display: block;
  margin-bottom: 1rem;
}

.login-header h1 {
  margin-bottom: 0.5rem;
  font-size: 1.5rem;
}

.btn-block {
  width: 100%;
  margin-top: 1.5rem;
  height: 46px;
  font-size: 1rem;
}

.error-badge {
  background-color: #fee2e2;
  color: var(--danger);
  padding: 0.75rem;
  border-radius: var(--radius-md);
  margin-top: 1.5rem;
  font-size: 0.875rem;
  text-align: center;
  border: 1px solid #fecaca;
}

.login-footer {
  text-align: center;
  margin-top: 2rem;
}

.login-footer a {
  color: var(--primary);
  text-decoration: none;
}
</style>
