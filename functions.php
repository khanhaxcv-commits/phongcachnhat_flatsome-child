<?php
$theme_includes = array(
    'inc/cleanup/bootstrap.php',
    'inc/blog/bootstrap.php',
    'inc/rewrite/bootstrap.php',
    'inc/admin/bootstrap.php',
);

$woocommerce_is_active = class_exists('WooCommerce');

if ($woocommerce_is_active) {
    $theme_includes[] = 'inc/woocommerce/bootstrap.php';
}

$theme_includes[] = 'inc/assets/bootstrap.php';
$theme_includes[] = 'inc/modules/bootstrap.php';
$theme_includes[] = 'inc/integrations/bootstrap.php';

if ($woocommerce_is_active) {
    $theme_includes[] = 'inc/product-filter/bootstrap.php';
}

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
