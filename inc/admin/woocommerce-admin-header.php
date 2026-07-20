<?php

defined('ABSPATH') || exit;

// ẩn thanh stick trong admin
function custom_admin_inline_css()
{
    $custom_css = "
        .woocommerce-layout__header {
            position: unset !important;
        }
    ";

    wp_add_inline_style('wp-admin', $custom_css); // Thêm sau stylesheet 'wp-admin' chung
}

add_action('admin_enqueue_scripts', 'custom_admin_inline_css');
