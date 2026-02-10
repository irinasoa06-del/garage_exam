# Ã‰tape 1: Sauvegarder vos fichiers originaux
Copy-Item Dockerfile Dockerfile.original -Force
Copy-Item docker-compose-fixed.yml docker-compose-fixed.yml.original -Force

# Ã‰tape 2: CrÃ©er un Dockerfile temporaire sans l'Ã©tape problÃ©matique
$dockerfileContent = Get-Content Dockerfile
$dockerfileContent = $dockerfileContent -replace 'RUN composer run-script post-autoload-dump', '# TEMPORAIREMENT COMMENTÃ‰: RUN composer run-script post-autoload-dump'
$dockerfileContent | Set-Content Dockerfile.temp

# Ã‰tape 3: Construire l'image
docker build -t garage-laravel -f Dockerfile.temp .

# Ã‰tape 4: Restaurer le Dockerfile original
Copy-Item Dockerfile.original Dockerfile -Force

# Ã‰tape 5: DÃ©marrer avec docker-compose modifiÃ© temporairement
docker compose -f docker-compose-fixed.yml down -v
docker compose -f docker-compose-fixed.yml up -d postgres
Start-Sleep -Seconds 10

# Ã‰tape 6: DÃ©marrer Laravel avec l'image construite
docker run -d --name garage-laravel 
  --network laravel-web_garage-network 
  -p 8000:8000 
  -e APP_ENV=local 
  -e APP_DEBUG=true 
  -e DB_CONNECTION=pgsql 
  -e DB_HOST=postgres 
  -e DB_PORT=5432 
  -e DB_DATABASE=garage_auto 
  -e DB_USERNAME=garage_user 
  -e DB_PASSWORD=garage_password 
  -v "${PWD}:/var/www/html" 
  garage-laravel

# Ã‰tape 7: ExÃ©cuter les commandes manquantes
docker exec garage-laravel composer run-script post-autoload-dump
docker exec garage-laravel php artisan key:generate
docker exec garage-laravel php artisan migrate --force
docker exec garage-laravel php artisan db:seed --force

Write-Host "
âœ… TERMINÃ‰! AccÃ©dez Ã  http://localhost:8000" -ForegroundColor Green
