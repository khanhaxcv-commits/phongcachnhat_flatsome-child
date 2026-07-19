<?php


add_filter(
    'woocommerce_product_query_tax_query',
    'product_filter_query'
);


function product_filter_query($tax_query)
{


    if (empty($_GET)) {
        return $tax_query;
    }



    /**
     * Lấy config hiện tại
     */
    $config = get_filter_config();



    $category = get_queried_object();



    if (
        ! $category ||
        empty($config[$category->slug])
    ) {
        return $tax_query;
    }



    /**
     * Mapping:
     *
     * cs_brand
     *      ↓
     * brand
     *      ↓
     * pa_thuong-hieu
     */
    foreach ($config[$category->slug] as $key => $taxonomy) {



        $param = 'cs_' . $key;



        if (
            empty($_GET[$param])
        ) {
            continue;
        }



        $term_id = intval($_GET[$param]);



        if (!$term_id) {
            continue;
        }



        $tax_query[] = [

            'taxonomy' => $taxonomy,

            'field'    => 'term_id',

            'terms'    => $term_id,

        ];
    }



    return $tax_query;
}
