<?php

/**
 * Fathom Analytics — cookieless, privacy-first page analytics.
 *
 * Replaces the fathom-analytics plugin, which only injected this one snippet.
 *
 * Domain and site id can be overridden with constants in wp-config.php,
 * mirroring the fathom.domain / fathom.id config used on other projects:
 *   define('FATHOM_DOMAIN', 'fathom.example.org');   // custom domain (adblock-resistant)
 *   define('FATHOM_SITE_ID', 'LNRZMMVR');
 */

add_action('wp_head', function () {
	// Don't count logged-in staff — matches the plugin's excluded roles. WordPress
	// has logged-in users the static house snippet doesn't; remove to drop this.
	if (is_user_logged_in() && array_intersect(['administrator', 'editor'], (array) wp_get_current_user()->roles)) {
		return;
	}

	$domain  = defined('FATHOM_DOMAIN') ? FATHOM_DOMAIN : 'cdn.usefathom.com';
	$site_id = defined('FATHOM_SITE_ID') ? FATHOM_SITE_ID : 'LNRZMMVR';

	// A third-party analytics tag with custom data-* attributes — kept as the shared house snippet.
	echo '<script src="' . esc_url('https://' . $domain . '/script.js') . '" data-site="' . esc_attr($site_id) . '" defer data-excluded-domains="localhost,127.0.0.1,0.0.0.0"></script>' . "\n"; // phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript -- house analytics snippet.
});
