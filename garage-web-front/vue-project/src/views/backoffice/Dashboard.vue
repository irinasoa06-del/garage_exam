<!-- src/views/backoffice/Dashboard.vue -->
<template>
  <div class="dashboard">
    <header class="dashboard-header">
      <h1>Backoffice Garage - Tableau de bord</h1>
      <button @click="logout" class="logout-btn">Déconnexion</button>
    </header>
    
    <div class="dashboard-nav">
      <router-link to="/backoffice/statistics" class="nav-btn">📊 Statistiques</router-link>
      <router-link to="/backoffice/interventions" class="nav-btn">🔧 Gestion des Interventions</router-link>
      <router-link to="/backoffice/sync" class="nav-btn">🔄 Synchronisation Firebase</router-link>
    </div>
    
    <div class="dashboard-overview">
      <h2>Aperçu rapide</h2>
      <div class="stats-grid">
        <div class="stat-card">
          <h3>Clients actifs</h3>
          <p class="stat-number">{{ activeClients }}</p>
        </div>
        <div class="stat-card">
          <h3>Voitures en réparation</h3>
          <p class="stat-number">{{ carsInRepair }}/2</p>
        </div>
        <div class="stat-card">
          <h3>Chiffre d'affaires du jour</h3>
          <p class="stat-number">{{ dailyRevenue }} €</p>
        </div>
      </div>
    </div>
    
    <div class="current-repairs">
      <h2>Réparations en cours</h2>
      <div v-if="loading" class="loading">Chargement...</div>
      <div v-else-if="currentRepairs.length === 0" class="empty-state">
        Aucune réparation en cours
      </div>
      <div v-else class="repairs-list">
        <div v-for="repair in currentRepairs" :key="repair.id" class="repair-item">
          <span class="car-info">Voiture #{{ repair.carId }}</span>
          <span class="client-info">{{ repair.clientName }}</span>
          <span class="status" :class="repair.status">{{ repair.status }}</span>
          <span class="slot">Slot: {{ repair.slot }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Dashboard',
  data() {
    return {
      activeClients: 0,
      carsInRepair: 0,
      dailyRevenue: 0,
      currentRepairs: [],
      loading: true
    }
  },
  async mounted() {
    await this.fetchDashboardData();
  },
  methods: {
    async fetchDashboardData() {
      try {
        // Récupération des données depuis l'API
        const response = await fetch('/api/dashboard');
        const data = await response.json();
        
        this.activeClients = data.activeClients;
        this.carsInRepair = data.carsInRepair;
        this.dailyRevenue = data.dailyRevenue;
        this.currentRepairs = data.currentRepairs;
      } catch (error) {
        console.error('Erreur lors du chargement:', error);
      } finally {
        this.loading = false;
      }
    },
    logout() {
      localStorage.removeItem('auth_token');
      this.$router.push('/backoffice/login');
    }
  }
}
</script>

<style scoped>
.dashboard {
  padding: 20px;
  font-family: Arial, sans-serif;
}

.dashboard-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
  padding-bottom: 15px;
  border-bottom: 2px solid #ddd;
}

.dashboard-nav {
  display: flex;
  gap: 15px;
  margin-bottom: 30px;
}

.nav-btn {
  padding: 12px 24px;
  background-color: #3498db;
  color: white;
  text-decoration: none;
  border-radius: 6px;
  transition: background-color 0.3s;
}

.nav-btn:hover {
  background-color: #2980b9;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
  margin: 20px 0;
}

.stat-card {
  background: white;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  text-align: center;
}

.stat-number {
  font-size: 2rem;
  font-weight: bold;
  color: #2c3e50;
  margin: 10px 0;
}

.current-repairs {
  background: white;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  margin-top: 30px;
}

.repairs-list {
  margin-top: 15px;
}

.repair-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px;
  border-bottom: 1px solid #eee;
}

.repair-item:last-child {
  border-bottom: none;
}

.status {
  padding: 5px 10px;
  border-radius: 4px;
  font-size: 0.9rem;
}

.status.en-cours {
  background-color: #f39c12;
  color: white;
}

.status.termine {
  background-color: #2ecc71;
  color: white;
}

.logout-btn {
  padding: 10px 20px;
  background-color: #e74c3c;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.logout-btn:hover {
  background-color: #c0392b;
}

.empty-state {
  text-align: center;
  padding: 40px;
  color: #7f8c8d;
  font-style: italic;
}
</style>