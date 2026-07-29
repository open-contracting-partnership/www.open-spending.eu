import path from "node:path";
import { fileURLToPath } from "node:url";

import autoprefixer from "autoprefixer";
import browserslist from "browserslist";
import * as esbuild from "esbuild";
import { esbuildPluginBrowserslist } from "esbuild-plugin-browserslist";
import { sassPlugin } from "esbuild-sass-plugin";
import postcss from "postcss";
import tailwindcss from "tailwindcss";

const production = process.env.NODE_ENV === "production";

const root = path.dirname(fileURLToPath(import.meta.url));

const options = {
  entryPoints: {
    "css/app": "src/sass/app.scss",
    "js/app": "src/js/app.js",
  },
  bundle: true,
  outdir: "dist",
  minify: production,
  sourcemap: !production,
  legalComments: "linked",
  logLevel: "info",
  // The "dataurl" loader inlines the icons the SCSS references by url(), which costs about
  // 1.6kB gzipped over serving them as files and saves eleven requests. The alternative,
  // "file", copies each one to outdir under a content-hashed name, but dist/ is committed and
  // WordPress serves it as-is, so those copies would be duplicate — and, once an image
  // changes, stale — binaries in git. The originals stay where PHP also reads them.
  loader: {
    ".svg": "dataurl",
  },
  alias: {
    icons: path.join(root, "dist/images/icons"),
  },
  plugins: [
    esbuildPluginBrowserslist(browserslist(), { printUnknownTargets: false }),
    sassPlugin({
      // The stylesheets predate the @use migration.
      silenceDeprecations: ["import"],
      quietDeps: true,
      async transform(source) {
        const { css } = await postcss([
          tailwindcss(path.join(root, "tailwind.config.cjs")),
          autoprefixer,
        ]).process(source, { from: undefined });
        return css;
      },
    }),
  ],
};

if (process.argv.includes("--watch")) {
  const context = await esbuild.context(options);
  await context.watch();
  console.log("Watching for changes …");
} else {
  await esbuild.build(options);
}
