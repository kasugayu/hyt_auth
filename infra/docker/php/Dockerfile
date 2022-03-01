FROM php:8.0-fpm-buster
SHELL ["/bin/bash", "-oeux", "pipefail", "-c"]

# timezone environment
ENV TZ=Asia/Tokyo \
  # locale
  LANG=en_US.UTF-8 \
  LANGUAGE=en_US:en \
  LC_ALL=en_US.UTF-8 \
  # composer environment
  COMPOSER_ALLOW_SUPERUSER=1 \
  COMPOSER_HOME=/composer

COPY --from=composer:2.0 /usr/bin/composer /usr/bin/composer

RUN apt-get update && \
  apt-get -y install git libicu-dev libonig-dev libzip-dev unzip locales \
  # https://oki2a24.com/2018/08/29/how-to-install-php-gd-to-php-5-6-apache-with-dockerfile/
  # for gd
  libfreetype6-dev \
  libjpeg62-turbo-dev \
  libpng-dev \
  #----------------------------------------------------------------------------------------
  # gd support jpeg
  # https://qiita.com/takeru56/items/74d3e402660ae4473e88
   libjpeg-dev \
  #----------------------------------------------------------------------------------------
  && \
  apt-get clean && \
  rm -rf /var/lib/apt/lists/* && \
  locale-gen en_US.UTF-8 && \
  localedef -f UTF-8 -i en_US en_US.UTF-8 && \
  mkdir /var/run/php-fpm && \
  #----------------------------------------------------------------------------------------
  # add gd
  # docker-php-ext-install intl pdo_mysql zip bcmath && \
  docker-php-ext-install gd intl pdo_mysql zip bcmath && \
  # gd support jpeg
  docker-php-ext-configure gd \
  # --with-png=/usr/include/ \ 
  --with-jpeg=/usr/include/ \ 
  --with-freetype=/usr/include/ && \
  docker-php-ext-install -j$(nproc) gd exif && \
  #
  #----------------------------------------------------------------------------------------
  composer config -g process-timeout 3600 && \
  composer config -g repos.packagist composer https://packagist.org

#https://qiita.com/manbolila/items/498aae00f3574c72f031
#https://www.webopixel.net/php/1661.html
RUN apt-get -y update
RUN apt-get install -y \
    curl \
    gnupg
RUN curl -sL https://deb.nodesource.com/setup_lts.x | bash -
RUN apt-get install -y nodejs
RUN npm install npm@latest -g

COPY ./infra/docker/php/php-fpm.d/zzz-www.conf /usr/local/etc/php-fpm.d/zzz-www.conf
COPY ./infra/docker/php/php.ini /usr/local/etc/php/php.ini

WORKDIR /work/backend

# Old
# RUN docker-php-ext-configure gd
# --with-png-dir=/usr/include/
# --with-jpeg-dir=/usr/include/
# --with-freetype-dir=/usr/include/

# New
# RUN docker-php-ext-configure gd
# --with-png=/usr/include/
# --with-jpeg=/usr/include/
# --with-freetype=/usr/include/
