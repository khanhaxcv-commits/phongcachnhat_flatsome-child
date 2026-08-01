<?php

defined('ABSPATH') || exit;

/**
 * Render thanh bộ lọc sản phẩm.
 */
function render_product_filter_bar()
{
    $filters = get_dynamic_product_filters();

    if (empty($filters)) {
        return;
    }

    $active_filters = get_product_filter_active_filters(
        null,
        wp_list_pluck($filters, 'taxonomy')
    );

    $has_active_filters = !empty($active_filters);

    foreach ($filters as $filter_index => $filter) {
        $selected_value = !empty($active_filters[$filter['taxonomy']])
            ? reset($active_filters[$filter['taxonomy']])
            : '';

        $filters[$filter_index]['has_terms'] = !empty($filter['terms']);
        $filters[$filter_index]['selected_value'] = $selected_value;
        $filters[$filter_index]['select_state_classes'] = !empty($selected_value)
            ? '!border-accent-soft !bg-surface-accent !text-accent-800'
            : '!border-input !bg-surface-muted !text-input';
        $filters[$filter_index]['mobile_placeholder'] = 'Chọn ' . mb_strtolower($filter['label']);
    }

    $current_order = isset($_GET['orderby'])
        ? wc_clean(wp_unslash($_GET['orderby']))
        : 'menu_order';

    $mobile_orders = array(
        'menu_order' => 'Nổi bật',
        'popularity' => 'Bán chạy',
        'date'       => 'Mới nhất',
        'price'      => 'Giá thấp - cao',
        'price-desc' => 'Giá cao - thấp',
    );

    $template_file = locate_template(
        'template-parts/components/product-filter/filter-bar.php',
        false,
        false
    );

    if (!$template_file) {
        return;
    }

    require $template_file;
}
