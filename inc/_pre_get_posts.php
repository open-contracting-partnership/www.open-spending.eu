<?php


// set posts_per_page for post

function filter_main_query( $query ) {
	if ( $query->is_main_query() && is_post_type_archive( 'member' ) ) {
		// post per pagination
		$post_per_page = -1;
		$query->set( 'posts_per_page', $post_per_page );
	} elseif ( $query->is_main_query() && is_post_type_archive() ) {
		// post per pagination
		$post_per_page = 12;
		$query->set( 'posts_per_page', $post_per_page );
	} elseif ( is_search() ) {
		// post per pagination
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
