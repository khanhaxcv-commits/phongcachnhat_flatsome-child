<?php
/**
 * Custom cart totals for the Phong Cach Nhat cart UI.
 *
 * @package WooCommerce\Templates
 * @version 2.3.6
 */

defined('ABSPATH') || exit;

$regular_total = 0.0;
foreach (WC()->cart->get_cart() as $cart_item) {
    $product = $cart_item['data'];
    if (!($product instanceof WC_Product)) {
        continue;
    }
    $regular = (float) $product->get_regular_price();
    $current = (float) $product->get_price();
    $regular_total += max($regular, $current) * (float) $cart_item['quantity'];
}

$current_items_total = (float) WC()->cart->get_cart_contents_total();
$direct_saving = max(0, $regular_total - $current_items_total);
?>
<div class="cart_totals">
    <div class="custom-cart-total-lines">
        <div class="custom-cart-total-line">
            <span>Tổng tiền hàng</span>
            <strong data-cart-subtotal><?php wc_cart_totals_subtotal_html(); ?></strong>
        </div>

        <div class="custom-cart-total-line">
            <span>Phí vận chuyển</span>
            <strong>
                <?php echo WC()->cart->needs_shipping() && (float) WC()->cart->get_shipping_total() > 0
                    ? wp_kses_post(wc_price(WC()->cart->get_shipping_total()))
                    : esc_html__('Miễn phí', 'woocommerce'); ?>
            </strong>
        </div>

        <?php foreach (WC()->cart->get_coupons() as $code => $coupon) : ?>
            <div class="custom-cart-total-line custom-cart-total-discount">
                <span><?php wc_cart_totals_coupon_label($coupon); ?></span>
                <strong><?php wc_cart_totals_coupon_html($coupon); ?></strong>
            </div>
        <?php endforeach; ?>

        <div class="custom-cart-total-line custom-cart-total-discount<?php echo $direct_saving > 0 ? '' : ' is-hidden'; ?>" data-direct-saving-row>
            <span>Giảm giá trực tiếp</span>
            <strong data-cart-saving>- <?php echo wp_kses_post(wc_price($direct_saving)); ?></strong>
        </div>

        <?php foreach (WC()->cart->get_fees() as $fee) : ?>
            <div class="custom-cart-total-line">
                <span><?php echo esc_html($fee->name); ?></span>
                <strong><?php wc_cart_totals_fee_html($fee); ?></strong>
            </div>
        <?php endforeach; ?>

        <div class="custom-cart-total-line custom-cart-order-total">
            <span>TỔNG TIỀN<small>(Đã bao gồm VAT và được làm tròn)</small></span>
            <strong data-cart-total><?php wc_cart_totals_order_total_html(); ?></strong>
        </div>

        <div class="custom-cart-total-line custom-cart-saved-line<?php echo $direct_saving > 0 ? '' : ' is-hidden'; ?>" data-saved-line>
            <span>Bạn đã tiết kiệm được</span>
            <strong data-cart-saving>- <?php echo wp_kses_post(wc_price($direct_saving)); ?></strong>
        </div>
    </div>

    <?php do_action('woocommerce_after_cart_totals'); ?>
</div>
