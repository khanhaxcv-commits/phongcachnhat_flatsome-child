<?php

defined('ABSPATH') || exit;

/**
 * AJAX dành cho người dùng đã đăng nhập.
 */
add_action(
    'wp_ajax_load_filtered_products',
    'load_filtered_products'
);

/**
 * AJAX dành cho khách chưa đăng nhập.
 */
add_action(
    'wp_ajax_nopriv_load_filtered_products',
    'load_filtered_products'
);

/**
 * Render nút xem thêm sản phẩm.
 *
 * @param int $current_page  Trang hiện tại.
 * @param int $total_pages   Tổng số trang.
 * @param int $total         Tổng số sản phẩm.
 * @param int $per_page      Số sản phẩm mỗi lần tải.
 */
function render_product_load_more_button(
    $current_page,
    $total_pages,
    $total,
    $per_page
) {
    $current_page = max(1, absint($current_page));
    $total_pages  = max(1, absint($total_pages));
    $total        = max(0, absint($total));
    $per_page     = max(1, absint($per_page));

    if ($current_page >= $total_pages) {
        return;
    }

    $displayed = min(
        $total,
        $current_page * $per_page
    );

    $remaining = max(
        0,
        $total - $displayed
    );

    if (!$remaining) {
        return;
    }
?>

    <div class="product-load-more-wrap">

        <button
            type="button"
            class="product-load-more"
            data-current-page="<?php echo esc_attr($current_page); ?>"
            data-max-pages="<?php echo esc_attr($total_pages); ?>"
            data-remaining="<?php echo esc_attr($remaining); ?>">

            <span class="product-load-more__label">
                Xem thêm
                <strong class="product-load-more__count">
                    <?php echo esc_html($remaining); ?>
                </strong>
                sản phẩm
            </span>

            <i class="fa-light fa-angle-down product-load-more__icon"></i>

            <span
                class="product-load-more__spinner"
                aria-hidden="true">
            </span>

        </button>

    </div>

<?php
}

/**
 * Lấy các taxonomy filter được gửi từ AJAX.
 *
 * @param array $filters Bộ lọc được gửi lên.
 *
 * @return array
 */
function get_product_filter_ajax_tax_query($filters)
{
    $tax_query = WC()->query->get_tax_query();

    if (!is_array($tax_query)) {
        $tax_query = array();
    }

    foreach ($filters as $filter_key => $filter_value) {

        $filter_key = sanitize_title($filter_key);
        $term_id    = absint($filter_value);

        if (
            empty($filter_key)
            || empty($term_id)
        ) {
            continue;
        }

        $taxonomy = 'pa_' . $filter_key;

        if (!taxonomy_exists($taxonomy)) {
            continue;
        }

        $term = get_term(
            $term_id,
            $taxonomy
        );

        if (
            !$term
            || is_wp_error($term)
        ) {
            continue;
        }

        $tax_query[] = array(
            'taxonomy' => $taxonomy,
            'field'    => 'term_id',
            'terms'    => array($term_id),
            'operator' => 'IN',
        );
    }

    return $tax_query;
}

/**
 * Render riêng các thẻ sản phẩm.
 *
 * Dùng khi nhấn nút "Xem thêm" để nối sản phẩm mới
 * vào danh sách hiện tại.
 *
 * @param WP_Query $product_query Product query.
 *
 * @return string
 */
function get_product_filter_ajax_items_html($product_query)
{
    if (
        !$product_query instanceof WP_Query
        || !$product_query->have_posts()
    ) {
        return '';
    }

    ob_start();

    while ($product_query->have_posts()) {

        $product_query->the_post();

        do_action('woocommerce_shop_loop');

        wc_get_template_part(
            'content',
            'product'
        );
    }

    return ob_get_clean();
}

/**
 * Render toàn bộ nội dung vùng kết quả.
 *
 * Dùng khi thay đổi filter hoặc sắp xếp.
 *
 * @param WP_Query $product_query Product query.
 * @param int      $current_page Trang hiện tại.
 * @param int      $per_page     Số sản phẩm mỗi trang.
 *
 * @return string
 */
function get_product_filter_ajax_results_html(
    $product_query,
    $current_page,
    $per_page
) {
    ob_start();

    if ($product_query->have_posts()) {

        /**
         * Result count và hook trước product loop.
         */
        do_action('woocommerce_before_shop_loop');

        woocommerce_product_loop_start();

        while ($product_query->have_posts()) {

            $product_query->the_post();

            do_action('woocommerce_shop_loop');

            wc_get_template_part(
                'content',
                'product'
            );
        }

        woocommerce_product_loop_end();

        render_product_load_more_button(
            $current_page,
            $product_query->max_num_pages,
            $product_query->found_posts,
            $per_page
        );
    } else {
        do_action('woocommerce_no_products_found');
    }

    return ob_get_clean();
}

/**
 * Xử lý AJAX lọc, sắp xếp và xem thêm sản phẩm.
 */
function load_filtered_products()
{
    check_ajax_referer(
        'product_filter_ajax',
        'nonce'
    );

    $category_id = isset($_POST['category_id'])
        ? absint($_POST['category_id'])
        : 0;

    $paged = isset($_POST['paged'])
        ? max(1, absint($_POST['paged']))
        : 1;

    $per_page = isset($_POST['per_page'])
        ? absint($_POST['per_page'])
        : 12;

    $per_page = max(
        1,
        min(100, $per_page)
    );

    $orderby = isset($_POST['orderby'])
        ? wc_clean(wp_unslash($_POST['orderby']))
        : 'menu_order';

    $request_mode = isset($_POST['request_mode'])
        ? sanitize_key(wp_unslash($_POST['request_mode']))
        : 'replace';

    if (!in_array($request_mode, array('replace', 'append'), true)) {
        $request_mode = 'replace';
    }

    $filters = array();

    if (
        isset($_POST['filters'])
        && is_array($_POST['filters'])
    ) {
        $filters = wc_clean(
            wp_unslash($_POST['filters'])
        );
    }

    /**
     * Chỉ cho phép các kiểu sắp xếp hợp lệ.
     */
    $allowed_orderby = array(
        'menu_order',
        'popularity',
        'date',
        'price',
        'price-desc',
    );

    if (!in_array($orderby, $allowed_orderby, true)) {
        $orderby = 'menu_order';
    }

    /**
     * Thiết lập kiểu sắp xếp chuẩn WooCommerce.
     */
    $ordering_args = WC()->query->get_catalog_ordering_args(
        $orderby,
        ''
    );

    /**
     * Tax query mặc định của WooCommerce.
     */
    $tax_query = get_product_filter_ajax_tax_query(
        $filters
    );

    /**
     * Giới hạn trong danh mục hiện tại.
     */
    if ($category_id) {

        $category_term = get_term(
            $category_id,
            'product_cat'
        );

        if (
            $category_term
            && !is_wp_error($category_term)
        ) {
            $tax_query[] = array(
                'taxonomy'         => 'product_cat',
                'field'            => 'term_id',
                'terms'            => array($category_id),
                'include_children' => true,
                'operator'         => 'IN',
            );
        }
    }

    /**
     * Meta query mặc định WooCommerce.
     */
    $meta_query = WC()->query->get_meta_query();

    if (!is_array($meta_query)) {
        $meta_query = array();
    }

    /**
     * Query sản phẩm.
     */
    $query_args = array(
        'post_type'              => 'product',
        'post_status'            => 'publish',
        'posts_per_page'         => $per_page,
        'paged'                  => $paged,
        'orderby'                => $ordering_args['orderby'],
        'order'                  => $ordering_args['order'],
        'tax_query'              => $tax_query,
        'meta_query'             => $meta_query,
        'ignore_sticky_posts'    => true,
        'no_found_rows'          => false,
        'update_post_meta_cache' => true,
        'update_post_term_cache' => true,
    );

    if (!empty($ordering_args['meta_key'])) {
        $query_args['meta_key'] = $ordering_args['meta_key'];
    }

    /**
     * Cho phép module khác tùy chỉnh query AJAX.
     */
    $query_args = apply_filters(
        'product_filter_ajax_query_args',
        $query_args,
        $filters,
        $orderby,
        $category_id,
        $request_mode
    );

    $product_query = new WP_Query(
        $query_args
    );

    /**
     * Thiết lập thông tin WooCommerce loop.
     */
    wc_set_loop_prop(
        'current_page',
        $paged
    );

    wc_set_loop_prop(
        'is_paginated',
        true
    );

    wc_set_loop_prop(
        'per_page',
        $per_page
    );

    wc_set_loop_prop(
        'total',
        (int) $product_query->found_posts
    );

    wc_set_loop_prop(
        'total_pages',
        (int) $product_query->max_num_pages
    );

    wc_set_loop_prop(
        'columns',
        wc_get_default_products_per_row()
    );

    /**
     * Đưa query AJAX thành query hiện hành để
     * result count và template WooCommerce hoạt động đúng.
     */
    global $wp_query;

    $original_wp_query = $wp_query;
    $wp_query          = $product_query;

    if ($request_mode === 'append') {

        $html = get_product_filter_ajax_items_html(
            $product_query
        );
    } else {

        $html = get_product_filter_ajax_results_html(
            $product_query,
            $paged,
            $per_page
        );
    }

    $displayed = min(
        (int) $product_query->found_posts,
        $paged * $per_page
    );

    $remaining = max(
        0,
        (int) $product_query->found_posts - $displayed
    );

    $has_more = (
        $paged < (int) $product_query->max_num_pages
    );

    wp_reset_postdata();

    $wp_query = $original_wp_query;

    wp_send_json_success(
        array(
            'html'          => $html,
            'requestMode'   => $request_mode,
            'foundProducts' => (int) $product_query->found_posts,
            'currentPage'   => $paged,
            'maxPages'      => (int) $product_query->max_num_pages,
            'remaining'     => $remaining,
            'hasMore'       => $has_more,
        )
    );
}
