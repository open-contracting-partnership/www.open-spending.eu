import autoprefixer from "autoprefixer";
import browserslist from "browserslist";
import * as esbuild from "esbuild";
import { esbuildPluginBrowserslist } from "esbuild-plugin-browserslist";
import { sassPlugin } from "esbuild-sass-plugin";
import postcss from "postcss";
import tailwindcss from "tailwindcss";

const production = process.env.NODE_ENV === "production";

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
  loader: {
    ".svg": "dataurl",
  },
  alias: {
    icons: "./dist/images/icons",
  },
  plugins: [
    esbuildPluginBrowserslist(browserslist(), { printUnknownTargets: false }),
    sassPlugin({
      silenceDeprecations: ["import"],
      async transform(source) {
        const { css } = await postcss([tailwindcss("tailwind.config.cjs"), autoprefixer]).process(source, {
          from: undefined,
        });
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
