FROM berglab/php:7.4-apache

LABEL maintainer="TJ Ward" \
    io.openshift.tags="gene-lookup:v1" \
    io.k8s.description="An application for lookin up information about genes for use in informatics projects" \
    io.openshift.expose-services="8080:http,8443:https" \
    io.k8s.display-name="gene-lookup version 1" \
    io.openshift.tags="php,apache"

COPY . /srv/app

USER root
RUN chgrp -R 0 /srv/app \
    && chmod -R g+w /srv/app \
    # && pecl install xdebug-2.9.5 \
    # && docker-php-ext-enable xdebug \
    && chown -R www-data:www-data /srv/app

# COPY .docker/php/xdebug-dev.ini /usr/local/etc/php/conf.d/xdebug-dev.ini

# RUN cp -R /usr/local/etc/php/conf.d /usr/local/etc/php/conf.d-dev \
#     && rm -f /usr/local//etc/php/conf.d/*-dev.ini \
#     && rm -f /usr/local/etc/php/conf.d/*xdebug.ini

USER 1001
WORKDIR /srv/app
