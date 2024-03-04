up:
	docker compose up -d

bash:
	docker compose exec api bash

nginx:
	docker compose exec nginx sh

queue:
	docker compose exec queue redis-cli -a qmind

db:
	docker compose exec mysql mysql -u turnobank -pturnobank

restart:
	docker compose restart

stop:
	docker compose stop

build:
	docker compose up -d --build

down:
	docker compose down

logs:
	docker compose logs -f api

logs-nginx:
	docker compose logs -f nginx

logs-mysql:
	docker compose logs -f mysql

enable-xdebug:
	docker compose exec --user root api bash -c 'sed -i "/;zend_extension=\/usr\/local\/lib\/php\/extensions\/no-debug-non-zts-20230831\/xdebug.so/c\zend_extension=\/usr\/local\/lib\/php\/extensions\/no-debug-non-zts-20230831\/xdebug.so" /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini'
	docker compose exec --user root api bash -c 'supervisorctl restart php'

disable-xdebug:
	docker compose exec --user root api bash -c 'sed -i "/zend_extension=\/usr\/local\/lib\/php\/extensions\/no-debug-non-zts-20230831\/xdebug.so/c\;zend_extension=\/usr\/local\/lib\/php\/extensions\/no-debug-non-zts-20230831\/xdebug.so" /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini'
	docker compose exec --user root api bash -c 'supervisorctl restart php'
