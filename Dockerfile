FROM php:8.2-fpm AS builder

ARG APP_ENV
ENV APP_ENV=${APP_ENV}

RUN apt-get update && apt-get install -y \
    git unzip libicu-dev libzip-dev zip \
    libxml2-dev libonig-dev libcurl4-openssl-dev pkg-config libssl-dev \
    && docker-php-ext-install intl pdo pdo_mysql zip opcache \
    && pecl install apcu && docker-php-ext-enable apcu \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g yarn \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/symfony
COPY . .

# Installation uniquement en prod (image finale prête)
RUN if [ "$APP_ENV" = "prod" ]; then \
        composer install --no-dev --optimize-autoloader --no-interaction; \
        yarn install && yarn build; \
    fi


FROM php:8.2-fpm

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/symfony

RUN apt-get update && apt-get install -y \
    libicu-dev libzip-dev unzip git \
    && docker-php-ext-install intl pdo pdo_mysql zip opcache \
    && pecl install apcu && docker-php-ext-enable apcu \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Copie du code et des dépendances si build en prod
COPY --from=builder /var/www/symfony /var/www/symfony

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Assure que www-data a la main sur tout, y compris vendor/ et var/
RUN mkdir -p var vendor && chown -R www-data:www-data /var/www/symfony

USER www-data

ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]
