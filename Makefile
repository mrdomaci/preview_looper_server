CONTAINER_NAME := preview_looper_server-php-fpm-1

### DOCKER ###

up:
	@docker compose up -d

down:
	@docker compose down

php:
	@docker exec -it $(CONTAINER_NAME) bash

phpstan:
	@docker exec -it $(CONTAINER_NAME) ./vendor/bin/phpstan analyse app --level 6

ccs:
	@docker exec -it $(CONTAINER_NAME) composer ccs

fcs:
	@docker exec -it $(CONTAINER_NAME) composer fcs