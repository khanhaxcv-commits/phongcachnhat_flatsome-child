<?php

defined('ABSPATH') || exit;

//widgets mặc định 
add_filter('use_widgets_block_editor', '__return_false');

/** trả bộ soạn thảo mặc định */
add_filter('use_block_editor_for_post', '__return_false');

// ------------------------------code mới--------------------------------------

add_action('init', function () {
    remove_filter('the_content', 'wpautop');
    remove_filter('the_excerpt', 'wpautop');
    remove_filter('comment_text', 'wpautop', 30);
});
