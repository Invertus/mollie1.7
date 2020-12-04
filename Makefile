bv: build-vendor
build-vendor:
	composer update
	cd vendorBuilder && php ./vendor/bin/php-scoper add-prefix
	rm -rf vendor
	mv vendorBuilder/build/vendor vendor
	composer dumpautoload
	find vendor/prestashop/ -type f -exec sed -i 's/MolliePrefix\\Composer\\Autoload\\ClassLoader/Composer\\Autoload\\ClassLoader/g' {} \;

bvn: build-vendor-no-dev
build-vendor-no-dev:
	composer update --no-dev --optimize-autoloader --classmap-authoritative
	cd vendorBuilder && php ./vendor/bin/php-scoper add-prefix
	rm -rf vendor
	mv vendorBuilder/build/vendor vendor
	composer dumpautoload

fl: fix-lint
fix-lint:
	docker run --rm -it -w=/app -v ${PWD}:/app oskarstark/php-cs-fixer-ga:latest

e2e: test-e2e
test-e2e:
	docker exec -i prestashop-17 sh -c "rm -rf /var/www/html/install"
	-docker exec -i prestashop-17 sh -c "mv /var/www/html/admin /var/www/html/admin966z7uc2l"
	docker exec -i prestashop-17 sh -c "cd /var/www/html && php  bin/console prestashop:module install mollie"
	docker exec -i prestashop-17 sh -c "chmod -R 777 /var/www/html"
