<?php


/**
 * ================================================
 * Local sync - Save json file
 * ================================================
 */

add_filter( 'acf/settings/save_json', 'my_acf_json_save_point' );

/**
 * Write field-group JSON into the theme instead of ACF's default location.
 *
 * @param string $path ACF's default save path.
 * @return string The theme's acf_json directory.
 */
function my_acf_json_save_point( $path ) {

	$path = get_stylesheet_directory() . '/acf_json';

	return $path;
}

/**
 * ================================================
 * Local Sync - Load json file on theme load.
 * ================================================
 */

add_filter( 'acf/settings/load_json', 'my_acf_json_load_point' );

/**
 * Load field-group JSON from the theme, replacing ACF's default path.
 *
 * @param array $paths ACF's default load paths.
 * @return array Paths with ACF's own directory swapped for the theme's acf_json.
 */
function my_acf_json_load_point( $paths ) {

	unset( $paths[0] );

	$paths[] = get_stylesheet_directory() . '/acf_json';

	return $paths;
}

/**
 * ================================================
 * Production is read-only for field definitions.
 * ================================================
 *
 * Field groups are version-controlled via Local JSON (acf_json/) and edited in
 * dev, then committed. Hiding the ACF admin editor in production keeps the DB
 * from drifting ahead of the committed JSON (ACF gives no signal when it does,
 * and Local JSON wins at runtime, so a prod-only edit would be silently lost).
 * Field *values* stay fully editable — this only hides the field-group editor.
 */

if ( function_exists( 'wp_get_environment_type' ) && wp_get_environment_type() === 'production' ) {
	add_filter( 'acf/settings/show_admin', '__return_false' );
}
