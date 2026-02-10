# Frontend Web – Garage Auto

## Présentation
Tableau de bord d’administration et vue client développés avec **Vue 3** et **Vite**. L’interface utilise **Firebase** pour la synchronisation des données en temps réel.

## Structure du Projet
```text
src/
├── components/
│   └── NavBar.vue           # Barre de navigation
├── views/
│   ├── LoginView.vue        # Page de connexion
│   ├── Admin/
│   │   ├── DashboardView.vue     # Stats et liste globale
│   │   └── InterventionsView.vue # CRUD Types d'interventions
│   └── Public/
│       └── ClientListView.vue    # Vue Client (état réparations)
├── router/
│   └── index.js             # Configuration des routes
├── services/
│   └── firebase.js          # Service Firebase
└── main.js
```

## Fonctionnalités
### Backoffice (Admin)
- **Login** : Accès sécurisé (simulé via localStorage).
- **Dashboard** : Vue d'ensemble, montant total, nombre de clients.
- **Interventions** : Gestion des types de réparations (Prix, Durée).

### Frontoffice (Public)
- **Liste Clients** : Suivi en temps réel de l'état des réparations.

## Installation & Démarrage

1. **Installation**
   ```bash
   npm install
   ```

2. **Configuration Firebase**
   - Ouvrez `src/services/firebase.js`.
   - Remplacez l'objet `firebaseConfig` par vos propres clés (Console Firebase).

3. **Lancement**
   ```bash
   npm run dev
   ```

## Routes
- **Public** : `/`
- **Login** : `/login`
- **Admin** : `/admin/dashboard`, `/admin/interventions`
