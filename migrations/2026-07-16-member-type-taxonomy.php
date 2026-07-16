<?php

/**
 * One-time data migration: populate the `type_of_member` taxonomy from the
 * legacy ACF field (`members_type_of_member`), so member classification no
 * longer depends on ACF Extended's `acfe_taxonomy_terms` field.
 *
 * The ACF field stored the term id in post meta but never wrote the taxonomy
 * relationship; this backfills it. Idempotent — safe to run more than once.
 *
 * Run locally:      make migrate
 * Run on the server: wp eval-file migrations/2026-07-16-member-type-taxonomy.php
 */

if (! function_exists('wp_set_object_terms')) {
	fwrite(STDERR, "Run inside WordPress (make migrate, or wp eval-file).\n");
	return;
}

$members = get_posts([
	'post_type'      => 'member',
	'post_status'    => 'any',
	'posts_per_page' => -1,
	'fields'         => 'ids',
]);

$set = 0;
$skipped = 0;

foreach ($members as $member_id) {
	$term_id = (int) get_post_meta($member_id, 'members_type_of_member', true);

	if (! $term_id || ! term_exists($term_id, 'type_of_member')) {
		$skipped++;
		continue;
	}

	$current = array_map('intval', (array) wp_get_object_terms($member_id, 'type_of_member', ['fields' => 'ids']));
	if ([$term_id] === $current) {
		$skipped++; // already migrated
		continue;
	}

	wp_set_object_terms($member_id, [$term_id], 'type_of_member', false);
	$set++;
}

// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Numeric counters printed to the CLI.
printf("type_of_member backfill: set %d, skipped %d, of %d members\n", $set, $skipped, count($members));
