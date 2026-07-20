<?php

defined('ABSPATH') || exit;

// Xóa các tab không cần thiết
add_filter('woocommerce_product_tabs', 'remove_unnecessary_tabs', 100, 1);

function remove_unnecessary_tabs($tabs)
{
    unset($tabs['additional_information']); // Xóa tab thông tin bổ sung
    unset($tabs['reviews']);

    return $tabs;
}

// Thêm các tab tùy chỉnh
add_filter('woocommerce_product_tabs', 'custom_product_tabs');

function custom_product_tabs($tabs)
{
    // Tab Danh Muc
    $tabs['lap_dat'] = array(
        'title'     => __('Hướng dẫn sử dụng', 'woocommerce'),
        'priority'  => 20,
        'callback'  => 'woo_product_tab_lap_dat'
    );

    return $tabs;
}

// Callback function cho tab hình ảnh lắp đặt
function woo_product_tab_lap_dat()
{
    // Lấy giá trị từ field 'su_dung'
    $su_dung_value = get_field('su_dung');

    // Kiểm tra xem field có giá trị hay không
    if ($su_dung_value) {
        // Nếu có, hiển thị giá trị đó
        echo $su_dung_value;
    } else {
        // Nếu không có giá trị (trống), hiển thị 'Đang update'
        echo 'Đang update';
    }
}
