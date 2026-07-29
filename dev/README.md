# Local dev server

Run the **current branch's** theme against a copy of the production WordPress
(files + database), driven by the `Makefile` in the repo root.

```bash
make up        # DB + WordPress + server (http://localhost:8090)
make help      # all targets
```

## Prerequisites

- **Docker** — runs MySQL 8.0 (matches production's collation).
- **Host PHP 8.x** with `mysqli`, `gd`, `mbstring` — runs the site via `php -S`.
- **Node** (latest LTS) and **pnpm** — only for `make build`.
- Two production backup artifacts, placed in the repo root (git-ignored):
  - the files tarball — `*_public_html_backup_*.tar`
  - the DB dump — `*_coalition_wp*.sql`

  Auto-detected; override with `make up TAR=/path/to.tar DUMP=/path/to.sql`.

## What it sets up (the gotchas)

| Concern | Handled by |
|---|---|
| MySQL 8.0 collation | `make db-up` (Docker `mysql:8.0`) |
| DB pointed local; `WP_HOME`/`WP_SITEURL` forced to localhost | `dev/patch-wp-config.php` |
| Cloudflare page-cache / Wordfence / SMTP / Fathom disabled | `make db-config` + drop-in neutralized |
| Theme served from **this checkout** (branch-aware) | `make wp-link` (symlink) |
| `php -S` won't serve symlinked assets → CSS/JS 301'd | `dev/router.php` |
| OPcache serving stale includes across a theme swap | `make serve` (OPcache off) |
| CPT archives 404/redirect until rewrite rules regenerate | `make flush` |

## Common tasks

```bash
make prepare   # DB + files, no server
make serve     # start server (foreground)
make flush     # regenerate rewrite rules for the active theme
make build     # recompile dist/ from src/ (SCSS + JS)
make db-shell  # MySQL prompt
make status    # what's running
make stop      # stop server + DB (keeps data)
make clean     # remove DB container + extracted WordPress
```

Switch branches with plain `git checkout` — the theme is a symlink to this
checkout, so the running server reflects the new branch immediately (run
`make flush` if you change CPT registration).

## Asset build

`make build` compiles `src/` into `dist/`. It wraps the two commands that do
the work, which you can also run directly:

```bash
pnpm install --frozen-lockfile --ignore-scripts
NODE_ENV=production node build.js
```

`build.js` bundles `src/sass/app.scss` and `src/js/app.js` into `dist/` with
esbuild, running Tailwind and autoprefixer over the SCSS via PostCSS. Icons
under 1 kB are inlined as data URIs; larger ones keep a content-hashed query
string. Without `NODE_ENV=production` the output is unminified with
sourcemaps, so **always build with it before committing `dist/`**.

`node build.js --watch` rebuilds on change.

> Note: `dist/` is committed and matches production, so rebuild and commit it
> whenever you change `src/`.
