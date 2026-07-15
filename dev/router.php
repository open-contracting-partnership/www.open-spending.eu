<?php
/**
 * Router for PHP's built-in server (`php -S`) to run WordPress locally.
 *
 * Why this exists: the local theme is a symlink pointing OUTSIDE the WP docroot
 * (to this git checkout), and the built-in server refuses to serve static files
 * whose real path is outside the docroot — so CSS/JS/images fall through to
 * WordPress and 301 away. This router serves any existing static asset directly
 * (following the symlink) and routes everything else through WordPress.
 */
$root = __DIR__;
$path = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

$file = realpath($root . $path);
if ($path !== '/' && $file && is_file($file)) {
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    $mimes = [
        'css'   => 'text/css',
        'js'    => 'application/javascript',
        'mjs'   => 'application/javascript',
        'svg'   => 'image/svg+xml',
        'png'   => 'image/png',
        'jpg'   => 'image/jpeg',
        'jpeg'  => 'image/jpeg',
        'gif'   => 'image/gif',
        'webp'  => 'image/webp',
        'avif'  => 'image/avif',
        'ico'   => 'image/x-icon',
        'woff'  => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf'   => 'font/ttf',
        'otf'   => 'font/otf',
        'eot'   => 'application/vnd.ms-fontobject',
        'json'  => 'application/json',
        'map'   => 'application/json',
        'mp4'   => 'video/mp4',
        'webm'  => 'video/webm',
        'txt'   => 'text/plain',
        'pdf'   => 'application/pdf',
    ];
    if (isset($mimes[$ext])) {
        header('Content-Type: ' . $mimes[$ext]);
        header('Content-Length: ' . filesize($file));
        header('Cache-Control: no-cache');
        readfile($file);
        return true;
    }
    return false; // let the built-in server handle other in-docroot files
}

$_SERVER['SCRIPT_NAME']     = '/index.php';
$_SERVER['SCRIPT_FILENAME'] = $root . '/index.php';
require $root . '/index.php';
