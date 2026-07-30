<?php

/**
 * Fathom Analytics
 *
 * You can override the domain and site ID in wp-config.php:
 *   define('FATHOM_DOMAIN', 'fathom.example.org');
 *   define('FATHOM_SITE_ID', 'LNRZMMVR');
 */

add_action('wp_head', function () {
	if ( is_user_logged_in() && array_intersect( array( 'administrator', 'editor' ), (array) wp_get_current_user()->roles ) ) {
		return;
	}

	$domain  = defined( 'FATHOM_DOMAIN' ) ? FATHOM_DOMAIN : 'cdn.usefathom.com';
	$site_id = defined( 'FATHOM_SITE_ID' ) ? FATHOM_SITE_ID : 'LNRZMMVR';

	echo '<script src="' . esc_url( 'https://' . $domain . '/script.js' ) . '" data-site="' . esc_attr( $site_id ) . '" defer data-excluded-domains="localhost,127.0.0.1,0.0.0.0"></script>' . "\n"; // phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript -- house analytics snippet.
});
