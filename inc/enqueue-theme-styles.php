<?php

/**
 * Enqueue Theme Styles
 *
 * Load all theme CSS files with proper dependency management
 * 
 * 1. flatsome-main
 * ↓
 * 2. reset.css
 * ↓
 * 3. style.css
 * ↓
 * 4. global.css
 * ↓
 * 5. header.css
 * ↓
 * 6. flatsome-mobile-menu.css
 * ↓
 * 7. flatsome-desktop-menu.css
 * ↓
 * 8. footer.css
 * ↓
 * 9. page-specific css
 * ↓
 * 10. customize.css
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Helper: Enqueue CSS file with automatic versioning
 *
 * Uses file modification time as version for cache busting.
 *
 * @param string $handle CSS handle.
 * @param string $file   Relative path to file in /assets/css/ folder.
 * @param array  $deps   CSS dependencies.
 *
 * @return bool True if file exists and was enqueued, false otherwise.
 */
function enqueue_css_file($handle, $file, $deps = array(), $media = 'all')
{
    $path = get_stylesheet_directory() . '/assets/css/' . $file;
    $uri  = get_stylesheet_directory_uri() . '/assets/css/' . $file;

    if (!file_exists($path)) {
        return false;
    }

    wp_enqueue_style(
        $handle,
        $uri,
        $deps,
        filemtime($path),
        $media
    );

    return true;
}

/**
 * Enqueue all theme styles
 *
 * Load reset, main stylesheet, global styles, menu styles, page-specific styles,
 * and final custom override stylesheet.
 */
function enqueue_styles()
{
    $style_path = get_stylesheet_directory() . '/style.css';

    /*
     * 1. Reset CSS
     *
     * Reset chạy trước style.css.
     */
    $has_reset = enqueue_css_file(
        'reset-css',
        'reset.css',
        array('flatsome-main')
    );

    /*
     * 2. Main child theme stylesheet
     *
     * style.css là nền tảng toàn site, chứa :root, token, biến màu.
     */
    wp_enqueue_style(
        'flatsome-child-style',
        get_stylesheet_uri(),
        $has_reset ? array('reset-css') : array('flatsome-main'),
        file_exists($style_path) ? filemtime($style_path) : null
    );

    /*
     * 3. Global CSS
     *
     * global.css chứa class chung, dùng biến từ style.css.
     */
    $has_global = enqueue_css_file(
        'global-css',
        'global.css',
        array('flatsome-child-style')
    );

    $global_deps = $has_global ? array('global-css') : array('flatsome-child-style');

    /*
     * 4. Header CSS
     */
    $has_header = enqueue_css_file(
        'header-css',
        'header.css',
        $global_deps
    );

    $header_deps = $has_header ? array('header-css') : $global_deps;

    /*
     * 5. Flatsome menu CSS
     *
     * Tách riêng để dễ tái sử dụng cho nhiều dự án Flatsome.
     */
    $layout_handles = array();

    if ($has_header) {
        $layout_handles[] = 'header-css';
    }

    if (
        enqueue_css_file(
            'flatsome-mobile-menu-css',
            'flatsome-mobile-menu.css',
            $header_deps,
            '(max-width: 849px)'
        )
    ) {
        $layout_handles[] = 'flatsome-mobile-menu-css';
    }

    if (
        enqueue_css_file(
            'flatsome-desktop-menu-css',
            'flatsome-desktop-menu.css',
            $header_deps,
            '(min-width: 850px)'
        )
    ) {
        $layout_handles[] = 'flatsome-desktop-menu-css';
    }

    /*
     * 6. Footer CSS
     */
    // if (
    //     enqueue_css_file(
    //         'footer-css',
    //         'footer.css',
    //         $global_deps
    //     )
    // ) {
    //     $layout_handles[] = 'footer-css';
    // }

    /*
     * 7. Archive / blog styles
     */
    // if (
    //     enqueue_css_file(
    //         'blog-css',
    //         'blog.css',
    //         $global_deps
    //     )
    // ) {
    //     $layout_handles[] = 'blog-css';
    // }

    // if (
    //     enqueue_css_file(
    //         'category-css',
    //         'category.css',
    //         $global_deps
    //     )
    // ) {
    //     $layout_handles[] = 'category-css';
    // }

    /*
     * 8. Page-specific styles
     */
    $page_handles = array();

    if (is_front_page() || is_home() || is_page('trang-chu')) {
        if (
            enqueue_css_file(
                'trang-chu-1-css',
                'trang-chu-1.css',
                $global_deps
            )
        ) {
            $page_handles[] = 'trang-chu-1-css';
        }
    }

    if (is_page('lien-he')) {
        if (
            enqueue_css_file(
                'lien-he-1-css',
                'lien-he-1.css',
                $global_deps
            )
        ) {
            $page_handles[] = 'lien-he-1-css';
        }
    }
    if (is_product_category()) {
        if (
            enqueue_css_file(
                'danh-muc-san-pham-css',
                'danh-muc-san-pham.css',
                $global_deps
            )
        ) {
            $page_handles[] = 'danh-muc-san-pham-css';
        }
        if (
            enqueue_css_file(
                'product-filter-css',
                'product-filter.css',
                $global_deps
            )
        ) {
            $page_handles[] = 'product-filter-css';
        }
    }
    if (function_exists('is_product') && is_product()) {
        if (
            enqueue_css_file(
                'san-pham-css',
                'san-pham.css',
                $global_deps
            )
        ) {
            $page_handles[] = 'san-pham-css';
        }
    }


    /*
     * 9. Customize CSS
     *
     * customize.css dùng làm file override cuối cùng.
     * Nếu file này đang chứa :root/token thì nên chuyển phần đó về style.css.
     */
    // $customize_deps = array_unique(
    //     array_merge(
    //         array('flatsome-child-style'),
    //         $global_deps,
    //         $layout_handles,
    //         $page_handles
    //     )
    // );

    // enqueue_css_file(
    //     'customize-css',
    //     'customize.css',
    //     $customize_deps
    // );
}

add_action('wp_enqueue_scripts', 'enqueue_styles', 1);
