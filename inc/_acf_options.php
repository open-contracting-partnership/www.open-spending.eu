<?php

/**
 * Native ACF Pro options pages for per-CPT archive settings (the archive
 * sub-heading, plus the archive breadcrumb for members).
 *
 * Values are stored in wp_options under `{posttype}_options` (e.g.
 * news_options_sub_heading), which is the `post_id` used here, so the theme's
 * get_field('sub_heading', "{$posttype}_options") reads resolve against them.
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
