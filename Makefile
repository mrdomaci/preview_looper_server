CONTAINER_NAME := laravel-app

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