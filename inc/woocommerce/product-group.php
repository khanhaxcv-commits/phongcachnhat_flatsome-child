<?php

defined('ABSPATH') || exit;

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
                $giaban = get_post_meta($id_sp, '_sale_price', true);

                if ($giaban) {
                    $gia = number_format($giaban, 0, ',', '.') . 'đ';
                } elseif ($gia_thitruiong) {
                    $gia = number_format($gia_thitruiong, 0, ',', '.') . 'đ';
                }

                $linksp = get_permalink($id_sp);
                $tensp = $sanpham['ten_thay_the'];

                if ($sanpham['ten_thay_the']) {
                    $tensp = $sanpham['ten_thay_the'];
                } else {
                    $tensp = get_the_title($id_sp);
                }
?>
                <div class="nhom-item">
                    <a href="<?php echo $linksp; ?>">
                        <div class="ten_nhoo"><?php echo $tensp; ?></div>
                        <div class="gia_nhom"><?php echo $gia; ?></div>
                    </a>
                </div>
<?php
            }
        }

        echo '</div>';
    }
}

add_action('woocommerce_single_product_summary', 'nhom_san_pham', 21);
