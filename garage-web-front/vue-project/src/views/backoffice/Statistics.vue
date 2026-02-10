<!-- src/views/backoffice/Statistics.vue -->
<template>
  <div class="statistics">
    <h1>Statistiques du Garage</h1>
    
    <!-- Filtres -->
    <div class="filters">
      <div class="filter-group">
        <label>Période:</label>
        <select v-model="selectedPeriod" @change="loadStatistics">
          <option value="today">Aujourd'hui</option>
          <option value="week">Cette semaine</option>
          <option value="month">Ce mois</option>
          <option value="year">Cette année</option>
          <option value="all">Toutes les périodes</option>
        </select>
      </div>
      
      <div class="filter-group">
        <label>Date de début:</label>
        <input type="date" v-model="startDate" @change="loadStatistics">
      </div>
      
      <div class="filter-group">
        <label>Date de fin:</label>
        <input type="date" v-model="endDate" @change="loadStatistics">
      </div>
    </div>
    
    <!-- Cartes de statistiques -->
    <div class="stats-cards">
      <div class="stat-card total-revenue">
        <div class="stat-icon">💰</div>
        <div class="stat-content">
          <h3>Chiffre d'affaires total</h3>
          <p class="stat-value">{{ formatCurrency(stats.totalRevenue) }}</p>
        </div>
      </div>
      
      <div class="stat-card total-clients">
        <div class="stat-icon">👥</div>
        <div class="stat-content">
          <h3>Nombre de clients</h3>
          <p class="stat-value">{{ stats.totalClients }}</p>
        </div>
      </div>
      
      <div class="stat-card total-interventions">
        <div class="stat-icon">🔧</div>
        <div class="stat-content">
          <h3>Interventions réalisées</h3>
          <p class="stat-value">{{ stats.totalInterventions }}</p>
        </div>
      </div>
      
      <div class="stat-card avg-repair-time">
        <div class="stat-icon">⏱️</div>
        <div class="stat-content">
          <h3>Temps moyen/réparation</h3>
          <p class="stat-value">{{ formatTime(stats.avgRepairTime) }}</p>
        </div>
      </div>
    </div>
    
    <!-- Graphiques -->
    <div class="charts-section">
      <div class="chart-container">
        <h3>Répartition par type d'intervention</h3>
        <canvas ref="interventionChart"></canvas>
      </div>
      
      <div class="chart-container">
        <h3>Évolution du chiffre d'affaires</h3>
        <canvas ref="revenueChart"></canvas>
      </div>
    </div>
    
    <!-- Top interventions -->
    <div class="top-interventions">
      <h3>Top 5 des interventions les plus fréquentes</h3>
      <table class="top-table">
        <thead>
          <tr>
            <th>Intervention</th>
            <th>Nombre</th>
            <th>Revenus générés</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in stats.topInterventions" :key="item.name">
            <td>{{ item.name }}</td>
            <td>{{ item.count }}</td>
            <td>{{ formatCurrency(item.revenue) }}</td>
          </tr>
        </tbody>
      </table>
    </div>
    
    <!-- Export -->
    <div class="export-section">
      <button @click="exportData" class="export-btn">📥 Exporter les statistiques (CSV)</button>
    </div>
  </div>
</template>

<script>
import { Chart, registerables } from 'chart.js';
Chart.register(...registerables);

export default {
  name: 'Statistics',
  data() {
    return {
      selectedPeriod: 'month',
      startDate: '',
      endDate: '',
      stats: {
        totalRevenue: 0,
        totalClients: 0,
        totalInterventions: 0,
        avgRepairTime: 0,
        topInterventions: []
      },
      interventionChart: null,
      revenueChart: null
    }
  },
  async mounted() {
    this.setDefaultDates();
    await this.loadStatistics();
  },
  methods: {
    setDefaultDates() {
      const today = new Date();
      const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
      
      this.startDate = firstDay.toISOString().split('T')[0];
      this.endDate = today.toISOString().split('T')[0];
    },
    
    async loadStatistics() {
      try {
        const params = new URLSearchParams({
          period: this.selectedPeriod,
          start_date: this.startDate,
          end_date: this.endDate
        });
        
        const response = await fetch(`/api/statistics?${params}`);
        this.stats = await response.json();
        
        this.updateCharts();
      } catch (error) {
        console.error('Erreur chargement stats:', error);
      }
    },
    
    formatCurrency(value) {
      return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
      }).format(value);
    },
    
    formatTime(seconds) {
      if (!seconds) return '0s';
      const hours = Math.floor(seconds / 3600);
      const minutes = Math.floor((seconds % 3600) / 60);
      const secs = seconds % 60;
      
      if (hours > 0) return `${hours}h ${minutes}m`;
      if (minutes > 0) return `${minutes}m ${secs}s`;
      return `${secs}s`;
    },
    
    updateCharts() {
      if (this.interventionChart) {
        this.interventionChart.destroy();
      }
      
      if (this.revenueChart) {
        this.revenueChart.destroy();
      }
      
      // Exemple de données pour le graphique
      const interventionData = {
        labels: ['Vidange', 'Frein', 'Pneu', 'Filtre', 'Batterie', 'Amortisseur', 'Embrayage', 'Refroidissement'],
        datasets: [{
          data: [30, 25, 20, 15, 10, 8, 5, 4],
          backgroundColor: [
            '#3498db', '#e74c3c', '#9b59b6', '#1abc9c',
            '#f39c12', '#d35400', '#34495e', '#16a085'
          ]
        }]
      };
      
      const revenueData = {
        labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'],
        datasets: [{
          label: 'Chiffre d\'affaires',
          data: [1500, 2000, 1800, 2200, 2500, 2800, 3000, 3200, 3100, 3500, 3800, 4000],
          borderColor: '#2ecc71',
          backgroundColor: 'rgba(46, 204, 113, 0.1)',
          fill: true
        }]
      };
      
      this.interventionChart = new Chart(this.$refs.interventionChart, {
        type: 'doughnut',
        data: interventionData,
        options: {
          responsive: true,
          plugins: {
            legend: { position: 'bottom' }
          }
        }
      });
      
      this.revenueChart = new Chart(this.$refs.revenueChart, {
        type: 'line',
        data: revenueData,
        options: {
          responsive: true,
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                callback: value => this.formatCurrency(value)
              }
            }
          }
        }
      });
    },
    
    exportData() {
      // Logique d'export CSV
      const data = [
        ['Statistique', 'Valeur'],
        ['Chiffre d\'affaires total', this.stats.totalRevenue],
        ['Nombre de clients', this.stats.totalClients],
        ['Interventions réalisées', this.stats.totalInterventions],
        ['Temps moyen par réparation', this.stats.avgRepairTime]
      ];
      
      const csvContent = data.map(row => row.join(',')).join('\n');
      const blob = new Blob([csvContent], { type: 'text/csv' });
      const url = window.URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = `statistiques-garage-${new Date().toISOString().split('T')[0]}.csv`;
      a.click();
    }
  }
}
</script>

<style scoped>
.statistics {
  padding: 20px;
}

.filters {
  display: flex;
  gap: 20px;
  margin-bottom: 30px;
  padding: 20px;
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.filter-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.filter-group label {
  font-weight: 500;
  font-size: 0.9rem;
  color: #555;
}

.filter-group select,
.filter-group input {
  padding: 8px 12px;
  border: 1px solid #ddd;
  border-radius: 4px;
  min-width: 150px;
}

.stats-cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 20px;
  margin-bottom: 30px;
}

.stat-card {
  display: flex;
  align-items: center;
  padding: 25px;
  background: white;
  border-radius: 10px;
  box-shadow: 0 3px 15px rgba(0,0,0,0.1);
  gap: 20px;
}

.stat-icon {
  font-size: 2.5rem;
}

.stat-content h3 {
  margin: 0 0 10px 0;
  font-size: 1rem;
  color: #666;
}

.stat-value {
  margin: 0;
  font-size: 2rem;
  font-weight: bold;
  color: #2c3e50;
}

.charts-section {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 30px;
  margin-bottom: 30px;
}

.chart-container {
  background: white;
  padding: 25px;
  border-radius: 10px;
  box-shadow: 0 3px 15px rgba(0,0,0,0.1);
}

.chart-container h3 {
  margin-top: 0;
  margin-bottom: 20px;
  color: #2c3e50;
}

.top-interventions {
  background: white;
  padding: 25px;
  border-radius: 10px;
  box-shadow: 0 3px 15px rgba(0,0,0,0.1);
  margin-bottom: 30px;
}

.top-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
}

.top-table th {
  background: #f8f9fa;
  padding: 15px;
  text-align: left;
  color: #495057;
  font-weight: 600;
}

.top-table td {
  padding: 15px;
  border-bottom: 1px solid #dee2e6;
}

.top-table tr:last-child td {
  border-bottom: none;
}

.export-section {
  text-align: center;
}

.export-btn {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  padding: 15px 30px;
  border-radius: 8px;
  font-size: 16px;
  cursor: pointer;
  transition: transform 0.2s;
}

.export-btn:hover {
  transform: translateY(-2px);
}

@media (max-width: 1024px) {
  .charts-section {
    grid-template-columns: 1fr;
  }
  
  .filters {
    flex-direction: column;
  }
}
</style>