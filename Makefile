# ============================================================================
#  Local development server for the Open Spending Coalition theme.
#
#  Runs the CURRENT branch's theme inside a copy of the production WordPress
#  (files + database), with all the gotchas we hit baked in:
#    * MySQL 8.0 in Docker (matches prod collation)              -> db-* targets
#    * wp-config pointed local + WP_HOME/SITEURL forced local    -> wp-config
#    * page-cache / security / analytics plugins disabled        -> db-config
#    * theme served via a symlink to THIS checkout               -> wp-link
#    * php -S router that serves symlinked assets (CSS/JS!)      -> dev/router.php
#    * OPcache OFF (else stale includes across a theme swap)      -> serve
#    * rewrite rules regenerated per theme                       -> flush
#    * asset build on Node 20 w/ pinned webpack                  -> build
#
#  Quick start:   make up          (then open http://localhost:8090)
#  Prereqs:       Docker, and host PHP 8.x with mysqli/gd/mbstring (for `serve`).
#
#  Run `make help` for the full target list.
# ============================================================================

SHELL       := bash
.SHELLFLAGS := -eu -o pipefail -c
.DEFAULT_GOAL := help

# ---- Config (override on the command line, e.g. `make up PORT=8080`) -------
THEME_SLUG   ?= www.open-spending.eu
PORT         ?= 8090
DB_PORT      ?= 3307
DB_NAME      ?= coalition_wp
DB_CONTAINER ?= coalition-mysql
MYSQL_IMAGE  ?= mysql:8.0
NODE_IMAGE   ?= node:20-bullseye

REPO         := $(CURDIR)
WORKDIR      ?= $(HOME)/.cache/coalition-devserver
WP_ROOT      := $(WORKDIR)/public_html
LOG          := $(WORKDIR)/php.log
BASE_URL     := http://localhost:$(PORT)

# Production backup artifacts. Auto-detected in the repo root; override with
# `make up TAR=/path/to.tar DUMP=/path/to.sql`.
BACKUP_DIR   ?= $(REPO)
TAR          ?= $(shell ls -t $(BACKUP_DIR)/*public_html*backup*.tar 2>/dev/null | head -1)
DUMP         ?= $(shell ls -t $(BACKUP_DIR)/*coalition_wp*.sql 2>/dev/null | head -1)

MYSQL        = docker exec -i $(DB_CONTAINER) mysql -uroot -proot
PLUGINS_KEEP = ["acf-extended/acf-extended.php","advanced-custom-fields-pro/acf.php","fluentform/fluentform.php","safe-svg/safe-svg.php","seo-by-rank-math/rank-math.php","simple-custom-post-order/simple-custom-post-order.php"]

# ============================================================================

.PHONY: help
help: ## Show this help
	@echo "Open Spending Coalition — local dev server"
	@echo
	@grep -hE '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) \
		| awk 'BEGIN{FS=":.*?## "}{printf "  \033[36m%-14s\033[0m %s\n", $$1, $$2}'
	@echo
	@echo "  Web:  $(BASE_URL)      DB: 127.0.0.1:$(DB_PORT) (root/root, db=$(DB_NAME))"
	@echo "  Work: $(WORKDIR)"

.PHONY: up
up: prepare flush ## Bring everything up and start the server (foreground)
	@$(MAKE) --no-print-directory serve

.PHONY: prepare
prepare: db wp ## Set up the DB and WordPress files (no server)
	@echo ">> ready. Run 'make serve' (or 'make up')."

# ---- Server ----------------------------------------------------------------

.PHONY: serve
serve: ## Start the PHP server (foreground; Ctrl-C to stop)
	@command -v php >/dev/null || { echo "host PHP not found (needed for the server)"; exit 1; }
	@test -f "$(WP_ROOT)/router.php" || { echo "not prepared — run 'make prepare' first"; exit 1; }
	@mkdir -p "$(WORKDIR)"
	@echo ">> serving $(THEME_SLUG) @ $(BASE_URL)  (OPcache off; Ctrl-C to stop)"
	@php -d display_errors=0 -d error_reporting=E_ALL \
		-d opcache.enable=0 -d opcache.enable_cli=0 \
		-S localhost:$(PORT) -t "$(WP_ROOT)" "$(WP_ROOT)/router.php" 2>&1 | tee "$(LOG)"

.PHONY: flush
flush: ## Regenerate rewrite rules for the active theme (fixes CPT archives)
	@$(MYSQL) $(DB_NAME) -e "DELETE FROM wp_options WHERE option_name='rewrite_rules';" 2>/dev/null || true
	@curl -s -o /dev/null "$(BASE_URL)/" 2>/dev/null || true
	@echo ">> rewrite rules flushed"

# ---- Database --------------------------------------------------------------

.PHONY: db
db: db-up db-load db-config ## Create the DB container, load the dump, configure it

.PHONY: db-up
db-up: ## Start (or create) the MySQL 8.0 container and wait until ready
	@if docker ps --format '{{.Names}}' | grep -qx '$(DB_CONTAINER)'; then \
		echo ">> $(DB_CONTAINER) already running"; \
	elif docker ps -a --format '{{.Names}}' | grep -qx '$(DB_CONTAINER)'; then \
		echo ">> starting existing $(DB_CONTAINER)"; docker start $(DB_CONTAINER) >/dev/null; \
	else \
		echo ">> creating $(DB_CONTAINER) (mysql:8.0) on port $(DB_PORT)"; \
		docker run -d --name $(DB_CONTAINER) \
			-e MYSQL_ROOT_PASSWORD=root -e MYSQL_DATABASE=$(DB_NAME) \
			-p $(DB_PORT):3306 $(MYSQL_IMAGE) \
			--character-set-server=utf8mb4 --collation-server=utf8mb4_0900_ai_ci >/dev/null; \
	fi
	@printf ">> waiting for MySQL"; \
	for i in $$(seq 1 60); do \
		if docker exec $(DB_CONTAINER) mysqladmin ping -uroot -proot --silent >/dev/null 2>&1; then echo " ready"; exit 0; fi; \
		printf "."; sleep 1; \
	done; echo " TIMED OUT"; exit 1

.PHONY: db-load
db-load: ## Import the SQL dump (skips if already loaded; FORCE=1 to reimport)
	@test -n "$(DUMP)" && test -f "$(DUMP)" || { echo "no SQL dump found — set DUMP=/path/to.sql"; exit 1; }
	@CT=$$($(MYSQL) -N -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='$(DB_NAME)';" 2>/dev/null || echo 0); \
	if [ "$$CT" -gt 0 ] && [ -z "$(FORCE)" ]; then \
		echo ">> DB already loaded ($$CT tables); use 'make db-load FORCE=1' to reimport"; \
	else \
		echo ">> importing $$(basename "$(DUMP)") ..."; \
		$(MYSQL) < "$(DUMP)"; \
		echo ">> imported"; \
	fi

.PHONY: db-config
db-config: ## Point site URL at localhost and disable cache/security plugins
	@$(MYSQL) $(DB_NAME) -e "UPDATE wp_options SET option_value='$(BASE_URL)' WHERE option_name IN ('siteurl','home');"
	@SER=$$(php -r 'echo serialize(json_decode($$argv[1], true));' '$(PLUGINS_KEEP)'); \
	printf "UPDATE wp_options SET option_value='%s' WHERE option_name='active_plugins';" "$$SER" | $(MYSQL) $(DB_NAME)
	@echo ">> DB configured (url=$(BASE_URL); heavy plugins disabled)"

.PHONY: db-shell
db-shell: ## Open a MySQL shell on the dev database
	@docker exec -it $(DB_CONTAINER) mysql -uroot -proot $(DB_NAME)

# ---- WordPress files -------------------------------------------------------

.PHONY: wp
wp: wp-extract wp-config wp-link ## Extract WP, patch config, symlink the theme

.PHONY: wp-extract
wp-extract: ## Extract the production files backup into the work dir
	@if [ -f "$(WP_ROOT)/wp-load.php" ]; then \
		echo ">> WordPress already extracted at $(WP_ROOT)"; \
	else \
		test -n "$(TAR)" && test -f "$(TAR)" || { echo "no files backup found — set TAR=/path/to.tar"; exit 1; }; \
		echo ">> extracting $$(basename "$(TAR)") ..."; \
		mkdir -p "$(WORKDIR)"; \
		tar -xf "$(TAR)" -C "$(WORKDIR)" --strip-components=2; \
		test -f "$(WP_ROOT)/wp-load.php" || { echo "extraction did not yield public_html/ — check the tar layout"; exit 1; }; \
		echo ">> extracted to $(WP_ROOT)"; \
	fi

.PHONY: wp-config
wp-config: ## Patch wp-config, neutralize drop-ins, install the router
	@php "$(REPO)/dev/patch-wp-config.php" "$(WP_ROOT)/wp-config.php" "127.0.0.1:$(DB_PORT)" root root "$(BASE_URL)"
	@# neutralize the page-cache drop-in and the auto-update mu-plugin
	@[ -f "$(WP_ROOT)/wp-content/advanced-cache.php" ] && mv -f "$(WP_ROOT)/wp-content/advanced-cache.php" "$(WP_ROOT)/wp-content/advanced-cache.php.disabled" || true
	@[ -f "$(WP_ROOT)/wp-content/mu-plugins/opencontracting_auto_update_plugin.php" ] && mv -f "$(WP_ROOT)/wp-content/mu-plugins/opencontracting_auto_update_plugin.php" "$(WP_ROOT)/wp-content/mu-plugins/opencontracting_auto_update_plugin.php.disabled" || true
	@cp "$(REPO)/dev/router.php" "$(WP_ROOT)/router.php"
	@echo ">> wp-config patched, drop-ins disabled, router installed"

.PHONY: wp-link
wp-link: ## Symlink the WP theme dir to THIS checkout (current branch)
	@# Remove the extracted production theme dir first, else `ln` would create
	@# the symlink INSIDE it and WordPress would keep serving the extracted copy.
	@rm -rf "$(WP_ROOT)/wp-content/themes/$(THEME_SLUG)"
	@ln -s "$(REPO)" "$(WP_ROOT)/wp-content/themes/$(THEME_SLUG)"
	@echo ">> theme '$(THEME_SLUG)' -> $(REPO)"

# ---- Assets ----------------------------------------------------------------

.PHONY: build
build: ## Compile SCSS/JS into dist/ (Node 20 + pinned webpack, via Docker)
	@bash "$(REPO)/dev/build.sh"

.PHONY: migrate
migrate: ## Run one-time data migrations (migrations/*.php) against the dev DB
	@shopt -s nullglob; files=("$(REPO)"/migrations/*.php); \
	[ $${#files[@]} -gt 0 ] || { echo "no migrations"; exit 0; }; \
	for f in "$${files[@]}"; do \
		echo ">> $$(basename "$$f")"; \
		php -d display_errors=1 -d error_reporting=E_ALL \
			-r "define('WP_USE_THEMES', false); require '$(WP_ROOT)/wp-load.php'; require '$$f';"; \
	done

# ---- Lifecycle -------------------------------------------------------------

.PHONY: open
open: ## Open the site in a browser
	@(command -v open >/dev/null && open "$(BASE_URL)") || (command -v xdg-open >/dev/null && xdg-open "$(BASE_URL)") || echo "$(BASE_URL)"

.PHONY: logs
logs: ## Tail the PHP server log
	@touch "$(LOG)"; tail -f "$(LOG)"

.PHONY: status
status: ## Show container / server / theme state
	@echo "DB container : $$(docker ps --filter name=$(DB_CONTAINER) --format '{{.Status}}' 2>/dev/null || echo not-running)"
	@echo "Web server   : $$(lsof -nP -iTCP:$(PORT) -sTCP:LISTEN >/dev/null 2>&1 && echo "listening on $(PORT)" || echo "not running")"
	@echo "Theme link   : $$(readlink "$(WP_ROOT)/wp-content/themes/$(THEME_SLUG)" 2>/dev/null || echo none)"
	@echo "Work dir     : $(WORKDIR)"

.PHONY: stop
stop: ## Stop the web server and the DB container (keeps data)
	@lsof -nP -iTCP:$(PORT) -sTCP:LISTEN -t 2>/dev/null | xargs kill 2>/dev/null || true
	@docker stop $(DB_CONTAINER) >/dev/null 2>&1 || true
	@echo ">> stopped (data preserved; 'make db-up' to resume)"

.PHONY: down
down: ## Stop the server and remove the DB container (data lost)
	@lsof -nP -iTCP:$(PORT) -sTCP:LISTEN -t 2>/dev/null | xargs kill 2>/dev/null || true
	@docker rm -f $(DB_CONTAINER) >/dev/null 2>&1 || true
	@echo ">> server stopped, DB container removed"

.PHONY: clean
clean: down ## Remove the DB container AND the extracted WordPress work dir
	@rm -rf "$(WORKDIR)"
	@echo ">> removed $(WORKDIR)"
