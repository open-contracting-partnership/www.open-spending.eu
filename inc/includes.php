<?php
/**
 * Load the theme's PHP modules.
 *
 * Order matters: post types and helper functions must exist before the ACF block
 * registrations and query filters that reference them.
 *
 * @package OpenSpendingCoalition
 */

require_once __DIR__ . '/_acf_conf.php';

require_once __DIR__ . '/_custom_posttype.php';

require_once __DIR__ . '/_custom_function.php';

require_once __DIR__ . '/_acf_register_block_type.php';

require_once __DIR__ . '/_pre_get_posts.php';

require_once __DIR__ . '/_archive_helpers.php';

require_once __DIR__ . '/_seo.php';

require_once __DIR__ . '/_analytics.php';

require_once __DIR__ . '/_acf_options.php';
