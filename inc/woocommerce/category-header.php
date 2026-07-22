<?php

defined('ABSPATH') || exit;

add_action('wp', 'register_category_header_hooks');

function register_category_header_hooks()
{
    if (!is_product_category()) {
        return;
    }

    remove_action(
        'flatsome_after_header',
        'flatsome_category_header',
        10
    );

    add_action(
        'flatsome_after_header',
        'render_category_header',
        20
    );
}
/**
 * Render layout header danh mục.
 */
function render_category_header()
{
?>
    <div class="row container !mt-5">

        <div>
            <?php do_action('category_breadcrumb'); ?>
        </div>

        <div class="category-intro">

            <div class="category-intro__content">
                <?php

                if (fl_woocommerce_version_check('8.8.0')) {
                    do_action('woocommerce_shop_loop_header');
                } else {
                    do_action('woocommerce_archive_description');
                }
                ?>
            </div>

            <div class="category-intro__banner">
                <?php do_action('category_banner'); ?>
            </div>

        </div>

        <div>
            <?php do_action('category_subcategories'); ?>
        </div>

    </div>
<?php
}
