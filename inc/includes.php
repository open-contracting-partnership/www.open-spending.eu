<?php
/**
 * Load the theme's PHP modules.
 *
 * Order matters: post types and helper functions must exist before the ACF block
 * registrations and query filters that reference them.
 *
 * @package OpenSpendingCoalition
 */

require_once __DIR__ . '/acf-conf.php';

require_once __DIR__ . '/custom-posttype.php';

require_once __DIR__ . '/custom-function.php';

require_once __DIR__ . '/acf-register-block-type.php';

require_once __DIR__ . '/pre-get-posts.php';

require_once __DIR__ . '/post-grids.php';

require_once __DIR__ . '/seo.php';

require_once __DIR__ . '/analytics.php';

require_once __DIR__ . '/acf-options.php';
