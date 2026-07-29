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
<div class="custom-cart-shell mx-auto w-full max-w-none pb-0 text-sm !text-main">
    <div class="custom-cart-topbar sticky top-0 z-40 mb-1 !flex min-h-11 flex-wrap items-center justify-center rounded-none border-b border-ui bg-surface px-4 py-2 desktop:static desktop:z-auto desktop:mb-3 desktop:justify-start desktop:rounded-xl desktop:border-b-0">
        <a class="custom-cart-back !absolute left-4 !inline-flex items-center gap-2 font-semibold !text-main hover:!text-primary-hover desktop:!static"
            href="javascript:void(0)"
            onclick="history.back();">
            <i class="fa-solid fa-chevron-left text-xs" aria-hidden="true"></i>
            <span class="!hidden desktop:!inline">Quay lại</span>
        </a>
        <span class="custom-cart-divider mx-3 !hidden !text-disabled desktop:!inline">/</span>
        <span class="custom-cart-current text-base font-bold !text-main desktop:text-sm desktop:font-normal desktop:!text-sub">Giỏ hàng của bạn</span>
    </div>

    <form class="woocommerce-cart-form custom-cart-layout !m-0 !rounded-none !border-0 !bg-transparent !p-0 !shadow-none desktop:!grid desktop:grid-cols-[minmax(0,2fr)_minmax(330px,1fr)] desktop:gap-3" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
        <section class="custom-cart-products min-w-0">
            <div class="custom-cart-selectbar mb-1 !flex min-h-11 items-center justify-start rounded-none border-b border-ui-strong bg-surface px-3 py-1.5 desktop:mb-3 desktop:min-h-14 desktop:justify-between desktop:rounded-xl desktop:border-b-0 desktop:py-2">
                <label class="!m-0 !hidden items-center gap-2.5 font-medium desktop:!flex"><input type="checkbox" class="custom-cart-select-all" checked> <span>Tất cả (<?php echo esc_html(count(WC()->cart->get_cart())); ?>)</span></label>
                <button type="submit" class="custom-cart-buy-outline !m-0 min-h-8 rounded-full !border !border-button-outline bg-surface px-[18px] font-bold normal-case !text-button-outline hover:bg-button-outline-hover hover:!text-button-outline-hover desktop:min-h-[34px] desktop:px-5" name="custom_cart_checkout_selected" value="1">Mua ngay</button>
            </div>

            <?php do_action('woocommerce_before_cart_table'); ?>
            <div class="custom-cart-list woocommerce-cart-form__contents !grid gap-1 desktop:gap-3">
                <?php do_action('woocommerce_before_cart_contents'); ?>
                <?php foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) :
                    $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
                    $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);
                    if (!($_product instanceof WC_Product) || !$_product->exists() || $cart_item['quantity'] <= 0 || !apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) continue;
                    $product_name = apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key);
                    $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
                    $regular_price = (float) $_product->get_regular_price();
                    $current_price = (float) $_product->get_price();
                    $quantity = max(1, (int) $cart_item['quantity']);
                    $min_quantity = 1;
                    $max_quantity = $_product->is_sold_individually() ? 1 : (int) $_product->get_max_purchase_quantity();
                    $minus_disabled = $quantity <= $min_quantity;
                    $plus_disabled = 1 === $max_quantity || ($max_quantity > 0 && $quantity >= $max_quantity);
                    $cart_item_class = apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key);
                    if ($minus_disabled) $cart_item_class .= ' is-min-quantity';
                ?>
                    <article class="custom-cart-item !grid min-h-[98px] grid-cols-[20px_54px_minmax(0,1fr)] items-center gap-2 rounded-none bg-surface px-2 py-[9px] transition-opacity desktop:min-h-[92px] desktop:grid-cols-[22px_64px_minmax(160px,1fr)_130px_130px] desktop:gap-2.5 desktop:rounded-xl desktop:px-3 desktop:py-3 <?php echo esc_attr($cart_item_class); ?>" data-current-price="<?php echo esc_attr($current_price); ?>" data-regular-price="<?php echo esc_attr(max($regular_price, $current_price)); ?>">
                        <label class="custom-cart-item-check !m-0" aria-label="<?php echo esc_attr(sprintf(__('Select %s', 'woocommerce'), wp_strip_all_tags($product_name))); ?>">
                            <input type="checkbox" name="custom_cart_selected_items[]" value="<?php echo esc_attr($cart_item_key); ?>" checked>
                        </label>
                        <div class="custom-cart-item-image [&_img]:m-0 [&_img]:h-[54px] [&_img]:w-[54px] [&_img]:object-contain desktop:[&_img]:h-16 desktop:[&_img]:w-16">
                            <?php $thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image('woocommerce_thumbnail'), $cart_item, $cart_item_key); ?>
                            <?php echo $product_permalink ? sprintf('<a href="%s">%s</a>', esc_url($product_permalink), $thumbnail) : $thumbnail; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
                            ?>
                        </div>
                        <div class="custom-cart-item-info min-w-0">
                            <h3 class="!mb-1 text-[13px] font-bold leading-snug desktop:text-sm"><?php echo $product_permalink ? sprintf('<a class="!text-main hover:!text-primary-hover" href="%s">%s</a>', esc_url($product_permalink), wp_kses_post($product_name)) : wp_kses_post($product_name); ?></h3>
                            <?php do_action('woocommerce_after_cart_item_name', $cart_item, $cart_item_key); ?>
                            <?php echo wc_get_formatted_cart_item_data($cart_item); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
                            ?>
                        </div>
                        <div class="custom-cart-item-price col-start-3 text-left desktop:col-auto desktop:text-right">
                            <strong class="!block whitespace-nowrap text-sm !text-price [&_.amount]:!text-price [&_.amount]:!font-bold"><?php echo wp_kses_post(WC()->cart->get_product_price($_product)); ?></strong>
                            <?php if ($regular_price > $current_price) : ?><del class="!block text-xs !text-price-old [&_.amount]:!text-price-old [&_.amount]:!font-normal"><?php echo wp_kses_post(wc_price($regular_price)); ?></del><?php endif; ?>
                        </div>
                        <div class="custom-cart-item-actions col-span-2 col-start-2 !flex items-center justify-end desktop:col-auto desktop:col-span-1 desktop:col-start-auto">
                            <div class="custom-cart-quantity m-0 !grid h-[34px] w-[110px] grid-cols-[repeat(3,34px)] items-center gap-1" data-min="<?php echo esc_attr($min_quantity); ?>" data-max="<?php echo esc_attr($max_quantity); ?>">
                                <span class="custom-cart-quantity-leading relative !grid h-[34px] w-[34px] place-items-center">
                                    <?php echo apply_filters('woocommerce_cart_item_remove_link', sprintf('<a role="button" href="%s" class="custom-cart-remove remove" aria-label="%s" data-product_id="%s" data-product_sku="%s"><i class="fa-solid fa-trash-can" aria-hidden="true"></i></a>', esc_url(wc_get_cart_remove_url($cart_item_key)), esc_attr(sprintf(__('Remove %s from cart', 'woocommerce'), wp_strip_all_tags($product_name))), esc_attr($product_id), esc_attr($_product->get_sku())), $cart_item_key); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
                                    ?>
                                    <button type="button" class="custom-cart-quantity-button custom-cart-quantity-minus" aria-label="Giảm số lượng" <?php disabled($minus_disabled); ?>>
                                        <i class="fa-solid fa-minus" aria-hidden="true"></i>
                                    </button>
                                </span>
                                <input type="hidden" class="custom-cart-quantity-input" name="cart[<?php echo esc_attr($cart_item_key); ?>][qty]" value="<?php echo esc_attr($quantity); ?>">
                                <span class="custom-cart-quantity-value !grid h-[34px] w-[34px] min-w-[34px] place-items-center bg-surface text-center text-sm font-semibold leading-none !text-main" aria-live="polite"><?php echo esc_html($quantity); ?></span>
                                <button type="button" class="custom-cart-quantity-button custom-cart-quantity-plus" aria-label="Tăng số lượng" <?php disabled($plus_disabled); ?>>
                                    <i class="fa-solid fa-plus" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
                <?php do_action('woocommerce_cart_contents'); ?>
            </div>

            <button type="submit" class="button custom-cart-update sr-only" name="update_cart" value="<?php esc_attr_e('Update cart', 'woocommerce'); ?>"><?php esc_html_e('Update cart', 'woocommerce'); ?></button>
            <?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>
            <?php wp_nonce_field('custom-cart-checkout-selected', 'custom_cart_nonce'); ?>
            <?php do_action('woocommerce_after_cart_contents'); ?>
            <?php do_action('woocommerce_after_cart_table'); ?>
        </section>

        <aside class="custom-cart-summary pointer-events-none !fixed inset-x-0 bottom-[66px] !z-[110] min-w-0 desktop:!static desktop:!z-auto desktop:pointer-events-auto">
            <div class="custom-cart-summary-card rounded-none border-0 bg-transparent p-0 shadow-none desktop:sticky desktop:top-3 desktop:rounded-xl desktop:bg-surface desktop:p-4">
                <div class="custom-cart-mobile-sheet-header">
                    <h3 class="!m-0 w-full text-center text-base font-bold">Chi tiết giá</h3>
                    <button type="button" class="custom-cart-mobile-sheet-close absolute right-[5px] !m-0 !grid h-9 w-9 place-items-center border-0 bg-transparent p-0 text-xl !text-main" aria-label="Đóng chi tiết giá"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
                </div>
                <h2 class="!mb-3.5 text-[15px] font-bold normal-case">Thông tin đơn hàng</h2>
                <?php if (wc_coupons_enabled()) : ?>
                    <div class="custom-cart-coupon-form !fixed inset-x-0 bottom-[66px] !z-[120] !m-0 !grid grid-cols-[minmax(0,1fr)_auto] gap-1.5 bg-surface p-2 shadow-[0_-8px_24px_rgba(var(--text-rgb),0.14)] desktop:!static desktop:!z-auto desktop:mb-2 desktop:p-0 desktop:shadow-none">
                        <input type="text" name="coupon_code" class="input-text !m-0 !h-[38px] !min-h-[38px] !w-full !rounded-md !border-input !bg-input !px-3 !text-sm !text-input !shadow-none placeholder:!text-input focus:!border-input-focus focus:!ring-4 focus:!ring-ui" placeholder="Nhập mã giảm giá">
                        <button type="submit" class="button !m-0 h-[38px] min-h-[38px] rounded-md !border-button-primary bg-button-primary px-3 !text-button-primary shadow-none hover:bg-button-primary-hover hover:!text-button-primary disabled:!border-disabled disabled:bg-disabled disabled:!text-disabled disabled:opacity-100 disabled:cursor-not-allowed" name="apply_coupon" value="Áp dụng" disabled>Áp dụng</button>
                        <?php do_action('woocommerce_cart_coupon'); ?>
                    </div>
                <?php endif; ?>
                <div class="custom-cart-summary-count min-h-[42px] items-center justify-between desktop:!flex"><span>Số lượng sản phẩm</span><strong data-selected-count><?php echo esc_html(WC()->cart->get_cart_contents_count()); ?></strong></div>
                <?php woocommerce_cart_totals(); ?>
                <button type="submit" class="custom-cart-checkout-button !m-0 mt-2.5 min-h-12 w-full rounded-lg border-0 bg-button-primary font-bold !text-button-primary hover:bg-button-primary-hover hover:!text-button-primary" name="custom_cart_checkout_selected" value="1">MUA NGAY (<span data-selected-count><?php echo esc_html(WC()->cart->get_cart_contents_count()); ?></span>)</button>
            </div>
        </aside>

        <button type="button" class="custom-cart-mobile-backdrop" aria-label="Đóng chi tiết giá"></button>
        <div class="custom-cart-mobile-bar !fixed inset-x-0 bottom-0 !z-[140] !grid min-h-[66px] grid-cols-[minmax(82px,auto)_minmax(92px,1fr)_minmax(126px,36%)] items-center gap-1.5 border-t border-ui bg-surface px-2 py-[7px] shadow-[0_-5px_18px_rgba(var(--text-rgb),0.12)] desktop:!hidden">
            <label class="custom-cart-mobile-select !m-0 !flex items-center gap-1 whitespace-nowrap text-xs"><input type="checkbox" class="custom-cart-select-all" checked><span>Tất cả (<?php echo esc_html(count(WC()->cart->get_cart())); ?>)</span></label>
            <button type="button" class="custom-cart-mobile-total-toggle !m-0 !flex min-w-0 flex-col items-end !border-0 bg-transparent p-0 !text-main normal-case leading-[1.2] !shadow-none" aria-expanded="false">
                <span class="custom-cart-mobile-total-row !flex items-center gap-[5px] text-base !text-price"><strong data-cart-total><?php echo wp_kses_post(WC()->cart->get_total()); ?></strong><i class="fa-solid fa-chevron-up" aria-hidden="true"></i></span>
            </button>
            <button type="submit" class="custom-cart-mobile-checkout !m-0 min-h-[42px] w-full rounded-lg !border-0 bg-button-primary px-2 text-sm font-bold normal-case !text-button-primary !shadow-none hover:bg-button-primary-hover hover:!text-button-primary" name="custom_cart_checkout_selected" value="1">Mua ngay (<span data-selected-count><?php echo esc_html(WC()->cart->get_cart_contents_count()); ?></span>)</button>
        </div>
    </form>
</div>
<?php do_action('woocommerce_after_cart'); ?>
