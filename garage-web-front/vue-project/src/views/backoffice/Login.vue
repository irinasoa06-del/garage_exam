<!-- src/views/backoffice/Login.vue -->
<template>
  <div class="login-container">
    <div class="login-box">
      <h1>Connexion Backoffice</h1>
      <form @submit.prevent="handleLogin" class="login-form">
        <div class="form-group">
          <label for="username">Nom d'utilisateur</label>
          <input
            type="text"
            id="username"
            v-model="username"
            required
            placeholder="admin"
          >
        </div>
        
        <div class="form-group">
          <label for="password">Mot de passe</label>
          <input
            type="password"
            id="password"
            v-model="password"
            required
            placeholder="••••••••"
          >
        </div>
        
        <div v-if="errorMessage" class="error-message">
          {{ errorMessage }}
        </div>
        
        <button type="submit" :disabled="loading" class="login-btn">
          {{ loading ? 'Connexion...' : 'Se connecter' }}
        </button>
      </form>
      
      <div class="demo-credentials">
        <p><strong>Identifiants par défaut :</strong></p>
        <p>Utilisateur: admin</p>
        <p>Mot de passe: admin123</p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Login',
  data() {
    return {
      username: '',
      password: '',
      errorMessage: '',
      loading: false
    }
  },
  methods: {
    async handleLogin() {
      this.loading = true;
      this.errorMessage = '';
      
      try {
        // Simulation d'authentification
        // En production, remplacer par un appel API
        await new Promise(resolve => setTimeout(resolve, 1000));
        
        if (this.username === 'admin' && this.password === 'admin123') {
          localStorage.setItem('auth_token', 'fake-jwt-token');
          localStorage.setItem('user_role', 'admin');
          this.$router.push('/backoffice/dashboard');
        } else {
          this.errorMessage = 'Identifiants incorrects';
        }
      } catch (error) {
        this.errorMessage = 'Erreur de connexion';
      } finally {
        this.loading = false;
      }
    }
  }
}
</script>

<style scoped>
.login-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.login-box {
  background: white;
  padding: 40px;
  border-radius: 10px;
  box-shadow: 0 15px 35px rgba(0,0,0,0.2);
  width: 100%;
  max-width: 400px;
}

.login-form {
  margin: 30px 0;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  margin-bottom: 8px;
  color: #333;
  font-weight: 500;
}

.form-group input {
  width: 100%;
  padding: 12px;
  border: 1px solid #ddd;
  border-radius: 6px;
  font-size: 16px;
  transition: border-color 0.3s;
}

.form-group input:focus {
  outline: none;
  border-color: #667eea;
}

.login-btn {
  width: 100%;
  padding: 14px;
  background: #667eea;
  color: white;
  border: none;
  border-radius: 6px;
  font-size: 16px;
  cursor: pointer;
  transition: background 0.3s;
}

.login-btn:hover:not(:disabled) {
  background: #5a67d8;
}

.login-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.error-message {
  background-color: #fed7d7;
  color: #c53030;
  padding: 12px;
  border-radius: 6px;
  margin-bottom: 20px;
  text-align: center;
}

.demo-credentials {
  margin-top: 20px;
  padding: 15px;
  background-color: #f7fafc;
  border-radius: 6px;
  border-left: 4px solid #4299e1;
}

.demo-credentials p {
  margin: 5px 0;
  color: #4a5568;
}
</style>