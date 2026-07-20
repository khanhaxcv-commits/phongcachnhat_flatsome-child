<?php

$theme_includes = array(
    // 'inc/cleanup/contact-form-7.php',
    // 'inc/cleanup/disable-wpautop.php',
    // 'inc/fonts.php',

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
    // 'inc/preloader.php',
    // 'inc/migration.php',
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

// css page ---------------------------------------------

function phongcachnhat_enqueue_styles()
{
    wp_enqueue_style(
        'plana-font-body',
        'https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,100..900;1,100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Great+Vibes&display=swap',
        array(),
        null
    );

    // wp_enqueue_style(
    //     'plana-font-heading',
    //     'https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap',
    //     array(),
    //     null
    // );
    // wp_enqueue_style(
    //     'phongcachnhat-font-merriweather',
    //     'https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700;900&display=swap',
    //     array(),
    //     null
    // );

    wp_enqueue_style(
        'phongcachnhat-reset',
        get_stylesheet_directory_uri() . '/reset.css',
        array(),
        filemtime(get_stylesheet_directory() . '/reset.css')
    );

    wp_enqueue_style(
        'phongcachnhat-style',
        get_stylesheet_uri(),
        array('phongcachnhat-reset', 'plana-font-body'),
        filemtime(get_stylesheet_directory() . '/style.css')
    );

    // Single post page
    if (is_singular('post')) {
        wp_enqueue_style(
            'phongcachnhat-blog',
            get_stylesheet_directory_uri() . '/assets/blog.css',
            array('phongcachnhat-style'),
            filemtime(get_stylesheet_directory() . '/assets/blog.css')
        );
    }

    // Category archive page
    if (is_category()) {
        wp_enqueue_style(
            'phongcachnhat-category',
            get_stylesheet_directory_uri() . '/assets/category.css',
            array('phongcachnhat-style'),
            filemtime(get_stylesheet_directory() . '/assets/category.css')
        );
    }


    if (is_front_page() || is_home() || is_page('trang-chu')) {
        wp_enqueue_style(
            'phongcachnhat-trang-chu-1',
            get_stylesheet_directory_uri() . '/assets/trang-chu-1.css',
            array('phongcachnhat-style'),
            filemtime(get_stylesheet_directory() . '/assets/trang-chu-1.css')
        );
    }

    if (is_page('lien-he')) {
        wp_enqueue_style(
            'phongcachnhat-lien-he-1',
            get_stylesheet_directory_uri() . '/assets/lien-he-1.css',
            array('phongcachnhat-style'),
            filemtime(get_stylesheet_directory() . '/assets/lien-he-1.css')
        );
    }

    if (function_exists('is_product') && is_product()) {
        wp_enqueue_style(
            'phongcachnhat-san-pham',
            get_stylesheet_directory_uri() . '/assets/san-pham.css',
            array('phongcachnhat-style'),
            filemtime(get_stylesheet_directory() . '/assets/san-pham.css')
        );
    }


    // add js 
    wp_enqueue_script(
        'phongcachnhat-custom',
        get_stylesheet_directory_uri() . '/assets/js/custom.js',
        array(),
        filemtime(get_stylesheet_directory() . '/assets/js/custom.js'),
        true
    );
    wp_enqueue_script(
        'phongcachnhat-prod-short-description',
        get_stylesheet_directory_uri() . '/assets/js/product-short-description.js',
        array(),
        filemtime(get_stylesheet_directory() . '/assets/js/product-short-description.js'),
        true
    );
}

add_action('wp_enqueue_scripts', 'phongcachnhat_enqueue_styles');

// --------------------------------------------------------------------------------


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
