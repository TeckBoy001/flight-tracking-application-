FROM php:8.2-fpm-alpine

# Install system dependencies and PHP extensions
RUN apk add --no-cache \
    nginx \
    shadow \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    zip \
    libzip-dev \
    unzip \
    git \
    curl \
    oniguruma-dev \
    postgresql-dev

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring zip gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy project files
COPY . .

# Install Laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# Create the inline Nginx configuration directly inside the container
RUN mkdir -p /run/nginx && \
    echo 'events { worker_connections 1024; } \
    http { \
        include /etc/nginx/mime.types; \
        default_type application/octet-stream; \
        server { \
            listen 10000; \
            root /var/www/public; \
            index index.php index.html; \
            charset utf-8; \
            location / { \
                try_files $uri $uri/ /index.php?$query_string; \
            } \
            location ~ \.php$ { \
                fastcgi_pass 127.0.0.1:9000; \
                fastcgi_index index.php; \
                fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name; \
                include fastcgi_params; \
            } \
            error_page 404 /index.php; \
        } \
    }' > /etc/nginx/nginx.conf

# Pre-create all missing Laravel storage subdirectories ahead of time
RUN mkdir -p /var/www/storage/framework/cache/data \
             /var/www/storage/framework/sessions \
             /var/www/storage/framework/views \
             /var/www/storage/logs

# Set exact permissions for the www-data user across the entire folder
RUN chown -R www-data:www-data /var/www && \
    chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Force PHP-FPM to accept outside environment variables
RUN echo "clear_env = no" >> /usr/local/etc/php-fpm.d/www.conf

EXPOSE 10000

# Clears hardcoded configuration cache, runs database migrations, and boots the application
CMD ["sh", "-c", "rm -f bootstrap/cache/config.php && php artisan config:clear && php artisan cache:clear && php artisan migrate --force && php-fpm -D && nginx -g 'daemon off;'"]
