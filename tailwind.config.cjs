/** @type {import('tailwindcss').Config} */
module.exports = {
  // Every file that can name a utility class. Omitting one silently purges the
  // classes only that file uses, so this covers all of the theme's markup:
  // archive.php and single.php at the root, and the block templates.
  content: [
    "./*.php",
    "./acf_block/*.php",
    "./archives/*.php",
    "./inc/*.php",
    "./patterns/*.php",
    "./singles/*.php",
    "./parts/*.html",
    "./templates/*.html",
    "./src/sass/**/*.scss",
  ],
  // src/js is deliberately not scanned: `!container.contains(...)` in app.js reads
  // as Tailwind's important-modifier syntax and generates a junk .\!container
  // rule. `hidden` is the only utility the JS toggles by name, so safelist it —
  // it is currently also present in archives/member.php, but this keeps the
  // members tabs working if that markup ever changes.
  safelist: ["hidden"],
  theme: {
    colors: {
      white: "#fff",
      teal: "#1D6FA3",
      s: {
        0: "#CBDEFF",
        10: "#275DF3",
        20: "#275DF3",
        30: "#0036CC",
        40: "#002899",
        50: "#0A2266",
      },
      d: {
        0: "#FFE600",
        10: "#FFE600",
        20: "#FFE600",
        30: "#DDCB27",
        40: "#9B8C03",
        50: "#776D11",
      },
      m: {
        0: "#6BBE67",
        10: "#6BBE67",
        20: "#6BBE67",
        30: "#569953",
        40: "#396637",
        50: "#2B4D29",
      },
      b: {
        0: "#FFCBCB",
        10: "#EE5C4F",
        20: "#EA3323",
        30: "#CC2C1F",
        40: "#992117",
        50: "#66160F",
      },
      t: {
        0: "#FF9F69",
        10: "#FF8744",
        20: "#FF8744",
        30: "#CC6C36",
        40: "#995129",
        50: "#66361B",
      },
      n: {
        0: "#ffffff",
        10: "#f4f4f5",
        20: "#e9e9ea",
        30: "#d3d4d5",
        40: "#bdbec0",
        50: "#a7a9ab",
        60: "#7c7d82",
        70: "#66686d",
        80: "#505258",
        90: "#3a3d43",
        100: "#24272e",
      },
    },
    extend: {
      fontSize: {
        "heading-1": "48px",
        "heading-2": "32px",
        "heading-3": "24px",
        "heading-4": "18px",
        "heading-5": "16px",
        "heading-6": "12px",
      },
    },
  },
  plugins: [],
};
