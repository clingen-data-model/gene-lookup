FROM jward3/php:7.4-apache

LABEL maintainer="TJ Ward" \
    io.openshift.tags="gene-lookup:v1" \
    io.k8s.description="An application for lookin up information about genes for use in informatics projects" \
    io.openshift.expose-services="8080:http,8443:https" \
    io.k8s.display-name="gene-lookup version 1" \
    io.openshift.tags="php,apache"

# An attempt to get apache to tell php it's using ssl.
COPY .docker/apache/vhost.conf /etc/apache2/sites-available/000-default.conf
# COPY .docker/apache/default-ssl.conf /etc/apache2/sites-enabled/
COPY .docker/apache/conf.d/ports.conf /etc/apache2/ports.conf

COPY . /srv/app

ENV XDG_CONFIG_HOME=/srv/app

USER root
RUN chgrp -R 0 /srv/app \
    && chmod -R g+w /srv/app \
    && chmod g+x /srv/app/.openshift/deploy.sh \
    && a2enmod ssl
    # && pecl install xdebug-2.9.5 \
    # && docker-php-ext-enable xdebug \

WORKDIR /srv/app

RUN composer install \
        --no-interaction \
        --no-plugins \
        --no-scripts \
        --prefer-dist

# COPY .docker/php/xdebug-dev.ini /usr/local/etc/php/conf.d/xdebug-dev.ini

# RUN cp -R /usr/local/etc/php/conf.d /usr/local/etc/php/conf.d-dev \
#     && rm -f /usr/local//etc/php/conf.d/*-dev.ini \
#     && rm -f /usr/local/etc/php/conf.d/*xdebug.ini

USER 1001

# RUN  echo 'alias art="php artisan"' >> ~/.bashrc