FROM php:8.3-apache

RUN rm -vfr /var/lib/apt/lists/*

RUN apt-get update -y
RUN apt-get install -y git vim

RUN set -eux; \
    if command -v a2enmod; then \
        a2enmod rewrite; \
    fi; \
    savedAptMark="$(apt-mark showmanual)"; \
    apt-get install -y \
        libfreetype6-dev \
        libjpeg-dev \
        libpng-dev \
        libpq-dev \
        libssl-dev \
        ca-certificates \
        libcurl4-openssl-dev \
        libgd-tools \
        libmcrypt-dev \
        zip \
        default-mysql-client \
        vim \
        wget \
        libicu-dev \
        libbz2-dev \
        libzip-dev \
        zlib1g-dev \
        freetype* \
        libfreetype6-dev; \
    docker-php-ext-configure gd \
        --with-freetype=/usr \
        --with-jpeg=/usr; \
    docker-php-ext-install -j "$(nproc)" \
        curl \
        pdo_mysql \
        zip \
        bcmath \
        bz2 \
        exif \
        ftp \
        gd \
        gettext \
        mysqli \
        opcache \
        shmop \
        sysvmsg \
        sysvsem \
        sysvshm \
        intl; \
    apt-mark auto '.*' > /dev/null; \
    apt-mark manual $savedAptMark; \
    ldd "$(php -r 'echo ini_get("extension_dir");')"/*.so \
        | awk '/=>/ { print $3 }' \
        | sort -u \
        | xargs -r dpkg-query -S \
        | cut -d: -f1 \
        | sort -u \
        | xargs -rt apt-mark manual; \
    apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false; \
    rm -rf /var/lib/apt/lists/*

# Set recommended PHP.ini settings
RUN { \
    echo 'memory_limit=1024M'; \
    echo 'output_buffering=Off'; \
    echo 'upload_max_filesize=1G'; \
    echo 'post_max_size=1G'; \
    echo 'opcache.enable_cli=1'; \
    echo 'opcache.memory_consumption=1024'; \
    echo 'opcache.interned_strings_buffer=8'; \
    echo 'opcache.max_accelerated_files=6000'; \
    echo 'opcache.revalidate_freq=60'; \
    echo 'opcache.fast_shutdown=1'; \
    echo 'error_reporting=E_ALL'; \
    echo 'error_log=/var/log/php_errors.log'; \
} > /usr/local/etc/php/conf.d/opcache-recommended.ini

# Set the working directory to /var/www/html
WORKDIR /var/www/html

# Copy the application code into /var/www/html/lsapp
COPY . /var/www/html/lsapp

# Create logs directory and set permissions
RUN mkdir /var/www/html/lsapp/logs
RUN chmod 777 /var/www/html/lsapp/logs
RUN chown -R www-data:www-data /var/www/html

# Configure Apache for subfolder access
RUN echo '<Directory /var/www/html/lsapp/>\n\
    Options Indexes FollowSymLinks MultiViews\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/lsapp.conf \
    && a2enconf lsapp

# Configure VirtualHost for lsapp
RUN echo '<VirtualHost *:8080>\n\
    DocumentRoot /var/www/html\n\
    <Directory "/var/www/html">\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# Expose port 8080
EXPOSE 8080

# Start Apache in the foreground
CMD ["apache2-foreground"]

RUN sed -i 's/80/8080/g' /etc/apache2/ports.conf
RUN cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini
RUN service apache2 restart