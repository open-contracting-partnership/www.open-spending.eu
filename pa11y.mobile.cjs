const base = require("./pa11y.default.cjs");

module.exports = {
  ...base,
  defaults: {
    ...base.defaults,
    viewport: {
      width: 320,
      height: 480,
      deviceScaleFactor: 2,
      isMobile: true,
    },
  },
};
