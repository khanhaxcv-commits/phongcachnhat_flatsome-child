<?php

/**
 * Product filter cache management and invalidation hooks.
 */

defined('ABSPATH') || exit;

/**
 * Lấy phiên bản cache hiện tại của bộ lọc sản phẩm.
 *
 * Khi dữ liệu sản phẩm, danh mục hoặc attribute thay đổi,
 * phiên bản này sẽ được tăng để vô hiệu hóa cache cũ.
 */
function get_product_filter_cache_version()
{
    return max(
        1,
        (int) get_option('product_filter_cache_version', 1)
    );
}

/**
 * Tăng phiên bản cache bộ lọc sản phẩm.
 */
function bump_product_filter_cache_version()
{
    static $bumped_this_request = false;

    if ($bumped_this_request) {
        return;
    }

    $version = get_product_filter_cache_version();

    update_option(
        'product_filter_cache_version',
        $version + 1,
        false
    );

    $bumped_this_request = true;
}

/**
 * Vô hiệu hóa cache bộ lọc khi sản phẩm được lưu.
 */
function maybe_bump_product_filter_cache_on_product_save($post_id)
{
    $post_id = (int) $post_id;

    if ($post_id <= 0) {
        return;
    }

    if (
        (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        || wp_is_post_revision($post_id)
        || wp_is_post_autosave($post_id)
    ) {
        return;
    }

    bump_product_filter_cache_version();
}

add_action(
    'save_post_product',
    'maybe_bump_product_filter_cache_on_product_save',
    10,
    1
);

/**
 * Vô hiệu hóa cache trước khi xóa sản phẩm.
 */
function maybe_bump_product_filter_cache_on_product_delete($post_id)
{
    $post_id = (int) $post_id;

    if (
        $post_id <= 0
        || get_post_type($post_id) !== 'product'
    ) {
        return;
    }

    bump_product_filter_cache_version();
}

add_action(
    'before_delete_post',
    'maybe_bump_product_filter_cache_on_product_delete',
    10,
    1
);

/**
 * Vô hiệu hóa cache khi danh mục sản phẩm
 * hoặc attribute term thay đổi.
 */
function maybe_bump_product_filter_cache_on_product_term_assignment(
    $object_id,
    $terms,
    $term_taxonomy_ids,
    $taxonomy
) {
    unset($terms, $term_taxonomy_ids);

    if (
        $taxonomy !== 'product_cat'
        && $taxonomy !== 'product_visibility'
        && strpos($taxonomy, 'pa_') !== 0
    ) {
        return;
    }

    if (get_post_type((int) $object_id) !== 'product') {
        return;
    }

    bump_product_filter_cache_version();
}

add_action(
    'set_object_terms',
    'maybe_bump_product_filter_cache_on_product_term_assignment',
    10,
    4
);

function maybe_bump_product_filter_cache_on_term_change(
    $term_id,
    $term_taxonomy_id,
    $taxonomy
) {
    unset($term_id, $term_taxonomy_id);

    if (
        $taxonomy !== 'product_cat'
        && strpos($taxonomy, 'pa_') !== 0
    ) {
        return;
    }

    bump_product_filter_cache_version();
}

add_action(
    'created_term',
    'maybe_bump_product_filter_cache_on_term_change',
    10,
    3
);

add_action(
    'edited_term',
    'maybe_bump_product_filter_cache_on_term_change',
    10,
    3
);

add_action(
    'delete_term',
    'maybe_bump_product_filter_cache_on_term_change',
    10,
    3
);
