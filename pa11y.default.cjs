const strategy = process.env.PA11Y_STRATEGY;
const includeWarnings = "PA11Y_INCLUDE_WARNINGS" in process.env;
const suppressKnownWarnings = "PA11Y_SUPPRESS_KNOWN_WARNINGS" in process.env;

const knownErrors = [
  {
    // "This form does not contain a submit button." The header search block has no button by
    // design, and Enter submits it. The archive filters submit on change, and their button is
    // inside a noscript element, which HTML CodeSniffer doesn't look inside.
    // https://www.w3.org/WAI/WCAG21/Techniques/html/H32
    rules: ["WCAG2AA.Principle3.Guideline3_2.3_2_2.H32.2"],
    selectors: ["header form[role='search']", "#filter-form"],
  },
  {
    // "This element has insufficient contrast at this conformance level" (reported as a 1:1
    // ratio). The white text sits on a cover block's photograph, which HTML CodeSniffer treats
    // as the page's white background. Measured against the painted pixels, the worst case is
    // 6.6:1 in the footer, 6.6:1 on Join Us and 4.4:1 (32px, so 3:1 applies) on About Us.
    // https://www.w3.org/WAI/WCAG21/Techniques/general/G18
    rules: ["WCAG2AA.Principle1.Guideline1_4.1_4_3.G18.Fail", "WCAG2AA.Principle1.Guideline1_4.1_4_3.G145.Fail"],
    selectors: [".footer .has-base-color", ".join-us-image-text", ".about-image-text"],
  },
];

const knownWarnings = [
  {
    // "This element's text is placed on a background image", "This element is absolutely
    // positioned and the background color can not be determined", and axe's "needs review"
    // contrast reports. Every one is a background the runner can't resolve rather than a
    // measured failure, and they land on too many elements to enumerate.
    // https://www.w3.org/WAI/WCAG21/Techniques/general/G18
    rules: [
      "WCAG2AA.Principle1.Guideline1_4.1_4_3.G18.BgImage",
      "WCAG2AA.Principle1.Guideline1_4.1_4_3.G18.Abs",
      "WCAG2AA.Principle1.Guideline1_4.1_4_3.G145.BgImage",
      "WCAG2AA.Principle1.Guideline1_4.1_4_3.G145.Abs",
      "WCAG2AA.Principle1.Guideline1_4.1_4_3_F24.F24.FGColour",
      "color-contrast",
    ],
    selectors: [],
  },
  {
    // "Img element is marked so that it is ignored by Assistive Technology." Fires on every
    // empty alt, which is right for a decorative image, so it can't be narrowed to the images
    // that are wrong. It therefore also hides a content image that needs alt text: review
    // those when publishing, since nothing here will report them.
    // https://www.w3.org/WAI/WCAG21/Techniques/html/H67
    rules: ["WCAG2AA.Principle1.Guideline1_1.1_1_1.H67.2"],
    selectors: [],
  },
  {
    // "If this element contains a navigation section, it is recommended that it be marked up as
    // a list." Advisory, and reported for card grids and button rows that aren't navigation.
    // https://www.w3.org/WAI/WCAG21/Techniques/html/H48
    rules: ["WCAG2AA.Principle1.Guideline1_3.1_3_1.H48"],
    selectors: [],
  },
  {
    // "Heading markup should be used if this content is intended as a heading." Reported for
    // the italic pull-quotes in post content, which are emphasis rather than headings.
    // https://www.w3.org/WAI/WCAG21/Techniques/html/H42
    rules: ["WCAG2AA.Principle1.Guideline1_3.1_3_1.H42"],
    selectors: [],
  },
  {
    // "If this selection list contains groups of related options, they should be grouped with
    // optgroup", and "This select element does not have a value available to an accessibility
    // API." Advisory: the country filter is one flat list, and its first option is a prompt.
    // https://www.w3.org/WAI/WCAG21/Techniques/html/H85
    rules: ["WCAG2AA.Principle1.Guideline1_3.1_3_1.H85.2", "WCAG2AA.Principle4.Guideline4_1.4_1_2.H91.Select.Value"],
    selectors: ["#country-filter"],
  },
  {
    // "Check that this applet or plugin provides the ability to move the focus away from itself
    // when using the keyboard." The browser's built-in PDF viewer, embedded by a core file
    // block, which does let the keyboard leave it.
    // https://www.w3.org/WAI/WCAG21/Techniques/failures/F10
    rules: ["WCAG2AA.Principle2.Guideline2_1.2_1_2.F10"],
    selectors: [".wp-block-file__embed"],
  },
];

function createDefaults(extraKnownWarnings = []) {
  const suppressions = [
    ...knownErrors,
    ...(includeWarnings && suppressKnownWarnings ? [...knownWarnings, ...extraKnownWarnings] : []),
  ];

  const withoutSelectors = suppressions.filter((suppression) => !suppression.selectors.length);
  const withSelectors = suppressions.filter((suppression) => suppression.selectors.length);

  const hideElements =
    strategy === "hideElements" ? withSelectors.flatMap((suppression) => suppression.selectors) : [];
  const ignore = [
    ...withoutSelectors.flatMap((suppression) => suppression.rules),
    ...(strategy === "ignore" ? withSelectors.flatMap((suppression) => suppression.rules) : []),
  ];

  return {
    runners: ["htmlcs", "axe"],
    levelCapWhenNeedsReview: "warning",
    includeWarnings: includeWarnings,
    ...(hideElements.length ? { hideElements: hideElements.join(", ") } : {}),
    ...(ignore.length ? { ignore: ignore } : {}),
  };
}

module.exports = {
  createDefaults,
  defaults: createDefaults(),
  // The sitemap omits these templates.
  urls: [
    // archives/member.php, and single.php's fallback (members have no singles/ partial)
    "http://localhost:8090/member/",
    "http://localhost:8090/member/transparency-international-hungary/",
    // inc/post-grids.php pagination: news is the only archive with enough posts (12 per page)
    "http://localhost:8090/news/page/2/",
    // templates/search.html
    "http://localhost:8090/?s=budget",
    // templates/404.html and patterns/hidden-404.php
    "http://localhost:8090/no-such-page/",
  ],
};
