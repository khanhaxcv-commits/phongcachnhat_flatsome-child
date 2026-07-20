<?php

defined('ABSPATH') || exit;

/*Sắp xếp lại thứ tự các field*/
add_filter("woocommerce_checkout_fields", "order_fields");

function order_fields($fields)
{

    //Shipping
    $order_shipping = array(
        "shipping_last_name",
        "shipping_phone",
        "shipping_address_1"
    );

    foreach ($order_shipping as $field_shipping) {
        $ordered_fields2[$field_shipping] = $fields["shipping"][$field_shipping];
    }

    $fields["shipping"] = $ordered_fields2;

    return $fields;
}

add_filter('woocommerce_checkout_fields', 'custom_override_checkout_fields', 99);

function custom_override_checkout_fields($fields)
{
    unset($fields['billing']['billing_company']);
    unset($fields['billing']['billing_first_name']);
    unset($fields['billing']['billing_postcode']);
    unset($fields['billing']['billing_country']);
    unset($fields['billing']['billing_city']);
    unset($fields['billing']['billing_state']);
    unset($fields['billing']['billing_address_2']);

    $fields['billing']['billing_last_name'] = array(
        'label' => __('Họ và tên', 'devvn'),
        'placeholder' => _x('Nhập đầy đủ họ và tên của bạn', 'placeholder', 'devvn'),
        'required' => true,
        'class' => array('form-row-wide'),
        'clear' => true
    );

    $fields['billing']['billing_address_1']['placeholder'] = 'Ví dụ: Số xx Ngõ xx Phú Kiều, Bắc Từ Liêm, Hà Nội';

    unset($fields['shipping']['shipping_company']);
    unset($fields['shipping']['shipping_postcode']);
    unset($fields['shipping']['shipping_country']);
    unset($fields['shipping']['shipping_city']);
    unset($fields['shipping']['shipping_state']);
    unset($fields['shipping']['shipping_address_2']);

    $fields['shipping']['shipping_phone'] = array(
        'label' => __('Điện thoại', 'devvn'),
        'placeholder' => _x('Số điện thoại người nhận hàng', 'placeholder', 'devvn'),
        'required' => true,
        'class' => array('form-row-wide'),
        'clear' => true
    );

    $fields['shipping']['shipping_last_name'] = array(
        'label' => __('Họ và tên', 'devvn'),
        'placeholder' => _x('Nhập đầy đủ họ và tên của người nhận', 'placeholder', 'devvn'),
        'required' => true,
        'class' => array('form-row-wide'),
        'clear' => true
    );

    $fields['shipping']['shipping_address_1']['placeholder'] = 'Ví dụ: Số xx Ngõ xx Phú Kiều, Bắc Từ Liêm, Hà Nội';

    return $fields;
}

add_action(
    'woocommerce_admin_order_data_after_shipping_address',
    'my_custom_checkout_field_display_admin_order_meta',
    10,
    1
);

function my_custom_checkout_field_display_admin_order_meta($order)
{
    echo '<p><strong>' . __('Số ĐT người nhận') . ':</strong> <br>' . get_post_meta($order->get_id(), '_shipping_phone', true) . '</p>';
}
