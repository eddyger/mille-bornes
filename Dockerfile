FROM php:8.2-fpm

RUN apt-get update \
    && apt-get install -y wget curl zip unzip npm

RUN docker-php-ext-install pdo_mysql

RUN curl -sS https://get.symfony.com/cli/installer | bash
RUN mv ~/.symfony5/bin/symfony /usr/local/bin/symfony

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer