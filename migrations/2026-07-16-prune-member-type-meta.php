<?php

/**
 * One-time cleanup: remove the legacy `members_type_of_member` ACF meta, left
 * orphaned after member classification moved to the native type_of_member
 * taxonomy (see 2026-07-16-member-type-taxonomy.php).
 *
 * GUARDED: only prunes a member that already has a type_of_member term, i.e.
 * the backfill has run for it. A member without a term is left untouched, so
 * running this before/without the backfill can never destroy the source data.
 * Idempotent.
 *
 * Run AFTER the taxonomy backfill is confirmed:
 *   make migrate   |   wp eval-file migrations/2026-07-16-prune-member-type-meta.php
 */

if (! function_exists('wp_get_object_terms')) {
	fwrite(STDERR, "Run inside WordPress (make migrate, or wp eval-file).\n");
	return;
}

$members = get_posts([
	'post_type'      => 'member',
	'post_status'    => 'any',
	'posts_per_page' => -1,
	'fields'         => 'ids',
]);

$pruned = 0;
$kept = 0;

foreach ($members as $member_id) {
	$terms = wp_get_object_terms($member_id, 'type_of_member', ['fields' => 'ids']);

	if (is_wp_error($terms) || empty($terms)) {
		$kept++; // not migrated yet — leave the source meta in place
		continue;
	}

	if (metadata_exists('post', $member_id, 'members_type_of_member')) {
		delete_post_meta($member_id, 'members_type_of_member');
		delete_post_meta($member_id, '_members_type_of_member');
		$pruned++;
	}
}

printf("members_type_of_member meta pruned: %d, kept (unmigrated): %d\n", $pruned, $kept); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CLI counters.
