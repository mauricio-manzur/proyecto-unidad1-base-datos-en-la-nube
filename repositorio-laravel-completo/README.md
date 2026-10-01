# Repositorio de archivos (Laravel 12 + MySQL/RDS + S3)

## Instalación
1. `composer install --no-dev --optimize-autoloader`
2. `cp .env.example .env` y completa `APP_URL`, `DB_*`, `AWS_BUCKET`.
3. `php artisan key:generate`
4. Importa el esquema: `mysql -h <RDS> -u admin -p < database/sql/repositorio_archivos.sql`
5. Permisos: `chown -R www-data:www-data storage bootstrap/cache && chmod -R 775 storage bootstrap/cache`
6. Apunta el servidor web (Nginx/Apache) a la carpeta `public/`.
7. Opcional en producción: `php artisan config:cache && php artisan route:cache && php artisan view:cache`

## Subidas de hasta 50 MB
La app valida `max:51200` KB. Ajusta también:
- `php.ini`: `upload_max_filesize = 50M` y `post_max_size = 55M`
- Nginx: `client_max_body_size 55M;`

## IAM Role de la EC2 (política mínima sobre el bucket)
Acciones `s3:PutObject`, `s3:GetObject`, `s3:DeleteObject` sobre `arn:aws:s3:::BUCKET/*` y `s3:ListBucket` sobre `arn:aws:s3:::BUCKET`.

## Notas
- Zona horaria de la app: UTC (coincide con `CURRENT_TIMESTAMP` de RDS).
- No hay migraciones: el esquema vive en `database/sql/repositorio_archivos.sql`.
- Idioma por defecto: español (`lang/es/validation.php`).
