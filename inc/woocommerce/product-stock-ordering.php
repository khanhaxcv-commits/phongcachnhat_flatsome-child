<?php

defined('ABSPATH') || exit;

add_filter('woocommerce_get_catalog_ordering_args', 'ss_sp_hethang', 9999);

function ss_sp_hethang($args)
{
    $args['orderby'] = 'meta_value';
    $args['order'] = 'ASC';
    $args['meta_key'] = '_stock_status';

    return $args;
}

function tim_sp_het_hang($query)
{
    if ($query->is_search && $query->is_main_query()) {
        $query->set('post_status', array('publish', 'outofstock'));
    }
}

add_action('pre_get_posts', 'tim_sp_het_hang');

class sap_xep_san_pham
{
    public function __construct()
    {
        if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
            add_filter('posts_clauses', array($this, 'tinh_trang_sp'), 2000);
        }
    }

    public function tinh_trang_sp($posts_clauses)
    {
        global $wpdb;

        if (is_woocommerce() && (is_shop() || is_product_category() || is_product_tag())) {
            $posts_clauses['join'] .= " INNER JOIN $wpdb->postmeta istockstatus ON ($wpdb->posts.ID = istockstatus.post_id) ";
            $posts_clauses['orderby'] = " istockstatus.meta_value ASC, " . $posts_clauses['orderby'];
            $posts_clauses['where'] = " AND istockstatus.meta_key = '_stock_status' AND istockstatus.meta_value <> '' " . $posts_clauses['where'];
        }

        return $posts_clauses;
    }
}

new sap_xep_san_pham;
