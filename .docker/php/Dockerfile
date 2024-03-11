FROM php:8.2-fpm

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN apt-get update --fix-missing && \
    apt-get install -y \
    git unzip \
    libc-client-dev \
    libssh-dev \
    librabbitmq-dev \
    mc \
    libkrb5-dev \
    libpq-dev \
    libcurl4-nss-dev \
    libonig-dev \
    libyaml-dev && rm -r /var/lib/apt/lists/* \
    && pecl install xdebug-3.2.0 \
    && docker-php-ext-enable xdebug \
    && docker-php-ext-install mysqli \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install pdo_pgsql \
    && docker-php-ext-install ctype \
    && docker-php-ext-install bcmath \
    && docker-php-ext-install mbstring \
    && docker-php-ext-install pgsql \
    && docker-php-ext-configure imap --with-kerberos --with-imap-ssl \
    && docker-php-ext-install imap \
    && pecl install amqp \
    && docker-php-ext-enable amqp \
    && pecl install memcache \
    && docker-php-ext-enable memcache

WORKDIR /var/www/ok
