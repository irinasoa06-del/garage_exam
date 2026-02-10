<template>
  <div class="login-container">
    <h1>Espace Garage</h1>
    <form @submit.prevent="login">
      <div class="form-group">
        <label>Email</label>
        <input type="email" v-model="email" required placeholder="admin@garage.com">
      </div>
      <div class="form-group">
        <label>Mot de passe</label>
        <input type="password" v-model="password" required placeholder="admin123">
      </div>
      <button type="submit">Se connecter</button>
    </form>
    <p v-if="error" class="error">{{ error }}</p>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import api from '../services/api';

const email = ref('');
const password = ref('');
const error = ref('');
const router = useRouter();

async function login() {
  try {
    const response = await api.post('login', {
      email: email.value,
      password: password.value
    });
    
    // Auth success
    if (response.data && response.data.token && response.data.user) {
      localStorage.setItem('auth_token', response.data.token);
      const user = response.data.user;
      localStorage.setItem('user_role', user.role || 'client');

      if (user.role === 'admin') {
        router.push('/admin/dashboard');
      } else {
        router.push('/');
      }
    } else {
      console.error('Invalid response structure:', response.data);
      error.value = 'Réponse du serveur invalide';
    }
    
  } catch (err) {
    console.error('Login Error:', err);
    error.value = err.response?.data?.message || err.message || 'Erreur de connexion';
  }
}
</script>

<style scoped>
.login-container {
  max-width: 400px;
  margin: 2rem auto;
  padding: 2rem;
  border: 1px solid #ccc;
  border-radius: 8px;
}
.form-group {
  margin-bottom: 1rem;
}
label {
  display: block;
  margin-bottom: 0.5rem;
}
input {
  width: 100%;
  padding: 0.5rem;
}
button {
  width: 100%;
  padding: 0.5rem;
  background-color: #2c3e50;
  color: white;
  border: none;
  cursor: pointer;
}
.error {
  color: red;
  margin-top: 1rem;
  text-align: center;
}
</style>
