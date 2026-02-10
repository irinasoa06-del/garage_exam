<template>
  <div class="admin-view">
    <div class="content">
      <div class="page-header">
        <h1>Gestion des Interventions</h1>
      </div>
      
      <!-- Formulaire Ajout -->
      <div class="card add-form mb-4">
        <h3 class="mb-4">Ajouter une intervention</h3>
        <form @submit.prevent="addIntervention">
          <div class="form-row">
            <div class="form-group flex-1">
              <label>Nom de l'intervention</label>
              <input v-model="newIntervention.nom" placeholder="ex: Vidange complète" required>
            </div>
            <div class="form-group" style="width: 150px;">
              <label>Prix (Ar)</label>
              <input v-model.number="newIntervention.prix_unitaire" type="number" placeholder="0" required>
            </div>
            <div class="form-group" style="width: 150px;">
              <label>Durée (s)</label>
              <input v-model.number="newIntervention.duree_secondes" type="number" placeholder="3600" required>
            </div>
            <div class="form-group flex items-end">
              <button type="submit" class="btn btn-primary" style="height: 42px;">Ajouter</button>
            </div>
          </div>
        </form>
      </div>

      <!-- Liste -->
      <div class="card table-container">
        <table>
          <thead>
            <tr>
              <th>Nom</th>
              <th>Prix unitaire</th>
              <th>Durée estimée</th>
              <th style="text-align: right;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in interventions" :key="item.type_id">
              <td style="font-weight: 500;">{{ item.nom }}</td>
              <td><span class="badge info">{{ item.prix_unitaire }} Ar</span></td>
              <td><span class="text-muted">{{ (item.duree_secondes / 3600).toFixed(1) }} h</span> <small class="text-muted">({{ item.duree_secondes }}s)</small></td>
              <td style="text-align: right;">
                <button @click="deleteItem(item.type_id)" class="btn btn-danger btn-sm">Supprimer</button>
              </td>
            </tr>
          </tbody>
        </table>
        <div v-if="interventions.length === 0" style="padding: 3rem; text-align: center; color: var(--text-muted);">
          Aucun type d'intervention enregistré
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../../services/api';

const interventions = ref([]);
const newIntervention = ref({ nom: '', prix_unitaire: '', duree_secondes: '' });

onMounted(() => {
  loadInterventions();
});

async function loadInterventions() {
  try {
    const response = await api.get('/types-intervention-manage');
    interventions.value = response.data.types || [];
  } catch (e) {
    console.error("Erreur chargement", e);
  }
}

async function addIntervention() {
  try {
    await api.post('/types-intervention-manage', newIntervention.value);
    newIntervention.value = { nom: '', prix_unitaire: '', duree_secondes: '' }; // Reset
    loadInterventions();
  } catch (e) {
    console.error("Erreur ajout", e);
    alert("Erreur lors de l'ajout");
  }
}

async function deleteItem(id) {
  if (confirm('Supprimer cette intervention ?')) {
    try {
      await api.delete(`/types-intervention-manage/${id}`);
      loadInterventions();
    } catch (e) {
      console.error("Erreur suppression", e);
    }
  }
}
</script>

<style scoped>
.content {
  max-width: 1200px;
  margin: 2rem auto;
  padding: 0 1.5rem;
}

.page-header {
  margin-bottom: 2rem;
}

.add-form {
  border-left: 4px solid var(--primary);
}

.form-row {
  display: flex;
  gap: 1.5rem;
  align-items: flex-start;
  flex-wrap: wrap;
}

.flex-1 {
  flex: 1;
  min-width: 250px;
}
</style>
