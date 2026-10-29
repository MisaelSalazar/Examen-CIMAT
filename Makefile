SHELL := /bin/bash
COMPOSE := docker compose

.DEFAULT_GOAL := help

.PHONY: help build up down restart logs ps shell db-shell migrate seed fresh test lint pint

help: ## Muestra esta ayuda
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-12s\033[0m %s\n", $$1, $$2}'

build: ## Construye las imágenes
	$(COMPOSE) build

up: ## Levanta la aplicación en http://localhost:8080
	$(COMPOSE) up -d --build
	@echo "Aplicación disponible en http://localhost:$${APP_PORT:-8090}"

down: ## Detiene y elimina los contenedores (conserva la BD)
	$(COMPOSE) down

restart: ## Reinicia los servicios
	$(COMPOSE) restart

logs: ## Muestra los logs de la aplicación
	$(COMPOSE) logs -f app web

ps: ## Estado de los servicios
	$(COMPOSE) ps

shell: ## Abre una shell dentro del contenedor de la aplicación
	$(COMPOSE) exec app sh

db-shell: ## Abre el cliente de MariaDB
	$(COMPOSE) exec db sh -c 'mariadb -u"$$MARIADB_USER" -p"$$MARIADB_PASSWORD" "$$MARIADB_DATABASE"'

migrate: ## Ejecuta las migraciones pendientes
	$(COMPOSE) exec app php artisan migrate --force

seed: ## Carga los datos de demostración
	$(COMPOSE) exec app php artisan db:seed --force

fresh: ## Reinicia todo desde cero (BORRA la base de datos)
	$(COMPOSE) down -v
	$(COMPOSE) up -d --build

test: ## Ejecuta la suite de pruebas en un contenedor aislado
	$(COMPOSE) --profile testing run --rm tests

lint: ## Verifica el estilo de código con Pint
	$(COMPOSE) --profile testing run --rm tests vendor/bin/pint --test

pint: ## Corrige automáticamente el estilo de código
	$(COMPOSE) --profile testing run --rm tests vendor/bin/pint
