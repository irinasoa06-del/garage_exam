<template>
  <div class="dashboard-container">
    <h1>Tableau de Bord Atelier</h1>
    
    <div class="stats-grid">
      <div class="stat-card">
        <h3>Réparations en cours</h3>
        <p class="number">{{ reparations.length }}</p>
      </div>
      <div class="stat-card">
        <h3>Chiffre d'affaires (Est.)</h3>
        <p class="number">{{ totalRevenue }} Ar</p>
      </div>
    </div>

    <div class="actions">
      <button @click="showNewReparationModal = true" class="btn-primary">Nouvelle Réparation</button>
      <button @click="logout" class="btn-danger">Déconnexion</button>
    </div>

    <div class="reparations-list">
      <h2>File d'attente & En cours</h2>
      <table>
        <thead>
          <tr>
            <th>Client</th>
            <th>Type</th>
            <th>Statut</th>
            <th>Progression</th>
            <th>Technicien</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="rep in reparations" :key="rep.id">
            <td>{{ rep.voiture?.user?.nom }} {{ rep.voiture?.user?.prenom }}<br><small>{{ rep.voiture?.modele }}</small></td>
            <td>{{ rep.type?.nom }}</td>
            <td><span :class="'badge ' + rep.statut">{{ rep.statut }}</span></td>
            <td>
              <div class="progress-bar">
                <div class="fill" :style="{ width: rep.progression + '%' }"></div>
              </div>
              {{ rep.progression }}%
            </td>
            <td>{{ rep.technicien?.nom || '-' }}</td>
            <td>
              <button v-if="rep.statut === 'en_attente'" @click="startReparation(rep.id)">Commencer</button>
              <button v-if="rep.statut === 'en_cours'" @click="updateProgress(rep.id, rep.progression + 10)">+10%</button>
              <button v-if="rep.statut === 'en_cours'" @click="finishReparation(rep.id)">Terminer</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import api from '../../services/api';
import { firebaseService } from '../../services/firebase';

const router = useRouter();
const reparations = ref([]);
const showNewReparationModal = ref(false);

// Calcul du CA mockup (basé sur les items chargés)
const totalRevenue = computed(() => {
    return reparations.value.reduce((acc, curr) => acc + (curr.type?.prix_unitaire || 0), 0);
});

onMounted(async () => {
    loadReparations();
    
    // Écoute temps réel Firebase pour les mises à jour
    firebaseService.getClients((data) => {
        // En vrai app, on mergerait : l'API donne les détails relationnels (user, voiture), Firebase donne le statut live.
        // Pour simplifier ici, on raffraichit la liste quand Firebase notifie un changement.
        console.log("Firebase update received", data);
        loadReparations();
    });
});

async function loadReparations() {
    try {
        const response = await api.get('/reparations/en-cours');
        reparations.value = response.data.reparations;
    } catch (e) {
        console.error("Erreur chargement", e);
    }
}

async function startReparation(id) {
    try {
        await api.post(`/reparations/${id}/commencer`);
        // loadReparations(); // Géré par Firebase callback en théorie, mais sécurité
    } catch (e) {
        alert(e.response?.data?.message || "Erreur");
    }
}

async function updateProgress(id, val) {
    if (val > 100) val = 100;
    try {
        await api.put(`/reparations/${id}/progression`, { progression: val });
    } catch (e) {
        console.error(e);
    }
}

async function finishReparation(id) {
    if (!confirm("Terminer cette réparation ?")) return;
    try {
        await api.post(`/reparations/${id}/terminer`);
    } catch (e) {
        console.error(e);
    }
}

function logout() {
    localStorage.removeItem('auth_token');
    router.push('/login');
}
</script>

<style scoped>
.dashboard-container { padding: 2rem; }
.stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
.stat-card { background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
.number { font-size: 2rem; font-weight: bold; color: #2c3e50; }
.actions { margin-bottom: 2rem; display: flex; gap: 1rem; }
button { cursor: pointer; padding: 0.5rem 1rem; border: none; border-radius: 4px; }
.btn-primary { background: #3498db; color: white; }
.btn-danger { background: #e74c3c; color: white; }
table { width: 100%; border-collapse: collapse; background: white; }
th, td { padding: 1rem; text-align: left; border-bottom: 1px solid #eee; }
.badge { padding: 0.25rem 0.5rem; border-radius: 12px; font-size: 0.8rem; }
.badge.en_attente { background: #f1c40f; color: #fff; }
.badge.en_cours { background: #3498db; color: #fff; }
.progress-bar { width: 100px; height: 8px; background: #eee; border-radius: 4px; display: inline-block; margin-right: 5px; }
.fill { height: 100%; background: #2ecc71; border-radius: 4px; transition: width 0.3s; }
</style>
