<?php

/**
 * All-in-One WP Migration exclusions.
 *
 * Exclude development files from export package.
 */

if (!defined('ABSPATH')) {
    exit;
}

add_filter('ai1wm_exclude_content_from_export', function ($exclude_filters) {
    $theme_dir_name = basename(get_stylesheet_directory());

    $exclude_filters[] = '.git';
    $exclude_filters[] = '*/.git/*';

    $exclude_filters[] = 'node_modules';
    $exclude_filters[] = '*/node_modules/*';

    $exclude_filters[] = 'themes/' . $theme_dir_name . '/.git';
    $exclude_filters[] = 'themes/' . $theme_dir_name . '/.git/*';

    $exclude_filters[] = 'themes/' . $theme_dir_name . '/node_modules';
    $exclude_filters[] = 'themes/' . $theme_dir_name . '/node_modules/*';

    return $exclude_filters;
});
