<?php
/**
 * Cart Page
 *
 * @package WooCommerce\Templates
 * @version 10.8.0
 */

defined('ABSPATH') || exit;

do_action('woocommerce_before_cart');
wc_print_notices();
?>
<div class="custom-cart-shell mx-auto pb-8 text-sm text-gray-900">
    <div class="custom-cart-topbar mb-3 flex min-h-11 flex-wrap items-center rounded-[14px] bg-white px-4 py-2">
        <a class="custom-cart-back inline-flex items-center gap-2 font-semibold text-gray-900 hover:text-red-600" href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>"><i class="fa-solid fa-chevron-left text-xs" aria-hidden="true"></i><span>Tiếp tục mua sắm</span></a>
        <span class="custom-cart-divider mx-3 text-gray-300">/</span>
        <span class="custom-cart-current text-gray-400">Giỏ hàng của bạn</span>
        <div class="custom-cart-shipping-note mt-2 w-full rounded-md bg-blue-100 px-3 py-1.5 text-center sm:ml-auto sm:mt-0 sm:w-[390px]"><strong class="text-blue-600">Miễn phí vận chuyển</strong> với đơn hàng từ 300.000đ</div>
    </div>

    <form class="woocommerce-cart-form custom-cart-layout !m-0 !border-0 !bg-transparent !p-0 !shadow-none lg:grid lg:grid-cols-[minmax(0,2fr)_minmax(330px,1fr)] lg:gap-3" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
        <section class="custom-cart-products min-w-0">
            <div class="custom-cart-selectbar mb-3 flex min-h-14 items-center justify-between rounded-[14px] bg-white px-4 py-2">
                <label class="!m-0 flex items-center gap-2.5 font-medium"><input type="checkbox" class="custom-cart-select-all" checked> <span>Tất cả (<?php echo esc_html(count(WC()->cart->get_cart())); ?>)</span></label>
                <button type="submit" class="custom-cart-buy-outline !m-0 min-h-[34px] rounded-full border border-blue-600 bg-white px-5 font-bold normal-case text-blue-600 hover:bg-blue-50" name="custom_cart_checkout_selected" value="1">Mua ngay</button>
            </div>

            <?php do_action('woocommerce_before_cart_table'); ?>
            <div class="custom-cart-list woocommerce-cart-form__contents grid gap-3">
                <?php do_action('woocommerce_before_cart_contents'); ?>
                <?php foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) :
                    $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
                    $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);
                    if (!($_product instanceof WC_Product) || !$_product->exists() || $cart_item['quantity'] <= 0 || !apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) continue;
                    $product_name = apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key);
                    $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
                    $regular_price = (float) $_product->get_regular_price();
                    $current_price = (float) $_product->get_price();
                ?>
                    <article class="custom-cart-item grid min-h-[92px] grid-cols-[20px_54px_minmax(0,1fr)] items-center gap-2 rounded-[14px] bg-white px-2.5 py-3 transition-opacity sm:grid-cols-[22px_64px_minmax(160px,1fr)_130px_130px] sm:gap-2.5 sm:px-3 <?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?>" data-current-price="<?php echo esc_attr($current_price); ?>" data-regular-price="<?php echo esc_attr(max($regular_price, $current_price)); ?>">
                        <label class="custom-cart-item-check !m-0" aria-label="<?php echo esc_attr(sprintf(__('Select %s', 'woocommerce'), wp_strip_all_tags($product_name))); ?>">
                            <input type="checkbox" name="custom_cart_selected_items[]" value="<?php echo esc_attr($cart_item_key); ?>" checked>
                        </label>
                        <div class="custom-cart-item-image [&_img]:m-0 [&_img]:h-[54px] [&_img]:w-[54px] [&_img]:object-contain sm:[&_img]:h-16 sm:[&_img]:w-16">
                            <?php $thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image('woocommerce_thumbnail'), $cart_item, $cart_item_key); ?>
                            <?php echo $product_permalink ? sprintf('<a href="%s">%s</a>', esc_url($product_permalink), $thumbnail) : $thumbnail; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        </div>
                        <div class="custom-cart-item-info min-w-0">
                            <h3 class="!mb-1 text-[13px] font-bold leading-snug sm:text-sm"><?php echo $product_permalink ? sprintf('<a class="text-black" href="%s">%s</a>', esc_url($product_permalink), wp_kses_post($product_name)) : wp_kses_post($product_name); ?></h3>
                            <?php do_action('woocommerce_after_cart_item_name', $cart_item, $cart_item_key); ?>
                            <?php echo wc_get_formatted_cart_item_data($cart_item); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        </div>
                        <div class="custom-cart-item-price col-start-3 text-left sm:col-auto sm:text-right">
                            <strong class="block whitespace-nowrap text-sm text-red-600 [&_.amount]:!text-red-600 [&_.amount]:!font-bold"><?php echo wp_kses_post(WC()->cart->get_product_price($_product)); ?></strong>
                            <?php if ($regular_price > $current_price) : ?><del class="block text-xs text-gray-400 [&_.amount]:!text-gray-400 [&_.amount]:!font-normal"><?php echo wp_kses_post(wc_price($regular_price)); ?></del><?php endif; ?>
                        </div>
                        <div class="custom-cart-item-actions col-span-2 col-start-2 flex items-center justify-end gap-2 sm:col-auto">
                            <?php echo apply_filters('woocommerce_cart_item_remove_link', sprintf('<a role="button" href="%s" class="custom-cart-remove remove" aria-label="%s" data-product_id="%s" data-product_sku="%s"><i class="fa-solid fa-trash-can" aria-hidden="true"></i></a>', esc_url(wc_get_cart_remove_url($cart_item_key)), esc_attr(sprintf(__('Remove %s from cart', 'woocommerce'), wp_strip_all_tags($product_name))), esc_attr($product_id), esc_attr($_product->get_sku())), $cart_item_key); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                            <?php
                            $quantity = max(1, (int) $cart_item['quantity']);
                            $min_quantity = 1;
                            $max_quantity = $_product->is_sold_individually() ? 1 : (int) $_product->get_max_purchase_quantity();
                            $minus_disabled = $quantity <= $min_quantity;
                            $plus_disabled = 1 === $max_quantity || ($max_quantity > 0 && $quantity >= $max_quantity);
                            ?>
                            <div class="custom-cart-quantity" data-min="<?php echo esc_attr($min_quantity); ?>" data-max="<?php echo esc_attr($max_quantity); ?>">
                                <button type="button" class="custom-cart-quantity-button custom-cart-quantity-minus" aria-label="Giảm số lượng"<?php disabled($minus_disabled); ?>>
                                    <i class="fa-solid fa-minus" aria-hidden="true"></i>
                                </button>
                                <input type="hidden" class="custom-cart-quantity-input" name="cart[<?php echo esc_attr($cart_item_key); ?>][qty]" value="<?php echo esc_attr($quantity); ?>">
                                <span class="custom-cart-quantity-value" aria-live="polite"><?php echo esc_html($quantity); ?></span>
                                <button type="button" class="custom-cart-quantity-button custom-cart-quantity-plus" aria-label="Tăng số lượng"<?php disabled($plus_disabled); ?>>
                                    <i class="fa-solid fa-plus" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
                <?php do_action('woocommerce_cart_contents'); ?>
            </div>

            <button type="submit" class="button custom-cart-update" name="update_cart" value="<?php esc_attr_e('Update cart', 'woocommerce'); ?>"><?php esc_html_e('Update cart', 'woocommerce'); ?></button>
            <?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>
            <?php wp_nonce_field('custom-cart-checkout-selected', 'custom_cart_nonce'); ?>
            <?php do_action('woocommerce_after_cart_contents'); ?>
            <?php do_action('woocommerce_after_cart_table'); ?>
        </section>

        <aside class="custom-cart-summary mt-3 min-w-0 lg:mt-0">
            <div class="custom-cart-summary-card rounded-[14px] bg-white p-4 lg:sticky lg:top-3">
                <h2 class="!mb-3.5 text-[15px] font-bold normal-case">Thông tin đơn hàng</h2>
                <?php if (wc_coupons_enabled()) : ?>
                    <div class="custom-cart-coupon-row mb-1.5 flex min-h-[42px] items-center justify-between rounded-lg bg-gray-50 px-2 py-1.5">
                        <span class="flex items-center gap-2"><i class="fa-solid fa-ticket text-red-600" aria-hidden="true"></i> Áp dụng mã giảm giá</span>
                        <button type="button" class="custom-cart-coupon-toggle !m-0 rounded-full border-0 bg-red-50 px-2.5 py-1 text-xs normal-case text-red-600">Chọn</button>
                    </div>
                    <div class="custom-cart-coupon-form" hidden>
                        <input type="text" name="coupon_code" class="input-text" placeholder="Nhập mã giảm giá">
                        <button type="submit" class="button" name="apply_coupon" value="Áp dụng" disabled>Áp dụng</button>
                        <?php do_action('woocommerce_cart_coupon'); ?>
                    </div>
                <?php endif; ?>
                <div class="custom-cart-summary-count flex min-h-[42px] items-center justify-between"><span>Số lượng sản phẩm</span><strong data-selected-count><?php echo esc_html(WC()->cart->get_cart_contents_count()); ?></strong></div>
                <?php woocommerce_cart_totals(); ?>
                <button type="submit" class="custom-cart-checkout-button !m-0 mt-2.5 min-h-12 w-full rounded-lg border-0 bg-red-600 font-bold text-white hover:bg-red-700" name="custom_cart_checkout_selected" value="1">MUA NGAY (<span data-selected-count><?php echo esc_html(WC()->cart->get_cart_contents_count()); ?></span>)</button>
            </div>
        </aside>
    </form>
</div>
<?php do_action('woocommerce_after_cart'); ?>
