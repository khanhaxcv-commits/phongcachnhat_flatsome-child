<?php

defined('ABSPATH') || exit;

/// vùa xem
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
        echo do_shortcode(
            '[ux_products style="normal" columns="5" show_cat="0" show_rating="0" show_quick_view="0" equalize_box="true" text_align="left" ids="' .
                $recently_viewed_ids_string .
                '" class="sanphamdangxem"]'
        );
    }
}
