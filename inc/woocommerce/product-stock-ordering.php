<?php

defined('ABSPATH') || exit;

/**
 * Keep catalog product ordering stable while restricting frontend catalog
 * queries to in-stock products.
 */
add_filter(
    'posts_clauses',
    'order_catalog_products_by_stock_status',
    20,
    2
);

add_filter(
    'product_filter_ajax_query_args',
    'pcn_filter_ajax_catalog_products_by_stock_status'
);

function pcn_filter_ajax_catalog_products_by_stock_status($query_args)
{
    $query_args['filter_catalog_instock_products'] = true;

    return $query_args;
}

function pcn_should_apply_catalog_stock_filter($query)
{
    if (!$query instanceof WP_Query) {
        return false;
    }

    if (is_admin() && !wp_doing_ajax()) {
        return false;
    }

    if ($query->get('filter_catalog_instock_products')) {
        return true;
    }

    if (!$query->is_main_query()) {
        return false;
    }

    $is_product_archive = (
        is_shop()
        || is_product_category()
        || is_product_tag()
    );

    $post_type = $query->get('post_type');

    $is_product_search = $query->is_search() && (
        $post_type === 'product'
        || (
            is_array($post_type)
            && in_array('product', $post_type, true)
        )
    );

    return $is_product_archive || $is_product_search;
}

function order_catalog_products_by_stock_status($clauses, $query)
{
    global $wpdb;

    if (!pcn_should_apply_catalog_stock_filter($query)) {
        return $clauses;
    }

    $lookup_table = $wpdb->wc_product_meta_lookup;
    $lookup_alias = 'stock_lookup';

    if (
        strpos(
            $clauses['join'],
            "{$lookup_table} AS {$lookup_alias}"
        ) === false
    ) {
        $clauses['join'] .= "
            INNER JOIN {$lookup_table} AS {$lookup_alias}
                ON {$wpdb->posts}.ID = {$lookup_alias}.product_id
        ";
    }

    if (
        strpos(
            $clauses['where'],
            "{$lookup_alias}.stock_status = 'instock'"
        ) === false
    ) {
        $clauses['where'] .= "
            AND {$lookup_alias}.stock_status = 'instock'
        ";
    }

    $stock_order = "
        CASE
            WHEN {$lookup_alias}.stock_status = 'instock' THEN 0
            WHEN {$lookup_alias}.stock_status = 'onbackorder' THEN 1
            ELSE 2
        END ASC
    ";

    if (!empty($clauses['orderby'])) {
        $clauses['orderby'] = $stock_order . ', ' . $clauses['orderby'];
    } else {
        $clauses['orderby'] = $stock_order . ", {$wpdb->posts}.menu_order ASC";
    }

    return $clauses;
}
