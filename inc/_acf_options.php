<?php

/**
 * Adds a small "Archive Settings" screen to the WordPress admin for each content
 * type — News, Evidence, Campaigns, Members, Tools, Best Practices. An editor
 * uses it to set the sub-heading shown on that type's listing (archive) page —
 * and, for Members, a custom breadcrumb — without anyone touching code.
 *
 * How it links to the templates: these screens are an ACF Pro feature called
 * "options pages". Each one saves its fields against the id "{type}_options"
 * (e.g. "news_options"), and the archive templates read the values back with the
 * matching get_field('sub_heading', "{type}_options").
 */

add_action('acf/init', function () {
	if (! function_exists('acf_add_options_sub_page')) {
		return;
	}

	$post_types = ['news', 'evidence', 'campaign', 'member', 'toolkit', 'best_practices'];

	foreach ($post_types as $post_type) {
		acf_add_options_sub_page([
			'page_title'  => __('Archive Settings', 'openspendingcoalition'),
			'menu_title'  => __('Archive Settings', 'openspendingcoalition'),
			'parent_slug' => 'edit.php?post_type=' . $post_type,
			'menu_slug'   => $post_type . '-archive-settings',
			'post_id'     => $post_type . '_options',
			'capability'  => 'edit_posts',
		]);
	}
});
