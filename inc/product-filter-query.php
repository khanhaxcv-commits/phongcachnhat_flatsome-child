<?php

defined('ABSPATH') || exit;


add_filter(
    'woocommerce_product_query_tax_query',
    'product_filter_query'
);



function product_filter_query($tax_query)
{
    if (!is_array($tax_query)) {
        $tax_query = [];
    }

    $category_id = is_product_category()
        ? get_queried_object_id()
        : 0;

    $allowed_taxonomies = $category_id
        ? get_product_filter_taxonomies_for_category($category_id)
        : wc_get_attribute_taxonomy_names();

    $custom_filters = [];

    foreach ($_GET as $query_key => $query_value) {
        if (strpos((string) $query_key, 'cs_') === 0) {
            $custom_filters[$query_key] = $query_value;
        }
    }

    $active_filters = get_product_filter_active_filters(
        $custom_filters,
        $allowed_taxonomies
    );

    foreach ($active_filters as $taxonomy => $term_ids) {
        if (empty($term_ids)) {
            continue;
        }

        $tax_query[] = [
            'taxonomy' => $taxonomy,
            'field'    => 'term_id',
            'terms'    => $term_ids,
            'operator' => 'IN',
        ];
    }

    return $tax_query;
}

function product_filter_query_legacy($tax_query)
{


    if (empty($_GET)) {

        return $tax_query;
    }




    foreach ($_GET as $key => $value) {



        /**
         * Chỉ xử lý param filter
         *
         * cs_thuong-hieu
         * cs_dung-tich
         */
        if (
            strpos($key, 'cs_') !== 0
        ) {

            continue;
        }





        $attribute = str_replace(
            'cs_',
            '',
            $key
        );





        if (
            empty($attribute) ||
            empty($value)
        ) {

            continue;
        }





        /**
         * Convert:
         *
         * thuong-hieu
         *
         * thành:
         *
         * pa_thuong-hieu
         */
        $taxonomy =
            'pa_' . $attribute;






        /**
         * Kiểm tra taxonomy tồn tại
         */
        if (
            !taxonomy_exists($taxonomy)
        ) {

            continue;
        }






        $term_id = intval($value);




        if (!$term_id) {

            continue;
        }





        $tax_query[] = [


            'taxonomy' => $taxonomy,


            'field' => 'term_id',


            'terms' => $term_id,


        ];
    }




    return $tax_query;
}
