FROM php:8.3-fpm

# Instalacja curl i unzip do Symfony CLI
RUN apt-get update && apt-get install -y \
    git unzip curl zip libicu-dev \
    && docker-php-ext-install intl pdo pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Symfony CLI
RUN curl -sS https://get.symfony.com/cli/installer | bash \
    && mv /root/.symfony*/bin/symfony /usr/local/bin/symfony
