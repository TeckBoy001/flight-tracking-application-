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
                fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name; \
                include fastcgi_params; \
            } \
            error_page 404 /index.php; \
        } \
    }' > /etc/nginx/nginx.conf

# Set permissions for Laravel storage
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 10000

CMD ["sh", "-c", "php-fpm -D && nginx -g 'daemon off;'"]
