# ============================================================================
# Dockerfile — aplicação PHP (procedural) para EasyPanel.
# Document root = public/ (docs/05). Código sensível fica fora do docroot.
# Base: skill-dockerfile.md e docs/13.
# ============================================================================
FROM php:8.3-apache

WORKDIR /var/www/html

# Extensões e utilitários mínimos (curl para health check).
RUN apt-get update \
    && apt-get install -y --no-install-recommends libzip-dev unzip curl \
    && docker-php-ext-install pdo pdo_mysql zip \
    && a2enmod rewrite headers \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# php.ini de produção (display_errors off; log on).
COPY docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini

# VirtualHost apontando o Apache para /public.
COPY docker/apache/vhost.conf /etc/apache2/sites-available/000-default.conf

# Código da aplicação.
COPY . /var/www/html

# Permissões: código somente leitura; storage gravável (uploads/logs).
RUN chown -R www-data:www-data /var/www/html \
    && find /var/www/html -type d -exec chmod 755 {} \; \
    && find /var/www/html -type f -exec chmod 644 {} \; \
    && chmod -R 775 /var/www/html/storage \
    && chown -R www-data:www-data /var/www/html/storage

HEALTHCHECK --interval=30s --timeout=5s --retries=3 \
  CMD curl -f http://localhost/api/v1/health || exit 1

EXPOSE 80
