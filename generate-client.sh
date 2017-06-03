#!/bin/bash

# Clean up and checkout current client.
rm -rf build/php/WP-Unit-Test-API-Client-PHP/
git clone https://github.com/octalmage/wp-unit-test-api-client-php build/php/WP-Unit-Test-API-Client-PHP/

# Generate new client.
docker run --rm  -v ${PWD}:/local swaggerapi/swagger-codegen-cli generate \
    -i /local/swagger.yml \
    -l php \
    -c /local/swagger-config.json \
    -o /local/build/php

# Push the new client.
pushd build/php/WP-Unit-Test-API-Client-PHP/
bash git_push.sh octalmage WP-Unit-Test-API-Client-PHP
popd
