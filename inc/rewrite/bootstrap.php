<?php

/**
 * Rewrite module bootstrap.
 */

defined('ABSPATH') || exit;

$rewrite_modules = array(
    'flat-category-urls.php',
);

foreach ($rewrite_modules as $rewrite_module) {
    $rewrite_module_file = __DIR__ . '/' . $rewrite_module;

    if (file_exists($rewrite_module_file)) {
        require_once $rewrite_module_file;
    }
}
