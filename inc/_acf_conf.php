<?php


/**
 * ================================================
 * Local sync - Save json file 
 * ================================================
 */

add_filter('acf/settings/save_json', 'my_acf_json_save_point');

function my_acf_json_save_point($path)
{

    $path = get_stylesheet_directory() . '/acf_json';

    return $path;
}

/**
 * ================================================
 * Local Sync - Load json file on theme load.
 * ================================================
 */

add_filter('acf/settings/load_json', 'my_acf_json_load_point');

function my_acf_json_load_point($paths)
{

    unset($paths[0]);

    $paths[] = get_stylesheet_directory() . '/acf_json';

    return $paths;
}
