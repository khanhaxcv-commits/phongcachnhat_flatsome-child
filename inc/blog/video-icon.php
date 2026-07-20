<?php

defined('ABSPATH') || exit;

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
