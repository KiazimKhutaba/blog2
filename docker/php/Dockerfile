FROM php:8.2-fpm

RUN apt-get update && apt-get install -y --no-install-recommends \
        libzip-dev \
        zlib1g-dev \
        libpq-dev \
        unzip git wget zip \
	&& docker-php-ext-install -j$(nproc) pgsql pdo_pgsql pdo_mysql \
    && apt-get clean -y && apt-get autoclean -y && apt-get autoremove -y \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

RUN groupadd --gid 1000 dapp \
  && useradd --uid 1000 --gid dapp --shell /bin/bash --create-home dapp