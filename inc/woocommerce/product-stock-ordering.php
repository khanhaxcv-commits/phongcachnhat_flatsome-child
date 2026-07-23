<?php

defined('ABSPATH') || exit;

/**
 * Đưa sản phẩm còn hàng lên trước, đặt trước sau,
 * và sản phẩm hết hàng xuống cuối danh sách.
 *
 * Giữ nguyên cách sắp xếp hiện tại của WooCommerce:
 * nổi bật, bán chạy, mới nhất, giá thấp - cao...
 */
add_filter(
    'posts_clauses',
    'order_catalog_products_by_stock_status',
    20,
    2
);

function order_catalog_products_by_stock_status($clauses, $query)
{
    global $wpdb;

    /**
     * Không can thiệp trong trang quản trị.
     */
    if (is_admin() && !wp_doing_ajax()) {
        return $clauses;
    }

    /**
     * Chỉ tác động lên main query.
     */
    if (!$query->is_main_query()) {
        return $clauses;
    }

    /**
     * Chỉ áp dụng cho:
     * - Trang cửa hàng
     * - Danh mục sản phẩm
     * - Thẻ sản phẩm
     * - Kết quả tìm kiếm sản phẩm
     */
    $is_product_archive = (
        is_shop()
        || is_product_category()
        || is_product_tag()
    );

    $is_product_search = (
        $query->is_search()
        && $query->get('post_type') === 'product'
    );

    if (!$is_product_archive && !$is_product_search) {
        return $clauses;
    }

    $lookup_table = $wpdb->wc_product_meta_lookup;
    $lookup_alias = 'stock_lookup';

    /**
     * Chỉ thêm JOIN nếu query chưa có alias của chúng ta.
     */
    if (
        strpos(
            $clauses['join'],
            "{$lookup_table} {$lookup_alias}"
        ) === false
    ) {
        $clauses['join'] .= "
            LEFT JOIN {$lookup_table} AS {$lookup_alias}
                ON {$wpdb->posts}.ID = {$lookup_alias}.product_id
        ";
    }

    /**
     * Xếp thứ tự trạng thái tồn kho:
     *
     * 0: còn hàng
     * 1: cho phép đặt trước
     * 2: hết hàng hoặc không xác định
     *
     * Sau trạng thái tồn kho vẫn giữ nguyên ORDER BY
     * của WooCommerce đang được người dùng lựa chọn.
     */
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
