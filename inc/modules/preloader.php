<?php

/**
 * Preloader
 *
 * Reusable preloader module for WordPress projects.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Hiển thị preloader ngay sau thẻ <body>.
 */
add_action('wp_body_open', function () {
    /**
     * Ưu tiên lấy Site Icon.
     */
    $loading_icon = get_site_icon_url(192);

    /**
     * Nếu chưa có Site Icon thì lấy Custom Logo.
     */
    if (!$loading_icon) {
        $custom_logo_id = get_theme_mod('custom_logo');

        $loading_icon = $custom_logo_id
            ? wp_get_attachment_image_url($custom_logo_id, 'full')
            : '';
    }

    /**
     * Logo dự phòng trong child theme.
     */
    if (!$loading_icon) {
        $loading_icon = get_stylesheet_directory_uri() . '/assets/images/logo.png';
    }
?>

    <!-- Preloader Start -->
    <div class="pcn-preloader" id="site-preloader" aria-hidden="true">
        <div class="pcn-preloader__container">
            <div class="pcn-preloader__spinner"></div>

            <div class="pcn-preloader__icon">
                <img src="<?php echo esc_url($loading_icon); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>"
                    width="110" height="110">
            </div>
        </div>
    </div>
    <!-- Preloader End -->

    <style>
        .pcn-preloader {
            position: fixed;
            inset: 0;
            z-index: 999999;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #ffffff;
        }

        .pcn-preloader.is-hidden {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }

        /* Khung chung chứa vòng xoay và logo */
        .pcn-preloader__container {
            position: relative;
            width: 150px;
            height: 150px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        /* Vòng xoay phải nằm tuyệt đối trong container */
        .pcn-preloader__spinner {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            box-sizing: border-box;
            border: 2px solid transparent;
            border-color: transparent var(--primary) transparent var(--primary);
            border-radius: 50%;
            animation: pcn-preloader-rotate 1.2s linear infinite;
            transform-origin: center;
            z-index: 3;
        }

        /* Khung logo nằm chính giữa vòng xoay */
        .pcn-preloader__icon {
            position: absolute;
            top: 50%;
            left: 50%;
            z-index: 2;
            width: 118px;
            height: 118px;
            display: flex;
            align-items: center;
            justify-content: center;
            /* padding: 12px; */
            box-sizing: border-box;
            border-radius: 50%;
            /* background-color: #ffffff; */
            /* box-shadow:
                0 8px 25px rgba(0, 0, 0, 0.1),
                inset 0 0 0 1px rgba(0, 0, 0, 0.04); */
            transform: translate(-50%, -50%);
        }

        .pcn-preloader__icon img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 0;
        }

        @keyframes pcn-preloader-rotate {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        @media (max-width: 549px) {
            .pcn-preloader__container {
                width: 130px;
                height: 130px;
            }

            .pcn-preloader__icon {
                width: 102px;
                height: 102px;
                padding: 10px;
            }
        }
    </style>

    <script>
        (function() {
            'use strict';

            const preloader = document.getElementById('site-preloader');

            if (!preloader) {
                return;
            }

            let isPreloaderHidden = false;

            /**
             * Làm mờ và ẩn preloader.
             */
            function hidePreloader() {
                if (isPreloaderHidden) {
                    return;
                }

                isPreloaderHidden = true;

                /*
                 * Khóa tương tác ngay khi bắt đầu ẩn,
                 * nhưng vẫn giữ preloader hiển thị để chạy hiệu ứng fade.
                 */
                preloader.style.pointerEvents = 'none';
                preloader.style.transition = 'opacity 600ms ease';
                preloader.style.opacity = '1';

                /*
                 * Chờ trình duyệt ghi nhận trạng thái opacity: 1
                 * rồi mới chuyển về opacity: 0.
                 */
                window.requestAnimationFrame(function() {
                    window.requestAnimationFrame(function() {
                        preloader.style.opacity = '0';
                    });
                });

                /*
                 * Sau khi hiệu ứng hoàn tất thì ẩn hoàn toàn.
                 */
                window.setTimeout(function() {
                    preloader.style.visibility = 'hidden';
                    preloader.style.display = 'none';
                }, 600);
            }

            /**
             * Nếu trang đã tải xong trước khi script chạy.
             */
            if (document.readyState === 'complete') {
                hidePreloader();
            } else {
                /**
                 * Ẩn sau khi toàn bộ trang, ảnh và tài nguyên tải xong.
                 */
                window.addEventListener('load', hidePreloader, {
                    once: true
                });
            }

            /**
             * Dự phòng để preloader không bị treo quá 8 giây.
             */
            window.setTimeout(hidePreloader, 8000);
        }());
    </script>

<?php
});
