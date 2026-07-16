<?php

/**
 * One-time cleanup: remove the superseded "page_sub_heading" ACF repeater from
 * wp_options.
 *
 * Archive sub-headings are stored per-CPT under {posttype}_options_sub_heading
 * (ACFE Post Type Archive) and read via get_field('sub_heading', "{$pt}_options").
 * The older options_page_sub_heading* repeater is no longer referenced by any
 * template. Idempotent — safe to run more than once.
 *
 * make migrate   |   wp eval-file migrations/2026-07-16-prune-legacy-subheading-options.php
 */

if (! function_exists('delete_option')) {
	fwrite(STDERR, "Run inside WordPress (make migrate, or wp eval-file).\n");
	return;
}

global $wpdb;

// Exact option names (escaped underscores so LIKE matches literally); no user input.
$names = $wpdb->get_col( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- one-off migration, literal query.
	"SELECT option_name FROM {$wpdb->options}
	 WHERE option_name LIKE 'options\\_page\\_sub\\_heading%'
	    OR option_name LIKE '\\_options\\_page\\_sub\\_heading%'"
);

$deleted = 0;
foreach ($names as $name) {
	if (delete_option($name)) {
		$deleted++;
	}
}

printf("legacy page_sub_heading options pruned: %d\n", $deleted); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CLI counter.
