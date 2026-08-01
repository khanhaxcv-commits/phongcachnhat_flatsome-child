<?php

/**
 * Theme asset loader bootstrap.
 */

defined('ABSPATH') || exit;

$theme_asset_modules = array(
    'external.php',
    'fonts.php',
    'fontawesome.php',
    'styles.php',
    'scripts.php',
);

foreach ($theme_asset_modules as $theme_asset_module) {
    $theme_asset_module_file = __DIR__ . '/' . $theme_asset_module;

    if (file_exists($theme_asset_module_file)) {
        require_once $theme_asset_module_file;
    }
}
