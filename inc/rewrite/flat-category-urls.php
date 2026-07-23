<?php

defined('ABSPATH') || exit;

/**
 * Đổi URL danh mục sản phẩm WooCommerce thành URL phẳng.
 *
 * Ví dụ:
 * /product-category/thiet-bi-nha-bep/
 * thành:
 * /thiet-bi-nha-bep/
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
 * Đổi URL category bài viết cấp con thành URL phẳng.
 */
add_filter('term_link', 'custom_flat_child_category_url', 10, 3);

function custom_flat_child_category_url($url, $term, $taxonomy)
{
    if (
        $taxonomy === 'category'
        && !empty($term->parent)
    ) {
        return home_url('/' . $term->slug . '/');
    }

    return $url;
}

/**
 * Tạo rewrite rule cho danh mục sản phẩm WooCommerce.
 */
add_action(
    'init',
    'custom_flat_product_cat_rewrite_rules',
    20
);

function custom_flat_product_cat_rewrite_rules()
{
    $terms = get_transient('flat_product_cat_rewrite_terms');

    if ($terms === false) {
        $terms = get_terms([
            'taxonomy'   => 'product_cat',
            'hide_empty' => false,
            'fields'     => 'id=>slug',
        ]);

        if (is_wp_error($terms)) {
            return;
        }

        set_transient(
            'flat_product_cat_rewrite_terms',
            $terms,
            DAY_IN_SECONDS
        );
    }

    if (empty($terms)) {
        return;
    }

    foreach ($terms as $slug) {
        $slug = sanitize_title($slug);

        if ($slug === '') {
            continue;
        }

        $escaped_slug = preg_quote($slug, '/');

        add_rewrite_rule(
            '^' . $escaped_slug . '/?$',
            'index.php?product_cat=' . $slug,
            'top'
        );

        add_rewrite_rule(
            '^' . $escaped_slug . '/page/([0-9]+)/?$',
            'index.php?product_cat=' . $slug . '&paged=$matches[1]',
            'top'
        );
    }
}

/**
 * Tạo rewrite rule cho category bài viết cấp con.
 */
add_action(
    'init',
    'custom_flat_child_category_rewrite_rules',
    30
);

function custom_flat_child_category_rewrite_rules()
{
    $terms = get_transient('flat_child_category_rewrite_terms');

    if ($terms === false) {
        $terms = get_terms([
            'taxonomy'   => 'category',
            'hide_empty' => false,
            'fields'     => 'all',
        ]);

        if (is_wp_error($terms)) {
            return;
        }

        $terms = array_values(
            array_filter(
                $terms,
                static function ($term) {
                    return !empty($term->parent);
                }
            )
        );

        set_transient(
            'flat_child_category_rewrite_terms',
            $terms,
            DAY_IN_SECONDS
        );
    }

    if (empty($terms)) {
        return;
    }

    foreach ($terms as $term) {
        if (
            !is_object($term)
            || empty($term->slug)
        ) {
            continue;
        }

        $slug         = sanitize_title($term->slug);
        $escaped_slug = preg_quote($slug, '/');

        if ($slug === '') {
            continue;
        }

        add_rewrite_rule(
            '^' . $escaped_slug . '/?$',
            'index.php?category_name=' . $slug,
            'top'
        );

        add_rewrite_rule(
            '^' . $escaped_slug . '/page/([0-9]+)/?$',
            'index.php?category_name=' . $slug . '&paged=$matches[1]',
            'top'
        );
    }
}

/**
 * Xóa cache danh sách term và đánh dấu cần flush rewrite rules.
 */
function clear_flat_rewrite_term_cache()
{
    delete_transient('flat_product_cat_rewrite_terms');
    delete_transient('flat_child_category_rewrite_terms');

    update_option(
        'flat_url_needs_flush',
        1,
        false
    );
}

/**
 * Theo dõi thay đổi danh mục sản phẩm.
 */
add_action(
    'created_product_cat',
    'clear_flat_rewrite_term_cache'
);

add_action(
    'edited_product_cat',
    'clear_flat_rewrite_term_cache'
);

add_action(
    'delete_product_cat',
    'clear_flat_rewrite_term_cache'
);

/**
 * Theo dõi thay đổi category bài viết.
 */
add_action(
    'created_category',
    'clear_flat_rewrite_term_cache'
);

add_action(
    'edited_category',
    'clear_flat_rewrite_term_cache'
);

add_action(
    'delete_category',
    'clear_flat_rewrite_term_cache'
);

/**
 * Flush rewrite rules ở request tiếp theo,
 * sau khi toàn bộ rule mới đã được đăng ký.
 */
add_action(
    'init',
    'maybe_flush_flat_rewrite_rules',
    999
);

function maybe_flush_flat_rewrite_rules()
{
    if (!get_option('flat_url_needs_flush')) {
        return;
    }

    delete_option('flat_url_needs_flush');

    flush_rewrite_rules(false);
}
