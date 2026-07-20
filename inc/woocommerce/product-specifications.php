<?php

defined('ABSPATH') || exit;

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
