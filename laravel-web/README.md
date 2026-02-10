# Backend Laravel – Garage Auto

## Présentation
Ce module fournit l’API REST principale du projet Garage :

- Laravel 10 avec PHP ≥ 8.1
- Authentification via Laravel Sanctum
- Base de données PostgreSQL
- Intégration Firebase (notifications) via `kreait/laravel-firebase`
- Conteneurisation Docker (nginx + PHP-FPM + PostgreSQL)

## Prérequis
- Docker Desktop et Docker Compose
- (Optionnel) PHP 8.1+, Composer 2.5+ si vous exécutez sans Docker
- Accès à une instance Firebase (pour les notifications)

## Structure clé
- `app/Http/Controllers/Api` : contrôleurs REST (auth, voitures, réparations…)
- `app/Models` : modèles Eloquent (User, Voiture, Intervention, Notification…)
- `database/migrations` : schéma PostgreSQL (tables utilisateurs, voitures, réparations…)
- `docker/` : configuration des conteneurs (nginx, PHP, PostgreSQL)
- `docker-compose-image.yml` : orchestrateur principal
- `structure` : diagrammes et instructions supplémentaires

## Installation & démarrage avec Docker
1. Copier le fichier d’environnement :
   ```powershell
   cd laravel-web
   copy .env.backup .env  # ou créer .env manuellement (voir ci-dessous)
   ```
   > Ne versionnez jamais `.env`. Ajustez les identifiants PostgreSQL/Firebase selon votre contexte.

2. Vérifier/éditer les variables principales dans `.env` :
   ```dotenv
   APP_NAME="Garage API"
   APP_URL=http://localhost:8000
   DB_CONNECTION=pgsql
   DB_HOST=postgres
   DB_PORT=5432
   DB_DATABASE=garage_auto
   DB_USERNAME=garage_user
   DB_PASSWORD=garage_password
   ```

3. Lancer les conteneurs et initialiser le projet :
   ```powershell
   docker compose -f docker-compose-image.yml up -d --build
   docker compose -f docker-compose-image.yml exec laravel composer install
   docker compose -f docker-compose-image.yml exec laravel php artisan key:generate
   docker compose -f docker-compose-image.yml exec laravel php artisan migrate --seed
   ```

4. Accéder à l’API : `http://localhost:8000/api/v1/...`

## Exécution locale sans Docker (optionnel)
1. Installer les dépendances :
   ```powershell
   composer install
   php artisan key:generate
   php artisan migrate --seed
   php artisan serve --port=8000
   ```
2. Configurer PostgreSQL localement avec les mêmes identifiants que `.env`.

## Configuration Firebase
- Créez un fichier `config/firebase.php` si absent. Exemple minimal :
  ```php
  return [
      'project_id' => env('FIREBASE_PROJECT_ID'),
      'credentials' => [
          'file' => env('FIREBASE_CREDENTIALS'), // chemin JSON service account
      ],
  ];
  ```
- Ajoutez les variables dans `.env` (sans les versionner) :
  ```dotenv
  FIREBASE_PROJECT_ID=...
  FIREBASE_CREDENTIALS=/var/www/html/storage/firebase-service-account.json
  ```
- Placez le fichier JSON de compte de service hors du dépôt et référencez-le via une variable.

## Tests
- Tests automatisés avec PHPUnit :
  ```powershell
  docker compose -f docker-compose-image.yml exec laravel php artisan test
  ```
  Complétez les tests pour couvrir les routes critiques (auth, voitures, réparations, notifications).

## Maintenance & scripts utiles
- `start-project.sh` : script PowerShell guidant la configuration (vérification containers, `.env`, migrations).
- `fix-docker.ps1` : commandes de nettoyage/redémarrage Docker.

## Points de vigilance
- Vérifier que tous les contrôleurs mentionnés dans `routes/api.php` sont implémentés (CommandeController, NotificationController…).
- Veiller à hasher les mots de passe via `Hash::make` (champ `mot_de_passe_hash`) ; si vous utilisez les fonctionnalités Laravel Auth classiques, adaptez les guards/factories pour ce champ personnalisé.
- Ne pas exposer de secrets dans le dépôt.
