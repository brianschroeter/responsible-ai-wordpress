.PHONY: help up down restart logs shell wp db-shell backup restore fresh clean status

# Colors for output
BLUE := \033[0;34m
GREEN := \033[0;32m
YELLOW := \033[0;33m
RED := \033[0;31m
NC := \033[0m # No Color

help: ## Show this help message
	@echo "$(BLUE)Responsible AI WordPress Docker Environment$(NC)"
	@echo ""
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "$(GREEN)%-15s$(NC) %s\n", $$1, $$2}'

up: ## Start all containers
	@echo "$(BLUE)Starting containers...$(NC)"
	docker-compose up -d
	@echo "$(GREEN)Containers started!$(NC)"
	@echo "WordPress: http://localhost:8080"
	@echo "phpMyAdmin: http://localhost:8081"
	@echo "Mailhog: http://localhost:8025"

down: ## Stop all containers
	@echo "$(YELLOW)Stopping containers...$(NC)"
	docker-compose down
	@echo "$(GREEN)Containers stopped!$(NC)"

restart: down up ## Restart all containers

logs: ## Show logs from all containers
	docker-compose logs -f

logs-wp: ## Show WordPress logs only
	docker-compose logs -f wordpress

logs-db: ## Show database logs only
	docker-compose logs -f db

shell: ## Open bash shell in WordPress container
	docker-compose exec wordpress bash

wp: ## Run WP-CLI command (usage: make wp cmd="plugin list")
	docker-compose run --rm --profile cli wp-cli wp $(cmd)

db-shell: ## Open MySQL shell
	docker-compose exec db mysql -u$(shell grep DB_USER .env | cut -d '=' -f2) -p$(shell grep DB_PASSWORD .env | cut -d '=' -f2) $(shell grep DB_NAME .env | cut -d '=' -f2)

backup: ## Backup database to ./backups directory
	@echo "$(BLUE)Creating database backup...$(NC)"
	@mkdir -p backups
	docker-compose exec -T db mysqldump -u$(shell grep DB_USER .env | cut -d '=' -f2) -p$(shell grep DB_PASSWORD .env | cut -d '=' -f2) $(shell grep DB_NAME .env | cut -d '=' -f2) > backups/backup_$(shell date +%Y%m%d_%H%M%S).sql
	@echo "$(GREEN)Backup created in ./backups directory$(NC)"

restore: ## Restore database from file (usage: make restore file=backups/backup_20240101_120000.sql)
	@echo "$(YELLOW)Restoring database from $(file)...$(NC)"
	docker-compose exec -T db mysql -u$(shell grep DB_USER .env | cut -d '=' -f2) -p$(shell grep DB_PASSWORD .env | cut -d '=' -f2) $(shell grep DB_NAME .env | cut -d '=' -f2) < $(file)
	@echo "$(GREEN)Database restored!$(NC)"

fresh: down ## Fresh install - removes all volumes and starts clean
	@echo "$(RED)WARNING: This will delete all data!$(NC)"
	@echo "Press Ctrl+C to cancel, or Enter to continue..."
	@read confirm
	docker-compose down -v
	rm -rf wp-content/themes wp-content/plugins wp-content/uploads
	mkdir -p wp-content/themes/responsibleai wp-content/plugins wp-content/uploads
	docker-compose up -d
	@echo "$(GREEN)Fresh environment created!$(NC)"

clean: down ## Remove all containers, volumes, and generated files
	@echo "$(RED)Removing all containers, volumes, and data...$(NC)"
	docker-compose down -v
	docker system prune -f
	@echo "$(GREEN)Environment cleaned!$(NC)"

status: ## Show status of all containers
	@echo "$(BLUE)Container Status:$(NC)"
	docker-compose ps

build: ## Rebuild containers
	@echo "$(BLUE)Building containers...$(NC)"
	docker-compose build --no-cache
	@echo "$(GREEN)Build complete!$(NC)"

install: up ## Install WordPress via WP-CLI
	@echo "$(BLUE)Installing WordPress...$(NC)"
	@sleep 10
	docker-compose run --rm --profile cli wp-cli wp core install \
		--url="$(shell grep SITE_URL .env | cut -d '=' -f2)" \
		--title="$(shell grep SITE_TITLE .env | cut -d '=' -f2)" \
		--admin_user="$(shell grep ADMIN_USER .env | cut -d '=' -f2)" \
		--admin_password="$(shell grep ADMIN_PASSWORD .env | cut -d '=' -f2)" \
		--admin_email="$(shell grep ADMIN_EMAIL .env | cut -d '=' -f2)"
	@echo "$(GREEN)WordPress installed!$(NC)"

update: ## Update WordPress core and all plugins
	docker-compose run --rm --profile cli wp-cli wp core update
	docker-compose run --rm --profile cli wp-cli wp plugin update --all
	docker-compose run --rm --profile cli wp-cli wp theme update --all
	@echo "$(GREEN)Updates complete!$(NC)"
