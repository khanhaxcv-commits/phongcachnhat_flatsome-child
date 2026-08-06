<?php

/**
 * Product catalog ordering controls.
 */

defined('ABSPATH') || exit;

/**
 * Render bộ sắp xếp sản phẩm.
 */
function render_custom_catalog_ordering()
{
    $current_order = isset($_GET['orderby'])
        ? wc_clean(wp_unslash($_GET['orderby']))
        : apply_filters(
            'woocommerce_default_catalog_orderby',
            get_option('woocommerce_default_catalog_orderby', 'menu_order')
        );

    $base_url = remove_query_arg('orderby');

    $orders = [
        'rating'     => 'Mặc định',
        'popularity' => 'Bán chạy',
        'date'       => 'Mới nhất',
        'price'      => 'Giá thấp - cao',
        'price-desc' => 'Giá cao - thấp',
    ];
?>

    <div class="custom-product-ordering flex items-center gap-3">

        <label class="ordering-label m-0 shrink-0 text-[14px] font-medium !text-soft">
            Sắp xếp theo:
        </label>

        <div class="ordering-select-wrap relative w-[240px]">

            <select
                class="ordering-select !m-0 !h-11 !w-full !rounded-lg !border !border-input !bg-surface-muted !px-4 !text-[14px] font-medium !text-input !shadow-none outline-none !transition !duration-200 hover:!border-accent-soft hover:!bg-surface-accent focus:!border-input-focus focus:!bg-surface focus:!ring-4 focus:!ring-ui"
                onchange="if(this.value){window.location.href=this.value;}"
                aria-label="Sắp xếp sản phẩm">

                <?php foreach ($orders as $key => $label) : ?>

                    <?php
                    $url = add_query_arg(
                        'orderby',
                        $key,
                        $base_url
                    );
                    ?>

                    <option
                        value="<?php echo esc_url($url); ?>"
                        <?php selected($current_order, $key); ?>>

                        <?php echo esc_html($label); ?>

                    </option>

                <?php endforeach; ?>

            </select>

        </div>

    </div>

<?php
}
