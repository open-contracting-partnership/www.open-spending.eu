# www.open-spending.eu WordPress theme

## Prerequisites

- **PHP 8.x**
- **MySQL** (`mysql -uroot`, no password)
- Two production backups in the repository root (auto-detected):
  - `*_public_html_backup_*.tar` files (override with `TAR=`)
  - `*_coalition_wp*.sql` database (override with `DUMP=`)

## Tasks

| Command | Description |
|---|---|
| `make up` | `setup` and `serve` |
| `make setup` | `db` and `wp` |
| `make db` | create and load the `coalition_wp` database (`FORCE=1` to re-load), rewrite the site URL to localhost, and disable production-only plugins |
| `make wp` | extract files into a working directory (`FORCE=1` to re-extract), patch `wp-config.php`, and symlink this directory as the theme |
| `make serve` | start PHP's built-in server (`php -S`) at http://localhost:8090, with OPcache off so file edits take effect immediately |
| `make flush` | drop cached rewrite rules |
| `make clean` | drop the `coalition_wp` database and remove the working directory |
| `make diff` | diff the built assets against git (`REF=HEAD`), pretty-printing them so the change is readable |
| `make help` | list the available commands (runs by default) |

> [!TIP]
> Run `make flush` if a custom post type or taxonomy archive returns a 404: for example, after switching git branches or changing its registration.

## Build assets

Install dependencies:

```bash
pnpm install --frozen-lockfile --ignore-scripts
```

For development:

```bash
node build.js --watch
```

For production:

```bash
env NODE_ENV=production node build.js
```

> [!IMPORTANT]
> After changing `src/`, always build assets for production and commit `dist/`.

`git diff` on the minified assets is one unreadable line. Run `make diff` to review the change instead: it pretty-prints `dist/js/app.js` and `dist/css/app.css` with `esbuild` before diffing, on both sides. Pass `REF=` to compare against something other than `HEAD`:

```bash
make diff REF=origin/main
```

## Accessibility

Install Chrome:

```bash
pnpm exec puppeteer browsers install chrome
```

Build assets for production, run `make serve`, then run [Pa11y](https://ocp-software-handbook.readthedocs.io/en/latest/python/a11y.html) using `http://localhost:8090/wp-sitemap.xml` as the sitemap.

> [!NOTE]
> There is no `a11y.yml` workflow, because serving the theme needs the two production backups. Run the checks locally before a release.

## Contributing

One-time setup:

```bash
composer install
pre-commit install
```
