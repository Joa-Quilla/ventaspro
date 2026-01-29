# VentasPro - Configuración de Despliegue

## Configuración de PHP para Producción

### 1. Usando Apache (.htaccess)

Si tu servidor usa Apache, agrega esto al archivo `.htaccess` en la carpeta `public/`:

```apache
php_value max_execution_time 120
php_value memory_limit 256M
php_value post_max_size 50M
php_value upload_max_filesize 50M
```

### 2. Usando Nginx

En tu configuración de Nginx, agrega dentro del bloque `location ~ \.php$`:

```nginx
fastcgi_read_timeout 120;
fastcgi_param PHP_VALUE "max_execution_time=120
memory_limit=256M
post_max_size=50M
upload_max_filesize=50M";
```

### 3. Usando cPanel o Hosting Compartido

El archivo `.user.ini` en la carpeta `public/` ya está configurado con:

```ini
max_execution_time = 120
memory_limit = 256M
```

**Nota:** Los cambios en `.user.ini` pueden tardar hasta 5 minutos en aplicarse.

### 4. Editar php.ini directamente (VPS/Dedicado)

Si tienes acceso root, edita el archivo `php.ini`:

```ini
max_execution_time = 120
memory_limit = 256M
post_max_size = 50M
upload_max_filesize = 50M
```

Luego reinicia PHP-FPM:

```bash
sudo systemctl restart php8.4-fpm
```

## Verificar Configuración Actual

Para verificar que los cambios se aplicaron:

```bash
php -i | grep max_execution_time
```

O crea un archivo `info.php` en `public/` con:

```php
<?php phpinfo(); ?>
```

Y accede a `http://tudominio.com/info.php` (¡elimínalo después de verificar!)

## Optimizaciones Adicionales

### Cache de Configuración (Producción)

Antes de desplegar, ejecuta:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan filament:cache-components
```

### Variables de Entorno para Producción

En tu archivo `.env` de producción:

```env
APP_ENV=production
APP_DEBUG=false
MAX_EXECUTION_TIME=120
```

## Troubleshooting

Si el problema persiste después de configurar el timeout:

1. **Revisa logs del servidor:**
    - Apache: `/var/log/apache2/error.log`
    - Nginx: `/var/log/nginx/error.log`
2. **Revisa logs de Laravel:**

    ```bash
    tail -f storage/logs/laravel.log
    ```

3. **Verifica la conexión a la base de datos** - asegúrate que no haya latencia de red

4. **Optimiza queries** si hay muchos datos:
    - Considera agregar índices a las tablas
    - Usa paginación en todas las tablas
    - Implementa cache para queries frecuentes
