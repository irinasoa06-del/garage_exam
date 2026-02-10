<template>
  <div class="client-history-view">
    <div class="content">
      <!-- Header avec retour -->
      <div class="page-header">
        <button @click="goBack" class="btn-back">← Retour</button>
        <div v-if="!loading && client" class="client-info">
          <h1>{{ client.prenom }} {{ client.nom }}</h1>
          <p class="text-muted">{{ client.email }} • {{ client.telephone }}</p>
        </div>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="text-center py-8">
        <div class="spinner"></div>
        <p class="mt-4 text-muted">Chargement de l'historique...</p>
      </div>

      <!-- Contenu -->
      <div v-else-if="client">
        <!-- Statistiques -->
        <div class="stats-grid">
          <div class="card stat-card">
            <h3>Total dépensé</h3>
            <p class="number">{{ statistiques.total_depense }} Ar</p>
          </div>
          <div class="card stat-card">
            <h3>Réparations</h3>
            <p class="number">{{ statistiques.nombre_reparations }}</p>
          </div>
          <div class="card stat-card">
            <h3>Terminées</h3>
            <p class="number">{{ statistiques.reparations_terminees }}</p>
          </div>
        </div>

        <!-- Historique des réparations -->
        <div class="card table-container">
          <div style="padding: 1.5rem; border-bottom: 1px solid var(--border-color);">
            <h2 style="margin: 0; font-size: 1.125rem;">Historique des réparations</h2>
          </div>
          
          <div v-if="historique.length === 0" style="padding: 3rem; text-align: center; color: var(--text-muted);">
            Aucune réparation enregistrée pour ce client
          </div>

          <table v-else>
            <thead>
              <tr>
                <th>Date</th>
                <th>Voiture</th>
                <th>Type d'intervention</th>
                <th>Montant</th>
                <th>Statut</th>
                <th>Progression</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="rep in historique" :key="rep.reparation_id">
                <td>
                  <div style="font-weight: 500;">{{ formatDate(rep.date) }}</div>
                  <div class="text-muted text-sm" v-if="rep.date_debut">
                    Début: {{ formatDate(rep.date_debut) }}
                  </div>
                </td>
                <td>
                  <div style="font-weight: 500;">{{ rep.voiture.marque }} {{ rep.voiture.modele }}</div>
                  <div class="text-muted text-sm">{{ rep.voiture.immatriculation }}</div>
                </td>
                <td><span class="badge info">{{ rep.type_intervention }}</span></td>
                <td><strong>{{ rep.montant }} Ar</strong></td>
                <td><span :class="'badge ' + rep.statut">{{ formatStatut(rep.statut) }}</span></td>
                <td>
                  <div class="flex items-center">
                    <div class="progress-bar">
                      <div class="fill" :style="{ width: rep.progression + '%' }"></div>
                    </div>
                    <span class="text-sm font-bold">{{ rep.progression }}%</span>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Erreur -->
      <div v-else class="card error-state text-center">
        <p class="text-muted">Client non trouvé</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import api from '../../services/api';

const router = useRouter();
const route = useRoute();

const loading = ref(true);
const client = ref(null);
const statistiques = ref({
  total_depense: 0,
  nombre_reparations: 0,
  reparations_terminees: 0
});
const historique = ref([]);

onMounted(async () => {
  await loadClientHistory();
});

async function loadClientHistory() {
  try {
    const clientId = route.params.id;
    const response = await api.get(`/clients/${clientId}/repair-history`);
    
    client.value = response.data.client;
    statistiques.value = response.data.statistiques;
    historique.value = response.data.historique;
  } catch (error) {
    console.error('Erreur chargement historique:', error);
  } finally {
    loading.value = false;
  }
}

function goBack() {
  router.push('/');
}

function formatDate(date) {
  if (!date) return '-';
  return new Date(date).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
}

function formatStatut(statut) {
  const statuts = {
    'en_attente': 'En attente',
    'en_cours': 'En cours',
    'terminee': 'Terminée'
  };
  return statuts[statut] || statut;
}
</script>

<style scoped>
.client-history-view {
  min-height: 100vh;
  background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
}

.content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem 1.5rem;
}

.page-header {
  margin-bottom: 2.5rem;
  padding: 2rem;
  background: var(--primary-gradient);
  border-radius: var(--radius-xl);
  color: white;
  box-shadow: 0 10px 40px rgba(24, 103, 132, 0.3);
  position: relative;
  overflow: hidden;
  animation: fadeInUp 0.5s ease-out;
}

.page-header::before {
  content: '';
  position: absolute;
  top: -50%;
  right: -10%;
  width: 300px;
  height: 300px;
  background: rgba(255, 255, 255, 0.05);
  border-radius: 50%;
}

.btn-back {
  background: rgba(255, 255, 255, 0.2);
  border: 1px solid rgba(255, 255, 255, 0.3);
  color: white;
  padding: 0.5rem 1.25rem;
  border-radius: var(--radius-md);
  cursor: pointer;
  font-weight: 600;
  margin-bottom: 1.5rem;
  transition: var(--transition);
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  backdrop-filter: blur(10px);
}

.btn-back:hover {
  background: rgba(255, 255, 255, 0.3);
  transform: translateX(-4px);
}

.client-info {
  position: relative;
  z-index: 1;
}

.client-info h1 {
  margin: 0;
  font-size: 2.25rem;
  font-weight: 800;
  color: white;
}

.client-info p {
  margin: 0.75rem 0 0 0;
  font-size: 1.125rem;
  opacity: 0.9;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2.5rem;
  animation: fadeInUp 0.6s ease-out;
}

.stat-card {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  position: relative;
  overflow: hidden;
  background: linear-gradient(135deg, rgba(12, 46, 61, 0.95) 0%, rgba(24, 103, 132, 0.8) 100%);
  border: 2px solid rgba(24, 103, 132, 0.4);
}

.stat-card::before {
  font-family: 'Font Awesome 5 Free';
  font-weight: 900;
  position: absolute;
  top: 1rem;
  right: 1rem;
  font-size: 2.5rem;
  opacity: 0.1;
}

.stat-card:nth-child(1)::before {
  content: '\\f555';  /* Wallet icon */
}

.stat-card:nth-child(2)::before {
  content: '\\f0ad';  /* Wrench icon */
}

.stat-card:nth-child(3)::before {
  content: '\\f058';  /* Check circle icon */
}

.stat-card:hover {
  box-shadow: var(--shadow-xl), var(--shadow-glow);
  transform: translateY(-4px);
}

.stat-card h3 {
  font-size: 0.875rem;
  color: var(--text-muted);
  font-weight: 600;
  margin: 0;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.number {
  font-size: 2.5rem;
  font-weight: 800;
  background: var(--primary-gradient);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  margin: 0;
}

.table-container {
  animation: fadeInUp 0.7s ease-out;
  overflow: hidden;
}

.table-container > div:first-child {
  background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
  padding: 1.5rem;
  border-bottom: 2px solid var(--border-color);
}

.table-container h2 {
  margin: 0;
  font-size: 1.25rem;
  font-weight: 700;
  background: var(--primary-gradient);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

table tbody tr {
  transition: var(--transition);
}

table tbody tr:hover {
  transform: scale(1.005);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

table td strong {
  color: var(--accent-violet);
  font-size: 1.125rem;
}

.progress-bar {
  width: 100px;
  height: 10px;
  background: linear-gradient(90deg, #e5e7eb 0%, #d1d5db 100%);
  border-radius: 9999px;
  display: inline-block;
  margin-right: 8px;
  overflow: hidden;
  vertical-align: middle;
  box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
}

.fill {
  height: 100%;
  background: linear-gradient(90deg, #10b981 0%, #059669 100%);
  border-radius: 9999px;
  transition: width 0.5s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 0 10px rgba(16, 185, 129, 0.5);
}

.error-state {
  padding: 3rem;
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(254, 242, 242, 0.9) 100%);
}

.text-center { text-align: center; }
.py-8 { padding: 4rem 0; }
.mt-4 { margin-top: 1rem; }

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

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
