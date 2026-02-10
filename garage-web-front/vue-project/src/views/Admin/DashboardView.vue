<template>
  <div class="dashboard-container">
    <div class="page-header">
      <h1>Tableau de Bord Atelier</h1>
      <div class="actions">
        <button @click="showNewReparationModal = true" class="btn btn-primary">
          <span>+</span> Nouvelle Réparation
        </button>
      </div>
    </div>
    
    <div class="stats-grid">
      <div class="card stat-card">
        <h3>Réparations en cours</h3>
        <p class="number">{{ reparations.length }}</p>
      </div>
      <div class="card stat-card">
        <h3>Chiffre d'affaires (Est.)</h3>
        <p class="number">{{ totalRevenue }} Ar</p>
      </div>
    </div>

    <div class="card table-container">
      <div style="padding: 1.5rem; border-bottom: 1px solid var(--border-color);">
        <h2 style="margin: 0; font-size: 1.125rem;">File d'attente & En cours</h2>
      </div>
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
          <tr v-for="rep in reparations" :key="rep.reparation_id">
            <td>
              <div style="font-weight: 500;">{{ rep.voiture?.user?.nom }} {{ rep.voiture?.user?.prenom }}</div>
              <div class="text-muted text-sm">{{ rep.voiture?.modele }} - {{ rep.voiture?.immatriculation }}</div>
            </td>
            <td><span class="badge info">{{ rep.type?.nom }}</span></td>
            <td><span :class="'badge ' + rep.statut">{{ rep.statut }}</span></td>
            <td>
              <div class="flex items-center">
                <div class="progress-bar">
                  <div class="fill" :style="{ width: rep.progression + '%' }"></div>
                </div>
                <span class="text-sm font-bold">{{ rep.progression }}%</span>
              </div>
            </td>
            <td>{{ rep.technicien?.nom || '-' }}</td>
            <td>
              <div class="flex gap-2">
                <button v-if="rep.statut === 'en_attente'" @click="startReparation(rep.reparation_id)" class="btn btn-primary btn-sm">Commencer</button>
                <div v-if="rep.statut === 'en_cours'" class="flex gap-2">
                   <button @click="updateProgress(rep.reparation_id, rep.progression + 10)" class="btn btn-secondary btn-sm">+10%</button>
                   <button @click="finishReparation(rep.reparation_id)" class="btn btn-success btn-sm">Terminer</button>
                </div>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
      <div v-if="reparations.length === 0" style="padding: 3rem; text-align: center; color: var(--text-muted);">
        Aucune réparation en cours
      </div>
    </div>

    <!-- Modal Nouvelle Réparation -->
    <div v-if="showNewReparationModal" class="modal-overlay" @click.self="showNewReparationModal = false">
      <div class="modal-content">
        <h2>Nouvelle Réparation</h2>
        <form @submit.prevent="createReparation">
          <div class="form-group">
            <label>Voiture (Client)</label>
            <select v-model="newReparation.voiture_id" required>
              <option value="">Sélectionner une voiture</option>
              <option v-for="v in voitures" :key="v.voiture_id" :value="v.voiture_id">
                {{ v.marque }} {{ v.modele }} - {{ v.immatriculation }} ({{ v.user?.nom }})
              </option>
            </select>
          </div>
          <div class="form-group">
            <label>Type d'intervention</label>
            <select v-model="newReparation.type_intervention_id" required>
              <option value="">Sélectionner un type</option>
              <option v-for="t in typesIntervention" :key="t.type_id" :value="t.type_id">
                {{ t.nom }} - {{ t.prix_unitaire }} Ar
              </option>
            </select>
          </div>
          <div class="form-group">
            <label>Description (optionnel)</label>
            <textarea v-model="newReparation.description" rows="3"></textarea>
          </div>
          <div class="modal-actions">
            <button type="button" @click="showNewReparationModal = false" class="btn-secondary">Annuler</button>
            <button type="submit" class="btn-primary" :disabled="creatingReparation">
              {{ creatingReparation ? 'Création...' : 'Créer' }}
            </button>
          </div>
        </form>
      </div>
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
const voitures = ref([]); // List of available cars
const typesIntervention = ref([]); // List of intervention types
const creatingReparation = ref(false);

const newReparation = ref({
    voiture_id: '',
    type_intervention_id: '',
    description: ''
});

// Load helper data when modal opens
import { watch } from 'vue';
watch(showNewReparationModal, (newVal) => {
    if (newVal) {
        loadVoitures();
        loadTypesIntervention();
    }
});

async function loadVoitures() {
    try {
        const response = await api.get('/voitures');
        // Handle { voitures: [...] } structure
        voitures.value = response.data.voitures || response.data.data || response.data || [];
    } catch (e) {
        console.error("Erreur chargement voitures", e);
    }
}

async function loadTypesIntervention() {
    try {
        const response = await api.get('/types-intervention');
        // Handle { types: [...] } structure
        typesIntervention.value = response.data.types || response.data.data || response.data || [];
    } catch (e) {
        console.error("Erreur chargement types", e);
    }
}

async function createReparation() {
    creatingReparation.value = true;
    try {
        await api.post('/reparations', newReparation.value);
        showNewReparationModal.value = false;
        // Reset form
        newReparation.value = { voiture_id: '', type_intervention_id: '', description: '' };
        loadReparations(); // Reload list
    } catch (e) {
        alert(e.response?.data?.message || "Erreur création");
    } finally {
        creatingReparation.value = false;
    }
}

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
        
        // Handle BOM and string response edge cases
        let data = response.data;
        if (typeof data === 'string') {
            data = data.replace(/^\uFEFF/, '').replace(/^[\s\u200B-\u200D\uFEFF]+/, '').trim();
            try {
                data = JSON.parse(data);
            } catch (e) {
                console.error("Failed to parse reparations response:", e);
                reparations.value = [];
                return;
            }
        }
        
        // Safely assign reparations array - ensure it's always an array
        let result = [];
        if (Array.isArray(data)) {
            result = data;
        } else if (data && Array.isArray(data.reparations)) {
            result = data.reparations;
        } else if (data && Array.isArray(data.data)) {
            result = data.data;
        }
        reparations.value = result;
    } catch (e) {
        console.error("Erreur chargement", e);
        reparations.value = [];
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
.dashboard-container { 
  max-width: 1200px;
  margin: 2rem auto;
  padding: 0 1.5rem;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  padding: 2.5rem;
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
  right: -10%;
  width: 400px;
  height: 400px;
  background: rgba(255, 255, 255, 0.05);
  border-radius: 50%;
}

.page-header h1 {
  color: white;
  margin: 0;
  font-size: 2rem;
  font-weight: 800;
}

.stats-grid { 
  display: grid; 
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); 
  gap: 1.5rem; 
  margin-bottom: 2rem; 
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
  content: '';
  position: absolute;
  top: 0;
  right: 0;
  width: 100px;
  height: 100px;
  background: var(--primary-gradient);
  opacity: 0.2;
  border-radius: 50%;
  transform: translate(30%, -30%);
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
  background-clip:text;
}

.table-container {
  overflow: hidden;
}

.table-container > div:first-child {
  background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
}

table tbody tr {
  transition: var(--transition);
}

table tbody tr:hover {
  transform: scale(1.01);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

/* Modal specific overrides */
.modal-overlay { 
  position: fixed; 
  top: 0; 
  left: 0; 
  width: 100%; 
  height: 100%; 
  background: rgba(15, 23, 42, 0.6); 
  backdrop-filter: blur(4px);
  display: flex; 
  justify-content: center; 
  align-items: center; 
  z-index: 1000; 
  animation: fadeIn 0.2s;
}

.modal-content { 
  background: var(--bg-surface); 
  padding: 2.5rem; 
  border-radius: var(--radius-xl); 
  width: 500px; 
  max-width: 90%; 
  box-shadow: var(--shadow-xl);
  animation: scaleIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
  border: 1px solid var(--border-color);
}

.modal-content h2 {
  margin: 0 0 1.5rem 0;
  background: var(--primary-gradient);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 2rem;
}

.modal-actions .btn {
  min-width: 100px;
}

.progress-bar { 
  width: 100px; 
  height: 8px; 
  background: #e5e7eb; 
  border-radius: 4px; 
  display: inline-block; 
  margin-right: 8px; 
  overflow: hidden;
  vertical-align: middle;
}

.fill { 
  height: 100%; 
  background: var(--success); 
  border-radius: 4px; 
  transition: width 0.3s ease; 
}

@keyframes fadeIn { 
  from { opacity: 0; } 
  to { opacity: 1; } 
}

@keyframes scaleIn { 
  from { 
    transform: scale(0.9);
    opacity: 0; 
  } 
  to { 
    transform: scale(1);
    opacity: 1; 
  } 
}

/* Action buttons in table */
.btn-sm {
  font-size: 0.75rem;
  padding: 0.375rem 0.75rem;
}
</style>
