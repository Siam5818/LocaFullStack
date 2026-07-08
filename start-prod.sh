#/* cspell:disable */

#!/bin/bash
set -e

echo "🚀 Démarrage de l'environnement de production local..."

# Charger les variables d'environnement
export $(cat .env.prod | grep -v '#' | xargs)

echo "📦 Build et lancement des conteneurs..."
docker compose -f docker-compose.prod.yml --env-file .env.prod up -d --build

echo "⏳ Attente que PostgreSQL soit prêt..."
sleep 10

echo "🗄️  Migrations Laravel..."
docker exec loca_backend php artisan migrate --force

echo "🔗 Lien storage..."
docker exec loca_backend php artisan storage:link

echo "⚡ Cache de configuration..."
docker exec loca_backend php artisan config:cache
docker exec loca_backend php artisan route:cache
docker exec loca_backend php artisan view:cache

echo ""
echo "✅ Application disponible sur http://localhost"
echo "📊 Logs backend  : docker logs loca_backend -f"
echo "📊 Logs frontend : docker logs loca_frontend -f"
echo "📊 Logs nginx    : docker logs loca_nginx -f"