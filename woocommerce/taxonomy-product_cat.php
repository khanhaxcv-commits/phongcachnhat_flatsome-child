<?php

/**
 * Template danh mục sản phẩm custom.
 *
 * Bỏ qua bộ chọn Catalog Layout của Flatsome và luôn dùng UI danh mục riêng
 * của child theme. WooCommerce vẫn quản lý main query, sắp xếp, visibility và
 * trạng thái product loop.
 *
 * @package Phongcachnhat
 */

defined('ABSPATH') || exit;

get_header('shop');

get_template_part('template-parts/components/product-archive');

get_footer('shop');
