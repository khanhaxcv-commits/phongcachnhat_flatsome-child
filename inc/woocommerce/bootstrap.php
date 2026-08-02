<?php

/**
 * WooCommerce module bootstrap.
 */

defined('ABSPATH') || exit;

$woocommerce_modules = array(
    'category-header.php',
    'category-breadcrumb.php',
    'primary-product-category.php',
    'checkout-fields.php',
    'product-tabs.php',
    'installation-gallery.php',
    'product-description-readmore.php',
    'product-stock-ordering.php',
    'disable-structured-data.php',
    'product-attributes.php',
    'product-group.php',
    'daily-countdown.php',
    'category-banner-subcategories.php',
    'recently-viewed-products.php',
    'product-specifications.php',
    'product-video.php',
    'product-price-meta.php',
    'category-description-readmore.php',
    // 'brand-description-editor.php',
    'product-subcategory-shortcode.php',
    'product-affiliate-links.php',
    'cart.php',
);

foreach ($woocommerce_modules as $woocommerce_module) {
    $woocommerce_module_file = __DIR__ . '/' . $woocommerce_module;

    if (file_exists($woocommerce_module_file)) {
        require_once $woocommerce_module_file;
    }
}
