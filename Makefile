# Local dev server — see README.md.

SHELL         := bash
.SHELLFLAGS   := -eu -o pipefail -c
.DEFAULT_GOAL := help

THEME   := www.open-spending.eu
DB      ?= coalition_wp
DISABLE := ["wp-cloudflare-page-cache/wp-cloudflare-super-page-cache.php","wordfence/wordfence.php"]

PORT  ?= 8090
URL   := http://localhost:$(PORT)

REPO    := $(CURDIR)
WORKDIR ?= $(HOME)/.cache/$(THEME)
WP      := $(WORKDIR)/public_html

TAR    ?= $(shell ls -t $(REPO)/*public_html*backup*.tar 2>/dev/null | head -1)
DUMP   ?= $(shell ls -t $(REPO)/*$(DB)*.sql 2>/dev/null | head -1)
SOCKET := $(or $(shell mysql -uroot -N -sse "SELECT @@socket" 2>/dev/null),/tmp/mysql.sock)
MYSQL  := mysql -uroot

.PHONY: help up setup db wp serve flush clean

help: ## list the available commands (runs by default)
	@grep -hE '^[a-z-]+:.*##' $(MAKEFILE_LIST) | sed -E 's/:[^#]*## /\t/'
	@echo "  serves $(URL), db=$(DB)"

up: setup ## setup and serve
	@$(MAKE) --no-print-directory serve

setup: db wp ## db and wp

db: ## create and load the coalition_wp database (FORCE=1 reloads), rewrite the site URL to localhost, disable production-only plugins
	@test -f "$(DUMP)" || { echo "no SQL dump — set DUMP=/path/to.sql"; exit 1; }
	@$(MYSQL) -e 'CREATE DATABASE IF NOT EXISTS `$(DB)`;'
	@if [ -z "$(FORCE)" ] && [ "$$($(MYSQL) -N -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='$(DB)'")" -gt 0 ]; then \
		echo ">> DB already loaded (FORCE=1 to reimport)"; \
	else \
		echo ">> importing $$(basename "$(DUMP)")"; $(MYSQL) "$(DB)" < "$(DUMP)"; \
	fi
	@$(MYSQL) "$(DB)" -e "UPDATE wp_options SET option_value='$(URL)' WHERE option_name IN ('siteurl','home'); DELETE FROM wp_options WHERE option_name='rewrite_rules';"
	@active="$$($(MYSQL) "$(DB)" -N -e "SELECT option_value FROM wp_options WHERE option_name='active_plugins'")"; \
	printf "UPDATE wp_options SET option_value='%s' WHERE option_name='active_plugins';" \
		"$$(php -r 'echo serialize(array_values(array_diff(unserialize($$argv[1]) ?: [], json_decode($$argv[2], true))));' "$$active" '$(DISABLE)')" \
		| $(MYSQL) "$(DB)"
	@echo ">> DB ready (url=$(URL), dev plugins disabled)"

wp: ## extract files into a working directory, patch wp-config.php, symlink this directory as the theme
	@if [ ! -f "$(WP)/wp-load.php" ]; then \
		test -f "$(TAR)" || { echo "no files backup — set TAR=/path/to.tar"; exit 1; }; \
		echo ">> extracting $$(basename "$(TAR)")"; mkdir -p "$(WORKDIR)"; \
		tar -xf "$(TAR)" -C "$(WORKDIR)" --strip-components=2; \
	fi
	@sed -i.bak -E \
		-e "s#define\( *'DB_HOST'[^;]*;#define('DB_HOST', 'localhost:$(SOCKET)');#" \
		-e "s#define\( *'DB_USER'[^;]*;#define('DB_USER', 'root');#" \
		-e "s#define\( *'DB_PASSWORD'[^;]*;#define('DB_PASSWORD', '');#" \
		-e "s#define\( *'WP_CACHE'[^;]*;#define('WP_CACHE', false);#" \
		"$(WP)/wp-config.php" && rm -f "$(WP)/wp-config.php.bak"
	@for f in wp-content/advanced-cache.php wp-content/mu-plugins/opencontracting-auto-update-plugin.php; do \
		[ -f "$(WP)/$$f" ] && mv -f "$(WP)/$$f" "$(WP)/$$f.disabled" || true; \
	done
	@rm -rf "$(WP)/wp-content/themes/$(THEME)" && ln -s "$(REPO)" "$(WP)/wp-content/themes/$(THEME)"
	@echo ">> WordPress ready, theme -> this checkout"

serve: ## start PHP's built-in server (php -S), with OPcache off so file edits take effect immediately
	@test -f "$(WP)/wp-load.php" || { echo "run 'make setup' first"; exit 1; }
	@echo ">> $(URL)  (Ctrl-C to stop)"
	@WP_ENVIRONMENT_TYPE=local php -d opcache.enable=0 -S localhost:$(PORT) -t "$(WP)"

flush: ## drop cached rewrite rules
	@$(MYSQL) "$(DB)" -e "DELETE FROM wp_options WHERE option_name='rewrite_rules';"
	@curl -s -o /dev/null "$(URL)/" || true
	@echo ">> flushed"

clean: ## drop the coalition_wp database and remove the working directory
	@rm -rf "$(WORKDIR)"
	@$(MYSQL) -e 'DROP DATABASE IF EXISTS `$(DB)`;'
	@echo ">> cleaned"
