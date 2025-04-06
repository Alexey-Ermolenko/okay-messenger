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
    libevent-dev \
    libyaml-dev && rm -r /var/lib/apt/lists/* \
    && pecl install xdebug-3.2.0 \
    && docker-php-ext-enable xdebug \
    && echo "zend_extension=xdebug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_port=9000" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.idekey=PHPSTORM" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.log=/var/log/xdebug.log" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && docker-php-ext-install mysqli \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install pdo_pgsql \
    && docker-php-ext-install ctype \
    && docker-php-ext-install bcmath \
    && docker-php-ext-install mbstring \
    && docker-php-ext-install pgsql \
    && docker-php-ext-configure imap --with-kerberos --with-imap-ssl \
    && docker-php-ext-install imap \
    && docker-php-ext-install sockets \
    && pecl install amqp \
    && docker-php-ext-enable amqp \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && pecl install memcache \
    && docker-php-ext-enable memcache

RUN pecl install excimer \
    && docker-php-ext-enable excimer

WORKDIR /var/www/ok

#COPY xdebug.ini /usr/local/etc/php/