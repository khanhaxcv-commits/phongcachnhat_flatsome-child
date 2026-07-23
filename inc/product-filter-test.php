<?php

/**
 * Test dynamic product filter data
 * Only run on product category archive
 */

// add_action('woocommerce_before_shop_loop', 'test_dynamic_filter_data', 5);

function test_dynamic_filter_data()
{


    // Chỉ chạy ở trang danh mục sản phẩm
    if (! is_product_category()) {
        return;
    }


    echo '<div style="
        background:#f5f5f5;
        padding:20px;
        margin-bottom:20px;
        border:1px solid #ddd;
        font-size:14px;
    ">';


    /**
     * 1. Lấy category hiện tại
     */
    $category = get_queried_object();


    echo '<h3>Category hiện tại</h3>';

    echo '<pre>';
    print_r([
        'ID'   => $category->term_id,
        'Name' => $category->name,
        'Slug' => $category->slug,
    ]);
    echo '</pre>';



    /**
     * 2. Lấy sản phẩm trong category
     */
    $products = new WP_Query([

        'post_type'      => 'product',

        'posts_per_page' => -1,

        'fields'         => 'ids',

        'tax_query' => [

            [
                'taxonomy' => 'product_cat',

                'field'    => 'term_id',

                'terms'    => $category->term_id,
            ]

        ]

    ]);



    $product_ids = $products->posts;



    echo '<h3>Số sản phẩm</h3>';

    echo count($product_ids);



    /**
     * 3. Lấy Brand
     */
    echo '<h3>Thương hiệu (product_brand)</h3>';

    $brands = wp_get_object_terms(

        $product_ids,

        'product_brand',

        [
            'fields' => 'names'
        ]

    );


    echo '<pre>';
    print_r(array_unique($brands));
    echo '</pre>';




    /**
     * 4. Lấy toàn bộ WooCommerce Attribute
     */
    echo '<h3>Attributes</h3>';


    $attribute_taxonomies = wc_get_attribute_taxonomies();


    foreach ($attribute_taxonomies as $attribute) {


        $taxonomy = wc_attribute_taxonomy_name(
            $attribute->attribute_name
        );


        $terms = wp_get_object_terms(

            $product_ids,

            $taxonomy,

            [
                'fields' => 'names'
            ]

        );


        if (
            ! empty($terms)
            &&
            ! is_wp_error($terms)
        ) {


            echo '<strong>';

            echo $attribute->attribute_label;

            echo '</strong>';

            echo '<pre>';

            print_r(array_unique($terms));

            echo '</pre>';
        }
    }


    echo '<h3>Dynamic Product Filters</h3>';

    echo '<pre>';

    print_r(
        get_dynamic_product_filters()
    );

    echo '</pre>';


    echo '</div>';
}
