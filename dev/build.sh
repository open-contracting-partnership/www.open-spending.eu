#!/usr/bin/env bash
#
# Build the theme's compiled assets (dist/css/app.css, dist/js/app.js).
#
# The toolchain is rotted: there is no committed lockfile, so webpack floats to
# a version too new for Laravel Mix 6, and the host runs Node 26 which Mix 6
# cannot use. This script works around both by building inside a Node 20
# container with webpack pinned (and de-duplicated via `resolutions`) to a
# version compatible with Mix 6, using the OpenSSL legacy provider.
#
# It never leaves package.json modified (restored on exit via trap).
set -euo pipefail

REPO="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
NODE_IMAGE="${NODE_IMAGE:-node:20-bullseye}"
cd "$REPO"

command -v docker >/dev/null || { echo "docker is required" >&2; exit 1; }

BAK="$(mktemp)"
cp package.json "$BAK"
restore() { cp "$BAK" package.json; rm -f "$BAK"; }
trap restore EXIT

# Pin webpack/webpack-cli (compatible with Mix 6) as direct deps AND
# resolutions so only ONE copy of webpack ends up in node_modules — otherwise
# Mix fails with "compilation argument must be an instance of Compilation".
node -e '
  const fs = require("fs");
  const p = JSON.parse(fs.readFileSync("package.json", "utf8"));
  const pin = { "webpack": "5.74.0", "webpack-cli": "4.10.0", "@babel/preset-env": "^7.23.0" };
  p.devDependencies = Object.assign({}, p.devDependencies, pin);
  p.resolutions = Object.assign({}, p.resolutions, { "webpack": "5.74.0", "webpack-cli": "4.10.0" });
  fs.writeFileSync("package.json", JSON.stringify(p, null, 2) + "\n");
'

# A stale node_modules (with a different webpack) breaks the pin, so start clean
# unless the pinned webpack is already installed.
if [ ! -x node_modules/.bin/mix ] || ! grep -q '"5.74.0"' node_modules/webpack/package.json 2>/dev/null; then
  echo ">> installing dependencies (clean)…"
  rm -rf node_modules yarn.lock
fi

docker run --rm -v "$REPO":/app -w /app "$NODE_IMAGE" bash -lc '
  set -e
  export PATH=/app/node_modules/.bin:$PATH
  [ -x node_modules/.bin/mix ] || yarn install
  echo ">> building (mix --production)…"
  NODE_OPTIONS=--openssl-legacy-provider mix --production
'

echo ">> built:"
ls -l dist/css/app.css dist/js/app.js
