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

    $theme_development_names = array(
        '.git',
        'node_modules',
        '.tailwind',
        '.vscode',
        '.idea',
        'package.json',
        'package-lock.json',
        'yarn.lock',
        'tailwind.config.js',
        'README.md',
        'src',
        '.gitignore',
        'AGENTS.md',
        '.editorconfig',
        '.codex',
        'tests',
    );

    $theme_directory = untrailingslashit(wp_normalize_path(get_stylesheet_directory()));
    $theme_relative_directory = 'themes/' . $theme_dir_name;

    try {
        $theme_files = new RecursiveIteratorIterator(
            new RecursiveCallbackFilterIterator(
                new RecursiveDirectoryIterator($theme_directory, FilesystemIterator::SKIP_DOTS),
                function ($theme_file) use (&$exclude_filters, $theme_development_names, $theme_directory, $theme_relative_directory) {
                    if (!in_array($theme_file->getBasename(), $theme_development_names, true)) {
                        return true;
                    }

                    $theme_file_path = wp_normalize_path($theme_file->getPathname());
                    $relative_file_path = $theme_relative_directory . substr($theme_file_path, strlen($theme_directory));

                    $exclude_filters[] = ltrim($relative_file_path, '/');

                    return false;
                }
            ),
            RecursiveIteratorIterator::SELF_FIRST
        );

        iterator_count($theme_files);
    } catch (UnexpectedValueException $exception) {
        // Keep the export running if a theme directory cannot be enumerated.
    }

    return $exclude_filters;
});
