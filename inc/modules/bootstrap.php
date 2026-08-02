<?php

/**
 * Standalone module bootstrap.
 */

defined('ABSPATH') || exit;

$standalone_modules = array(
    // 'preloader.php',
    'tailwind-database-content.php',
);

foreach ($standalone_modules as $standalone_module) {
    $standalone_module_file = __DIR__ . '/' . $standalone_module;

    if (file_exists($standalone_module_file)) {
        require_once $standalone_module_file;
    }
}
