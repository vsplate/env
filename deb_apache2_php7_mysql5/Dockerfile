FROM php:7.2.2-apache

MAINTAINER Ambulong <zeng.ambulong@gmail.com>

RUN apt-get update && apt-get install -y \
        apt-utils \
        supervisor \
        apt-transport-https \
        lsb-release \
        curl \
        wget \
        ca-certificates \
        libssl-dev \
        unzip \
        libaio1 \
        --no-install-recommends


# Install PHP dependencies
RUN apt-get update && apt-get install -y g++ \
                      openssl \
                      libc-client-dev \
                      libkrb5-dev \
                      libxml2-dev \
                      libfreetype6-dev \
                      libgd-dev \
                      libldap2-dev \
                      libsasl2-dev \
                      libmcrypt-dev \
                      libcurl4-openssl-dev \
                      libgmp-dev \
                      --no-install-recommends

RUN docker-php-ext-configure imap --with-kerberos --with-imap-ssl && \
        docker-php-ext-configure gd --with-freetype-dir=/usr && \
        docker-php-ext-configure bcmath

RUN docker-php-ext-install mysqli pdo_mysql imap gd exif dom gettext mbstring soap zip bcmath gmp


# MySQL set up
RUN echo 'mysql-server mysql-server/root_password password toor' | debconf-set-selections && \
    echo 'mysql-server mysql-server/root_password_again password toor' | debconf-set-selections && \
    apt-get -y install mysql-server

RUN rm -rf /var/www/html/*

COPY php.ini /usr/local/etc/php/conf.d/vsplate.ini
COPY supervisord.conf /etc/supervisord.conf

ENTRYPOINT ["/usr/bin/supervisord", "-n", "-c",  "/etc/supervisord.conf"]

EXPOSE 80
