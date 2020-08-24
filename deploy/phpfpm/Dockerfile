FROM php:fpm-alpine

ARG BUILD_DATE="develop"
ARG VCS_REF="develop"
# build for production
ARG APP_ENV=prod

# The maintainer list
LABEL authors="Christopher Thomas <christopher.thomas@antimatter-studios.com>"
LABEL org.label-schema.build-date=$BUILD_DATE
LABEL org.label-schema.vcs-ref=$VCS_REF

# persistent / runtime deps
RUN apk add --no-cache acl file gettext git

RUN set -xeu \
	&& apk add --no-cache --virtual .build-deps $PHPIZE_DEPS icu-dev libzip-dev zlib-dev oniguruma-dev \
	&& docker-php-ext-install -j$(nproc) intl zip sockets pdo_mysql mysqli tokenizer exif mbstring \
	&& pecl install apcu xdebug \
	&& pecl clear-cache \
	&& docker-php-ext-enable xdebug apcu opcache \
	&& runDeps="$( \
		scanelf --needed --nobanner --format '%n#p' --recursive /usr/local/lib/php/extensions \
			| tr ',' '\n' \
			| sort -u \
			| awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }' \
		)" \
	&& apk add --no-cache --virtual .api-phpexts-rundeps $runDeps \
	&& apk del .build-deps

# Add configuration for the phpfpm container
COPY deploy/phpfpm/php.ini /usr/local/etc/php/php.ini

# the zzz in the filename, is to make sure this file is loaded last into the configuration
COPY deploy/phpfpm/phpfpm.conf /usr/local/etc/php-fpm.d/zzz_config.conf

# Configure XDEBUG extension and configuration
COPY deploy/phpfpm/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

RUN mkdir -p cache/log

COPY . /www
WORKDIR /www

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV PATH="${PATH}:/root/.composer/vendor/bin"

RUN set -eux; \
	composer global require "hirak/prestissimo:^0.3" --prefer-dist --no-progress --no-suggest --classmap-authoritative; \
	composer install --prefer-dist --no-dev --no-suggest

RUN chmod -R 755 /www/storage
RUN chown -R www-data /www/storage

# Copy the container entrypoint
COPY deploy/phpfpm/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Copy the XDebug configuration
COPY deploy/phpfpm/xdebug.sh /xdebug.sh
RUN chmod +x /xdebug.sh

EXPOSE 9000
ENTRYPOINT ["/entrypoint.sh"]
CMD ["php-fpm"]
