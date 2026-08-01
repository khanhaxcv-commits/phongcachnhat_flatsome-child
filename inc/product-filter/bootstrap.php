<?php

/**
 * Product filter module bootstrap.
 */

defined('ABSPATH') || exit;

$product_filter_modules = array(
    'cache.php',
    'request.php',
    'data.php',
    'catalog-ordering.php',
    'catalog-query.php',
    'filter-bar.php',
    'ajax-products.php',
);

foreach ($product_filter_modules as $product_filter_module) {
    $product_filter_module_file = __DIR__ . '/' . $product_filter_module;

    if (file_exists($product_filter_module_file)) {
        require_once $product_filter_module_file;
    }
}
