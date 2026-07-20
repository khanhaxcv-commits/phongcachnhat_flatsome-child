<?php

defined('ABSPATH') || exit;

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