<?php

/**
 * Administration module bootstrap.
 */

defined('ABSPATH') || exit;

$admin_modules = array(
    'rank-math-shop-manager.php',
);

if (class_exists('WooCommerce')) {
    $admin_modules[] = 'woocommerce-admin-header.php';
}

foreach ($admin_modules as $admin_module) {
    $admin_module_file = __DIR__ . '/' . $admin_module;

    if (file_exists($admin_module_file)) {
        require_once $admin_module_file;
    }
}
