<?php

defined('ABSPATH') || exit;

/**
 * Add a preferred product category selector for WooCommerce breadcrumbs.
 */
add_action('add_meta_boxes_product', 'add_primary_product_cat_box');

function add_primary_product_cat_box()
{
    add_meta_box(
        'primary_product_cat_box',
        __('Danh mục ưu tiên (Breadcrumb)', 'flatsome-child'),
        'primary_product_cat_box_html',
        'product',
        'side',
        'default'
    );
}

function primary_product_cat_box_html($post)
{
    wp_nonce_field('save_primary_product_cat', 'primary_product_cat_nonce');

    $selected_cat = (int) get_post_meta($post->ID, '_primary_product_cat', true);

    echo '<p style="margin-top:0;font-size:13px;">' .
        esc_html__('Chọn danh mục hiển thị trong breadcrumb.', 'flatsome-child') .
        '</p>';

    wp_dropdown_categories(array(
        'taxonomy'          => 'product_cat',
        'hide_empty'        => false,
        'name'              => 'primary_product_cat',
        'id'                => 'primary_product_cat',
        'selected'          => $selected_cat,
        'show_option_none'  => __('— WooCommerce tự chọn —', 'flatsome-child'),
        'option_none_value' => 0,
        'hierarchical'      => true,
        'orderby'           => 'name',
        'order'             => 'ASC',
        'class'             => 'widefat',
    ));

    echo '<p style="font-size:12px;color:#666;margin-bottom:0;">' .
        esc_html__('Danh mục được chọn phải đồng thời được gán cho sản phẩm.', 'flatsome-child') .
        '</p>';
}

add_action('save_post_product', 'save_primary_product_cat', 20, 2);
add_action('woocommerce_process_product_meta', 'save_primary_product_cat_wc', 20);

function save_primary_product_cat($post_id, $post)
{
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!$post || $post->post_type !== 'product') {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    _do_save_primary_cat($post_id);
}

function save_primary_product_cat_wc($post_id)
{
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    _do_save_primary_cat($post_id);
}

function _do_save_primary_cat($post_id)
{
    $nonce = isset($_POST['primary_product_cat_nonce'])
        ? sanitize_text_field(wp_unslash($_POST['primary_product_cat_nonce']))
        : '';

    if (!$nonce || !wp_verify_nonce($nonce, 'save_primary_product_cat')) {
        return;
    }

    $cat_id = isset($_POST['primary_product_cat'])
        ? absint(wp_unslash($_POST['primary_product_cat']))
        : 0;

    if ($cat_id <= 0) {
        delete_post_meta($post_id, '_primary_product_cat');
        return;
    }

    $term = get_term($cat_id, 'product_cat');

    if (!$term || is_wp_error($term)) {
        delete_post_meta($post_id, '_primary_product_cat');
        return;
    }

    update_post_meta($post_id, '_primary_product_cat', $cat_id);
}

/**
 * Make the WooCommerce breadcrumb use the saved preferred category.
 */
add_filter('woocommerce_breadcrumb_main_term', 'use_primary_cat_for_breadcrumb', 99, 2);

function use_primary_cat_for_breadcrumb($main_term, $terms)
{
    if (!is_singular('product')) {
        return $main_term;
    }

    $post_id = get_queried_object_id();

    if (!$post_id) {
        return $main_term;
    }

    $primary_cat_id = (int) get_post_meta($post_id, '_primary_product_cat', true);

    if ($primary_cat_id <= 0 || empty($terms) || is_wp_error($terms)) {
        return $main_term;
    }

    foreach ($terms as $term) {
        if ((int) $term->term_id === $primary_cat_id) {
            return $term;
        }
    }

    return $main_term;
}
