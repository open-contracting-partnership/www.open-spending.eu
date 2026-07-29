# Local dev server: run this branch's theme inside a copy of production
# WordPress, using the host's MySQL and PHP.
#
#   make up      set up everything and serve at http://localhost:8090
#   make build   rebuild dist/ (esbuild)
#
# Needs: PHP 8.x (mysqli/gd/mbstring), a running MySQL (root, no password), pnpm.
# Put the production *.sql dump and *public_html*.tar backup in the repo root,
# or pass DUMP=... / TAR=...

SHELL         := bash
.SHELLFLAGS   := -eu -o pipefail -c
.DEFAULT_GOAL := help

THEME := www.open-spending.eu
PORT  ?= 8090
DB    ?= coalition_wp
URL   := http://localhost:$(PORT)

REPO    := $(CURDIR)
WORKDIR ?= $(HOME)/.cache/coalition-devserver
WP      := $(WORKDIR)/public_html

TAR    ?= $(shell ls -t $(REPO)/*public_html*backup*.tar 2>/dev/null | head -1)
DUMP   ?= $(shell ls -t $(REPO)/*coalition_wp*.sql 2>/dev/null | head -1)
SOCKET := $(or $(shell mysql -uroot -N -sse "SELECT @@socket" 2>/dev/null),/tmp/mysql.sock)
MYSQL  := mysql -uroot

# Keep only these plugins active locally (drops the page-cache/security/analytics ones).
KEEP := ["acf-extended/acf-extended.php","advanced-custom-fields-pro/acf.php","fluentform/fluentform.php","safe-svg/safe-svg.php","seo-by-rank-math/rank-math.php","simple-custom-post-order/simple-custom-post-order.php"]

.PHONY: help up setup db wp serve build flush clean

help: ## list targets
	@grep -hE '^[a-z-]+:.*##' $(MAKEFILE_LIST) | sed -E 's/:[^#]*## /\t/' | sort
	@echo "  serves $(URL), db=$(DB)"

up: setup ## set up everything, then serve
	@$(MAKE) --no-print-directory serve

setup: db wp ## load the DB + WordPress files (no server)

db: ## create the dev DB, import the dump, point it at localhost (FORCE=1 reimports)
	@test -f "$(DUMP)" || { echo "no SQL dump — set DUMP=/path/to.sql"; exit 1; }
	@$(MYSQL) -e 'CREATE DATABASE IF NOT EXISTS `$(DB)`;'
	@if [ -z "$(FORCE)" ] && [ "$$($(MYSQL) -N -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='$(DB)'")" -gt 0 ]; then \
		echo ">> DB already loaded (FORCE=1 to reimport)"; \
	else \
		echo ">> importing $$(basename "$(DUMP)")"; $(MYSQL) "$(DB)" < "$(DUMP)"; \
	fi
	@$(MYSQL) "$(DB)" -e "UPDATE wp_options SET option_value='$(URL)' WHERE option_name IN ('siteurl','home'); DELETE FROM wp_options WHERE option_name='rewrite_rules';"
	@printf "UPDATE wp_options SET option_value='%s' WHERE option_name='active_plugins';" \
		"$$(php -r 'echo serialize(json_decode($$argv[1], true));' '$(KEEP)')" | $(MYSQL) "$(DB)"
	@echo ">> DB ready (url=$(URL), heavy plugins off)"

wp: ## extract WordPress files, patch wp-config, symlink this theme
	@if [ ! -f "$(WP)/wp-load.php" ]; then \
		test -f "$(TAR)" || { echo "no files backup — set TAR=/path/to.tar"; exit 1; }; \
		echo ">> extracting $$(basename "$(TAR)")"; mkdir -p "$(WORKDIR)"; \
		tar -xf "$(TAR)" -C "$(WORKDIR)" --strip-components=2; \
	fi
	@php "$(REPO)/dev/patch-wp-config.php" "$(WP)/wp-config.php" "localhost:$(SOCKET)" root "" "$(URL)"
	@for f in wp-content/advanced-cache.php wp-content/mu-plugins/opencontracting_auto_update_plugin.php; do \
		[ -f "$(WP)/$$f" ] && mv -f "$(WP)/$$f" "$(WP)/$$f.disabled" || true; \
	done
	@cp "$(REPO)/dev/router.php" "$(WP)/router.php"
	@rm -rf "$(WP)/wp-content/themes/$(THEME)" && ln -s "$(REPO)" "$(WP)/wp-content/themes/$(THEME)"
	@echo ">> WordPress ready, theme -> this checkout"

serve: ## start the PHP dev server (foreground, OPcache off; Ctrl-C to stop)
	@test -f "$(WP)/router.php" || { echo "run 'make setup' first"; exit 1; }
	@echo ">> $(URL)  (Ctrl-C to stop)"
	@php -d opcache.enable=0 -S localhost:$(PORT) -t "$(WP)" "$(WP)/router.php"

build: ## compile SCSS/JS into dist/ (esbuild)
	@pnpm install --frozen-lockfile --ignore-scripts
	@NODE_ENV=production node "$(REPO)/build.js"

flush: ## drop cached rewrite rules (fixes CPT archive 404s after a theme swap)
	@$(MYSQL) "$(DB)" -e "DELETE FROM wp_options WHERE option_name='rewrite_rules';"
	@curl -s -o /dev/null "$(URL)/" || true
	@echo ">> flushed"

clean: ## remove the WordPress work dir and drop the dev DB
	@rm -rf "$(WORKDIR)"
	@$(MYSQL) -e 'DROP DATABASE IF EXISTS `$(DB)`;'
	@echo ">> cleaned"
