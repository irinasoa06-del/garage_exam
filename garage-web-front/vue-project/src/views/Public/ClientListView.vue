<template>
  <div class="public-view">
    <NavBar /> 
    <div class="content">
      <h1>Suivi des Réparations</h1>
      
      <div v-if="loading">Chargement...</div>
      
      <div v-else class="status-board">
        <div v-if="clients.length === 0" class="empty-state">
          Aucune voiture en réparation actuellement.
        </div>
        
        <div v-else class="clients-grid">
          <div v-for="client in clients" :key="client.id" class="client-card">
            <h3>{{ client.nom }}</h3>
            <div class="details">
              <p><strong>Voiture:</strong> {{ client.voiture }}</p>
              <p><strong>Statut:</strong> <span :class="client.statut">{{ client.statut }}</span></p>
              <p><strong>Intervention:</strong> {{ client.typeIntervention }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import NavBar from '../../components/NavBar.vue';
import { firebaseService } from '../../services/firebase';

const clients = ref([]);
const loading = ref(true);

onMounted(() => {
  // Setup real-time listener
  firebaseService.getClients((data) => {
    clients.value = data;
    loading.value = false;
  });
});
</script>

<style scoped>
.content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
}
.clients-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 2rem;
}
.client-card {
  border: 1px solid #ddd;
  padding: 1.5rem;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.details p {
  margin: 0.5rem 0;
}
.en-cours { color: orange; font-weight: bold; }
.termine { color: green; font-weight: bold; }
</style>
