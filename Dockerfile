# Start with the latest WordPress image.
FROM wordpress:php8.1

# Set up nodejs PPA
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash -

# Install server dependencies.
RUN apt-get update && apt-get install -qq -y nodejs build-essential pkg-config libcairo2-dev libjpeg-dev libgif-dev git subversion default-mysql-client zip unzip vim libyaml-dev --fix-missing --no-install-recommends

COPY bin/install-wp-tests.sh /
RUN cat /install-wp-tests.sh | bash /dev/stdin wordpress root password mysql latest true

# Setup phpunit dependencies (needed for coverage).
RUN pecl install xdebug && \
		docker-php-ext-enable xdebug

# Download wp-cli
RUN curl -o /usr/local/bin/wp https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar && chmod 755 /usr/local/bin/wp

# Disable PHP opcache (not great while developing)
RUN rm -rf /usr/local/etc/php/conf.d/opcache-recommended.ini

# Install composer.
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer

ENV PATH="/root/.composer/vendor/bin::${PATH}"

RUN composer global require "phpunit/phpunit=8.*"

# The WP test suite requires the PHPUnit Polyfills library.
RUN composer global require "yoast/phpunit-polyfills:^1.1"
ENV WP_TESTS_PHPUNIT_POLYFILLS_PATH="/root/.composer/vendor/yoast/phpunit-polyfills"
RUN composer global config --no-plugins allow-plugins.dealerdirect/phpcodesniffer-composer-installer true
RUN composer global require -W "squizlabs/php_codesniffer:^3.13" "dealerdirect/phpcodesniffer-composer-installer" "wp-coding-standards/wpcs:^3.1"
