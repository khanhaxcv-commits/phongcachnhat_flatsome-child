<?php

/**
 * Blog module bootstrap.
 */

defined('ABSPATH') || exit;

$blog_modules = array(
    'video-icon.php',
    'single-post-header.php',
    'category-pagination.php',
    'blog-category-archive.php',
    'blog-single.php',
);

foreach ($blog_modules as $blog_module) {
    $blog_module_file = __DIR__ . '/' . $blog_module;

    if (file_exists($blog_module_file)) {
        require_once $blog_module_file;
    }
}
