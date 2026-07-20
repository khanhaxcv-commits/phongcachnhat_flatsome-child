<?php

defined('ABSPATH') || exit;

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
