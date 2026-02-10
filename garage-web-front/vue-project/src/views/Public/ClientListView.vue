<template>
  <div class="public-view">
    <div class="content">
      <div class="page-header text-center">
        <h1>Suivi en Temps Réel</h1>
        <p class="text-muted">Consultez l'avancement des réparations à l'atelier</p>
      </div>
      
      <div v-if="loading" class="text-center py-8">
        <div class="spinner"></div>
        <p class="mt-4 text-muted">Récupération des données...</p>
      </div>
      
      <div v-else class="status-board">
        <div v-if="clients.length === 0" class="card empty-state text-center">
          <p class="text-muted mb-0">Aucun véhicule n'est actuellement en cours de traitement.</p>
        </div>
        
        <div v-else class="clients-grid">
          <div v-for="client in clients" :key="client.user_id" 
               class="card client-card"
               @click="viewClientHistory(client.user_id)"
               style="cursor: pointer;">
            <div class="card-header justify-between items-center mb-4">
               <h3>{{ client.nom }} {{ client.prenom }}</h3>
            </div>
            <div class="details">
              <div class="detail-row">
                 <span class="label">Email :</span>
                 <span class="value">{{ client.email }}</span>
              </div>
              <div class="detail-row">
                 <span class="label">Téléphone :</span>
                 <span class="value">{{ client.telephone || '-' }}</span>
              </div>
              <div class="detail-row">
                 <span class="label">Voitures :</span>
                 <span class="value">{{ client.voitures?.length || 0 }}</span>
              </div>
            </div>
            <div class="mt-4" style="text-align: center; color: var(--primary); font-weight: 500;">
              Cliquez pour voir l'historique →
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '../../services/api';

const router = useRouter();
const clients = ref([]);
const loading = ref(true);

onMounted(async () => {
  await loadClients();
});

async function loadClients() {
  try {
    const response = await api.get('/clients');
    // Transformer les données pour afficher les clients avec leurs réparations en cours
    clients.value = response.data.clients || [];
  } catch (error) {
    console.error('Erreur chargement clients:', error);
  } finally {
    loading.value = false;
  }
}

function viewClientHistory(clientId) {
  router.push(`/client/${clientId}/history`);
}
</script>

<style scoped>
.public-view {
  min-height: calc(100vh - 64px);
}

.content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 3rem 1.5rem;
}

.page-header {
  margin-bottom: 3rem;
  padding: 3rem 2rem;
  background: var(--primary-gradient);
  border-radius: var(--radius-xl);
  color: white;
  box-shadow: 0 10px 40px rgba(24, 103, 132, 0.3);
  position: relative;
  overflow: hidden;
}

.page-header::before {
  content: '';
  position: absolute;
  top: -50%;
  right: -20%;
  width: 400px;
  height: 400px;
  background: rgba(255, 255, 255, 0.05);
  border-radius: 50%;
}

.page-header h1 {
  font-size: 2.5rem;
  font-weight: 800;
  margin: 0 0 0.5rem 0;
  color: white;
  position: relative;
  z-index: 1;
}

.page-header p {
  font-size: 1.125rem;
  margin: 0;
  opacity: 0.9;
  position: relative;
  z-index: 1;
}

.text-center {
  text-align: center;
}

.py-8 {
  padding-top: 2rem;
  padding-bottom: 2rem;
}

.spinner {
  margin: 0 auto;
}

.clients-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 1.5rem;
  animation: fadeInUp 0.5s ease-out;
}

.client-card {
  cursor: pointer !important;
  transition: var(--transition-slow);
  position: relative;
  overflow: hidden;
  background: linear-gradient(135deg, rgba(12, 46, 61, 0.95) 0%, rgba(24, 103, 132, 0.8) 100%);
  border: 2px solid rgba(24, 103, 132, 0.4);
}

.client-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 4px;
  background: var(--primary-gradient);
  transform: scaleX(0);
  transform-origin: left;
  transition: var(--transition);
}

.client-card:hover::before {
  transform: scaleX(1);
}

.client-card:hover {
  transform: translateY(-8px) scale(1.02);
  box-shadow: var(--shadow-xl), var(--shadow-glow-hover);
}

.client-card:active {
  transform: translateY(-6px) scale(1.01);
}

.card-header {
  display: flex;
  position: relative;
}

.card-header h3 {
  margin: 0;
  font-size: 1.25rem;
  font-weight: 700;
  background: var(--primary-gradient);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.client-card::after {
  content: '\\f007';  /* FontAwesome user icon */
  font-family: 'Font Awesome 5 Free';
  font-weight: 900;
  position: absolute;
  top: 1rem;
  right: 1rem;
  font-size: 3rem;
  opacity: 0.1;
  pointer-events: none;
}

.details {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.detail-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.5rem 0;
  border-bottom: 1px solid var(--border-color);
}

.detail-row:last-child {
  border-bottom: none;
}

.detail-row .label {
  font-size: 0.875rem;
  color: var(--text-muted);
  font-weight: 600;
}

.detail-row .value {
  font-size: 0.875rem;
  color: var(--text-main);
}

.text-right { text-align: right; }

.py-8 { padding: 4rem 0; }

.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid var(--border-color);
  border-top-color: var(--primary);
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}
</style>
