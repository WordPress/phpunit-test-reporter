SHELL := /bin/bash

# WordPress management via docker-compose
start:
	docker-compose -f docker-compose.yml up -d --build

stop:
	docker-compose -f docker-compose.yml stop

# One liner to get a shell inside the WordPress container.
shell:
	docker-compose exec wordpress /bin/bash -c "cd /var/www/html/wp-content/plugins/wp-unit-test-reporter/; /bin/bash"

test:
	docker-compose exec wordpress /bin/bash -c "cd /var/www/html/wp-content/plugins/wp-unit-test-reporter/; phpcs --standard=phpcs.xml.dist ./ && phpunit"

metadiff:
	if [ -d "./build/wordpress.org" ]; then \
		git -C ./build/wordpress.org reset --hard HEAD && \
		git -C ./build/wordpress.org pull --rebase; \
	else \
		git clone https://github.com/WordPress/wordpress.org.git ./build/wordpress.org; \
	fi
	cp -rvp phpunit-test-reporter.php readme.txt src parts ./build/wordpress.org/wordpress.org/public_html/wp-content/plugins/phpunit-test-reporter
	git -C ./build/wordpress.org diff --no-prefix > build/metadiff.diff
