<?php

defined('ABSPATH') || exit;

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
