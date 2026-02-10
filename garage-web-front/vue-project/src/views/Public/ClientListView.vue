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
          <div v-for="client in clients" :key="client.id" class="card client-card">
            <div class="card-header justify-between items-center mb-4">
               <h3>{{ client.nom }}</h3>
               <span :class="['badge', client.statut === 'terminee' ? 'success' : 'warning']">
                 {{ client.statut === 'terminee' ? 'Terminé' : 'En cours' }}
               </span>
            </div>
            <div class="details">
              <div class="detail-row">
                 <span class="label">Véhicule :</span>
                 <span class="value">{{ client.voiture }}</span>
              </div>
              <div class="detail-row">
                 <span class="label">Intervention :</span>
                 <span class="value">{{ client.typeIntervention }}</span>
              </div>
            </div>
            <div v-if="client.statut !== 'terminee'" class="progress-section mt-4">
               <div class="progress-bar-container">
                  <div class="progress-bar-fill" :style="{ width: (client.progression || 0) + '%' }"></div>
               </div>
               <div class="text-right text-sm text-muted mt-1">{{ client.progression || 0 }}%</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { firebaseService } from '../../services/firebase';

const clients = ref([]);
const loading = ref(true);

onMounted(() => {
  firebaseService.getClients((data) => {
    clients.value = data;
    loading.value = false;
  });
});
</script>

<style scoped>
.content {
  max-width: 1200px;
  margin: 3rem auto;
  padding: 0 1.5rem;
}

.page-header {
  margin-bottom: 3rem;
}

.text-center { text-align: center; }

.clients-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 2rem;
}

.client-card {
  transition: transform 0.2s;
  border-top: 4px solid var(--primary);
}

.client-card:hover {
  transform: translateY(-4px);
  box-shadow: var(--shadow-lg);
}

.card-header {
  display: flex;
}

.detail-row {
  display: flex;
  margin-bottom: 0.75rem;
  font-size: 0.9375rem;
}

.label {
  color: var(--text-muted);
  width: 100px;
  flex-shrink: 0;
}

.value {
  font-weight: 500;
  color: var(--text-main);
}

.progress-bar-container {
  height: 8px;
  background-color: var(--bg-app);
  border-radius: 4px;
  overflow: hidden;
}

.progress-bar-fill {
  height: 100%;
  background-color: var(--success);
  transition: width 0.5s ease-in-out;
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
