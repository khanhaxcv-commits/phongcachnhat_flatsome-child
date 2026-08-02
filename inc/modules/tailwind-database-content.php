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
         * Đánh dấu Tailwind cần được build ở cuối request hiện tại.
         *
         * @var bool
         */
        private static $build_scheduled = false;

        /**
         * Đăng ký các hook cập nhật nội dung database cho Tailwind.
         */
        public static function init()
        {
            if (!self::is_local_environment()) {
                return;
            }

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
         * Kiểm tra website đang chạy trong môi trường local.
         */
        private static function is_local_environment()
        {
            $site_host = wp_parse_url(home_url(), PHP_URL_HOST);

            if (!is_string($site_host)) {
                return false;
            }

            return $site_host === 'localhost'
                || $site_host === '127.0.0.1'
                || substr($site_host, -6) === '.local'
                || substr($site_host, -5) === '.test';
        }

        /**
         * Gộp nhiều lần lưu trong cùng request thành một lần build Tailwind.
         */
        private static function schedule_build()
        {
            if (self::$build_scheduled) {
                return;
            }

            self::$build_scheduled = true;
            add_action('shutdown', array(__CLASS__, 'build'), PHP_INT_MAX);
        }

        /**
         * Lấy đường dẫn Node.js dùng để chạy Tailwind CLI.
         */
        private static function get_node_binary()
        {
            if (PHP_OS_FAMILY !== 'Windows') {
                return 'node';
            }

            $program_files = getenv('ProgramFiles');

            if (is_string($program_files) && $program_files !== '') {
                $node_binary = trailingslashit($program_files) . 'nodejs/node.exe';

                if (file_exists($node_binary)) {
                    return $node_binary;
                }
            }

            return 'node';
        }

        /**
         * Build Tailwind bằng CLI đã cài trong node_modules của child theme.
         */
        public static function build()
        {
            self::$build_scheduled = false;

            if (!function_exists('proc_open')) {
                return false;
            }

            $theme_directory = get_stylesheet_directory();
            $tailwind_cli     = $theme_directory . '/node_modules/tailwindcss/lib/cli.js';
            $input_file       = $theme_directory . '/assets/src/tailwind-input.css';
            $output_file      = $theme_directory . '/assets/css/generated/tailwind.css';

            if (!file_exists($tailwind_cli) || !file_exists($input_file)) {
                return false;
            }

            $lock_handle = fopen(self::get_directory() . '/build.lock', 'c');

            if ($lock_handle === false) {
                return false;
            }

            if (!flock($lock_handle, LOCK_EX | LOCK_NB)) {
                fclose($lock_handle);
                return false;
            }

            $command = array(
                self::get_node_binary(),
                $tailwind_cli,
                '-i',
                $input_file,
                '-o',
                $output_file,
                '--minify',
            );

            $descriptor_spec = array(
                0 => array('pipe', 'r'),
                1 => array('pipe', 'w'),
                2 => array('pipe', 'w'),
            );

            $process = proc_open(
                $command,
                $descriptor_spec,
                $pipes,
                $theme_directory,
                null,
                array('bypass_shell' => true)
            );

            if (!is_resource($process)) {
                flock($lock_handle, LOCK_UN);
                fclose($lock_handle);
                return false;
            }

            fclose($pipes[0]);
            stream_get_contents($pipes[1]);
            fclose($pipes[1]);
            stream_get_contents($pipes[2]);
            fclose($pipes[2]);

            $exit_code = proc_close($process);

            flock($lock_handle, LOCK_UN);
            fclose($lock_handle);

            return $exit_code === 0 && file_exists($output_file);
        }

        /**
         * Cập nhật file sau khi lưu nội dung WordPress.
         */
        public static function update_after_save($post_id)
        {
            if (wp_is_post_autosave($post_id) || wp_is_post_revision($post_id)) {
                return;
            }

            if (self::generate()) {
                self::schedule_build();
            }
        }

        /**
         * Cập nhật file khi xóa, đưa vào thùng rác hoặc khôi phục nội dung.
         */
        public static function refresh()
        {
            if (self::generate()) {
                self::schedule_build();
            }
        }

        /**
         * Tạo file lần đầu nếu chưa tồn tại.
         */
        public static function maybe_generate()
        {
            if (!file_exists(self::get_file())) {
                if (self::generate()) {
                    self::schedule_build();
                }
            }
        }
    }

    Tailwind_Database_Content::init();
}
