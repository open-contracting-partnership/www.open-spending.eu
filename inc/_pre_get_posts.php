<?php
/**
 * Main-query adjustments: per-archive page sizes, and campaign ordering.
 *
 * @package OpenSpendingCoalition
 */

/**
 * Set posts_per_page per archive type: members are unpaginated (the archive
 * renders every member into two tabbed columns), other post-type archives show
 * 12, and search results 9.
 *
 * @param WP_Query $query The query being prepared.
 */
function filter_main_query( $query ) {
	if ( $query->is_main_query() && is_post_type_archive( 'member' ) ) {
		$post_per_page = -1;
		$query->set( 'posts_per_page', $post_per_page );
	} elseif ( $query->is_main_query() && is_post_type_archive() ) {
		$post_per_page = 12;
		$query->set( 'posts_per_page', $post_per_page );
	} elseif ( is_search() ) {
		$post_per_page = 9;
		$query->set( 'posts_per_page', $post_per_page );
	}
}
add_action( 'pre_get_posts', 'filter_main_query', 11 );

/**
 * Order campaigns by their manual menu_order on the front end. The order is
 * stored in wp_posts.menu_order (a core column); the campaign post type gets
 * `page-attributes` support so editors keep the "Order" field to change it.
 * Applies to every front-end campaign query (the archive and the home page's
 * Campaigns section).
 *
 * @param WP_Query $query The query being prepared.
 */
function order_campaigns_by_menu_order( $query ) {
	if ( is_admin() ) {
		return;
	}

	$post_type   = $query->get( 'post_type' );
	$is_campaign = ( 'campaign' === $post_type )
		|| ( is_array( $post_type ) && in_array( 'campaign', $post_type, true ) )
		|| $query->is_post_type_archive( 'campaign' );

	if ( $is_campaign ) {
		$query->set( 'orderby', 'menu_order' );
		$query->set( 'order', 'ASC' );
	}
}
add_action( 'pre_get_posts', 'order_campaigns_by_menu_order' );
