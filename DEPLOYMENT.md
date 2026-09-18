# SkyBook Deployment

SkyBook is a Laravel 12 application that uses server-side PHP, a relational database, and a Vite-built frontend. It is not a static Netlify-only application.

## Server requirements

- PHP 8.2 or newer, with `pdo`, `pdo_mysql` or `pdo_pgsql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`, and `bcmath` available.
- Composer 2.x.
- Node.js 22 LTS and npm for building frontend assets.
- MySQL 8+ or PostgreSQL 14+ in production.
- Nginx or Apache configured with the document root set to `public/`, or the included Dockerfile.
- Writable `storage/` and `bootstrap/cache/` directories.

## Environment configuration

Copy `.env.example` to `.env` and replace placeholders with values from the hosting provider. Never commit `.env`.

```dotenv
APP_NAME=SkyBook
APP_ENV=production
APP_KEY=base64:GENERATE_WITH_ARTISAN
APP_DEBUG=false
APP_URL=https://your-production-domain.example
ADMIN_NAME=Admin
ADMIN_EMAIL=admin@your-production-domain.example
ADMIN_PASSWORD=use-a-long-random-password
SEED_DEMO_DATA=false

DB_CONNECTION=pgsql
DB_HOST=your-database-host
DB_PORT=5432
DB_DATABASE=your-database-name
DB_USERNAME=your-database-user
DB_PASSWORD=your-database-password
DB_SSLMODE=require

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
CACHE_STORE=database
QUEUE_CONNECTION=database

MAIL_MAILER=smtp
MAIL_SCHEME=tls
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-smtp-user
MAIL_PASSWORD=your-smtp-password
MAIL_FROM_ADDRESS=no-reply@your-domain.example
MAIL_FROM_NAME=SkyBook

MAP_TILE_URL=https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png
MAP_ATTRIBUTION="&copy; OpenStreetMap contributors"
```

MySQL deployments should use `DB_CONNECTION=mysql`, port `3306`, and the provider's MySQL values instead. No payment or external aviation API is required by the current application.

## Deployment steps

From the project root:

```bash
composer install --no-dev --prefer-dist --optimize-autoloader
npm install
npm run build
php artisan migrate --force
rm -rf public/storage
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Run `php artisan key:generate --force` only once when creating a new `.env` with no `APP_KEY`. For an existing production installation, set the original `APP_KEY` in the hosting provider and never regenerate it during deployment; changing it invalidates encrypted sessions and other encrypted application data. Removing only `public/storage` before `storage:link` makes redeploys safe when the link already exists.

On Windows PowerShell, copy the environment template with:

```powershell
Copy-Item .env.example .env
Remove-Item -LiteralPath public/storage -Force -ErrorAction SilentlyContinue
php artisan storage:link
```

Point the web server at `public/`. Run a queue worker only if queued jobs are introduced:

```bash
php artisan queue:work --sleep=3 --tries=3 --timeout=90
```

The admin seeder only creates an admin when `ADMIN_EMAIL` and `ADMIN_PASSWORD` are supplied, and it does not overwrite an existing user. Demo flights and demo bookings are disabled outside local/testing unless `SEED_DEMO_DATA=true`; this prevents `FlightSeeder` from modifying production flight rows. Do not enable demo data in production.

## Docker deployment

The included Dockerfile builds the Vite assets, installs PHP dependencies, applies migrations at container startup, and serves `/public` through Nginx/PHP-FPM on port `10000`. Supply the environment variables through the hosting provider; the image does not contain credentials.

```bash
docker build -t skybook .
docker run --env-file .env -p 10000:10000 skybook
```

## Map behavior

Leaflet and OpenStreetMap tiles load over HTTPS from the configured public URLs. The map requires internet access and is not offline functionality. If Leaflet cannot load, the page keeps the flight details and displays a clear map fallback message.

## Verification

Before deployment:

```bash
php artisan test
npm run build
php artisan route:list
php artisan migrate:status
```

The production database credentials, domain, SMTP credentials, and hosting-specific process configuration must be supplied outside the repository.