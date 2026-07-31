<?php
/**
 * Functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package OpenSpendingCoalition
 * @since 1.0.0
 */

if ( ! function_exists( 'theme_setup' ) ) {
	/**
	 * Declare the theme's editor and markup support on after_setup_theme.
	 */
	function theme_setup() {

		// Let WordPress render <title>; seo.php filters its parts.
		add_theme_support( 'title-tag' );

		// Post Thumbnails on posts and pages.
		add_theme_support( 'post-thumbnails' );

		// Add support for Block Styles.
		add_theme_support( 'wp-block-styles' );

		// Add support for full and wide align images.
		add_theme_support( 'align-wide' );

		// Add support for editor styles.
		add_theme_support( 'editor-styles' );

		// Add support for responsive embedded content.
		add_theme_support( 'responsive-embeds' );

		// Use HTML5 markup for search forms, comment forms, comment lists,
		// galleries and captions.
		add_theme_support(
			'html5',
			array(
				'navigation-widgets',
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'style',
				'script',
			)
		);
	}

	add_action( 'after_setup_theme', 'theme_setup' );
}


/**
 * Enqueue the style.css file.
 *
 * @since 1.0.0
 */
/**
 * Content hash for a bundled asset, for use as its enqueue version.
 *
 * A rebuild cache-busts, while the URL stays identical across deploys and
 * servers when the bytes are unchanged.
 *
 * @param string $path Path relative to the theme directory, e.g. '/dist/js/app.js'.
 * @return string|null The hash, or null when the file is missing.
 */
function theme_asset_version( $path ) {
	$file = get_stylesheet_directory() . $path;

	return file_exists( $file ) ? hash_file( 'crc32b', $file ) : null;
}

/**
 * Enqueue the theme's front-end styles and scripts.
 */
function theme_scripts() {
	wp_enqueue_style(
		'fse-style',
		get_stylesheet_directory_uri() . '/dist/css/app.css',
		array(),
		theme_asset_version( '/dist/css/app.css' )
	);

	wp_enqueue_script(
		'main-script',
		get_stylesheet_directory_uri() . '/dist/js/app.js',
		array(),
		theme_asset_version( '/dist/js/app.js' ),
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);

	wp_enqueue_script(
		'slick-slider',
		get_stylesheet_directory_uri() . '/dist/js/slick.min.js',
		array( 'jquery' ),
		theme_asset_version( '/dist/js/slick.min.js' ),
		true
	);

	// Slick initialisation (the only jQuery consumer).
	wp_enqueue_script(
		'slider-init',
		get_stylesheet_directory_uri() . '/dist/js/slider.js',
		array( 'slick-slider' ),
		theme_asset_version( '/dist/js/slider.js' ),
		true
	);
}
add_action( 'wp_enqueue_scripts', 'theme_scripts' );

/**
 * Remove jQuery Migrate outside the WordPress Admin.
 */
add_action(
	'wp_default_scripts',
	function ( $scripts ) {
		if ( is_admin() || empty( $scripts->registered['jquery'] ) ) {
			return;
		}
		$scripts->registered['jquery']->deps = array_diff(
			$scripts->registered['jquery']->deps,
			array( 'jquery-migrate' )
		);
	}
);

/**
 * Load jQuery in the footer instead of the <head>.
 */
add_action(
	'wp_enqueue_scripts',
	function () {
		wp_scripts()->add_data( 'jquery', 'group', 1 );
		wp_scripts()->add_data( 'jquery-core', 'group', 1 );
	},
	100
);

/**
 * Preload the hero background so the LCP image starts downloading from the
 * initial HTML, instead of only after the header block's inline <style> parses.
 */
add_action(
	'wp_head',
	function () {
		printf(
			'<link rel="preload" as="image" href="%s" fetchpriority="high">' . "\n",
			esc_url( get_template_directory_uri() . '/dist/images/hero.webp' )
		);
	},
	1
);

/**
 * ===================================================
 * theme's styles for editor block
 * ===================================================
 */

add_editor_style( 'dist/css/app.css' );

/**
 * Load the theme's PHP modules.
 */
require_once __DIR__ . '/inc/includes.php';
