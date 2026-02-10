# Garage Auto - Application de Gestion de Garage

Système complet de gestion de garage automobile avec backend Laravel, frontend Vue.js, et application mobile Ionic.

## 📋 Table des matières

- [Prérequis](#prérequis)
- [Architecture](#architecture)
- [Installation et Lancement avec Docker](#installation-et-lancement-avec-docker)
- [Configuration](#configuration)
- [Utilisation](#utilisation)
- [API Endpoints](#api-endpoints)
- [Commandes Utiles](#commandes-utiles)

## 🔧 Prérequis

- **Docker** (version 20.10+)
- **Docker Compose** (version 2.0+)
- **Node.js** (version 20.19+) - Pour le développement local frontend
- **Git**

## 🏗️ Architecture

Le projet est composé de 3 parties principales :

```
Examen_garage/
├── laravel-web/          # Backend API (Laravel + PostgreSQL)
├── garage-web-front/     # Frontend Web (Vue.js)
└── garage-mobile/        # Application Mobile (Ionic + Capacitor)
```

### Technologies utilisées

- **Backend** : Laravel 10, PostgreSQL, Firebase
- **Frontend** : Vue.js 3, Vite, Vue Router, Axios
- **Mobile** : Ionic 7, Capacitor, Firebase
- **Infrastructure** : Docker, Docker Compose, Nginx

## 🐳 Installation et Lancement avec Docker

### Option 1 : Lancement avec Docker Compose (Recommandé)

#### 1. Créer le fichier docker-compose.yml

Créez un fichier `docker-compose.yml` à la racine du projet :

```yaml
version: '3.8'

services:
  # Base de données PostgreSQL
  postgres:
    image: postgres:15-alpine
    container_name: garage-postgres
    restart: unless-stopped
    environment:
      POSTGRES_DB: garage_auto
      POSTGRES_USER: garage_user
      POSTGRES_PASSWORD: garage_password
    volumes:
      - postgres_data:/var/lib/postgresql/data
      - ./laravel-web/docker/postgres/init.sql:/docker-entrypoint-initdb.d/init.sql
    ports:
      - "5432:5432"
    networks:
      - garage-network
    healthcheck:
      test: ["CMD-SHELL", "pg_isready -U garage_user -d garage_auto"]
      interval: 10s
      timeout: 5s
      retries: 5

  # Backend Laravel (API)
  laravel:
    build: ./laravel-web
    image: garage-laravel
    container_name: garage-laravel
    restart: unless-stopped
    depends_on:
      postgres:
        condition: service_healthy
    environment:
      APP_ENV: local
      APP_DEBUG: "true"
      APP_URL: http://localhost:8000
      DB_CONNECTION: pgsql
      DB_HOST: postgres
      DB_PORT: 5432
      DB_DATABASE: garage_auto
      DB_USERNAME: garage_user
      DB_PASSWORD: garage_password
    volumes:
      - ./laravel-web:/var/www/html
    ports:
      - "8000:8000"
    networks:
      - garage-network
    command: sh -c "composer install && php artisan migrate --force && php artisan db:seed --force && php -S 0.0.0.0:8000 -t public"

  # Frontend Vue.js
  frontend:
    build: ./garage-web-front
    image: garage-frontend
    container_name: garage-frontend
    restart: unless-stopped
    depends_on:
      - laravel
    volumes:
      - ./garage-web-front/vue-project:/app
      - /app/node_modules
    ports:
      - "5173:5173"
    networks:
      - garage-network
    command: npm run dev -- --host 0.0.0.0

networks:
  garage-network:
    driver: bridge

volumes:
  postgres_data:
```

#### 2. Créer le Dockerfile pour le frontend

Créez `garage-web-front/Dockerfile` :

```dockerfile
FROM node:20-alpine

WORKDIR /app

# Copier les fichiers package
COPY vue-project/package*.json ./

# Installer les dépendances
RUN npm install

# Copier le reste des fichiers
COPY vue-project/ ./

# Exposer le port
EXPOSE 5173

# Commande par défaut
CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0"]
```

#### 3. Lancer tous les services

```powershell
# À la racine du projet
docker-compose up -d
```

**Temps de démarrage initial** : ~2-3 minutes (téléchargement des images, installation des dépendances)

#### 4. Vérifier que tout fonctionne

```powershell
# Vérifier le statut des conteneurs
docker-compose ps

# Vérifier les logs
docker-compose logs -f
```

### Option 2 : Lancement Manuel des Services

#### Backend Laravel (API)

```powershell
# Aller dans le dossier laravel
cd laravel-web

# Construire l'image Docker
docker build -t garage-laravel .

# Lancer PostgreSQL
docker run -d \
  --name garage-postgres \
  -e POSTGRES_DB=garage_auto \
  -e POSTGRES_USER=garage_user \
  -e POSTGRES_PASSWORD=garage_password \
  -p 5432:5432 \
  -v ${PWD}/docker/postgres/init.sql:/docker-entrypoint-initdb.d/init.sql \
  postgres:15-alpine

# Attendre que PostgreSQL soit prêt (~10 secondes)
Start-Sleep -Seconds 10

# Lancer Laravel
docker run -d \
  --name garage-laravel \
  --link garage-postgres:postgres \
  -e DB_HOST=postgres \
  -e DB_DATABASE=garage_auto \
  -e DB_USERNAME=garage_user \
  -e DB_PASSWORD=garage_password \
  -p 8000:8000 \
  -v ${PWD}:/var/www/html \
  garage-laravel
```

#### Frontend Vue.js

```powershell
# Aller dans le dossier frontend
cd garage-web-front/vue-project

# Installer les dépendances
npm install

# Lancer le serveur de développement
npm run dev
```

## ⚙️ Configuration

### Variables d'environnement

#### Backend (.env)

Le fichier `laravel-web/.env` contient déjà les valeurs par défaut :

```env
APP_NAME=Garage
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1  # Utilisez 'postgres' dans Docker
DB_PORT=5432
DB_DATABASE=garage_auto
DB_USERNAME=garage_user
DB_PASSWORD=garage_password

# Firebase (optionnel si pas utilisé)
FIREBASE_PROJECT_ID=garage-exam
FIREBASE_CREDENTIALS=/var/www/html/firebase_credentials.json
```

#### Frontend (API URL)

Le fichier `garage-web-front/vue-project/src/services/api.js` est configuré pour :

```javascript
baseURL: 'http://localhost:8000/api/v1/'
```

### Firebase (Optionnel)

Si vous utilisez Firebase pour les notifications en temps réel :

1. Placez votre `firebase_credentials.json` dans `laravel-web/`
2. Configurez les variables dans `.env`

## 🚀 Utilisation

### Accès aux applications

Une fois tous les services lancés :

| Service | URL | Description |
|---------|-----|-------------|
| **Backend API** | http://localhost:8000 | API REST Laravel |
| **Frontend Web** | http://localhost:5173 | Interface Vue.js |
| **PostgreSQL** | localhost:5432 | Base de données |

### Comptes par défaut

Les seeders créent automatiquement :

**Admin** :
- Email : `admin@garage.com`
- Mot de passe : `password123`

**Clients de test** :
- Créés automatiquement via les seeders

### Fonctionnalités principales

#### Backoffice (Admin)
- Gestion des réparations (jusqu'à **3 voitures simultanément**)
- Suivi de la progression
- Gestion des interventions
- Statistiques

#### Frontoffice (Public)
- Liste des clients
- **Historique des réparations par client** (montant et date)
- Suivi en temps réel

## 📡 API Endpoints

### Authentification
- `POST /api/v1/login` - Connexion
- `POST /api/v1/register` - Inscription
- `POST /api/v1/logout` - Déconnexion (auth)

### Voitures
- `GET /api/v1/voitures` - Liste des voitures
- `POST /api/v1/voitures` - Créer une voiture
- `GET /api/v1/voitures/{id}` - Détails d'une voiture

### Réparations
- `GET /api/v1/reparations` - Toutes les réparations
- `POST /api/v1/reparations` - Créer une réparation
- `GET /api/v1/reparations/en-cours` - Réparations en cours
- `GET /api/v1/reparations/terminees` - Réparations terminées
- `POST /api/v1/reparations/{id}/commencer` - Commencer une réparation
- `PUT /api/v1/reparations/{id}/progression` - Mettre à jour la progression
- `POST /api/v1/reparations/{id}/terminer` - Terminer une réparation
- `GET /api/v1/reparations/slots` - Slots disponibles (max 3)

### Clients (Nouveau)
- `GET /api/v1/clients` - Liste des clients
- `GET /api/v1/clients/{id}` - Détails d'un client
- `GET /api/v1/clients/{id}/repair-history` - **Historique des réparations**

### Types d'intervention
- `GET /api/v1/types-intervention` - Liste des types

## 🛠️ Commandes Utiles

### Docker Compose

```powershell
# Démarrer tous les services
docker-compose up -d

# Arrêter tous les services
docker-compose down

# Voir les logs en temps réel
docker-compose logs -f

# Voir les logs d'un service spécifique
docker-compose logs -f laravel

# Redémarrer un service
docker-compose restart laravel

# Reconstruire les images
docker-compose build --no-cache

# Supprimer tout (conteneurs, volumes, images)
docker-compose down -v --rmi all
```

### Laravel (Backend)

```powershell
# Entrer dans le conteneur Laravel
docker exec -it garage-laravel sh

# Migrations
docker exec garage-laravel php artisan migrate

# Seeders
docker exec garage-laravel php artisan db:seed

# Reset + migrate + seed
docker exec garage-laravel php artisan migrate:fresh --seed

# Cache clear
docker exec garage-laravel php artisan cache:clear
docker exec garage-laravel php artisan config:clear

# Voir les routes
docker exec garage-laravel php artisan route:list
```

### PostgreSQL

```powershell
# Entrer dans PostgreSQL
docker exec -it garage-postgres psql -U garage_user -d garage_auto

# Backup de la base
docker exec garage-postgres pg_dump -U garage_user garage_auto > backup.sql

# Restore
docker exec -i garage-postgres psql -U garage_user garage_auto < backup.sql
```

### Frontend

```powershell
# Entrer dans le conteneur frontend
docker exec -it garage-frontend sh

# Installer une nouvelle dépendance
docker exec garage-frontend npm install <package>

# Build de production
docker exec garage-frontend npm run build
```

## 🧪 Tests

### Backend

```powershell
# Lancer les tests
docker exec garage-laravel php artisan test

# Tests avec coverage
docker exec garage-laravel php artisan test --coverage
```

### Frontend

```powershell
# Tests unitaires
cd garage-web-front/vue-project
npm run test:unit
```

## 📝 Notes importantes

### Capacité du garage
- Le garage peut accueillir **3 voitures simultanément** en réparation
- Les réparations supplémentaires sont mises en file d'attente

### Historique client
- Accessible depuis le frontoffice en cliquant sur un client
- Affiche toutes les réparations (terminées et en cours)
- Montants basés sur le prix du type d'intervention
- Statistiques : total dépensé, nombre de réparations

## 🐛 Dépannage

### Le backend ne démarre pas
```powershell
# Vérifier que PostgreSQL est prêt
docker logs garage-postgres

# Vérifier les logs Laravel
docker logs garage-laravel

# Recréer le conteneur
docker-compose down
docker-compose up -d --force-recreate laravel
```

### Le frontend ne se connecte pas à l'API
- Vérifier que l'API est accessible : http://localhost:8000
- Vérifier `baseURL` dans `src/services/api.js`
- Vérifier les CORS dans `laravel-web/config/cors.php`

### Erreur de connexion PostgreSQL
```powershell
# Attendre que PostgreSQL soit complètement démarré
docker-compose logs postgres

# Si nécessaire, recréer le volume
docker-compose down -v
docker-compose up -d
```

## 📚 Documentation supplémentaire

- [Collection Postman](./garage-api.postman_collection.json) - Pour tester l'API
- [Laravel Documentation](https://laravel.com/docs)
- [Vue.js Documentation](https://vuejs.org/)
- [Docker Documentation](https://docs.docker.com/)

## 👥 Contribution

Projet éducatif - DS Système & Réseaux S5 - 2026

---

**Version** : 1.0.0  
**Dernière mise à jour** : Février 2026
