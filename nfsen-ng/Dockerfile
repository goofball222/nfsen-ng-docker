FROM debian:bookworm-slim AS builder

ARG NFDUMP_VERSION=1.7.4

ENV \
    DEBIANFRONTEND=noninteractive

WORKDIR /artifacts

RUN echo 'debconf debconf/frontend select Noninteractive' | debconf-set-selections \
    && apt-get update -qq \
    && apt-get install --no-install-recommends --no-install-suggests -y \
       autoconf autogen automake bison \
       build-essential ca-certificates curl flex \
       libbz2-dev librrd-dev libtool m4 \
       pkg-config \
    && curl -sSL https://github.com/phaag/nfdump/archive/refs/tags/v$NFDUMP_VERSION.tar.gz -o nfdump.tar.gz \
    && tar -xzf nfdump.tar.gz \
    && cd nfdump-${NFDUMP_VERSION} \
    && bash autogen.sh \
    && mkdir -p /artifacts/nfdump \
    && ./configure \
       --prefix=/artifacts/nfdump \
       --enable-nfprofile \
       --enable-nftrack \
       --enable-sflow \
    && make \
    && make install

FROM php:8.3-apache

ENV \
    DEBIAN_FRONTEND=noninteractive \
    PROFILES_DATA_PATH=/var/nfdump/profiles-data/ \
    APACHE_RUN_USER=www-data \
    APACHE_RUN_GROUP=www-data \
    APACHE_LOG_DIR=/var/log/apache2 \
    APACHE_PID_FILE=/var/run/apache2.pid \
    APACHE_RUN_DIR=/var/run/apache2 \
    APACHE_LOCK_DIR=/var/lock/apache2 \
    APACHE_LOG_DIR=/var/log/apache2

WORKDIR /var/www/html

# Copy artifacts
COPY --from=builder /artifacts/nfdump/ /usr/local

RUN \
    apt-get -qqy update \
    && apt-get -qqy install --no-install-recommends --no-install-suggests \
      git librrd-dev pkg-config rrdtool \
      tzdata \
    && nfdump -V \
    && a2enmod rewrite deflate headers expires \
    && pecl install rrd \
    && mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && echo "extension=rrd.so" >> /usr/local/etc/php/conf.d/rrd.ini \
    && sed -e '/<Directory \/var\/www\/>/,/<\/Directory>/s/AllowOverride None/AllowOverride All/' -i /etc/apache2/apache2.conf \
    && git clone https://github.com/mbolli/nfsen-ng.git . \
    && apt-get -qqy purge \
      git \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /var/log/*

COPY settings.php backend/settings/settings.tmpl
COPY entrypoint.sh /

RUN \
    chown -R www-data:www-data .\
    && chmod +x backend/cli.php /entrypoint.sh

RUN \
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php -r "if (hash_file('sha384', 'composer-setup.php') === 'dac665fdc30fdd8ec78b38b9800061b4150413ff2e3b6f88543c636f7cd84f6db9189d43a81e5503cda447da73c7e5b6') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
    && php composer-setup.php \
    && php -r "unlink('composer-setup.php');"

RUN php composer.phar install --no-dev

VOLUME [ "/var/nfdump/profiles-data" ]

EXPOSE 80

ENTRYPOINT [ "/entrypoint.sh" ]
