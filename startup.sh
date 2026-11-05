#!/bin/sh
# Script de arranque para Azure App Service (Linux PHP 8.3 + Nginx)
# Remediación OWASP ZAP:
# - Copia la configuración endurecida de Nginx (default)
# - Precalienta cachés de configuración, rutas y vistas de Laravel (Insight 98% respuestas lentas)

cp /home/site/wwwroot/default /etc/nginx/sites-available/default
cp /home/site/wwwroot/default /etc/nginx/sites-enabled/default
cp /home/site/wwwroot/php.ini /usr/local/etc/php/conf.d/zz-security-zap.ini 2>/dev/null || true

cd /home/site/wwwroot
php artisan config:cache
php artisan route:cache
php artisan view:cache

service nginx reload
