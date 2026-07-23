<?php

/**
 * Category layout with no sidebar.
 *
 * @package          Flatsome/WooCommerce/Templates
 * @flatsome-version 3.18.7
 */

defined('ABSPATH') || exit;

$current_page = max(
    1,
    absint(wc_get_loop_prop('current_page'))
);

$per_page = max(
    1,
    absint(wc_get_loop_prop('per_page'))
);

$total_products = max(
    0,
    absint(wc_get_loop_prop('total'))
);

$total_pages = max(
    1,
    absint(wc_get_loop_prop('total_pages'))
);
?>

<div class="row">

    <div class="col large-12">

        <?php

        /**
         * Hook: flatsome_products_before.
         */
        do_action('flatsome_products_before');

        /**
         * Hook: woocommerce_before_main_content.
         *
         * @hooked woocommerce_output_content_wrapper - 10
         * @hooked woocommerce_breadcrumb - 20
         * @hooked WC_Structured_Data::generate_website_data() - 30
         */
        do_action('woocommerce_before_main_content');

        /**
         * Custom product filter.
         */
        render_product_filter_bar();

        ?>

        <!-- ======================================
             AJAX PRODUCT RESULTS
        ======================================= -->
        <div
            id="product-filter-results"
            class="product-filter-results"
            data-category-id="<?php echo esc_attr(get_queried_object_id()); ?>"
            data-per-page="<?php echo esc_attr($per_page); ?>"
            aria-live="polite"
            aria-busy="false">

            <!-- AJAX LOADING -->
            <div
                class="product-filter-loading"
                hidden
                aria-hidden="true">

                <div class="product-filter-loading__spinner"></div>

            </div>

            <!-- AJAX CONTENT -->
            <div class="product-filter-results__content">

                <?php if (woocommerce_product_loop()) : ?>

                    <?php

                    /**
                     * Hook: woocommerce_before_shop_loop.
                     *
                     * @hooked wc_print_notices - 10
                     * @hooked woocommerce_result_count - 20
                     * @hooked woocommerce_catalog_ordering - 30
                     */
                    do_action('woocommerce_before_shop_loop');

                    /**
                     * Product loop start.
                     */
                    woocommerce_product_loop_start();

                    if (wc_get_loop_prop('total')) {

                        while (have_posts()) {

                            the_post();

                            /**
                             * Hook: woocommerce_shop_loop.
                             */
                            do_action('woocommerce_shop_loop');

                            wc_get_template_part(
                                'content',
                                'product'
                            );
                        }
                    }

                    /**
                     * Product loop end.
                     */
                    woocommerce_product_loop_end();

                    /**
                     * Thay phân trang WooCommerce bằng nút xem thêm.
                     */
                    if (
                        function_exists('render_product_load_more_button')
                        && $current_page < $total_pages
                    ) {
                        render_product_load_more_button(
                            $current_page,
                            $total_pages,
                            $total_products,
                            $per_page
                        );
                    }

                    ?>

                <?php else : ?>

                    <?php

                    /**
                     * Hook: woocommerce_no_products_found.
                     */
                    do_action('woocommerce_no_products_found');

                    ?>

                <?php endif; ?>

            </div>

        </div>

        <?php

        /**
         * Hook: flatsome_products_after.
         */
        do_action('flatsome_products_after');

        /**
         * Hook: woocommerce_after_main_content.
         */
        do_action('woocommerce_after_main_content');

        ?>

    </div>

</div>