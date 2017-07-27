# Start with the latest WordPress image.
FROM wordpress:4.7.3

# Set up nodejs PPA
RUN curl -sL https://deb.nodesource.com/setup_6.x | bash

# Install server dependencies.
RUN apt-get update && apt-get install -qq -y php5-mysql nodejs build-essential pkg-config libcairo2-dev libjpeg-dev libgif-dev git subversion mysql-client zip unzip vim libyaml-dev --fix-missing --no-install-recommends

# Setup phpunit dependencies (needed for coverage).
RUN pecl install xdebug && \
		docker-php-ext-enable xdebug

# Download wp-cli
RUN curl -o /usr/local/bin/wp https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar && chmod 755 /usr/local/bin/wp

# Disable PHP opcache (not great while developing)
RUN rm -rf /usr/local/etc/php/conf.d/opcache-recommended.ini

# Install composer.
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer

RUN composer global require "phpunit/phpunit=5.7.*"

ENV PATH="/root/.composer/vendor/bin::${PATH}"
