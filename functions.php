<?php

$theme_includes = array(
    'inc/cleanup/contact-form-7.php',
    // 'inc/cleanup/disable-wpautop.php',
    'inc/cleanup/editor.php',
    'inc/fonts.php',

    'inc/blog/video-icon.php',
    'inc/blog/single-post-header.php',

    'inc/rewrite/flat-category-urls.php',

    'inc/admin/rank-math-shop-manager.php',
    'inc/admin/woocommerce-admin-header.php',

    'inc/woocommerce/checkout-fields.php',
    'inc/woocommerce/product-tabs.php',
    'inc/woocommerce/installation-gallery.php',
    'inc/woocommerce/product-description-readmore.php',
    'inc/woocommerce/product-stock-ordering.php',
    'inc/woocommerce/disable-structured-data.php',
    'inc/woocommerce/product-attributes.php',
    'inc/woocommerce/product-group.php',
    'inc/woocommerce/daily-countdown.php',
    'inc/woocommerce/category-banner-subcategories.php',
    'inc/woocommerce/recently-viewed-products.php',
    'inc/woocommerce/product-specifications.php',
    'inc/woocommerce/product-video.php',
    'inc/woocommerce/product-price-meta.php',
    'inc/woocommerce/category-description-readmore.php',
    'inc/woocommerce/brand-description-editor.php',
    'inc/woocommerce/product-subcategory-shortcode.php',
    'inc/woocommerce/product-affiliate-links.php',

    'inc/enqueue-theme-styles.php',
    // 'inc/enqueue-external-assets.php',
    // 'inc/enqueue-vendor-scripts.php',
    'inc/enqueue-theme-scripts.php',
    'inc/enqueue-fontawesome.php',

    // 'inc/breadcrumbs.php',
    // 'inc/modules/blog-category-archive.php',
    // 'inc/modules/blog-single.php',
    // 'inc/blog.php',
    'inc/preloader.php',
    'inc/migration.php',
    // 'inc/product-filter-test.php',
    '/inc/product-filter-functions.php',
    '/inc/product-filter-render.php',
    '/inc/product-filter-query.php'
);

foreach ($theme_includes as $theme_include) {

    $theme_file = get_stylesheet_directory() . '/' . $theme_include;

    if (file_exists($theme_file)) {

        require_once $theme_file;
    }
}



//add_action( 'phpmailer_init', function( $phpmailer ) {
//    if(!is_object( $phpmailer )) return;
//    $phpmailer = (object) $phpmailer;
////    $phpmailer->Mailer     = 'smtp';
//    $phpmailer->Host       = 'smtp.gmail.com';
//    $phpmailer->SMTPAuth   = 1;
//    $phpmailer->Port       = 587;
//    $phpmailer->Username   = 'tuananhphan25185@gmail.com';
//    $phpmailer->Password   = 'gvnshyzvostcfrrh';
//    $phpmailer->SMTPSecure = 'TLS';
//    $phpmailer->From       = 'tuananhphan25185@gmail.com';
//    $phpmailer->FromName   = 'Đơn hàng từ Phongcachnhat.vn';
//});
