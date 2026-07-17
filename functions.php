<?php

$theme_includes = array(
    'inc/cleanup/contact-form-7.php',
    'inc/cleanup/disable-wpautop.php',
    'inc/fonts.php',

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
);

foreach ($theme_includes as $theme_include) {

    $theme_file = get_stylesheet_directory() . '/' . $theme_include;

    if (file_exists($theme_file)) {

        require_once $theme_file;
    }
}
function add_rankmath_caps_to_shop_manager()
{
    $role = get_role('shop_manager'); // role Quản lý cửa hàng (WooCommerce)

    if ($role) {

        // Cho phép chỉnh SEO trên bài viết & sản phẩm
        $role->add_cap('edit_posts');
        $role->add_cap('edit_products');

        // Quan trọng: quyền dùng metabox Rank Math
        $role->add_cap('rank_math_onpage_general');
        $role->add_cap('rank_math_onpage_advanced');
        $role->add_cap('rank_math_onpage_snippet');
        $role->add_cap('rank_math_onpage_social');

        $role->add_cap('rank_math_onpage');
        $role->add_cap('rank_math_onpage_general');
        $role->add_cap('rank_math_onpage_analysis'); // QUAN TRỌNG cho chấm điểm
        $role->add_cap('rank_math_onpage_advanced');

        // Các quyền cơ bản của Rank Math
        $role->add_cap('rank_math_general');
        $role->add_cap('rank_math_titles');
        $role->add_cap('rank_math_sitemap');
        $role->add_cap('rank_math_404_monitor');
        $role->add_cap('rank_math_redirections');
        $role->add_cap('rank_math_role_manager');
        $role->add_cap('rank_math_analytics');
    }
}
add_action('init', 'add_rankmath_caps_to_shop_manager');

//widgets mặc định 
add_filter('use_widgets_block_editor', '__return_false');
/** trả bộ soạn thảo mặc định */
add_filter('use_block_editor_for_post', '__return_false');

// --------------------------------------------------------------------------------





/*Sắp xếp lại thứ tự các field*/
add_filter("woocommerce_checkout_fields", "order_fields");
function order_fields($fields)
{

    //Shipping
    $order_shipping = array(
        "shipping_last_name",
        "shipping_phone",
        "shipping_address_1"
    );
    foreach ($order_shipping as $field_shipping) {
        $ordered_fields2[$field_shipping] = $fields["shipping"][$field_shipping];
    }
    $fields["shipping"] = $ordered_fields2;
    return $fields;
}

add_filter('woocommerce_checkout_fields', 'custom_override_checkout_fields', 99);
function custom_override_checkout_fields($fields)
{
    unset($fields['billing']['billing_company']);
    unset($fields['billing']['billing_first_name']);
    unset($fields['billing']['billing_postcode']);
    unset($fields['billing']['billing_country']);
    unset($fields['billing']['billing_city']);
    unset($fields['billing']['billing_state']);
    unset($fields['billing']['billing_address_2']);
    $fields['billing']['billing_last_name'] = array(
        'label' => __('Họ và tên', 'devvn'),
        'placeholder' => _x('Nhập đầy đủ họ và tên của bạn', 'placeholder', 'devvn'),
        'required' => true,
        'class' => array('form-row-wide'),
        'clear' => true
    );
    $fields['billing']['billing_address_1']['placeholder'] = 'Ví dụ: Số xx Ngõ xx Phú Kiều, Bắc Từ Liêm, Hà Nội';

    unset($fields['shipping']['shipping_company']);
    unset($fields['shipping']['shipping_postcode']);
    unset($fields['shipping']['shipping_country']);
    unset($fields['shipping']['shipping_city']);
    unset($fields['shipping']['shipping_state']);
    unset($fields['shipping']['shipping_address_2']);

    $fields['shipping']['shipping_phone'] = array(
        'label' => __('Điện thoại', 'devvn'),
        'placeholder' => _x('Số điện thoại người nhận hàng', 'placeholder', 'devvn'),
        'required' => true,
        'class' => array('form-row-wide'),
        'clear' => true
    );
    $fields['shipping']['shipping_last_name'] = array(
        'label' => __('Họ và tên', 'devvn'),
        'placeholder' => _x('Nhập đầy đủ họ và tên của người nhận', 'placeholder', 'devvn'),
        'required' => true,
        'class' => array('form-row-wide'),
        'clear' => true
    );
    $fields['shipping']['shipping_address_1']['placeholder'] = 'Ví dụ: Số xx Ngõ xx Phú Kiều, Bắc Từ Liêm, Hà Nội';

    return $fields;
}

add_action('woocommerce_admin_order_data_after_shipping_address', 'my_custom_checkout_field_display_admin_order_meta', 10, 1);
function my_custom_checkout_field_display_admin_order_meta($order)
{
    echo '<p><strong>' . __('Số ĐT người nhận') . ':</strong> <br>' . get_post_meta($order->get_id(), '_shipping_phone', true) . '</p>';
}
add_action('woocommerce_single_product_summary', 'san', 31);
function san()
{ ?>
    <?php if (get_field('shopee') || get_field('tiki')  || get_field('lazada')): ?>
        <div class="theme-aff">
            <ul>

                <li class="text"> MUA SẢN PHẨM TẠI </li>

                <?php if (get_field('shopee')): ?>
                    <li class="shopee"><a href="<?php the_field('shopee'); ?>" target="_Blank" alt="Demo web"> <img src="/wp-content/uploads/2023/02/shopee.png" atl="logo shopee" /></a> </li>
                <?php endif; ?>
                <?php if (get_field('lazada')): ?>
                    <li class="2"> <a href="<?php the_field('lazada'); ?>" target="_Blank" alt="Demo web"> <img src="/wp-content/uploads/2023/02/Lazada_29_icon.png" atl="logo Lazada" /></a> </li>
                <?php endif; ?>
                <?php if (get_field('tiki')): ?>
                    <li class="3"> <a href="<?php the_field('tiki'); ?>" target="_Blank" alt="Demo web"> <img src="/wp-content/uploads/2023/02/tiki.png" atl="logo shopee" /></a> </li>
                <?php endif; ?>
            </ul>
        </div>
    <?php endif; ?>
    <style>
        .theme-aff ul {
            display: flex;
        }

        li.text {
            width: 80%;
        }

        .theme-aff ul li {
            display: block;
            list-style: none;
            margin-left: 1px;
            align-self: center;
        }

        .theme-aff {
            display: block;
            align-items: center;
            text-align: center;
            font-size: 112%;
        }

        .theme-aff img {
            width: 70px;
            height: auto;
            object-fit: cover;
            border-radius: 99px;
            text-align: -webkit-center;
            align-content: center;
            float: right;
            padding: 3px;
        }

        .theme-aff {
            display: flex;
            align-items: center;
            text-align: center;
            font-size: 112%;
        }

        .theme-aff .shopee img {
            width: 82px;
        }
    </style>
    <?php
}
//
//
//
//
// Xóa các tab không cần thiết
add_filter('woocommerce_product_tabs', 'remove_unnecessary_tabs', 100, 1);
function remove_unnecessary_tabs($tabs)
{
    unset($tabs['additional_information']); // Xóa tab thông tin bổ sung
    unset($tabs['reviews']);
    return $tabs;
}

// Thêm các tab tùy chỉnh
add_filter('woocommerce_product_tabs', 'custom_product_tabs');
function custom_product_tabs($tabs)
{
    // Tab Danh Muc
    $tabs['lap_dat'] = array(
        'title'     => __('Hướng dẫn sử dụng', 'woocommerce'),
        'priority'  => 20,
        'callback'  => 'woo_product_tab_lap_dat'
    );

    return $tabs;
}

// Callback function cho tab hình ảnh lắp đặt
function woo_product_tab_lap_dat()
{
    // Lấy giá trị từ field 'su_dung'
    $su_dung_value = get_field('su_dung');

    // Kiểm tra xem field có giá trị hay không
    if ($su_dung_value) {
        // Nếu có, hiển thị giá trị đó
        echo $su_dung_value;
    } else {
        // Nếu không có giá trị (trống), hiển thị 'Đang update'
        echo 'Đang update';
    }
}


/* Shortcode hiển thị hình ảnh lắp đặt */
add_shortcode('lap_dat', 'display_lap_dat_images');
function display_lap_dat_images()
{
    if (have_rows('hinh_anh')): ?>
        <div id="lapdat" class="lapdat gallery">
            <ul>
                <?php while (have_rows('hinh_anh')): the_row();
                    $ghichu = get_sub_field('ghi_chu');
                    $image = get_sub_field('hinh');
                    $content = '<li>';
                    $content .= '<a class="gallery_image" href="' . $image['url'] . '">';
                    $content .= '<img src="' . $image['sizes']['large'] . '" alt="' . $image['alt'] . '" />';
                    $content .= '</a><p>' . $ghichu . '</p>';
                    $content .= '</li>';
                    if (function_exists('slb_activate')) {
                        $content = slb_activate($content);
                    }
                    echo $content;
                endwhile; ?>
            </ul>
        </div>
    <?php else: ?>
        <p id="lapdat" class="no-data-message">Đang cập nhật thông tin</p>
    <?php endif;
}

//
//
//
//
//
//
//
//
//
add_action('wp_footer', 'devvn_readmore_flatsome');
function devvn_readmore_flatsome()
{
    ?>
    <style>
        .woocommerce-Tabs-panel--description {
            overflow: hidden;
            position: relative;
            padding-bottom: 25px;
        }

        .fix_height {
            max-height: 800px;
            overflow: hidden;
            position: relative;
        }

        .single-product .tab-panels div#tab-description.panel:not(.active) {
            height: 0 !important;
        }

        .devvn_readmore_flatsome {
            text-align: center;
            cursor: pointer;
            position: absolute;
            z-index: 10;
            bottom: 0;
            width: 100%;
            background: #fff;
        }

        .devvn_readmore_flatsome:before {
            height: 55px;
            margin-top: -45px;
            content: "";
            background: -moz-linear-gradient(top, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 1) 100%);
            background: -webkit-linear-gradient(top, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 1) 100%);
            background: linear-gradient(to bottom, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 1) 100%);
            filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffff00', endColorstr='#ffffff', GradientType=0);
            display: block;
        }

        .devvn_readmore_flatsome a {
            color: #ff5a14;
            display: block;
        }

        .devvn_readmore_flatsome a:after {
            content: '';
            width: 0;
            right: 0;
            border-top: 6px solid #ff5a14;
            border-left: 6px solid transparent;
            border-right: 6px solid transparent;
            display: inline-block;
            vertical-align: middle;
            margin: -2px 0 0 5px;
        }

        .devvn_readmore_flatsome_less a:after {
            border-top: 0;
            border-left: 6px solid transparent;
            border-right: 6px solid transparent;
            border-bottom: 6px solid #ff5a14;
        }

        .devvn_readmore_flatsome_less:before {
            display: none;
        }
    </style>
    <script>
        (function($) {
            $(document).ready(function() {
                $(window).on('load', function() {
                    if ($('.woocommerce-Tabs-panel--description').length > 0) {
                        let wrap = $('.woocommerce-Tabs-panel--description');
                        let current_height = wrap.height();
                        let your_height = 650;
                        if (current_height > your_height) {
                            wrap.addClass('fix_height');
                            wrap.append(function() {
                                return '<div class="devvn_readmore_flatsome devvn_readmore_flatsome_more"><a title="Xem thêm" href="javascript:void(0);">Xem thêm</a></div>';
                            });
                            wrap.append(function() {
                                return '<div class="devvn_readmore_flatsome devvn_readmore_flatsome_less" style="display: none;"><a title="Xem thêm" href="javascript:void(0);">Thu gọn</a></div>';
                            });
                            $('body').on('click', '.devvn_readmore_flatsome_more', function() {
                                wrap.removeClass('fix_height');
                                $('body .devvn_readmore_flatsome_more').hide();
                                $('body .devvn_readmore_flatsome_less').show();
                            });
                            $('body').on('click', '.devvn_readmore_flatsome_less', function() {
                                wrap.addClass('fix_height');
                                $('body .devvn_readmore_flatsome_less').hide();
                                $('body .devvn_readmore_flatsome_more').show();
                            });
                        }
                    }
                });
            });
        })(jQuery);
    </script>
<?php
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
//
// Hiện thị short Video icon
//hook vào sau tũa đề blog
function showhook()
{ ?>
    <?php if (get_field('video')): ?>
        <div class="hinhanh-video">
            <i class="fa-solid fa-circle-play"></i>
        </div>
    <?php endif; ?>
    <?php }
add_action('flatsome_blog_post_after', 'showhook');
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
/*
* Remove the default WooCommerce 3 JSON/LD structured data format
*/
function remove_output_structured_data()
{
    remove_action('wp_footer', array(WC()->structured_data, 'output_structured_data'), 10); // Frontend pages
    remove_action('woocommerce_email_order_details', array(WC()->structured_data, 'output_email_structured_data'), 30); // Emails
}
add_action('init', 'remove_output_structured_data');
//
//
//
//
//
//
//Thuộc tính 
function thuoc_tinh()
{
    // Hiển thị thuộc tính 1
    $thuoctinh1 = get_field('thuoctinh1');
    if ($thuoctinh1) {
        echo '<div class="danhsach"><ul class="thuoctinh">';
        foreach ($thuoctinh1 as $item) {
            $gb = $item['ten_thuoc_tinh_1'];
            $sanpham_id = $item['link_sp1'];
            $link = get_permalink($sanpham_id);
            $thumbnail = get_the_post_thumbnail($sanpham_id, 'thumbnail');

            // Lấy giá sản phẩm từ ID sản phẩm
            $product = wc_get_product($sanpham_id);
            $gia = $product ? $product->get_price() : 0;
            $gia_formatted = $gia ? number_format($gia, 0, ',', '.') . ' đ' : 'Liên hệ';

            echo '<li>';
            echo '<a href="' . $link . '">';
            if ($thumbnail) {
                echo '<div class="thuoctinh-thumbnail">' . $thumbnail . '</div>';
            }
            echo '<div class="thuoctinh-container">';
            echo '<div class="thuoctinh-info">';
            echo '<div class="thuoctinh-ten">' . $gb . '</div>';
            echo '<div class="gia-thuoctinh">' . $gia_formatted . '</div>';
            echo '</div>';
            echo '</div>';
            echo '</a>';
            echo '</li>';
        }
        echo '</ul></div>';
    }

    // Hiển thị thuộc tính 2
    $thuoctinh2 = get_field('thuoctinh2');
    if ($thuoctinh2) {
        echo '<div class="danhsach"><ul class="thuoctinh">';
        foreach ($thuoctinh2 as $item) {
            $gb2 = $item['ten_thuoc_tinh_2'];
            $sanpham_id2 = $item['link_sp2'];
            $gia2 = $item['gia_ban_2'];
            $link2 = get_permalink($sanpham_id2);
            $thumbnail2 = get_the_post_thumbnail($sanpham_id2, 'thumbnail');
            $gia_formatted2 = number_format($gia2, 0, ',', '.') . ' đ';

            echo '<li>';
            echo '<a href="' . $link2 . '">';
            if ($thumbnail2) {
                echo '<div class="thuoctinh-thumbnail">' . $thumbnail2 . '</div>';
            }
            echo '<div class="thuoctinh-container">';
            echo '<div class="thuoctinh-info">';
            echo '<div class="thuoctinh-ten">' . $gb2 . '</div>';
            echo '<div class="gia-thuoctinh">' . $gia_formatted2 . '</div>';
            echo '</div>';

            echo '</div>';
            echo '</a>';
            echo '</li>';
        }
        echo '</ul></div>';
    }
}
add_shortcode('thuoctinh', 'thuoc_tinh');
//add_action( 'woocommerce_single_product_summary', 'thuoc_tinh', 20 );
//add_action('woocommerce_before_add_to_cart_button', 'thuoc_tinh');
//
//
//
//
//
//
function nhom_san_pham()
{
    $nhom = get_field('nhom_san_pham');
    if ($nhom) {
        echo '<div class="danhsach_nhom">';
        foreach ($nhom as $sanpham) {
            $id_sp = $sanpham['link_san_pham_nhom'];
            if ($id_sp) {
                $gia = '';
                $gia_thitruiong = get_post_meta($id_sp, 'regular_price', true);
                $giaban =  get_post_meta($id_sp, '_sale_price', true);
                if ($giaban) {
                    $gia  = number_format($giaban, 0, ',', '.') . 'đ';
                } elseif ($gia_thitruiong) {
                    $gia  = number_format($gia_thitruiong, 0, ',', '.') . 'đ';
                }
                $linksp = get_permalink($id_sp);
                $tensp = $sanpham['ten_thay_the'];
                if ($sanpham['ten_thay_the']) {
                    $tensp = $sanpham['ten_thay_the'];
                } else {
                    $tensp = get_the_title($id_sp);
                }
    ?>
                <div class="nhom-item"><a href="<?php echo $linksp; ?>">
                        <div class="ten_nhoo"><?php echo $tensp; ?></div>
                        <div class="gia_nhom"><?php echo $gia; ?></div>
                    </a></div>
    <?php
            }
        }
        echo '</div>';
    }
}
add_action('woocommerce_single_product_summary', 'nhom_san_pham', 21);

//hiện thị giờ gian đếm lui
//
function daily_countdown_shortcode()
{
    // Enqueue the CSS
    wp_enqueue_style('countdown-style', plugins_url('countdown.css', __FILE__));

    // Output the HTML structure
    $output = '
    <div class="countdown-container">
        <div class="countdown-display">
            <span class="countdown-hours">00</span>:
            <span class="countdown-minutes">00</span>:
            <span class="countdown-seconds">00</span>
        </div>
    </div>';

    // Add the JavaScript
    $output .= '
    <script>
    function updateCountdown() {
        const now = new Date();
        const target = new Date();
        
        // Set target to 22:00 today
        target.setHours(22, 0, 0, 0);
        
        // If it\'s already past 22:00, set to 22:00 tomorrow
        if (now > target) {
            target.setDate(target.getDate() + 1);
        }
        
        // Calculate difference in seconds
        const diff = Math.floor((target - now) / 1000);
        
        // Calculate hours, minutes, seconds
        const hours = Math.floor(diff / 3600).toString().padStart(2, "0");
        const minutes = Math.floor((diff % 3600) / 60).toString().padStart(2, "0");
        const seconds = Math.floor(diff % 60).toString().padStart(2, "0");
        
        // Update the display
        document.querySelector(".countdown-hours").textContent = hours;
        document.querySelector(".countdown-minutes").textContent = minutes;
        document.querySelector(".countdown-seconds").textContent = seconds;
    }
    
    // Update immediately and then every second
    updateCountdown();
    setInterval(updateCountdown, 1000);
    </script>';

    return $output;
}
add_shortcode('daily_countdown', 'daily_countdown_shortcode');

// Add inline CSS (or you can put this in a separate CSS file)
function countdown_styles()
{
    echo '
    <style>
.countdown-container {
    margin-top: 15px;
}
    
    .countdown-title {
        font-size: 1.2rem;
        font-weight: 300;
        letter-spacing: 1px;
        color: rgba(255,255,255,0.9);
    }
    
.countdown-display {
    font-size: 1.7rem;
    font-weight: 700;
    font-family: "Courier New", monospace;
    background: rgba(0, 0, 0, 0.2);
    padding: 10px 9px;
    border-radius: 8px;
    display: inline-block;
    position: relative;
    overflow: hidden;
    float: right;
}   
    .countdown-display::after {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(
            90deg,
            transparent,
            rgba(255,255,255,0.1),
            transparent
        );
        animation: shine 3s infinite;
    }
    
    @keyframes shine {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }
    
    .countdown-hours, .countdown-minutes, .countdown-seconds {
        display: inline-block;
        min-width: 50px;
        text-align: center;
        background: rgba(0,0,0,0.3);
        padding: 5px;
        border-radius: 5px;
        margin: 0 2px;
    }
    
    @media (max-width: 480px) {
        .countdown-display {
            font-size: 2rem;
            padding: 10px 15px;
        }
        
        .countdown-title {
            font-size: 1rem;
        }
    }
    </style>';
}
add_action('wp_head', 'countdown_styles');

//
///
//
//
//
//
//
//
// hook vào danh mục sản phẩm
//add_action('woocommerce_archive_description', 'isures_after_shop_loop_item_title_func');
add_action('flatsome_after_header', 'display_current_subcategories');
function display_current_subcategories()
{

    // lấy banner đi theo ID của danh mục sản phẩm
    $term = get_queried_object();

    if ($term && property_exists($term, 'term_id')) {
        $term_id = $term->term_id;
        $taxonomy = $term->taxonomy;

        // Lấy field ACF banner
        $banner = get_field('banner', $taxonomy . '_' . $term_id);

        if ($banner) {
            echo '<div class="row category-page-row product-category-banner">';
            echo wp_get_attachment_image($banner, 'full'); // Nếu $banner là ID số
            // Hoặc nếu $banner là mảng:
            // echo wp_get_attachment_image($banner['ID'], 'full');
            echo '</div>';
        }
    }

    // Chỉ hiển thị trên trang danh mục sản phẩm hoặc trang chi tiết sản phẩm

    if (is_product_category()) {
        $current_cat_id = 0;

        // Lấy danh mục hiện tại
        if (is_product_category()) {
            $current_cat = get_queried_object();
            $current_cat_id = $current_cat->term_id;
        } elseif (is_product()) {
            $product_terms = get_the_terms(get_the_ID(), 'product_cat');
            if (!empty($product_terms)) {
                $current_cat_id = $product_terms[0]->term_id;
            }
        }

        if ($current_cat_id) {
            // Lấy tất cả danh mục con của danh mục hiện tại
            $subcategories = get_terms([
                'taxonomy' => 'product_cat',
                'fields' => 'ids',
                'hide_empty' => true,
                'child_of' => $current_cat_id
            ]);

            // Nếu có danh mục con, hiển thị chúng
            if (!empty($subcategories) && !is_wp_error($subcategories)) {
                $subcategory_ids = implode(',', $subcategories);

                echo '<div class="current-subcategories">';
                echo do_shortcode('[ux_product_categories style="default" col_spacing="normal" columns="8" ids="' . $subcategory_ids . '" show_count="0" image_height="100%"]');
                echo '</div>';

                // Thêm CSS tùy chỉnh
                echo '<style>
                    .current-subcategories {
                        margin: 20px 0;
                    }
                    .current-subcategories .product-category {
                        text-align: center;
                    }
                    .current-subcategories .product-category-title {
                        font-size: 14px;
                        margin-top: 10px;
                    }
                </style>';
            }
        }
    }
}


/// vùa xem
//
//
// Khởi tạo phiên làm việc - Sản phẩm vừa xem
function init_recently_viewed_products()
{
    if (!session_id()) {
        session_start();
    }
}
add_action('init', 'init_recently_viewed_products');

// Thêm sản phẩm vào danh sách đã xem
function add_to_recently_viewed($product_id)
{
    if (isset($_SESSION['recently_viewed'])) {
        // Nếu danh sách đã tồn tại, thêm sản phẩm vào
        if (!in_array($product_id, $_SESSION['recently_viewed'])) {
            $_SESSION['recently_viewed'][] = $product_id;
        }
        // Giới hạn danh sách chỉ giữ lại 10 sản phẩm
        if (count($_SESSION['recently_viewed']) > 12) {
            array_shift($_SESSION['recently_viewed']);
        }
    } else {
        // Nếu danh sách chưa có, khởi tạo danh sách
        $_SESSION['recently_viewed'] = array($product_id);
    }
}

// Gọi lại mạng và gán IDs trên vào Slider
add_action('flatsome_custom_single_product_3', 'hook_flatsomexyz');
function hook_flatsomexyz()
{
    global $product;
    add_to_recently_viewed($product->get_id());
    // Hiển thị sản phẩm vừa xem
    if (isset($_SESSION['recently_viewed'])) {
        $recently_viewed_ids = $_SESSION['recently_viewed'];

        // Chuyển đổi mảng ID thành chuỗi để sử dụng trong shortcode
        $recently_viewed_ids_string = implode(',', $recently_viewed_ids);

        // Sử dụng shortcode để hiển thị sản phẩm
        echo do_shortcode('[ux_products style="normal" columns="5" show_cat="0" show_rating="0" show_quick_view="0" equalize_box="true" text_align="left" ids="' . $recently_viewed_ids_string . '" class="sanphamdangxem"]');
    }
}

//
//
//
//
// Tạo shortcode hiển thị thông số kỹ thuật
add_action('flatsome_custom_single_product_2', 'thongso_kythuat_shortcode');
add_shortcode('display_thongso_kythuat', 'thongso_kythuat_shortcode');
function thongso_kythuat_shortcode()
{
    // Lấy giá trị field ACF
    $thongso = get_field('thongso_kythuat');
    static $thongso_lightbox_printed = false;

    if ($thongso) {
        echo '<div class="thongso-kythuat">
            <h3>Thông số kỹ thuật</h3>
            ' . $thongso . '
        </div>';

        if (!$thongso_lightbox_printed) {
            echo '<div id="thongso" class="lightbox-by-id lightbox-content mfp-hide lightbox-white thongso-lightbox" style="max-width:650px;padding:20px;">
                <div class="thongso-kythuat">
                    <h3>Thông số kỹ thuật</h3>
                    ' . $thongso . '
                </div>
            </div>';

            $thongso_lightbox_printed = true;
        }
    } else {
        echo '<div class="thongso-kythuat empty">
            <h3>Thông số kỹ thuật</h3>
            Đang cập nhật
        </div>
        <style>.thongso_full{display:none;}</style>';
    }

    return '';
}
//
//
//
//
//
//
//
//
//
//
//
function display_video_html_field()
{
    // Lấy ID bài viết hiện tại
    $post_id = get_the_ID();

    // Lấy nội dung HTML từ field ACF có tên 'video'
    $video_html = get_field('video', $post_id);

    // Kiểm tra nếu có dữ liệu
    if ($video_html) {
        // Hiển thị khu vực video
        echo '<div class="custom-video-container"><h3> Video giới thiệu </h3>';
        echo $video_html; // Hiển thị nội dung HTML
        echo '</div>';

        // Thêm CSS tùy chọn
        echo '<style>
.custom-video-container {
    margin: 0 0 10px 1px;
    padding: 0 10px 0;
    border-radius: 5px;
    color: #ffff;
}
.custom-video-container iframe {
    max-width: 100%;
    height: auto;
    aspect-ratio: 16 / 9;
    border: 3px solid #ff0000;
    border-radius: 7px;
}
        </style>';
    }
    // Nếu không có dữ liệu, không hiển thị gì
}

// Thêm hook tùy vào vị trí bạn muốn hiển thị
add_action('flatsome_custom_single_product_1', 'display_video_html_field');

//
//
//
//
//
/*Sale price by sago media - sagomedia.vn*/
function sago_price_html($product, $is_variation = false)
{
    ob_start();

    // Khởi tạo biến để lưu thông tin thương hiệu và mã sản phẩm.
    $brand = '';
    $brand_link = '';
    $product_code = '';

    // --- Lấy thông tin Thương hiệu ---
    // Ưu tiên lấy từ taxonomy 'product_brand' (thường dùng cho các plugin Thương hiệu).
    $brand_terms = get_the_terms($product->get_id(), 'product_brand');

    // Nếu không tìm thấy từ 'product_brand' HOẶC có lỗi, thử lấy từ thuộc tính 'pa_thuong-hieu'.
    if (empty($brand_terms) || is_wp_error($brand_terms)) {
        $brand_terms = get_the_terms($product->get_id(), 'pa_thuong-hieu');
    }

    // Kiểm tra xem có bất kỳ term thương hiệu nào được tìm thấy và không có lỗi.
    if (! empty($brand_terms) && ! is_wp_error($brand_terms)) {
        // Lấy đối tượng term thương hiệu đầu tiên.
        // Thường một sản phẩm chỉ có một thương hiệu chính, hoặc bạn muốn hiển thị thương hiệu đầu tiên tìm được.
        $first_brand_term = reset($brand_terms);

        // Gán tên thương hiệu (đã được làm sạch HTML).
        $brand = esc_html($first_brand_term->name);

        // Lấy permalink của term thương hiệu đó.
        $link = get_term_link($first_brand_term);

        // Kiểm tra xem link có hợp lệ không (không phải đối tượng lỗi WordPress).
        if (! is_wp_error($link)) {
            // Gán URL thương hiệu (đã được làm sạch URL).
            $brand_link = esc_url($link);
        }
    }
    // Lấy mã sản phẩm (giả sử dùng SKU)
    $product_code = $product->get_sku() ? $product->get_sku() : 'Chờ cập nhật';

    // Hiển thị thương hiệu và mã sản phẩm
    ?>
    <div class="product-meta-info">
        <div class="product-brand product-code">

            <span class="label">Thương hiệu:</span> <span class="value"> <b><a href="<?php echo $brand_link; ?>"> <?php echo $brand; ?> </a>
                </b>
            </span>
            <span class="label">Mã sản phẩm:</span>
            <span class="value"><b><?php echo esc_html($product_code); ?></b> </span>
        </div>
    </div>
    <?php

    // Hiển thị giá sản phẩm
    if ($product->is_on_sale() && ($is_variation || $product->is_type('simple') || $product->is_type('external'))) {
        $sale_price = $product->get_sale_price();
        $regular_price = $product->get_regular_price();
        if ($regular_price) {
            $sale = round(((floatval($regular_price) - floatval($sale_price)) / floatval($regular_price)) * 100);
            $sale_amout = $regular_price - $sale_price;
    ?>
            <div class="price-text">
                <ul>
                    <li class="price">
                        <span class="km"><?php echo wc_price($sale_price); ?></span> <span class="goc"><?php echo wc_price($regular_price); ?></span>
                    </li>
                </ul>
            </div>
            <?php
        }
    } elseif ($product->is_on_sale() && $product->is_type('variable')) {
        $prices = $product->get_variation_prices(true);
        if (empty($prices['price'])) {
            $price = apply_filters('woocommerce_variable_empty_price_html', '', $product);
        } else {
            $min_price = current($prices['price']);
            $max_price = end($prices['price']);
            $min_reg_price = current($prices['regular_price']);
            $max_reg_price = end($prices['regular_price']);

            if ($min_price !== $max_price) {
                $price = wc_format_price_range($min_price, $max_price) . $product->get_price_suffix();
            } elseif ($product->is_on_sale() && $min_reg_price === $max_reg_price) {
                $sale = round(((floatval($max_reg_price) - floatval($min_price)) / floatval($max_reg_price)) * 100);
                $sale_amout = $max_reg_price - $min_price;
            ?>
                <div class="price-text price">
                    <span class="km"><?php echo wc_price($min_price); ?></span> <span class="goc"><?php echo wc_price($max_reg_price); ?></span>
                </div>
        <?php
            } else {
                $price = wc_price($min_price) . $product->get_price_suffix();
            }
        }
        echo $price;
    } else { ?>
        <div class="price-text">
            <ul>
                <li class="price">
                    Giá bán: <span><?php echo $product->get_price_html(); ?></span>
                </li>
            </ul>
        </div>

    <?php }
    // tình trạng hàng hóa	
    $khuyen_mai = get_field('khuyen_mai');
    if ($khuyen_mai) {
        echo '<div class="khuyen-mai">';
        echo $khuyen_mai;
        echo '</div>';
    }
    return ob_get_clean();
}

function woocommerce_template_single_price()
{
    global $product;
    echo sago_price_html($product);
}

add_filter('woocommerce_available_variation', 'sago_woocommerce_available_variation', 10, 3);
function sago_woocommerce_available_variation($args, $thisC, $variation)
{
    $old_price_html = $args['price_html'];
    if ($old_price_html) {
        $args['price_html'] = sago_price_html($variation, true);
    }
    return $args;
}
//
//
//
//
//
//
//
//
//
//
//
//
/*
 * Thêm nút Xem thêm vào phần mô tả của danh mục sản phẩm
*/
add_action('wp_footer', 'devvn_readmore_taxonomy_flatsome');
function devvn_readmore_taxonomy_flatsome()
{
    if (is_woocommerce() && is_tax('product_cat')):
    ?>
        <style>
            .term-description {
                overflow: hidden;
                position: relative;
                margin-bottom: 20px;
                padding-bottom: 25px;
            }

            .devvn_readmore_taxonomy_flatsome {
                text-align: center;
                cursor: pointer;
                position: absolute;
                z-index: 10;
                bottom: 0;
                width: 100%;
                background: #fff;
            }

            .devvn_readmore_taxonomy_flatsome:before {
                height: 55px;
                margin-top: -45px;
                content: "";
                background: -moz-linear-gradient(top, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 1) 100%);
                background: -webkit-linear-gradient(top, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 1) 100%);
                background: linear-gradient(to bottom, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 1) 100%);
                filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffff00', endColorstr='#ffffff', GradientType=0);
                display: block;
            }

            .devvn_readmore_taxonomy_flatsome a {
                color: #318A00;
                display: block;
            }

            .devvn_readmore_taxonomy_flatsome a:after {
                content: '';
                width: 0;
                right: 0;
                border-top: 6px solid #318A00;
                border-left: 6px solid transparent;
                border-right: 6px solid transparent;
                display: inline-block;
                vertical-align: middle;
                margin: -2px 0 0 5px;
            }

            .devvn_readmore_taxonomy_flatsome_less:before {
                display: none;
            }

            .devvn_readmore_taxonomy_flatsome_less a:after {
                border-top: 0;
                border-left: 6px solid transparent;
                border-right: 6px solid transparent;
                border-bottom: 6px solid #318A00;
            }
        </style>
        <script>
            (function($) {
                $(window).on('load', function() {
                    if ($('.term-description').length > 0) {
                        var wrap = $('.term-description');
                        var current_height = wrap.height();
                        var your_height = 300;
                        if (current_height > your_height) {
                            wrap.css('height', your_height + 'px');
                            wrap.append(function() {
                                return '<div class="devvn_readmore_taxonomy_flatsome devvn_readmore_taxonomy_flatsome_show"><a title="Xem thêm" href="javascript:void(0);">Xem thêm</a></div>';
                            });
                            wrap.append(function() {
                                return '<div class="devvn_readmore_taxonomy_flatsome devvn_readmore_taxonomy_flatsome_less" style="display: none"><a title="Thu gọn" href="javascript:void(0);">Thu gọn</a></div>';
                            });
                            $('body').on('click', '.devvn_readmore_taxonomy_flatsome_show', function() {
                                wrap.removeAttr('style');
                                $('body .devvn_readmore_taxonomy_flatsome_show').hide();
                                $('body .devvn_readmore_taxonomy_flatsome_less').show();
                            });
                            $('body').on('click', '.devvn_readmore_taxonomy_flatsome_less', function() {
                                wrap.css('height', your_height + 'px');
                                $('body .devvn_readmore_taxonomy_flatsome_show').show();
                                $('body .devvn_readmore_taxonomy_flatsome_less').hide();
                            });
                        }
                    }
                });
            })(jQuery);
        </script>
    <?php
    endif;
}
//
//
//
//
//
//
//
//
//
//
//
//
//
//
// Khởi tạo biến.
$current_brand_taxonomy_slug = null;

// Kiểm tra nếu chúng ta đang ở trang admin và có tham số 'taxonomy' trong URL.
if (is_admin() && isset($_GET['taxonomy'])) {
    $current_brand_taxonomy_slug = sanitize_key($_GET['taxonomy']);

    // Tùy chọn: Bạn có thể thêm kiểm tra để đảm bảo đây là taxonomy của thương hiệu bạn muốn.
    // Ví dụ: chỉ chấp nhận nếu slug là 'product_brand' hoặc 'brand'.
    // if ( ! in_array( $current_brand_taxonomy_slug, ['product_brand', 'brand', 'shop_brand'] ) ) {
    //     $current_brand_taxonomy_slug = null; // Reset nếu không phải taxonomy mong muốn.
    // }
}

// Nếu chúng ta đã xác định được slug, thì mới định nghĩa hằng số.
if ($current_brand_taxonomy_slug) {
    define('BRAND_TAXONOMY_SLUG', $current_brand_taxonomy_slug);

    /**
     * Thêm trình soạn thảo Rich Text vào trường Mô tả trên màn hình chỉnh sửa Thương hiệu.
     * Điều này sẽ thay thế trường Mô tả mặc định.
     *
     * @param WP_Term $term Đối tượng term hiện tại đang được chỉnh sửa.
     */
    function custom_brand_description_editor($term)
    {
        // Lấy nội dung mô tả hiện có từ trường 'description' mặc định của term.
        $description = html_entity_decode($term->description);

    ?>
        <tr class="form-field term-description-wrap">
            <th scope="row"><label for="description">Mô tả Thương hiệu</label></th>
            <td>
                <?php
                // Định nghĩa các cài đặt cho trình soạn thảo TinyMCE.
                $editor_settings = array(
                    'textarea_name' => 'description', // RẤT QUAN TRỌNG: Đây phải là 'description'
                    // để WordPress tự động lưu vào cột mô tả của term.
                    'textarea_rows' => 15,          // Số hàng hiển thị ban đầu.
                    'editor_height' => 300,         // Chiều cao của trình soạn thảo.
                    'media_buttons' => true,        // Cho phép nút thêm media (hình ảnh, video).
                    'teeny'         => false,       // false cho phép đầy đủ thanh công cụ, true cho thanh công cụ tối giản.
                    'tinymce'       => true,        // Kích hoạt TinyMCE (trình soạn thảo WYSIWYG).
                    'quicktags'     => true,        // Kích hoạt các thẻ nhanh HTML.
                );

                // Hiển thị trình soạn thảo WordPress.
                // Tham số đầu tiên là nội dung, tham số thứ hai là ID của textarea (độc nhất).
                wp_editor($description, 'brand_description_full_editor', $editor_settings);
                ?>
                <p class="description">
                    Viết mô tả chi tiết và phong phú cho thương hiệu của bạn tại đây.
                    Bạn có thể sử dụng các công cụ định dạng văn bản và chèn media.
                </p>
            </td>
        </tr>
        <?php
    }

    /**
     * Hook hàm của chúng ta vào hành động chỉnh sửa form trường của phân loại thương hiệu.
     * Hook sẽ có dạng '{$taxonomy_slug}_edit_form_fields'.
     */
    add_action(BRAND_TAXONOMY_SLUG . '_edit_form_fields', 'custom_brand_description_editor', 10, 2);


    /**
     * Ẩn trường Mô tả mặc định của WordPress trên trang chỉnh sửa,
     * vì chúng ta đã thêm một trình soạn thảo riêng đầy đủ chức năng.
     * Điều này ngăn chặn việc hiển thị hai trường mô tả.
     */
    function hide_default_brand_description_textarea()
    {
        // Chỉ chạy trong trang quản trị
        if (! is_admin()) {
            return;
        }

        // Lấy thông tin màn hình hiện tại
        $screen = get_current_screen();

        // Kiểm tra xem chúng ta có đang ở trang chỉnh sửa term của phân loại thương hiệu không.
        if ($screen && ($screen->base === 'edit-tags' || $screen->base === 'term') && $screen->taxonomy === BRAND_TAXONOMY_SLUG) {
        ?>
            <script type="text/javascript">
                jQuery(document).ready(function($) {
                    // Tìm trường textarea mặc định có id 'description' và ẩn toàn bộ hàng (tr) của nó.
                    $('#description').closest('tr').hide();
                });
            </script>
    <?php
        }
    }
    // Hook JavaScript vào cuối admin_footer để đảm bảo DOM đã tải đầy đủ.
    add_action('admin_footer', 'hide_default_brand_description_textarea');
}
//
//
//
//
//
//
//
// ẩn thanh stick trong admin
function custom_admin_inline_css()
{
    $custom_css = "
        .woocommerce-layout__header {
            position: unset !important;
        }
    ";

    wp_add_inline_style('wp-admin', $custom_css); // Thêm sau stylesheet 'wp-admin' chung
}
add_action('admin_enqueue_scripts', 'custom_admin_inline_css');



// ----------------------------

if (false && !function_exists('cvn_single_post_breadcrumb')) {
    function cvn_single_post_breadcrumb()
    {
        if (!is_singular('post')) {
            return;
        }

        $items = array(
            array(
                'label' => __('Trang chủ', 'flatsome'),
                'url'   => home_url('/'),
            ),
        );

        $categories = get_the_category();
        if (!empty($categories) && !is_wp_error($categories)) {
            $primary_category = $categories[0];
            $primary_category_id = (int) get_post_meta(get_the_ID(), 'rank_math_primary_category', true);

            if ($primary_category_id) {
                foreach ($categories as $category) {
                    if ((int) $category->term_id === $primary_category_id) {
                        $primary_category = $category;
                        break;
                    }
                }
            }

            $category_link = get_category_link($primary_category);
            if (!is_wp_error($category_link)) {
                $items[] = array(
                    'label' => $primary_category->name,
                    'url'   => $category_link,
                );
            }
        }

        $items[] = array(
            'label' => get_the_title(),
            'url'   => '',
        );

        echo '<nav class="cvn-single-breadcrumbs" aria-label="' . esc_attr__('Breadcrumb', 'flatsome') . '">';
        $last_index = count($items) - 1;

        foreach ($items as $index => $item) {
            if ($index > 0) {
                echo '<span class="cvn-single-breadcrumbs__divider" aria-hidden="true">/</span>';
            }

            if ($index === $last_index || empty($item['url'])) {
                echo '<span>' . esc_html($item['label']) . '</span>';
                continue;
            }

            echo '<a href="' . esc_url($item['url']) . '">' . esc_html($item['label']) . '</a>';
        }

        echo '</nav>';
    }
}


function cvn_single_post_breadcrumb()
{
    if (function_exists('rank_math_get_breadcrumbs')) {
        $breadcrumb = trim(rank_math_get_breadcrumbs(array(
            'wrap_before' => '<nav class="cvn-single-breadcrumbs" aria-label="Breadcrumb">',
            'wrap_after' => '</nav>',
            'separator' => '<span class="cvn-single-breadcrumbs__divider">›</span>',
        )));

        if ($breadcrumb !== '') {
            echo $breadcrumb;
            return;
        }
    }

    $items = array(
        '<a href="' . esc_url(home_url('/')) . '">Trang chủ</a>',
    );

    $categories = get_the_category();
    if (!empty($categories)) {
        $category = $categories[0];
        $ancestors = array_reverse(get_ancestors((int) $category->term_id, 'category'));

        foreach ($ancestors as $ancestor_id) {
            $ancestor = get_category($ancestor_id);
            if (!$ancestor || is_wp_error($ancestor)) {
                continue;
            }

            $items[] = '<a href="' . esc_url(get_category_link($ancestor)) . '">' . esc_html($ancestor->name) . '</a>';
        }

        $items[] = '<a href="' . esc_url(get_category_link($category)) . '">' . esc_html($category->name) . '</a>';
    }

    $items[] = '<span>' . esc_html(get_the_title()) . '</span>';

    echo '<nav class="cvn-single-breadcrumbs" aria-label="Breadcrumb">' . implode('<span class="cvn-single-breadcrumbs__divider">›</span>', $items) . '</nav>';
}

add_action('flatsome_before_blog', 'cvn_render_single_post_header', 20);
function cvn_render_single_post_header()
{
    if (!is_singular('post')) {
        return;
    }

    $post_id = get_queried_object_id();
    $thumbnail_id = $post_id ? get_post_thumbnail_id($post_id) : 0;
    $hero_class = $thumbnail_id ? 'cvn-single-hero' : 'cvn-single-hero cvn-single-hero--no-image';

    ?>
    <div class="cvn-single-breadcrumb-bar">
        <?php cvn_single_post_breadcrumb(); ?>
    </div>

    <div class="cvn-single-header">
        <div class="<?php echo esc_attr($hero_class); ?>">
            <div class="cvn-single-hero__content">
                <h1 class="cvn-single-title"><?php the_title(); ?></h1>
            </div>

            <?php if ($thumbnail_id) : ?>
                <div class="cvn-single-hero__image">
                    <?php echo wp_get_attachment_image($thumbnail_id, 'full', false, array('loading' => 'eager')); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php
}


/**
 * 1. Đổi link hiển thị của danh mục sản phẩm WooCommerce
 */
add_filter('term_link', 'custom_flat_product_cat_url', 10, 3);

function custom_flat_product_cat_url($url, $term, $taxonomy)
{

    if ($taxonomy === 'product_cat') {
        return home_url('/' . $term->slug . '/');
    }

    return $url;
}


/**
 * 2. Đổi link hiển thị của danh mục bài viết cấp con
 */
add_filter('term_link', 'custom_flat_child_category_url', 10, 3);

function custom_flat_child_category_url($url, $term, $taxonomy)
{

    if ($taxonomy === 'category' && !empty($term->parent)) {
        return home_url('/' . $term->slug . '/');
    }

    return $url;
}


/**
 * 3. Tạo rewrite rule cho danh mục sản phẩm WooCommerce
 */
add_action('init', 'custom_flat_product_cat_rewrite_rules');

function custom_flat_product_cat_rewrite_rules()
{

    $terms = get_terms(array(
        'taxonomy' => 'product_cat',
        'hide_empty' => false,
    ));

    if (empty($terms) || is_wp_error($terms)) {
        return;
    }

    foreach ($terms as $term) {

        add_rewrite_rule(
            '^' . $term->slug . '/?$',
            'index.php?product_cat=' . $term->slug,
            'top'
        );

        add_rewrite_rule(
            '^' . $term->slug . '/page/([0-9]+)/?$',
            'index.php?product_cat=' . $term->slug . '&paged=$matches[1]',
            'top'
        );
    }
}


/**
 * 4. Tạo rewrite rule cho danh mục bài viết cấp con
 */
add_action('init', 'custom_flat_child_category_rewrite_rules');

function custom_flat_child_category_rewrite_rules()
{

    $terms = get_terms(array(
        'taxonomy' => 'category',
        'hide_empty' => false,
    ));

    if (empty($terms) || is_wp_error($terms)) {
        return;
    }

    foreach ($terms as $term) {

        // Chỉ áp dụng cho category cấp con
        if (!empty($term->parent)) {

            add_rewrite_rule(
                '^' . $term->slug . '/?$',
                'index.php?category_name=' . $term->slug,
                'top'
            );

            add_rewrite_rule(
                '^' . $term->slug . '/page/([0-9]+)/?$',
                'index.php?category_name=' . $term->slug . '&paged=$matches[1]',
                'top'
            );
        }
    }
}

function ishen_decode_vietnamese_text($text)
{
    $text = (string) $text;

    return html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/**
 * Shortcode hiển thị danh mục con sản phẩm dạng ảnh tròn
 *
 * Cách dùng:
 * [dmsp_con]
 * [dmsp_con columns="4"]
 * [dmsp_con columns="5"]
 * [dmsp_con columns="6"]
 * [dmsp_con ids="15,8,22,10" columns="5"]
 *
 * Ghi chú:
 * - Chỉ hoạt động ở trang danh mục sản phẩm WooCommerce.
 * - Không có danh mục con thì không hiển thị gì.
 * - Nếu truyền ids thì hiển thị đúng thứ tự ids.
 * - columns là số item trên 1 dòng ở desktop.
 */
remove_shortcode('dmsp_con');
add_shortcode('dmsp_con', 'ishen_dmsp_con_shortcode');

function ishen_dmsp_con_shortcode($atts)
{

    /**
     * Chỉ chạy ở trang danh mục sản phẩm WooCommerce.
     * Nếu shortcode bị chèn ở Page/Post/UX Block khác thì dừng ngay.
     */
    if (! function_exists('is_product_category') || ! is_product_category()) {
        return '';
    }

    $atts = shortcode_atts(
        array(
            'ids'        => '',
            'columns'    => 5,
            'hide_empty' => 0,
        ),
        $atts,
        'dmsp_con'
    );

    $taxonomy = 'product_cat';
    $terms    = array();

    /**
     * Xử lý số cột desktop.
     * Cho phép linh động: 1 đến 8 cột.
     */
    $columns = absint($atts['columns']);

    if ($columns < 1) {
        $columns = 5;
    }

    if ($columns > 8) {
        $columns = 8;
    }

    /**
     * Trường hợp 1:
     * Có truyền ids thì lấy đúng các danh mục đó
     * và giữ đúng thứ tự ids.
     *
     * Ví dụ:
     * [dmsp_con ids="15,8,22,10" columns="5"]
     */
    if (! empty($atts['ids'])) {

        $ids = array_filter(
            array_map(
                'absint',
                explode(',', $atts['ids'])
            )
        );

        if (empty($ids)) {
            return '';
        }

        $terms = get_terms(
            array(
                'taxonomy'   => $taxonomy,
                'include'    => $ids,
                'orderby'    => 'include',
                'hide_empty' => (bool) $atts['hide_empty'],
            )
        );
    } else {

        /**
         * Trường hợp 2:
         * Không truyền ids thì tự lấy danh mục hiện tại làm cha,
         * sau đó hiển thị danh mục con của nó.
         *
         * Ví dụ:
         * [dmsp_con columns="5"]
         */
        $current_term = get_queried_object();

        if (
            ! $current_term ||
            ! isset($current_term->term_id) ||
            ! isset($current_term->taxonomy) ||
            $current_term->taxonomy !== $taxonomy
        ) {
            return '';
        }

        $terms = get_terms(
            array(
                'taxonomy'   => $taxonomy,
                'parent'     => absint($current_term->term_id),
                'hide_empty' => (bool) $atts['hide_empty'],
                'orderby'    => 'menu_order',
                'order'      => 'ASC',
            )
        );
    }

    /**
     * Không có danh mục con / danh mục hợp lệ thì không hiển thị gì.
     */
    if (empty($terms) || is_wp_error($terms)) {
        return '';
    }

    ob_start();
?>

    <div class="ishen-dmsp-con-wrap container" style="--dmsp-columns: <?php echo esc_attr($columns); ?>;">

        <?php foreach ($terms as $term) : ?>

            <?php
            if (! $term instanceof WP_Term) {
                continue;
            }

            $term_link = get_term_link($term, $taxonomy);

            if (is_wp_error($term_link)) {
                continue;
            }

            // Decode tên danh mục trước khi đưa ra frontend để không bị hiện dạng Ch&#432;a c&#7853;p nh&#7853;t.
            $term_name = ishen_decode_vietnamese_text($term->name);

            $thumbnail_id = get_term_meta($term->term_id, 'thumbnail_id', true);

            if ($thumbnail_id) {
                $image_html = wp_get_attachment_image(
                    $thumbnail_id,
                    'medium',
                    false,
                    array(
                        'class'   => 'ishen-dmsp-con-img-tag',
                        'alt'     => esc_attr($term_name),
                        'loading' => 'lazy',
                    )
                );
            } else {
                $image_html = '<img class="ishen-dmsp-con-img-tag" src="' . esc_url(wc_placeholder_img_src()) . '" alt="' . esc_attr($term_name) . '" loading="lazy">';
            }
            ?>

            <a class="ishen-dmsp-con-item" href="<?php echo esc_url($term_link); ?>">

                <span class="ishen-dmsp-con-img">
                    <?php echo $image_html; ?>
                </span>

                <span class="ishen-dmsp-con-title">
                    <?php echo esc_html($term_name); ?>
                </span>

            </a>

        <?php endforeach; ?>

    </div>

<?php
    return ob_get_clean();
}
