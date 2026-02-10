<template>
  <div class="admin-view">
    <NavBar />
    <div class="content">
      <h1>Gestion des Interventions</h1>
      
      <!-- Formulaire Ajout -->
      <div class="add-form">
        <h3>Ajouter une intervention</h3>
        <form @submit.prevent="addIntervention">
          <div class="form-row">
            <input v-model="newIntervention.nom" placeholder="Nom (ex: Vidange)" required>
            <input v-model.number="newIntervention.prix" type="number" placeholder="Prix (€)" required>
            <input v-model.number="newIntervention.duree" type="number" placeholder="Durée (secondes)" required>
            <button type="submit">Ajouter</button>
          </div>
        </form>
      </div>

      <!-- Liste -->
      <div class="list-container">
        <table>
          <thead>
            <tr>
              <th>Nom</th>
              <th>Prix</th>
              <th>Durée</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in interventions" :key="item.id">
              <td>{{ item.nom }}</td>
              <td>{{ item.prix }} €</td>
              <td>{{ item.duree }} s</td>
              <td>
                <button @click="deleteItem(item.id)" class="delete-btn">Supprimer</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import NavBar from '../../components/NavBar.vue';
import { firebaseService } from '../../services/firebase';

const interventions = ref([]);
const newIntervention = ref({ nom: '', prix: '', duree: '' });

onMounted(() => {
  firebaseService.getInterventions((data) => {
    interventions.value = data;
  });
});

async function addIntervention() {
  try {
    await firebaseService.addIntervention({ ...newIntervention.value });
    newIntervention.value = { nom: '', prix: '', duree: '' }; // Reset
  } catch (e) {
    console.error("Erreur ajout", e);
    alert("Erreur lors de l'ajout");
  }
}

async function deleteItem(id) {
  if (confirm('Supprimer cette intervention ?')) {
    try {
      await firebaseService.deleteIntervention(id);
    } catch (e) {
      console.error("Erreur suppression", e);
    }
  }
}
</script>

<style scoped>
.content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
}
.add-form {
  background: #f8f9fa;
  padding: 1.5rem;
  border-radius: 8px;
  margin-bottom: 2rem;
}
.form-row {
  display: flex;
  gap: 1rem;
}
input {
  padding: 0.5rem;
  flex: 1;
}
button {
  padding: 0.5rem 1rem;
  background-color: #27ae60;
  color: white;
  border: none;
  cursor: pointer;
}
.delete-btn {
  background-color: #e74c3c;
  padding: 0.25rem 0.5rem;
  font-size: 0.9rem;
}
table {
  width: 100%;
  border-collapse: collapse;
}
th, td {
  padding: 1rem;
  text-align: left;
  border-bottom: 1px solid #eee;
}
</style>
