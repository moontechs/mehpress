# Multi-stage build for Laravel with FrankenPHP

# Composer stage (must come first for dependencies)
FROM composer:2 AS composer-builder

# Install required PHP extensions for composer dependencies
RUN apk add --no-cache \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libzip-dev \
    postgresql-dev \
    icu-dev \
    autoconf \
    build-base \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        gd \
        pdo_pgsql \
        pgsql \
        intl \
        zip \
    && pecl install ast \
    && docker-php-ext-enable ast

WORKDIR /app

# Copy composer files
COPY composer.json composer.lock ./

# Install PHP dependencies (production only)
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# Node.js stage for building assets
FROM node:20-alpine AS node-builder

# Install pnpm
RUN corepack enable && corepack prepare pnpm@latest --activate

WORKDIR /app

# Copy package files and install dependencies
COPY package.json pnpm-lock.yaml* ./
RUN pnpm install --frozen-lockfile

# Copy source files
COPY . .

# Copy vendor from composer stage (needed for JS imports)
COPY --from=composer-builder /app/vendor ./vendor

# Build assets
RUN pnpm run build

# Final production stage with FrankenPHP
FROM dunglas/frankenphp:1-php8.4

# Install system dependencies
# Install system dependencies and cronn
RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip \
    make \
    curl \
    && echo "$(dpkg --print-architecture)" \
    && ARCH="$(dpkg --print-architecture)"; \
       curl -L -o /tmp/cronn.deb "https://github.com/umputun/cronn/releases/download/v1.3.0/cronn_v1.3.0_linux_${ARCH}.deb" \
    && echo '#!/bin/bash\nexit 0' > /usr/bin/systemctl \
    && chmod +x /usr/bin/systemctl \
    && dpkg -i /tmp/cronn.deb || true \
    && rm /usr/bin/systemctl \
    && rm /tmp/cronn.deb \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN install-php-extensions \
    gd \
    pdo_mysql \
    pdo_pgsql \
    bcmath \
    ctype \
    curl \
    dom \
    fileinfo \
    filter \
    hash \
    mbstring \
    openssl \
    pcre \
    pdo \
    session \
    tokenizer \
    xml \
    intl \
    zip \
    opcache \
    redis

# Configure PHP for production
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Configure OPcache for production (preload will be enabled after app files are copied)
RUN { \
    echo 'opcache.enable=1'; \
    echo 'opcache.enable_cli=1'; \
    echo 'opcache.memory_consumption=256'; \
    echo 'opcache.interned_strings_buffer=16'; \
    echo 'opcache.max_accelerated_files=20000'; \
    echo 'opcache.max_wasted_percentage=10'; \
    echo 'opcache.use_cwd=1'; \
    echo 'opcache.validate_timestamps=0'; \
    echo 'opcache.revalidate_freq=0'; \
    echo 'opcache.save_comments=0'; \
    echo 'opcache.fast_shutdown=1'; \
    echo 'opcache.enable_file_override=1'; \
    echo 'opcache.optimization_level=0xffffffff'; \
} > /usr/local/etc/php/conf.d/opcache.ini

# Additional production PHP optimizations
RUN { \
    echo 'memory_limit=512M'; \
    echo 'max_execution_time=60'; \
    echo 'max_input_vars=3000'; \
    echo 'post_max_size=50M'; \
    echo 'upload_max_filesize=50M'; \
    echo 'session.cookie_httponly=1'; \
    echo 'session.cookie_secure=1'; \
    echo 'session.use_strict_mode=1'; \
    echo 'expose_php=off'; \
    echo 'display_errors=off'; \
    echo 'display_startup_errors=off'; \
    echo 'log_errors=on'; \
    echo 'error_log=/var/log/php_errors.log'; \
    echo 'date.timezone=UTC'; \
} > /usr/local/etc/php/conf.d/production.ini

# Set working directory
WORKDIR /app

# Copy vendor dependencies from composer stage
COPY --from=composer-builder --chown=www-data:www-data /app/vendor ./vendor

# Copy built assets from node stage
COPY --from=node-builder --chown=www-data:www-data /app/public/build ./public/build

# Copy application files
COPY --chown=www-data:www-data . .

# Enable opcache preloading with production-safe preload file
RUN echo 'opcache.preload=/app/bootstrap/preload.php' >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo 'opcache.preload_user=www-data' >> /usr/local/etc/php/conf.d/opcache.ini

# Copy custom Caddyfile
COPY --chown=root:root docker/Caddyfile /etc/caddy/Caddyfile
COPY docker/entrypoint.sh /usr/local/bin/docker-entrypoint.sh

# Create required directories and set permissions
RUN mkdir -p storage/app/public storage/logs storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache \
    && chmod +x /usr/local/bin/docker-entrypoint.sh

# Set environment variables defaults (can be overridden at runtime)
ENV SERVER_NAME=:80 \
    APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr \
    CACHE_STORE=file \
    SESSION_DRIVER=files \
    DB_DATABASE=database/mount/database.sqlite

HEALTHCHECK --interval=60s --timeout=3s --start-period=5s --retries=3 \
    CMD curl -f http://localhost/up || exit 1

EXPOSE 80 443

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
