DOCKER_COMPOSE_FILES ?= -f docker-compose.yml

ifeq ($(shell uname -s),Darwin)
	DOCKER_COMPOSE_FILES += -f docker-compose.mac.yml
endif
ifeq (,$(DOCKER_COMPOSE_FILES $(docker-compose.local.yml)))
	DOCKER_FILES += -f docker-compose.local.yml
endif

DOCKER_COMPOSE ?= docker-compose $(DOCKER_COMPOSE_FILES)
EXECUTE_APP ?= $(DOCKER_COMPOSE) exec -e XDEBUG_MODE=off php

# Docker
docker-build: ## [Docker] Builds, (re)creates, and start docker containers
	docker build -t localhost ./.docker; \
	docker compose up --build -d; \
.PHONY: docker-build

docker-up: ## [Docker] start docker containers
	docker compose up -d
.PHONY: docker-up

docker-stop: ## [Docker] Stop containers
	docker compose stop
.PHONY: docker-stop

docker-exec: ## [Docker] SSH into container
	docker exec -it php /bin/bash
.PHONY: docker-exec

ps: ## [Docker] List containers
	$(DOCKER_COMPOSE) ps
.PHONY: ps

help: ## Display this help message
	@cat $(MAKEFILE_LIST) | grep -e "^[a-zA-Z_\-]*: *.*## *" | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'
.PHONY: help