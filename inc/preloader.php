<?php

/**
 * Preloader
 *
 * Reusable preloader module for WordPress projects.
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_body_open', function () {
    $loading_icon = get_site_icon_url(192);

    if (!$loading_icon) {
        $custom_logo_id = get_theme_mod('custom_logo');
        $loading_icon = $custom_logo_id ? wp_get_attachment_image_url($custom_logo_id, 'full') : '';
    }

    if (!$loading_icon) {
        $loading_icon = get_stylesheet_directory_uri() . '/assets/images/logo.png';
    }
?>
    <!-- Preloader Start -->
    <div class="preloader">
        <div class="loading-container">
            <div class="loading"></div>

            <div id="loading-icon">
                <img
                    src="<?php echo esc_url($loading_icon); ?>"
                    alt="<?php echo esc_attr(get_bloginfo('name')); ?>">
            </div>
        </div>
    </div>
    <!-- Preloader End -->
<?php
});
