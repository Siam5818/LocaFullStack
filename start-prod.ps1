#/* cspell:disable */

Write-Host "Démarrage de l'environnement de production local..." -ForegroundColor Cyan

Write-Host "Build et lancement des conteneurs..." -ForegroundColor Yellow
docker compose -f docker-compose.prod.yml --env-file .env.prod up -d --build

Write-Host "Attente que PostgreSQL soit prêt (15s)..." -ForegroundColor Yellow
Start-Sleep -Seconds 15

Write-Host "Migrations Laravel..." -ForegroundColor Yellow
docker exec loca_backend php artisan migrate --force

Write-Host "Lien storage..." -ForegroundColor Yellow
docker exec loca_backend php artisan storage:link

Write-Host "Cache de configuration..." -ForegroundColor Yellow
docker exec loca_backend php artisan config:cache
docker exec loca_backend php artisan route:cache
docker exec loca_backend php artisan view:cache

Write-Host ""
Write-Host "Application disponible sur http://localhost" -ForegroundColor Green
Write-Host "Swagger API : http://localhost/api/documentation" -ForegroundColor Green