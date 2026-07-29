<?php
/**
 * Custom cart totals for the Phong Cach Nhat cart UI.
 *
 * @package WooCommerce\Templates
 * @version 2.3.6
 */

defined('ABSPATH') || exit;

?>
<div class="cart_totals w-full !rounded-none !border-0 !bg-transparent !p-0 !shadow-none">
    <div class="custom-cart-total-lines !grid">
        <div class="custom-cart-total-line custom-cart-line-subtotal !flex min-h-[29px] items-center justify-between gap-4 px-2 py-1">
            <span>Tổng tiền hàng</span>
            <strong class="whitespace-nowrap text-right [&_.amount]:!text-[inherit] [&_.amount]:!font-[inherit]" data-cart-subtotal><?php wc_cart_totals_subtotal_html(); ?></strong>
        </div>

        <div class="custom-cart-total-line custom-cart-line-shipping !flex min-h-[29px] items-center justify-between gap-4 px-2 py-1">
            <span>Phí vận chuyển</span>
            <strong class="whitespace-nowrap text-right">
                <?php echo WC()->cart->needs_shipping() && (float) WC()->cart->get_shipping_total() > 0
                    ? wp_kses_post(wc_price(WC()->cart->get_shipping_total()))
                    : esc_html__('Miễn phí', 'woocommerce'); ?>
            </strong>
        </div>

        <?php foreach (WC()->cart->get_coupons() as $code => $coupon) : ?>
            <div class="custom-cart-total-line custom-cart-total-discount mt-2 !flex min-h-[29px] items-center justify-between gap-4 border-t border-ui px-2 pb-1 pt-3">
                <span><?php wc_cart_totals_coupon_label($coupon); ?></span>
                <strong class="whitespace-nowrap text-right !text-success [&_.amount]:!text-[inherit] [&_.amount]:!font-[inherit]"><?php wc_cart_totals_coupon_html($coupon); ?></strong>
            </div>
        <?php endforeach; ?>

        <?php foreach (WC()->cart->get_fees() as $fee) : ?>
            <div class="custom-cart-total-line !flex min-h-[29px] items-center justify-between gap-4 px-2 py-1">
                <span><?php echo esc_html($fee->name); ?></span>
                <strong class="whitespace-nowrap text-right [&_.amount]:!text-[inherit] [&_.amount]:!font-[inherit]"><?php wc_cart_totals_fee_html($fee); ?></strong>
            </div>
        <?php endforeach; ?>

        <div class="custom-cart-total-line custom-cart-order-total mt-2 !flex min-h-[52px] items-center justify-between gap-4 border-t border-ui px-2 pb-1 pt-2.5">
            <span class="text-[15px] font-bold [&_small]:!block [&_small]:text-[10px] [&_small]:font-normal [&_small]:!text-disabled">TỔNG TIỀN<small>(Đã bao gồm VAT và được làm tròn)</small></span>
            <strong class="whitespace-nowrap text-right text-lg !text-price [&_.amount]:!text-price [&_.amount]:!font-bold" data-cart-total><?php wc_cart_totals_order_total_html(); ?></strong>
        </div>

    </div>

    <?php do_action('woocommerce_after_cart_totals'); ?>
</div>
