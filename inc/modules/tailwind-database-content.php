<?php

/**
 * Xuất nội dung WordPress Database để Tailwind CSS quét class.
 *
 * Bao gồm nội dung từ Flatsome UX Builder, trang, bài viết, sản phẩm
 * WooCommerce, UX Blocks, shortcode, Contact Form 7 và HTML trong post_content.
 */

defined('ABSPATH') || exit;

if (!class_exists('Tailwind_Database_Content')) {
    class Tailwind_Database_Content
    {
        /**
         * Đăng ký các hook cập nhật nội dung database cho Tailwind.
         */
        public static function init()
        {
            add_action('save_post', array(__CLASS__, 'update_after_save'), 100);
            add_action('deleted_post', array(__CLASS__, 'refresh'));
            add_action('trashed_post', array(__CLASS__, 'refresh'));
            add_action('untrashed_post', array(__CLASS__, 'refresh'));
            add_action('admin_init', array(__CLASS__, 'maybe_generate'));
        }

        /**
         * Lấy đường dẫn thư mục lưu nội dung database.
         */
        public static function get_directory()
        {
            return trailingslashit(get_stylesheet_directory()) . '.tailwind';
        }

        /**
         * Lấy đường dẫn file Tailwind quét nội dung database.
         */
        public static function get_file()
        {
            return trailingslashit(self::get_directory()) . 'database-content.txt';
        }

        /**
         * Xuất toàn bộ post_content ra file văn bản.
         */
        public static function generate()
        {
            global $wpdb;

            $directory_path = self::get_directory();
            $file_path      = self::get_file();

            if (!is_dir($directory_path)) {
                wp_mkdir_p($directory_path);
            }

            $contents = $wpdb->get_col(
                "
                SELECT post_content
                FROM {$wpdb->posts}
                WHERE post_content IS NOT NULL
                AND post_content != ''
                AND post_status NOT IN (
                    'trash',
                    'auto-draft',
                    'inherit'
                )
                AND post_type NOT IN (
                    'revision',
                    'attachment',
                    'nav_menu_item'
                )
                ORDER BY ID ASC
                "
            );

            if (!is_array($contents)) {
                $contents = array();
            }

            $contents = array_map(
                static function ($content) {
                    if (!is_string($content)) {
                        return '';
                    }

                    return html_entity_decode(
                        $content,
                        ENT_QUOTES | ENT_HTML5,
                        'UTF-8'
                    );
                },
                $contents
            );

            $output = implode(PHP_EOL . PHP_EOL, $contents);
            $output = sprintf(
                "/* Generated: %s */%s%s",
                current_time('mysql'),
                PHP_EOL . PHP_EOL,
                $output
            );

            return file_put_contents($file_path, $output, LOCK_EX) !== false;
        }

        /**
         * Cập nhật file sau khi lưu nội dung WordPress.
         */
        public static function update_after_save($post_id)
        {
            if (wp_is_post_autosave($post_id) || wp_is_post_revision($post_id)) {
                return;
            }

            self::generate();
        }

        /**
         * Cập nhật file khi xóa, đưa vào thùng rác hoặc khôi phục nội dung.
         */
        public static function refresh()
        {
            self::generate();
        }

        /**
         * Tạo file lần đầu nếu chưa tồn tại.
         */
        public static function maybe_generate()
        {
            if (!file_exists(self::get_file())) {
                self::generate();
            }
        }
    }

    Tailwind_Database_Content::init();
}
