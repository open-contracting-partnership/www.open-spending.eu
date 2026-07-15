# Visual verification — comparing a branch against `main`

How to prove that a refactor branch renders the **same output** as `main`
(production), and to classify every difference as *identical*, *benign*,
*intended*, or *regression*.

This is the process used to validate the code-review branch. It renders both
theme versions against the **same** production database and uploads, then
compares the rendered HTML structurally and the screenshots pixel-by-pixel.

## Prerequisites

- The local dev server (`Makefile` / `dev/README.md`) — Docker + host PHP.
- A git worktree (or checkout) of the baseline, e.g. `main`.
- **ImageMagick** (`compare`, `magick`) for pixel diffs.
- **Google Chrome** (headless) for screenshots.

Set up a `main` worktree once:

```bash
git worktree add /tmp/tree-main main
```

## Why this environment matters (gotchas that silently invalidate results)

These each produced *false* results before they were fixed — check them first:

| Trap | Symptom | Fix |
|---|---|---|
| Theme CSS/JS not served | Everything looks broken/unstyled; huge pixel diffs | `dev/router.php` serves symlinked assets (built into `make serve`) |
| OPcache on across a theme swap | Random `500`s / stale `includes.php` after switching | `make serve` runs with OPcache **off** |
| Stale rewrite rules after swap | CPT archives `301` to the wrong place | `make flush` after every theme swap |
| Browser-cached `301` | A now-`200` page still redirects in screenshots | Use a **fresh** Chrome `--user-data-dir` per run |
| `dist/` not rebuilt | Branch's SCSS/JS changes don't show | Rebuild both sides, or note `dist/` is committed & matches live |
| Animated hero `<canvas>` | The hero band differs on *every* render (~1% RMSE) | Expected noise — `#heroCanvas` uses `Math.random()`; confirm via highlighted diff that only the particle layer differs |

## Method

1. **Capture** every target page on each side (HTML + full-page screenshot),
   swapping the served theme with `make wp-link REPO=<path>` + `make flush`.
2. **Compare** HTML structurally (normalized diff + element census) and
   screenshots perceptually (RMSE + highlighted diff).
3. **Scan** the server log for PHP errors during the branch render.
4. **Interpret** each surviving difference against the categories below.

### Target pages

Home, each CPT archive, a representative single per CPT, and the standalone
pages — the surfaces the theme actually drives:

```
home                  /
arch-news             /news/
arch-evidence         /evidence/
arch-campaign         /campaign/
arch-member           /member/
arch-toolkit          /toolkit/
arch-bestpractices    /best_practices/
single-news           /news/1030-2/
single-evidence       /evidence/corruption-risk-forecast/
single-campaign       /campaign/beneficial-ownership/
single-toolkit        /toolkit/corruption-cost-tracker/
single-bestpractices  /best_practices/access-to-information-course/
single-member         /member/access-info-europe-es/
page-about            /about-the-organization/
page-contact          /contact-us/
page-join             /join-us/
page-resources        /resources/
```

Save this as `urls.txt` (`<name> <path>` per line).

## Step 1 — bring the environment up

```bash
make up          # or: make prepare && make serve &   (leave it running)
```

## Step 2 — capture both sides

For each side, point the theme at it, flush, then fetch HTML and screenshot:

```bash
BASE=http://localhost:8090
CHROME="/Applications/Google Chrome.app/Contents/MacOS/Google Chrome"

capture () {            # $1 = side name   $2 = theme path
  make wp-link REPO="$2" >/dev/null && make flush >/dev/null
  mkdir -p out/$1/html out/$1/png
  while read -r name path; do
    curl -s -o "out/$1/html/$name.html" -w "$name %{http_code}\n" "$BASE$path"
    "$CHROME" --headless=new --disable-gpu --hide-scrollbars --disable-extensions \
      --user-data-dir=/tmp/verify-chrome --window-size=1440,2200 \
      --virtual-time-budget=9000 --screenshot="out/$1/png/$name.png" "$BASE$path" \
      >/dev/null 2>&1
  done < urls.txt
}

rm -rf out /tmp/verify-chrome
capture main   /tmp/tree-main
capture branch "$PWD"
make wp-link && make flush        # leave the server on the branch
```

`--virtual-time-budget` makes headless Chrome render on a fixed clock instead of
hanging on external resources (fonts/analytics).

## Step 3 — compare

### HTTP status + PHP errors

```bash
paste out/main/status.txt out/branch/status.txt      # any non-200?
grep -iE "Fatal error|Uncaught|PHP (Warning|Notice|Deprecated)" ~/.cache/coalition-devserver/php.log
```

### Structural HTML diff

Normalize per-request noise (nonces, `?ver=`, form-instance ids, the hero
`<style>` block) before diffing. Save as `normalize.pl`:

```perl
#!/usr/bin/perl
use strict; use warnings; local $/; my $h = <>;
$h =~ s/\?ver=[^"'&\s>]*/?ver=X/g;                          # asset cache-busters
$h =~ s/(name="[^"]*nonce[^"]*"\s+value=")[^"]*"/$1X"/gi;   # nonces
$h =~ s/("_?nonce"\s*:\s*")[^"]*"/$1X"/gi;
$h =~ s/(nonce=)[A-Za-z0-9]{6,}/${1}X/gi;
$h =~ s/ff_form_instance_\d+_\d+/ff_form_instance_X/g;      # FluentForm ids
$h =~ s/(id="ff_)\d+(_\d+)?/${1}X/g;
$h =~ s/<style>\s*header \{.*?<\/style>\s*//gs;             # hero bg <style>
print $h;
```

A raw normalized diff still flags cosmetic-only changes (whitespace, relative vs
absolute URLs, improved `alt` text, block-comment stripping). Apply a second
**canonicalization** to drop those and surface only *unexplained* differences:

```bash
canon () { perl -0777 -pe '
  s/\shref="https?:\/\/localhost:8090\//  href="\//g;   # abs -> rel host
  s/\ssrc="https?:\/\/localhost:8090\//  src="\//g;
  s/alt="[^"]*"/alt="A"/g;                               # ignore alt text
  s/<!--\s*\/?wp:[a-z-]+\s*(\{[^}]*\})?\s*-->//g;         # drop block delimiters
  s/ class="wp-block-paragraph"//g;
  s/[ \t]+/ /g; s/\n\s*\n/\n/g; s/^\s+//mg; s/\s+$//mg;'; }

for f in out/main/html/*.html; do n=$(basename "$f" .html)
  a=$(perl normalize.pl < "$f" | canon)
  b=$(perl normalize.pl < "out/branch/html/$n.html" | canon)
  echo "== $n =="; diff <(echo "$a") <(echo "$b")
done
```

**Element census** — catch dropped/added content as count mismatches:

```bash
census () { perl -0777 -ne 'my %c; for my $t (qw(a img h1 h2 h3 h4 section li form button svg input)){my $n=()=/<$t\b/g;$c{$t}=$n} print join(",",map{"$_=$c{$_}"}qw(a img h1 h2 h3 h4 section li form button svg input)),"\n"' "$1"; }
for f in out/main/html/*.html; do n=$(basename "$f" .html)
  [ "$(census "$f")" = "$(census out/branch/html/$n.html)" ] && echo "$n OK" || echo "$n DIFF"
done
```

> Note: the census regex counts `<a`/`<svg` inside HTML comments too, so removing
> commented-out markup shows up as a census delta — verify before alarming.

### Pixel diff

Normalized RMSE (`0` = identical); localize with a highlighted diff image:

```bash
for f in out/main/png/*.png; do n=$(basename "$f" .png); b=out/branch/png/$n.png
  dm=$(magick identify -format '%wx%h' "$f"); db=$(magick identify -format '%wx%h' "$b")
  if [ "$dm" != "$db" ]; then echo "$n SIZE $dm/$db"; continue; fi
  r=$(compare -metric RMSE "$f" "$b" null: 2>&1 | grep -oE '\([0-9.]+\)' | tr -d '()')
  echo "$n RMSE=$r"
done
# localize a specific page's differences (red = differs):
compare -highlight-color red out/main/png/page-contact.png out/branch/png/page-contact.png diff.png
```

Rules of thumb: `RMSE < 0.006` ≈ identical (sub-pixel AA); `> 0.02` warrants a
look. A vertical content shift lights up everything below it (footer/related
grid) — that's an offset, not a per-element change; confirm with the highlighted
diff.

## Step 4 — interpret

Classify every surviving difference:

- **Identical** — RMSE ~0, no structural diff. ✅
- **Benign refactor** — visually equal: whitespace, relative→absolute URLs,
  improved `alt` text, `echo $content` → `the_content()` (adds
  `wp-block-paragraph`, nofollow on external links), removed commented-out code,
  fixed invalid nesting (`<p>` in `<h4>`, nested `<a>`). ✅
- **Intended change** — a real, wanted difference (e.g. a placeholder archive
  replaced by a real one, a hardcoded field). Confirm it renders correctly. ✅
- **Regression** — content dropped, a link broken, text made unreadable, a page
  erroring. ❌ Fix it.

Always view the actual screenshots for any `DIFF`/high-RMSE page — the metrics
point you at *where* to look, not *whether* it's a problem.

## Result of the last run (via `make`, both sides with CSS)

- All 17 pages `200` on both sides.
- **Zero fatal errors.** The branch logged **0 PHP warnings**; `main` logged 10
  — the branch's PHP 8.5 hardening cleaned them up.
- Pixel differences were localized (via banded + highlighted diffs) to:
  - the animated hero `<canvas>` — per-render noise on every page's hero band;
  - `arch-toolkit` — placeholder → real filterable archive (**intended**);
  - `single-news` — a small block-spacing shift from `the_content()` (benign);
  - `page-contact` — social icons `<img>`→`<svg>`, confined to the info panel
    (benign); header/footer pixel-identical.
- `arch-member` differs in **HTML only**: a bug fix (per-card `data-id` was
  previously all-identical) plus removed commented-out markup — rendered output
  identical (RMSE ≈ 0.005).

**No regressions.** The branch is visually equivalent to `main` and cleaner at
the PHP level.
