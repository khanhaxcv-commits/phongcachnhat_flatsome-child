<?php
/**
 * Custom cart behaviour.
 *
 * Keeps unselected products in the customer session while the selected
 * products continue to checkout, then restores them after checkout or when
 * the customer returns to the cart.
 */

defined('ABSPATH') || exit;

/**
 * The cart has a complete custom UI. Loading Flatsome's shop stylesheet here
 * causes its table/quantity selectors to override the cart components.
 */
function custom_cart_dequeue_flatsome_shop()
{
    if (function_exists('is_cart') && is_cart()) {
        wp_dequeue_style('flatsome-shop');
        wp_deregister_style('flatsome-shop');
    }
}
add_action('wp_enqueue_scripts', 'custom_cart_dequeue_flatsome_shop', 999);

function custom_cart_store_unselected_items()
{
    if (empty($_POST['custom_cart_checkout_selected'])) {
        return;
    }

    if (!isset($_POST['custom_cart_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['custom_cart_nonce'])), 'custom-cart-checkout-selected')) {
        wc_add_notice(__('Phiên làm việc đã hết hạn. Vui lòng thử lại.', 'woocommerce'), 'error');
        return;
    }

    $selected = isset($_POST['custom_cart_selected_items']) ? array_map('wc_clean', (array) wp_unslash($_POST['custom_cart_selected_items'])) : array();
    $selected = array_values(array_intersect($selected, array_keys(WC()->cart->get_cart())));

    if (empty($selected)) {
        wc_add_notice(__('Vui lòng chọn ít nhất một sản phẩm.', 'woocommerce'), 'error');
        return;
    }

    $saved = array();
    foreach (WC()->cart->get_cart() as $key => $item) {
        if (in_array($key, $selected, true)) {
            continue;
        }

        $cart_item_data = $item;
        unset($cart_item_data['data'], $cart_item_data['product_id'], $cart_item_data['variation_id'], $cart_item_data['variation'], $cart_item_data['quantity']);
        $saved[] = array(
            'product_id'    => $item['product_id'],
            'quantity'      => $item['quantity'],
            'variation_id'  => $item['variation_id'],
            'variation'     => $item['variation'],
            'cart_item_data'=> $cart_item_data,
        );
        WC()->cart->remove_cart_item($key);
    }

    WC()->session->set('custom_cart_saved_items', $saved);
    WC()->cart->calculate_totals();
    wp_safe_redirect(wc_get_checkout_url());
    exit;
}
add_action('wp_loaded', 'custom_cart_store_unselected_items', 30);

function custom_cart_restore_saved_items()
{
    if (!WC()->session) {
        return;
    }

    $saved = WC()->session->get('custom_cart_saved_items', array());
    if (empty($saved)) {
        return;
    }

    WC()->session->__unset('custom_cart_saved_items');
    foreach ($saved as $item) {
        WC()->cart->add_to_cart(
            absint($item['product_id']),
            max(1, wc_stock_amount($item['quantity'])),
            absint($item['variation_id']),
            (array) $item['variation'],
            (array) $item['cart_item_data']
        );
    }
    WC()->cart->calculate_totals();
}

function custom_cart_restore_items_on_return()
{
    if (function_exists('is_cart') && is_cart()) {
        custom_cart_restore_saved_items();
    }
}
add_action('template_redirect', 'custom_cart_restore_items_on_return', 5);
add_action('woocommerce_thankyou', 'custom_cart_restore_saved_items', 20);
