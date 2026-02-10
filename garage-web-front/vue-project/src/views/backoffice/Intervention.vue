<!-- src/views/backoffice/Interventions.vue -->
<template>
  <div class="interventions">
    <div class="header">
      <h1>Gestion des Interventions</h1>
      <button @click="showAddForm = true" class="add-btn">+ Nouvelle Intervention</button>
    </div>
    
    <!-- Formulaire d'ajout -->
    <div v-if="showAddForm" class="modal-overlay">
      <div class="modal">
        <div class="modal-header">
          <h3>Nouvelle Intervention</h3>
          <button @click="showAddForm = false" class="close-btn">&times;</button>
        </div>
        
        <form @submit.prevent="addIntervention" class="intervention-form">
          <div class="form-row">
            <div class="form-group">
              <label>Nom de l'intervention *</label>
              <input v-model="newIntervention.name" required>
            </div>
            
            <div class="form-group">
              <label>Type *</label>
              <select v-model="newIntervention.type" required>
                <option value="">Sélectionner...</option>
                <option value="vidange">Vidange</option>
                <option value="frein">Frein</option>
                <option value="pneu">Pneu</option>
                <option value="filtre">Filtre</option>
                <option value="batterie">Batterie</option>
                <option value="amortisseur">Amortisseur</option>
                <option value="embrayage">Embrayage</option>
                <option value="refroidissement">Système de refroidissement</option>
              </select>
            </div>
          </div>
          
          <div class="form-row">
            <div class="form-group">
              <label>Prix (€) *</label>
              <input type="number" v-model="newIntervention.price" min="0" step="0.01" required>
            </div>
            
            <div class="form-group">
              <label>Durée (secondes) *</label>
              <input type="number" v-model="newIntervention.duration" min="1" required>
            </div>
          </div>
          
          <div class="form-group">
            <label>Description</label>
            <textarea v-model="newIntervention.description" rows="3"></textarea>
          </div>
          
          <div class="form-actions">
            <button type="button" @click="showAddForm = false" class="cancel-btn">Annuler</button>
            <button type="submit" class="save-btn">Enregistrer</button>
          </div>
        </form>
      </div>
    </div>
    
    <!-- Tableau des interventions -->
    <div class="table-container">
      <table class="interventions-table">
        <thead>
          <tr>
            <th>Nom</th>
            <th>Type</th>
            <th>Prix (€)</th>
            <th>Durée</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="intervention in interventions" :key="intervention.id">
            <td>{{ intervention.name }}</td>
            <td>
              <span class="type-badge" :class="intervention.type">
                {{ getTypeLabel(intervention.type) }}
              </span>
            </td>
            <td>{{ intervention.price }} €</td>
            <td>{{ formatDuration(intervention.duration) }}</td>
            <td>
              <button @click="editIntervention(intervention)" class="edit-btn">✏️</button>
              <button @click="deleteIntervention(intervention.id)" class="delete-btn">🗑️</button>
            </td>
          </tr>
        </tbody>
      </table>
      
      <div v-if="interventions.length === 0" class="empty-state">
        Aucune intervention configurée
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Interventions',
  data() {
    return {
      showAddForm: false,
      interventions: [],
      newIntervention: {
        name: '',
        type: '',
        price: 0,
        duration: 60,
        description: ''
      }
    }
  },
  async mounted() {
    await this.loadInterventions();
  },
  methods: {
    async loadInterventions() {
      try {
        const response = await fetch('/api/interventions');
        const data = await response.json();
        // Handle { interventions: [...] } structure
        this.interventions = data.interventions || data.data || data || [];
      } catch (error) {
        console.error('Erreur chargement:', error);
      }
    },
    
    getTypeLabel(type) {
      const labels = {
        vidange: 'Vidange',
        frein: 'Frein',
        pneu: 'Pneu',
        filtre: 'Filtre',
        batterie: 'Batterie',
        amortisseur: 'Amortisseur',
        embrayage: 'Embrayage',
        refroidissement: 'Refroidissement'
      };
      return labels[type] || type;
    },
    
    formatDuration(seconds) {
      const minutes = Math.floor(seconds / 60);
      const remainingSeconds = seconds % 60;
      return `${minutes}m ${remainingSeconds}s`;
    },
    
    async addIntervention() {
      try {
        const response = await fetch('/api/interventions', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(this.newIntervention)
        });
        
        if (response.ok) {
          this.showAddForm = false;
          this.resetForm();
          await this.loadInterventions();
        } else {
             const errorData = await response.json();
             alert(errorData.message || "Erreur lors de l'ajout");
        }
      } catch (error) {
        console.error('Erreur ajout:', error);
      }
    },
    
    editIntervention(intervention) {
      this.newIntervention = { ...intervention };
      this.showAddForm = true;
    },
    
    async deleteIntervention(id) {
      if (confirm('Supprimer cette intervention ?')) {
        try {
          await fetch(`/api/interventions/${id}`, { method: 'DELETE' });
          await this.loadInterventions();
        } catch (error) {
          console.error('Erreur suppression:', error);
        }
      }
    },
    
    resetForm() {
      this.newIntervention = {
        name: '',
        type: '',
        price: 0,
        duration: 60,
        description: ''
      };
    }
  }
}
</script>

<style scoped>
.interventions {
  padding: 20px;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
}

.add-btn {
  background-color: #2ecc71;
  color: white;
  border: none;
  padding: 12px 24px;
  border-radius: 6px;
  cursor: pointer;
  font-size: 16px;
}

.add-btn:hover {
  background-color: #27ae60;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0,0,0,0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal {
  background: white;
  width: 90%;
  max-width: 600px;
  border-radius: 10px;
  padding: 20px;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.close-btn {
  background: none;
  border: none;
  font-size: 24px;
  cursor: pointer;
  color: #666;
}

.intervention-form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-group label {
  margin-bottom: 8px;
  font-weight: 500;
}

.form-group input,
.form-group select,
.form-group textarea {
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 16px;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  margin-top: 20px;
}

.cancel-btn {
  padding: 10px 20px;
  background: #95a5a6;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.save-btn {
  padding: 10px 20px;
  background: #3498db;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.table-container {
  background: white;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.interventions-table {
  width: 100%;
  border-collapse: collapse;
}

.interventions-table th {
  background: #2c3e50;
  color: white;
  padding: 15px;
  text-align: left;
}

.interventions-table td {
  padding: 15px;
  border-bottom: 1px solid #eee;
}

.interventions-table tr:hover {
  background: #f5f5f5;
}

.type-badge {
  padding: 5px 10px;
  border-radius: 20px;
  font-size: 0.85rem;
  color: white;
}

.type-badge.vidange { background: #3498db; }
.type-badge.frein { background: #e74c3c; }
.type-badge.pneu { background: #9b59b6; }
.type-badge.filtre { background: #1abc9c; }
.type-badge.batterie { background: #f39c12; }
.type-badge.amortisseur { background: #d35400; }
.type-badge.embrayage { background: #34495e; }
.type-badge.refroidissement { background: #16a085; }

.edit-btn, .delete-btn {
  background: none;
  border: none;
  cursor: pointer;
  font-size: 18px;
  margin: 0 5px;
}

.edit-btn:hover { color: #3498db; }
.delete-btn:hover { color: #e74c3c; }

.empty-state {
  text-align: center;
  padding: 40px;
  color: #7f8c8d;
  font-style: italic;
}
</style>