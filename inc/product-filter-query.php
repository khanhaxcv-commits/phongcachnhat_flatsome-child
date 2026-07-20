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
