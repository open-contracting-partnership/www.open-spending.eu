<?php

/**
 * Functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package FSE
 * @since 1.0.0
 */

// theme set up
if (!function_exists('theme_setup')) {
	function theme_setup()
	{

		// manage document title
		add_theme_support('title-tag');

		// Post Thumbnails on posts and pages.
		add_theme_support('post-thumbnails');

		// Add support for Block Styles.
		add_theme_support('wp-block-styles');

		// Add support for full and wide align images.
		add_theme_support('align-wide');

		// Add support for editor styles.
		add_theme_support('editor-styles');

		// Add support for responsive embedded content.
		add_theme_support('responsive-embeds');

		// allows the use of HTML5 markup for the 
		// search forms, comment forms, comment lists, gallery, and caption
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

	add_action('after_setup_theme', 'theme_setup');
}


/**
 * Enqueue the style.css file.
 * 
 * @since 1.0.0
 */
// theme's scripts and styles for frontend
function theme_scripts()
{
	// Version the built assets by a content hash so a rebuild cache-busts, while
	// the URL stays identical across deploys/servers when the bytes are unchanged.
	$css = get_stylesheet_directory() . '/dist/css/app.css';
	$js  = get_stylesheet_directory() . '/dist/js/app.js';

	wp_enqueue_style(
		'fse-style',
		get_stylesheet_directory_uri() . '/dist/css/app.css',
		array(),
		file_exists($css) ? hash_file('crc32b', $css) : null
	);

	wp_enqueue_script(
		'main-script',
		get_stylesheet_directory_uri() . '/dist/js/app.js',
		array('jquery'),
		file_exists($js) ? hash_file('crc32b', $js) : null,
		true
	);

	wp_enqueue_script(
		'slick-slider',
		get_stylesheet_directory_uri() . '/dist/js/slick.min.js',
		['jquery'],
		false,
		true
	);

	wp_enqueue_script(
		'isotope-js',
		get_stylesheet_directory_uri() . '/dist/js/isotope.pkgd.min.js',
		['jquery'],
		false,
		true
	);
}
add_action('wp_enqueue_scripts', 'theme_scripts');

/**
 * ===================================================
 * theme's styles for editor block
 * ===================================================
 */

add_editor_style('dist/css/app.css');

add_action('init', function () {
	// Same content-hash version as the main enqueue, so this shares one cached
	// app.css URL instead of loading it a second time under a different ?ver.
	$app_css = get_stylesheet_directory() . '/dist/css/app.css';
	wp_register_style('awp-block-styles', get_stylesheet_directory_uri() . '/dist/css/app.css', false, file_exists($app_css) ? hash_file('crc32b', $app_css) : null);
	register_block_style('core/heading', [
		'name' => 'colored-bottom-border',
		'label' => __('Colored bottom border', 'openspendingcoalition'),
		'style_handle' => 'awp-block-styles'
	]);
});

/**
 * include custom functions
 * 
 */
require_once dirname(__FILE__) . '/inc/includes.php';
