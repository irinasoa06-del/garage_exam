# Projet Garage – Documentation principale

## Aperçu
Ce dépôt regroupe plusieurs applications destinées à la gestion d'un garage automobile :

- **Backend API** (`laravel-web/`) : API REST Laravel 10 avec base PostgreSQL, notifications Firebase et conteneurisation Docker.
- **Application mobile** (`garage-mobile/`) : client Ionic/Angular consommant l'API et recevant les notifications.
- **Interface web** (`garage-web-front/`) : tableau de bord Vue 3 (Vite) pour l’administration.
- **Jeu** (`garage-game/`, à venir) : intégration temps réel via Firebase.

## Structure du dépôt
```
Examen_garage/
├── README.md (ce fichier)
├── garage-mobile/        # App mobile Ionic + Angular
├── garage-web-front/     # Frontend web Vue 3
└── laravel-web/          # Backend Laravel 10 + PostgreSQL
```

Chaque module dispose de sa propre documentation interne (voir les README dédiés).

## Prérequis globaux
- Git ≥ 2.40
- Node.js ≥ 18 (pour les projets frontaux)
- npm ≥ 9 ou pnpm/yarn équivalent
- Docker Desktop (pour le backend Laravel conteneurisé)
- PHP ≥ 8.1 & Composer ≥ 2.5 si vous exécutez Laravel en local sans Docker

## Démarrage rapide
1. **Cloner le dépôt**
   ```bash
   git clone <url>
   cd Examen_garage
   ```

2. **Lancer tous les services**
   ```powershell
   ./start-all.ps1
   ```
   - Vérifie les prérequis (`docker`, `npm`).
   - Crée `.env` du backend si absent et démarre Docker (`laravel-web`).
   - Installe les dépendances et lance les serveurs frontaux (Vue à `http://localhost:5173`, Ionic à `http://localhost:8100`).
   - Options disponibles :
     - `-SkipBackend`, `-SkipWeb`, `-SkipMobile` pour ignorer un module.
     - `-ForceInstall` pour réinstaller les dépendances (`composer`, `npm`).

3. **Démarrage manuel (optionnel)**
   - Backend : suivre `laravel-web/README.md` (Docker ou PHP local).
   - Mobile : suivre `garage-mobile/README.md` (npm install, `ionic serve`).
   - Frontend web : suivre `garage-web-front/README.md` (`npm run dev`).

## Bonnes pratiques dépôt
- Les répertoires `node_modules/`, `vendor/` et fichiers `.env*` ne doivent pas être versionnés.
- Les secrets Firebase ou autres clés API doivent être stockés via variables d’environnement.
- Utiliser des branches fonctionnelles et des PRs atomiques pour faciliter la revue.

## Ressources
- Diagrammes d’architecture et instructions supplémentaires : voir les fichiers `structure` présents dans chaque module.
- Scripts de démarrage/dépannage (Docker, PowerShell) : `laravel-web/start-project.sh`, `laravel-web/fix-docker.ps1`.

Pour toute contribution, mettez à jour les README concernés afin de garder la documentation alignée avec l’implémentation réelle.
