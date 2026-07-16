<?php

/**
 * One-time cleanup: remove orphaned ACF Extended "Dynamic Block Type" records
 * (post_type `acfe-dbt`).
 *
 * The theme registers its blocks with acf_register_block_type (ACF Pro core),
 * not ACFE dynamic blocks. The only acfe-dbt record ("block-general") is unused
 * (no content references acf/block-general), so it's pure residue once ACF
 * Extended is removed. Queries wp_posts directly so it works whether or not the
 * (ACFE-registered) post type is still active. Idempotent.
 *
 * make migrate   |   wp eval-file migrations/2026-07-16-delete-orphan-acfe-block.php
 */

if (! function_exists('wp_delete_post')) {
	fwrite(STDERR, "Run inside WordPress (make migrate, or wp eval-file).\n");
	return;
}

global $wpdb;

$ids = $wpdb->get_col( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- one-off migration; the post type may be unregistered.
	$wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE post_type = %s", 'acfe-dbt')
);

$deleted = 0;
foreach ($ids as $id) {
	if (wp_delete_post((int) $id, true)) {
		$deleted++;
	}
}

printf("orphan acfe-dbt records removed: %d\n", $deleted); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CLI counter.
