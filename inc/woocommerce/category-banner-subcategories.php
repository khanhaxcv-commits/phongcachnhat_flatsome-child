<?php

defined('ABSPATH') || exit;

// hook vào danh mục sản phẩm
//add_action('woocommerce_archive_description', 'isures_after_shop_loop_item_title_func');
add_action('flatsome_after_header', 'display_current_subcategories');

function display_current_subcategories()
{

    // lấy banner đi theo ID của danh mục sản phẩm
    $term = get_queried_object();

    if ($term && property_exists($term, 'term_id')) {
        $term_id = $term->term_id;
        $taxonomy = $term->taxonomy;

        // Lấy field ACF banner
        $banner = get_field('banner', $taxonomy . '_' . $term_id);

        if ($banner) {
            echo '<div class="row category-page-row product-category-banner">';
            echo wp_get_attachment_image($banner, 'full'); // Nếu $banner là ID số
            // Hoặc nếu $banner là mảng:
            // echo wp_get_attachment_image($banner['ID'], 'full');
            echo '</div>';
        }
    }

    // Chỉ hiển thị trên trang danh mục sản phẩm hoặc trang chi tiết sản phẩm

    if (is_product_category()) {
        $current_cat_id = 0;

        // Lấy danh mục hiện tại
        if (is_product_category()) {
            $current_cat = get_queried_object();
            $current_cat_id = $current_cat->term_id;
        } elseif (is_product()) {
            $product_terms = get_the_terms(get_the_ID(), 'product_cat');

            if (!empty($product_terms)) {
                $current_cat_id = $product_terms[0]->term_id;
            }
        }

        if ($current_cat_id) {
            // Lấy tất cả danh mục con của danh mục hiện tại
            $subcategories = get_terms([
                'taxonomy' => 'product_cat',
                'fields' => 'ids',
                'hide_empty' => true,
                'child_of' => $current_cat_id
            ]);

            // Nếu có danh mục con, hiển thị chúng
            if (!empty($subcategories) && !is_wp_error($subcategories)) {
                $subcategory_ids = implode(',', $subcategories);

                echo '<div class="current-subcategories">';
                echo do_shortcode('[ux_product_categories style="default" col_spacing="normal" columns="8" ids="' . $subcategory_ids . '" show_count="0" image_height="100%"]');
                echo '</div>';

                // Thêm CSS tùy chỉnh
                echo '<style>
                    .current-subcategories {
                        margin: 20px 0;
                    }

                    .current-subcategories .product-category {
                        text-align: center;
                    }

                    .current-subcategories .product-category-title {
                        font-size: 14px;
                        margin-top: 10px;
                    }
                </style>';
            }
        }
    }
}
