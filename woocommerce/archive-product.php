<?php

/**
 * Product archive template.
 *
 * Trang Shop dùng chung UI archive custom với danh mục sản phẩm. Các taxonomy
 * sản phẩm khác vẫn giữ cách chọn layout hiện tại của Flatsome.
 *
 * @package          WooCommerce/Templates
 * @version          8.8.0
 * @flatsome-version 3.18.7
 */

defined('ABSPATH') || exit;

global $wp_query;

get_header('shop');

if (
    is_shop()
    && get_theme_mod('html_shop_page_content')
    && !$wp_query->is_search()
    && $wp_query->query_vars['paged'] < 1
) {
    echo do_shortcode('<div class="shop-page-content">' . get_theme_mod('html_shop_page_content') . '</div>');
} elseif (is_shop()) {
    get_template_part('template-parts/components/product-archive');
} else {
    wc_get_template_part('layouts/category', get_theme_mod('category_sidebar', 'left-sidebar'));
}

get_footer('shop');
