<?php
/**
 * Idempotently rewrite a WordPress wp-config.php for local development.
 *
 * Usage:
 *   php patch-wp-config.php <wp-config-path> <db-host> <db-user> <db-pass> <site-url>
 *
 * - Points the DB constants at the local MySQL.
 * - Forces WP_HOME / WP_SITEURL to the local URL so WordPress never redirects
 *   to the production domain (these are defined before the originals, and take
 *   precedence over the siteurl/home options in the database).
 * - Disables WP_CACHE so the page-cache drop-in is not loaded.
 * Safe to run repeatedly (guarded by a marker comment).
 */
$path    = $argv[1] ?? '';
$dbHost  = $argv[2] ?? '127.0.0.1';
$dbUser  = $argv[3] ?? 'root';
$dbPass  = $argv[4] ?? 'root';
$siteUrl = $argv[5] ?? 'http://localhost:8090';

if (!$path || !is_file($path)) {
    fwrite(STDERR, "wp-config not found: $path\n");
    exit(1);
}

$src = file_get_contents($path);

// Repoint DB constants (whatever their current values).
$src = preg_replace("/define\(\s*'DB_HOST'\s*,\s*'[^']*'\s*\)/",     "define('DB_HOST', '$dbHost')",     $src);
$src = preg_replace("/define\(\s*'DB_USER'\s*,\s*'[^']*'\s*\)/",     "define('DB_USER', '$dbUser')",     $src);
$src = preg_replace("/define\(\s*'DB_PASSWORD'\s*,\s*'[^']*'\s*\)/", "define('DB_PASSWORD', '$dbPass')", $src);

// Disable the page-cache drop-in.
$src = preg_replace("/define\(\s*'WP_CACHE'\s*,\s*(?:true|false)\s*\)/i", "define('WP_CACHE', false)", $src);

$marker = '/* devserver-overrides */';
if (strpos($src, $marker) === false) {
    $override = "\n$marker\n"
        . "define('WP_HOME', '$siteUrl');\n"
        . "define('WP_SITEURL', '$siteUrl');\n"
        . "if (!defined('WP_ENVIRONMENT_TYPE')) { define('WP_ENVIRONMENT_TYPE', 'local'); }\n"
        . "if (!defined('WP_CACHE')) { define('WP_CACHE', false); }\n"
        . "/* end devserver-overrides */\n";
    $src = preg_replace('/^<\?php\s*\n/', "<?php\n$override", $src, 1);
}

file_put_contents($path, $src);
echo "patched " . $path . "\n";
