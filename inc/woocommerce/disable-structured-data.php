<?php

defined('ABSPATH') || exit;

/*
 * Remove the default WooCommerce 3 JSON/LD structured data format
 */
function remove_output_structured_data()
{
    remove_action('wp_footer', array(WC()->structured_data, 'output_structured_data'), 10); // Frontend pages
    remove_action('woocommerce_email_order_details', array(WC()->structured_data, 'output_email_structured_data'), 30); // Emails
}

add_action('init', 'remove_output_structured_data');
