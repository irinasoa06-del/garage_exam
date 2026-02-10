<!-- src/views/frontoffice/Home.vue -->
<template>
  <div class="frontoffice-home">
    <!-- En-tête -->
    <header class="main-header">
      <div class="header-content">
        <h1>Garage Auto Pro</h1>
        <p class="tagline">Votre partenaire de confiance pour la réparation automobile</p>
      </div>
      <div class="header-stats">
        <div class="stat-badge">
          <span class="stat-icon">🚗</span>
          <span class="stat-text">{{ activeRepairs }} voitures en réparation</span>
        </div>
        <div class="stat-badge">
          <span class="stat-icon">⏰</span>
          <span class="stat-text">Ouvert 7j/7 • 8h-19h</span>
        </div>
      </div>
    </header>
    
    <!-- Navigation -->
    <nav class="main-nav">
      <router-link to="/" class="nav-link active">🏠 Accueil</router-link>
      <router-link to="/repairs" class="nav-link">🔧 Réparations en cours</router-link>
      <a href="/game" class="nav-link">🎮 Jouer au jeu de réparation</a>
      <a href="/mobile-app" class="nav-link">📱 Télécharger l'app mobile</a>
    </nav>
    
    <!-- Contenu principal -->
    <main class="main-content">
      <!-- Introduction -->
      <section class="intro-section">
        <div class="intro-content">
          <h2>Bienvenue dans notre garage virtuel</h2>
          <p>
            Suivez en temps réel les réparations de votre véhicule, 
            accédez à nos services en ligne, et découvrez notre simulateur 
            de réparation automobile.
          </p>
          <div class="cta-buttons">
            <router-link to="/repairs" class="cta-btn primary">
              Voir les réparations en cours
            </router-link>
            <a href="/game" class="cta-btn secondary">
              Essayer le simulateur
            </a>
          </div>
        </div>
        <div class="intro-image">
          <div class="garage-illustration">
            <div class="car-slot slot-1">🚗</div>
            <div class="car-slot slot-2">🚙</div>
            <div class="mechanics">🔧🔨⚡</div>
          </div>
        </div>
      </section>
      
      <!-- Services -->
      <section class="services-section">
        <h2>Nos services de réparation</h2>
        <div class="services-grid">
          <div v-for="service in services" :key="service.id" class="service-card">
            <div class="service-icon">{{ service.icon }}</div>
            <h3>{{ service.name }}</h3>
            <p>{{ service.description }}</p>
            <div class="service-details">
              <span class="price">~{{ service.price }}€</span>
              <span class="duration">⏱️ {{ service.duration }}</span>
            </div>
          </div>
        </div>
      </section>
      
      <!-- Garage en temps réel -->
      <section class="live-garage">
        <h2>Notre garage en direct</h2>
        <div class="garage-status">
          <div class="status-card" :class="{ available: availableSlots > 0 }">
            <h3>Places disponibles</h3>
            <p class="slot-count">{{ availableSlots }}/2</p>
            <p v-if="availableSlots > 0" class="status-available">✔ Disponible immédiatement</p>
            <p v-else class="status-busy">⏳ Complet - Attente possible</p>
          </div>
          
          <div class="status-card">
            <h3>Temps d'attente estimé</h3>
            <p class="wait-time">{{ estimatedWaitTime }}</p>
            <p class="wait-note">Basé sur les réparations en cours</p>
          </div>
          
          <div class="status-card">
            <h3>Mécaniciens actifs</h3>
            <div class="mechanics-active">
              <span class="mechanic">👨‍🔧</span>
              <span class="mechanic">👩‍🔧</span>
            </div>
            <p>2 mécaniciens sur site</p>
          </div>
        </div>
      </section>
    </main>
    
    <!-- Pied de page -->
    <footer class="main-footer">
      <div class="footer-content">
        <div class="footer-section">
          <h4>Contact</h4>
          <p>📞 01 23 45 67 89</p>
          <p>✉️ contact@garage-autopro.fr</p>
          <p>📍 123 Rue du Garage, 75000 Paris</p>
        </div>
        
        <div class="footer-section">
          <h4>Liens rapides</h4>
          <a href="/repairs">Réparations en cours</a>
          <a href="/game">Jeu de simulation</a>
          <a href="/mobile-app">Application mobile</a>
          <a href="/contact">Nous contacter</a>
        </div>
        
        <div class="footer-section">
          <h4>Réseaux sociaux</h4>
          <div class="social-links">
            <a href="#" class="social-link">📘 Facebook</a>
            <a href="#" class="social-link">🐦 Twitter</a>
            <a href="#" class="social-link">📸 Instagram</a>
          </div>
        </div>
      </div>
      
      <div class="footer-bottom">
        <p>© 2024 Garage Auto Pro - Tous droits réservés</p>
        <p>Projet éducatif DSProm4 - Design S5</p>
      </div>
    </footer>
  </div>
</template>

<script>
export default {
  name: 'FrontofficeHome',
  data() {
    return {
      activeRepairs: 1,
      availableSlots: 1,
      estimatedWaitTime: '45 minutes',
      services: [
        { id: 1, icon: '🛢️', name: 'Vidange', description: 'Changement d\'huile et filtre', price: 80, duration: '30 min' },
        { id: 2, icon: '🛑', name: 'Freins', description: 'Plaquettes et disques', price: 150, duration: '1h30' },
        { id: 3, icon: '🚗', name: 'Pneus', description: 'Changement et équilibrage', price: 120, duration: '45 min' },
        { id: 4, icon: '⚡', name: 'Batterie', description: 'Remplacement batterie', price: 100, duration: '20 min' },
        { id: 5, icon: '🔄', name: 'Filtres', description: 'Filtre à air et habitacle', price: 60, duration: '25 min' },
        { id: 6, icon: '🧊', name: 'Climatisation', description: 'Recharge climatisation', price: 90, duration: '40 min' }
      ]
    }
  },
  async mounted() {
    await this.fetchGarageStatus();
  },
  methods: {
    async fetchGarageStatus() {
      try {
        const response = await fetch('/api/garage/status');
        const data = await response.json();
        this.activeRepairs = data.activeRepairs;
        this.availableSlots = data.availableSlots;
        this.estimatedWaitTime = data.estimatedWaitTime;
      } catch (error) {
        console.error('Erreur statut garage:', error);
      }
    }
  }
}
</script>

<style scoped>
.frontoffice-home {
  min-height: 100vh;
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
}

.main-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 40px 20px;
  text-align: center;
  border-bottom-left-radius: 20px;
  border-bottom-right-radius: 20px;
}

.header-content h1 {
  margin: 0;
  font-size: 2.5rem;
  font-weight: 800;
}

.tagline {
  margin: 10px 0 0 0;
  font-size: 1.2rem;
  opacity: 0.9;
}

.header-stats {
  display: flex;
  justify-content: center;
  gap: 30px;
  margin-top: 30px;
}

.stat-badge {
  background: rgba(255, 255, 255, 0.2);
  padding: 15px 25px;
  border-radius: 50px;
  display: flex;
  align-items: center;
  gap: 10px;
  backdrop-filter: blur(10px);
}

.stat-icon {
  font-size: 1.5rem;
}

.stat-text {
  font-weight: 600;
}

.main-nav {
  display: flex;
  justify-content: center;
  gap: 20px;
  padding: 20px;
  background: white;
  margin: 20px auto;
  max-width: 1200px;
  border-radius: 15px;
  box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

.nav-link {
  padding: 15px 25px;
  text-decoration: none;
  color: #4a5568;
  border-radius: 10px;
  transition: all 0.3s;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 8px;
}

.nav-link:hover {
  background: #667eea;
  color: white;
  transform: translateY(-2px);
}

.nav-link.active {
  background: #764ba2;
  color: white;
}

.main-content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}

.intro-section {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 50px;
  align-items: center;
  margin-bottom: 60px;
  padding: 40px;
  background: white;
  border-radius: 20px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.intro-content h2 {
  color: #2d3748;
  font-size: 2rem;
  margin-bottom: 20px;
}

.intro-content p {
  color: #4a5568;
  font-size: 1.1rem;
  line-height: 1.6;
  margin-bottom: 30px;
}

.cta-buttons {
  display: flex;
  gap: 20px;
}

.cta-btn {
  padding: 15px 30px;
  border-radius: 10px;
  text-decoration: none;
  font-weight: 600;
  transition: all 0.3s;
  display: inline-block;
}

.cta-btn.primary {
  background: #667eea;
  color: white;
}

.cta-btn.primary:hover {
  background: #5a67d8;
  transform: translateY(-2px);
}

.cta-btn.secondary {
  background: white;
  color: #667eea;
  border: 2px solid #667eea;
}

.cta-btn.secondary:hover {
  background: #667eea;
  color: white;
}

.garage-illustration {
  background: #edf2f7;
  padding: 40px;
  border-radius: 20px;
  text-align: center;
  position: relative;
  height: 300px;
}

.car-slot {
  position: absolute;
  font-size: 4rem;
  transition: transform 0.3s;
}

.slot-1 {
  top: 50px;
  left: 50px;
}

.slot-2 {
  bottom: 50px;
  right: 50px;
}

.mechanics {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  font-size: 2.5rem;
}

.services-section {
  margin-bottom: 60px;
}

.services-section h2 {
  text-align: center;
  color: #2d3748;
  margin-bottom: 40px;
  font-size: 2rem;
}

.services-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 30px;
}

.service-card {
  background: white;
  padding: 30px;
  border-radius: 15px;
  text-align: center;
  box-shadow: 0 5px 15px rgba(0,0,0,0.08);
  transition: transform 0.3s;
}

.service-card:hover {
  transform: translateY(-5px);
}

.service-icon {
  font-size: 3rem;
  margin-bottom: 20px;
}

.service-card h3 {
  color: #2d3748;
  margin-bottom: 15px;
}

.service-card p {
  color: #4a5568;
  margin-bottom: 20px;
  min-height: 60px;
}

.service-details {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 20px;
  padding-top: 20px;
  border-top: 1px solid #e2e8f0;
}

.price {
  font-weight: 800;
  color: #2ecc71;
  font-size: 1.2rem;
}

.duration {
  color: #718096;
}

.live-garage {
  margin-bottom: 60px;
}

.live-garage h2 {
  text-align: center;
  color: #2d3748;
  margin-bottom: 40px;
  font-size: 2rem;
}

.garage-status {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 30px;
}

.status-card {
  background: white;
  padding: 30px;
  border-radius: 15px;
  text-align: center;
  box-shadow: 0 5px 15px rgba(0,0,0,0.08);
}

.status-card.available {
  border-top: 5px solid #2ecc71;
}

.status-card h3 {
  color: #2d3748;
  margin-bottom: 20px;
}

.slot-count {
  font-size: 3rem;
  font-weight: 800;
  color: #667eea;
  margin: 10px 0;
}

.wait-time {
  font-size: 2.5rem;
  font-weight: 800;
  color: #f39c12;
  margin: 10px 0;
}

.status-available {
  color: #2ecc71;
  font-weight: 600;
  margin-top: 10px;
}

.status-busy {
  color: #e74c3c;
  font-weight: 600;
  margin-top: 10px;
}

.mechanics-active {
  display: flex;
  justify-content: center;
  gap: 20px;
  margin: 20px 0;
}

.mechanic {
  font-size: 2.5rem;
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0% { transform: scale(1); }
  50% { transform: scale(1.1); }
  100% { transform: scale(1); }
}

.main-footer {
  background: #2d3748;
  color: white;
  padding: 40px 20px;
  margin-top: 60px;
  border-top-left-radius: 20px;
  border-top-right-radius: 20px;
}

.footer-content {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 40px;
  max-width: 1200px;
  margin: 0 auto;
}

.footer-section h4 {
  color: #a0aec0;
  margin-bottom: 20px;
  font-size: 1.1rem;
}

.footer-section p, .footer-section a {
  color: #cbd5e0;
  margin-bottom: 10px;
  display: block;
  text-decoration: none;
}

.footer-section a:hover {
  color: white;
  text-decoration: underline;
}

.social-links {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.footer-bottom {
  text-align: center;
  margin-top: 40px;
  padding-top: 20px;
  border-top: 1px solid #4a5568;
  color: #a0aec0;
  font-size: 0.9rem;
}

@media (max-width: 768px) {
  .intro-section {
    grid-template-columns: 1fr;
  }
  
  .main-nav {
    flex-direction: column;
    align-items: center;
  }
  
  .header-stats {
    flex-direction: column;
    align-items: center;
  }
  
  .cta-buttons {
    flex-direction: column;
  }
}
</style>