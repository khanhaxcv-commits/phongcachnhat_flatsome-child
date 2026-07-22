<?php

defined('ABSPATH') || exit;

add_action('wp', 'register_category_breadcrumb_hooks');

function register_category_breadcrumb_hooks()
{
    if (!is_product_category()) {
        return;
    }

    /**
     * Đăng ký breadcrumb vào hook riêng.
     */
    add_action(
        'category_breadcrumb',
        'render_category_breadcrumb',
        10
    );
}

/**
 * Render breadcrumb tùy chỉnh.
 */
function render_category_breadcrumb()
{
    woocommerce_breadcrumb([
        'delimiter'   => '<i class="fa-light fa-angle-right"></i>',
        'wrap_before' => '<nav class="category-breadcrumb" aria-label="Breadcrumb">',
        'wrap_after'  => '</nav>',
        'before'      => '<span class="breadcrumb-current">',
        'after'       => '</span>',
        'home'        => 'Home',
    ]);
}
