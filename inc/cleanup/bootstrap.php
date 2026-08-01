<?php

/**
 * Cleanup module bootstrap.
 */

defined('ABSPATH') || exit;

$cleanup_modules = array(
    'contact-form-7.php',
    // 'disable-wpautop.php',
    'editor.php',
);

foreach ($cleanup_modules as $cleanup_module) {
    $cleanup_module_file = __DIR__ . '/' . $cleanup_module;

    if (file_exists($cleanup_module_file)) {
        require_once $cleanup_module_file;
    }
}
