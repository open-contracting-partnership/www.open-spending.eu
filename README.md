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
| `make db` | create and load the `coalition_wp` database (`FORCE=1` reloads), rewrite the site URL to localhost, and disable production-only plugins |
| `make wp` | extract files into a working directory, patch `wp-config.php`, and symlink this directory as the theme |
| `make serve` | start PHP's built-in server (`php -S`) at http://localhost:8090, with OPcache off so file edits take effect immediately |
| `make flush` | drop cached rewrite rules |
| `make clean` | drop the `coalition_wp` database and remove the working directory |
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

## Contributing

One-time setup:

```bash
composer install
pre-commit install
```
