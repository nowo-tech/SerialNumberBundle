# FrankenPHP - PHP 8.2 Alpine
FROM dunglas/frankenphp:1-php8.2-alpine

RUN apk add --no-cache git unzip bash
RUN install-php-extensions zip
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN git config --global --add safe.directory /app

WORKDIR /app
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV PATH="/app/vendor/bin:${PATH}"
ENV SERVER_NAME=:80

COPY docker/frankenphp/Caddyfile /etc/frankenphp/Caddyfile

RUN echo '#!/bin/sh' > /usr/local/bin/docker-entrypoint.sh && \
    echo 'set -e' >> /usr/local/bin/docker-entrypoint.sh && \
    echo 'mkdir -p /app/var/cache /app/var/log' >> /usr/local/bin/docker-entrypoint.sh && \
    echo 'chmod -R 777 /app/var' >> /usr/local/bin/docker-entrypoint.sh && \
    echo 'if [ ! -f /app/vendor/autoload_runtime.php ]; then composer install --no-interaction; fi' >> /usr/local/bin/docker-entrypoint.sh && \
    echo 'exec frankenphp run --config /etc/frankenphp/Caddyfile --adapter caddyfile' >> /usr/local/bin/docker-entrypoint.sh && \
    chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["docker-entrypoint.sh"]
