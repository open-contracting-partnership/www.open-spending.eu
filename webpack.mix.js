const mix = require("laravel-mix");
const tailwindcss = require("tailwindcss");
const path = require("path");

mix.setPublicPath(path.normalize("dist"));
mix.setResourceRoot(path.normalize("src"));
mix.setResourceRoot("./../");

if (mix.inProduction()) {
  mix.options({
    terser: {
      terserOptions: {
        compress: {
          drop_console: true,
        },
      },
    },
    cssNano: {
      discardComments: { removeAll: true },
    },
  });
} else {
  mix.webpackConfig({ devtool: "inline-source-map" }).sourceMaps();
}

mix.webpackConfig({
  watchOptions: {
    ignored: /node_modules/,
  },
});

mix.sass("src/sass/app.scss", "dist/css").options({
  postCss: [tailwindcss("tailwind.config.js")],
});

mix.babel(["src/js/hero-animation.js", "src/js/app.js"], "dist/js/app.js");
