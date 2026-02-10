Write-Host "=== CONFIGURATION ET DÉMARRAGE DU BACKEND ===" -ForegroundColor Cyan

# 1. Vérifiez si les containers sont en cours d'exécution
Write-Host "`n1. Vérification des containers..." -ForegroundColor Yellow
docker-compose -f docker-compose-image.yml ps

# 2. Vérifiez le fichier .env
Write-Host "`n2. Vérification du fichier .env..." -ForegroundColor Yellow
if (Test-Path .env) {
    Write-Host "Fichier .env trouvé" -ForegroundColor Green
    Get-Content .env | Select-String -Pattern "DB_|APP_" | ForEach-Object { Write-Host $_ }
} else {
    Write-Host "Création du fichier .env..." -ForegroundColor Red
    @"
APP_NAME=Laravel
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=garage_auto
DB_USERNAME=garage_user
DB_PASSWORD=garage_password
"@ | Out-File .env -Encoding UTF8
    Write-Host "Fichier .env créé" -ForegroundColor Green
}

# 3. Générez la clé d'application
Write-Host "`n3. Génération de la clé d'application..." -ForegroundColor Yellow
docker-compose -f docker-compose-image.yml exec laravel php artisan key:generate

# 4. Exécutez les migrations
Write-Host "`n4. Exécution des migrations..." -ForegroundColor Yellow
docker-compose -f docker-compose-image.yml exec laravel php artisan migrate

# 5. Vérifiez l'état
Write-Host "`n5. État des migrations :" -ForegroundColor Yellow
docker-compose -f docker-compose-image.yml exec laravel php artisan migrate:status

Write-Host "`n=== CONFIGURATION TERMINÉE ===" -ForegroundColor Green