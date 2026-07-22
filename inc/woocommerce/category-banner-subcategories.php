<?php

defined('ABSPATH') || exit;

// Đăng ký action hooks
add_action('category_banner', 'render_category_banner', 10);
add_action('category_subcategories', 'render_category_subcategories', 10);

/**
 * Render banner danh mục.
 */
function render_category_banner()
{
    $term = get_queried_object();

    if ($term && property_exists($term, 'term_id')) {
        $term_id  = $term->term_id;
        $taxonomy = $term->taxonomy;

        // Lấy banner từ ACF field
        $banner = get_field('banner', $taxonomy . '_' . $term_id);

        if ($banner) {
            echo '<div class="">';
            echo wp_get_attachment_image($banner, 'full');
            echo '</div>';
        }
    }
}

/**
 * Render các danh mục con.
 */
function render_category_subcategories()
{
    if (is_product_category()) {
        $current_cat_id = 0;

        // Lấy ID danh mục hiện tại
        if (is_product_category()) {
            $current_cat    = get_queried_object();
            $current_cat_id = $current_cat->term_id;
        } elseif (is_product()) {
            $product_terms = get_the_terms(get_the_ID(), 'product_cat');

            if (!empty($product_terms)) {
                $current_cat_id = $product_terms[0]->term_id;
            }
        }

        if ($current_cat_id) {
            // Lấy tất cả danh mục con
            $subcategories = get_terms([
                'taxonomy'   => 'product_cat',
                'fields'     => 'ids',
                'hide_empty' => true,
                'child_of'   => $current_cat_id,
            ]);

            if (!empty($subcategories) && !is_wp_error($subcategories)) {
                $subcategory_ids = implode(',', $subcategories);

                echo '<div class="current-subcategories">';
                echo do_shortcode(
                    '[ux_product_categories style="default" col_spacing="normal" columns="8" ids="' .
                        $subcategory_ids .
                        '" show_count="0" image_height="100%"]'
                );
                echo '</div>';

                echo '<style>
                    .current-subcategories {
                    
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
