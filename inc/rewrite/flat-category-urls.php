<?php

defined('ABSPATH') || exit;

/**
 * 1. Đổi link hiển thị của danh mục sản phẩm WooCommerce
 */
add_filter('term_link', 'custom_flat_product_cat_url', 10, 3);

function custom_flat_product_cat_url($url, $term, $taxonomy)
{

    if ($taxonomy === 'product_cat') {
        return home_url('/' . $term->slug . '/');
    }

    return $url;
}


/**
 * 2. Đổi link hiển thị của danh mục bài viết cấp con
 */
add_filter('term_link', 'custom_flat_child_category_url', 10, 3);

function custom_flat_child_category_url($url, $term, $taxonomy)
{

    if ($taxonomy === 'category' && !empty($term->parent)) {
        return home_url('/' . $term->slug . '/');
    }

    return $url;
}


/**
 * 3. Tạo rewrite rule cho danh mục sản phẩm WooCommerce
 */
add_action('init', 'custom_flat_product_cat_rewrite_rules');

function custom_flat_product_cat_rewrite_rules()
{

    $terms = get_terms(array(
        'taxonomy' => 'product_cat',
        'hide_empty' => false,
    ));

    if (empty($terms) || is_wp_error($terms)) {
        return;
    }

    foreach ($terms as $term) {

        add_rewrite_rule(
            '^' . $term->slug . '/?$',
            'index.php?product_cat=' . $term->slug,
            'top'
        );

        add_rewrite_rule(
            '^' . $term->slug . '/page/([0-9]+)/?$',
            'index.php?product_cat=' . $term->slug . '&paged=$matches[1]',
            'top'
        );
    }
}


/**
 * 4. Tạo rewrite rule cho danh mục bài viết cấp con
 */
add_action('init', 'custom_flat_child_category_rewrite_rules');

function custom_flat_child_category_rewrite_rules()
{

    $terms = get_terms(array(
        'taxonomy' => 'category',
        'hide_empty' => false,
    ));

    if (empty($terms) || is_wp_error($terms)) {
        return;
    }

    foreach ($terms as $term) {

        // Chỉ áp dụng cho category cấp con
        if (!empty($term->parent)) {

            add_rewrite_rule(
                '^' . $term->slug . '/?$',
                'index.php?category_name=' . $term->slug,
                'top'
            );

            add_rewrite_rule(
                '^' . $term->slug . '/page/([0-9]+)/?$',
                'index.php?category_name=' . $term->slug . '&paged=$matches[1]',
                'top'
            );
        }
    }
}
