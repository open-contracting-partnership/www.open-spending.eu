<?php

function cptui_register_my_cpts()
{
	/**
	 * Post Type: News.
	 */
	$labels = [
		"name" => esc_html__("News", "openspendingcoalition"),
		"singular_name" => esc_html__("News", "openspendingcoalition"),
		"all_items" => esc_html__("All News", "openspendingcoalition"),
		"add_new" => esc_html__("Add News", "openspendingcoalition"),
		"add_new_item" => esc_html__("Add New", "openspendingcoalition"),
		"edit_item" => esc_html__("Edit News", "openspendingcoalition"),
		"new_item" => esc_html__("New News", "openspendingcoalition"),
		"view_item" => esc_html__("View News", "openspendingcoalition"),
		"view_items" => esc_html__("View News", "openspendingcoalition"),
		"search_items" => esc_html__("Search News", "openspendingcoalition"),
	];
	$args = [
		"label" => esc_html__("News", "openspendingcoalition"),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"rest_namespace" => "wp/v2",
		"has_archive" => true,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"can_export" => false,
		"rewrite" => ["slug" => "news", "with_front" => true],
		"query_var" => true,
		"menu_icon" => "dashicons-welcome-widgets-menus",
		"supports" => ["title", "editor", "thumbnail"],
		"show_in_graphql" => false,
	];
	register_post_type("news", $args);

	/**
	 * Post Type: Evidence.
	 */
	$labels = [
		"name" => esc_html__("Evidence", "openspendingcoalition"),
		"singular_name" => esc_html__("Evidence", "openspendingcoalition"),
		"all_items" => esc_html__("All Evidence", "openspendingcoalition"),
		"add_new" => esc_html__("Add Evidence", "openspendingcoalition"),
		"add_new_item" => esc_html__("Add Evidence", "openspendingcoalition"),
		"edit_item" => esc_html__("Edit Evidence", "openspendingcoalition"),
		"new_item" => esc_html__("New Evidence", "openspendingcoalition"),
		"view_item" => esc_html__("View Evidence", "openspendingcoalition"),
		"view_items" => esc_html__("View Evidence", "openspendingcoalition"),
		"search_items" => esc_html__("Search Evidence", "openspendingcoalition"),
	];
	$args = [
		"label" => esc_html__("Evidence", "openspendingcoalition"),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"rest_namespace" => "wp/v2",
		"has_archive" => true,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"can_export" => false,
		"rewrite" => ["slug" => "evidence", "with_front" => true],
		"query_var" => true,
		"menu_icon" => "dashicons-media-document",
		"supports" => ["title", "editor", "thumbnail"],
		"taxonomies" => [],
		"show_in_graphql" => false,
	];
	register_post_type("evidence", $args);

	/**
	 * Taxonomy: Countries.
	 */

	$labels = [
		"name" => esc_html__("Countries", "openspendingcoalition"),
		"singular_name" => esc_html__("Country", "openspendingcoalition"),
	];


	$args = [
		"label" => esc_html__("Countries", "openspendingcoalition"),
		"labels" => $labels,
		"public" => false,
		"publicly_queryable" => false,
		"hierarchical" => true,
		"show_ui" => true,
		"show_in_menu" => true,
		"show_in_nav_menus" => false,
		"query_var" => false,
		"rewrite" => false,
		"show_admin_column" => true,
		"show_in_rest" => true,
		"show_tagcloud" => true,
		"rest_base" => "country",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"rest_namespace" => "wp/v2",
		"show_in_quick_edit" => true,
		"sort" => true,
		"show_in_graphql" => false,
	];
	register_taxonomy("country", ["news", "toolkit", "evidence", "best_practices"], $args);

	/**
	 * Post Type: Campaigns.
	 */
	$labels = [
		"name" => esc_html__("Campaigns", "openspendingcoalition"),
		"singular_name" => esc_html__("Campaign", "openspendingcoalition"),
		"all_items" => esc_html__("All Campaign", "openspendingcoalition"),
		"add_new" => esc_html__("Add Campaign", "openspendingcoalition"),
		"add_new_item" => esc_html__("Add Campaign", "openspendingcoalition"),
		"edit_item" => esc_html__("Edit Campaign", "openspendingcoalition"),
		"new_item" => esc_html__("New Campaign", "openspendingcoalition"),
		"view_item" => esc_html__("View Campaign", "openspendingcoalition"),
		"view_items" => esc_html__("View Campaign", "openspendingcoalition"),
		"search_items" => esc_html__("Search Campaign", "openspendingcoalition"),
	];
	$args = [
		"label" => esc_html__("Campaigns", "openspendingcoalition"),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"rest_namespace" => "wp/v2",
		"has_archive" => true,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"can_export" => false,
		"rewrite" => ["slug" => "campaign", "with_front" => true],
		"query_var" => true,
		"menu_icon" => "dashicons-megaphone",
		"supports" => ["title", "editor", "thumbnail", "page-attributes"],
		"show_in_graphql" => false,
	];
	register_post_type("campaign", $args);
	/**
	 * Post Type: Members.
	 */
	$labels = [
		"name" => esc_html__("Members", "openspendingcoalition"),
		"singular_name" => esc_html__("Member", "openspendingcoalition"),
		"all_items" => esc_html__("All Member", "openspendingcoalition"),
		"add_new" => esc_html__("Add Member", "openspendingcoalition"),
		"add_new_item" => esc_html__("Add Member", "openspendingcoalition"),
		"edit_item" => esc_html__("Edit Member", "openspendingcoalition"),
		"new_item" => esc_html__("New Member", "openspendingcoalition"),
		"view_item" => esc_html__("View Member", "openspendingcoalition"),
		"view_items" => esc_html__("View Member", "openspendingcoalition"),
		"search_items" => esc_html__("Search Member", "openspendingcoalition"),
	];
	$args = [
		"label" => esc_html__("Members", "openspendingcoalition"),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"rest_namespace" => "wp/v2",
		"has_archive" => true,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => true,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"can_export" => false,
		"rewrite" => ["slug" => "member", "with_front" => true],
		"query_var" => true,
		"menu_icon" => "dashicons-groups",
		"supports" => ["title", "editor", "thumbnail"],
		"show_in_graphql" => false,
	];
	register_post_type("member", $args);

	/**
	 * Taxonomy: Countries.
	 */

	$labels = [
		"name" => esc_html__("Type of Members", "openspendingcoalition"),
		"singular_name" => esc_html__("Type of Member", "openspendingcoalition"),
	];


	$args = [
		"label" => esc_html__("Type of Members", "openspendingcoalition"),
		"labels" => $labels,
		"public" => false,
		"publicly_queryable" => false,
		"hierarchical" => true,
		"show_ui" => true,
		"show_in_menu" => true,
		"show_in_nav_menus" => false,
		"query_var" => false,
		"rewrite" => false,
		"show_admin_column" => true,
		"show_in_rest" => true,
		"show_tagcloud" => true,
		"rest_base" => "type_of_member",
		"meta_box_cb" => "member_type_meta_box",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"rest_namespace" => "wp/v2",
		"show_in_quick_edit" => true,
		"sort" => true,
		"show_in_graphql" => false,
	];
	register_taxonomy("type_of_member", ["member"], $args);

	/**
	 * Post Type: Toolkits.
	 */
	$labels = [
		"name" => esc_html__("Tools", "openspendingcoalition"),
		"singular_name" => esc_html__("Tool", "openspendingcoalition"),
		"all_items" => esc_html__("All Tools", "openspendingcoalition"),
		"add_new" => esc_html__("Add Tool", "openspendingcoalition"),
		"add_new_item" => esc_html__("Add New Tool", "openspendingcoalition"),
		"edit_item" => esc_html__("Edit Tool", "openspendingcoalition"),
		"new_item" => esc_html__("New Tool", "openspendingcoalition"),
		"view_item" => esc_html__("View Tool", "openspendingcoalition"),
		"view_items" => esc_html__("View Tools", "openspendingcoalition"),
		"search_items" => esc_html__("Search Tools", "openspendingcoalition"),
	];
	$args = [
		"label" => esc_html__("Tools", "openspendingcoalition"),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"rest_namespace" => "wp/v2",
		"has_archive" => true,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"can_export" => false,
		"rewrite" => ["slug" => "toolkit", "with_front" => true],
		"query_var" => true,
		"menu_icon" => "dashicons-list-view",
		"supports" => ["title", "editor", "thumbnail"],
		"show_in_graphql" => false,
	];
	register_post_type("toolkit", $args);
	/**
	 * Post Type: Best Practices.
	 */
	$labels = [
		"name" => esc_html__("Best Practices", "openspendingcoalition"),
		"singular_name" => esc_html__("Best Practice", "openspendingcoalition"),
		"all_items" => esc_html__("All Best Practice", "openspendingcoalition"),
		"add_new" => esc_html__("Add Best Practice", "openspendingcoalition"),
		"add_new_item" => esc_html__("Add Best Practice", "openspendingcoalition"),
		"edit_item" => esc_html__("Edit Best Practice", "openspendingcoalition"),
		"new_item" => esc_html__("New Best Practice", "openspendingcoalition"),
		"view_item" => esc_html__("View Best Practice", "openspendingcoalition"),
		"view_items" => esc_html__("View Best Practice", "openspendingcoalition"),
		"search_items" => esc_html__("Search Best Practice", "openspendingcoalition"),
	];
	$args = [
		"label" => esc_html__("Best Practices", "openspendingcoalition"),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"rest_namespace" => "wp/v2",
		"has_archive" => true,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"can_export" => false,
		"rewrite" => ["slug" => "best_practices", "with_front" => true],
		"query_var" => true,
		"menu_icon" => "dashicons-edit-page",
		"supports" => ["title", "editor", "thumbnail"],
		"show_in_graphql" => false,
	];
	register_post_type("best_practices", $args);
}
add_action('init', 'cptui_register_my_cpts');

/**
 * Single-select (radio) metabox for the type_of_member taxonomy — a member is
 * exactly one Person or Organization. Core saves the chosen term automatically
 * from the tax_input[] radio fields rendered below.
 */
function member_type_meta_box($post)
{
	$taxonomy = 'type_of_member';
	$terms    = get_terms(["taxonomy" => $taxonomy, "hide_empty" => false]);
	if (is_wp_error($terms) || !$terms) {
		echo '<p>' . esc_html__('No member types defined.', 'openspendingcoalition') . '</p>';
		return;
	}

	$assigned = wp_get_object_terms($post->ID, $taxonomy, ["fields" => "ids"]);
	$current  = (!is_wp_error($assigned) && $assigned) ? (int) $assigned[0] : 0;

	// A leading empty value keeps the field present in POST so a member can be cleared.
	echo '<input type="hidden" name="tax_input[' . esc_attr($taxonomy) . '][]" value="0" />';
	echo '<ul class="category-tabs" style="margin:6px 0;">';
	foreach ($terms as $term) {
		printf(
			'<li><label><input type="radio" name="tax_input[%1$s][]" value="%2$d" %3$s> %4$s</label></li>',
			esc_attr($taxonomy),
			(int) $term->term_id,
			checked($current, (int) $term->term_id, false),
			esc_html($term->name)
		);
	}
	echo '</ul>';
}
