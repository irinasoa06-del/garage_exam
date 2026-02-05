# Frontend Web – Garage Auto

## Présentation
Tableau de bord d’administration développé avec **Vue 3** et **Vite**. L’interface consomme l’API Laravel pour afficher les voitures, interventions, statistiques et gérer les utilisateurs.

> **Note :** Le dossier `src/` est actuellement vide. Reportez-vous au fichier `structure` à la racine du dépôt pour la structure visée (composants `LoginForm.vue`, `DashboardView.vue`, services `api.js`, etc.) et complétez ce dossier avant de lancer l’application.

## Prérequis
- Node.js ≥ 18
- npm ≥ 9 (ou pnpm/yarn)

## Installation
```bash
cd garage-web-front/vue-project
npm install
```

## Scripts utiles
- `npm run dev` : lance Vite en mode développement (par défaut sur `http://localhost:5173`)
- `npm run build` : construit la version production
- `npm run preview` : prévisualise la build
- `npm run lint` (à ajouter) : recommandation pour ESLint/Prettier afin de garantir la qualité du code

## Structure recommandée
```text
src/
├── components/
│   ├── auth/
│   │   ├── LoginForm.vue
│   │   └── RegisterForm.vue
│   ├── voitures/
│   │   ├── VoitureList.vue
│   │   └── VoitureForm.vue
│   └── common/
│       ├── Navbar.vue
│       └── Footer.vue
├── views/
│   ├── LoginView.vue
│   ├── DashboardView.vue
│   └── AdminView.vue
├── router/
│   └── index.ts
├── services/
│   └── api.ts
└── main.ts
```

Actualisez ce README si vous adaptez la structure (TypeScript vs JavaScript, composition API, etc.).

## Configuration API / environnement
- Centralisez l’URL de l’API Laravel et les clés publiques éventuelles (Firebase, analytics) dans un fichier `src/config/env.ts` ou via les variables Vite (`VITE_API_URL`, `VITE_FIREBASE_API_KEY`, …).
- Ne versionnez pas de secrets. Utilisez un fichier `.env.local` ignoré par Git (déjà couvert par le `.gitignore` global).

## Intégration Firebase (optionnel)
Si vous consommez Firebase pour les notifications en temps réel :
1. Ajoutez le SDK (`npm install firebase`).
2. Initialise-le dans un module dédié (`src/plugins/firebase.ts`) en lisant les variables `VITE_FIREBASE_*`.
3. Documentez la configuration requise dans ce README.

## Qualité et tests
- Ajoutez ESLint/Prettier pour la cohérence du code (configuration recommandée via `npm create vue@latest` ou plugin Vite).
- Préparez des tests unitaires (Vitest) et tests end-to-end (Cypress/Playwright) selon les besoins du projet.

## Déploiement
- `npm run build` génère les assets statiques dans `dist/`.
- Servez-les via un hébergeur statique ou un serveur web (nginx, Laravel mixte…).
- Mettez à jour ce README avec vos instructions spécifiques de déploiement (CI/CD, hébergeur, etc.).
