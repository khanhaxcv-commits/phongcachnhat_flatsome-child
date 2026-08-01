<?php

/**
 * Phân trang responsive cho trang danh mục bài viết.
 */

defined('ABSPATH') || exit;

if (!class_exists('Blog_Category_Pagination')) {
    class Blog_Category_Pagination
    {
        /**
         * Render phân trang cho query được truyền vào hoặc main query.
         *
         * @param WP_Query|null $query Query cần phân trang.
         *
         * @return void
         */
        public static function render($query = null)
        {
            if (!$query instanceof WP_Query) {
                global $wp_query;
                $query = $wp_query;
            }

            $total = isset($query->max_num_pages)
                ? (int) $query->max_num_pages
                : 0;

            if ($total <= 1) {
                return;
            }

            $current = max(1, (int) get_query_var('paged'));
            $previous_url = $current > 1
                ? get_pagenum_link($current - 1)
                : '';
            $next_url = $current < $total
                ? get_pagenum_link($current + 1)
                : '';
            $number_links = paginate_links(array(
                'current'   => $current,
                'total'     => $total,
                'mid_size'  => 1,
                'end_size'  => 1,
                'prev_next' => false,
                'type'      => 'array',
            ));
?>
            <nav class="my-7 sm:my-10" aria-label="Phân trang bài viết">
                <div class="!flex items-center justify-between gap-3 sm:!hidden">
                    <?php self::render_button($previous_url, 'Trước', 'fa-light fa-arrow-left'); ?>

                    <span class="shrink-0 !text-sub text-sm font-semibold">
                        Trang <?php echo esc_html(number_format_i18n($current)); ?>
                        / <?php echo esc_html(number_format_i18n($total)); ?>
                    </span>

                    <?php self::render_button($next_url, 'Sau', 'fa-light fa-arrow-right', true); ?>
                </div>

                <div class="!hidden flex-wrap items-center justify-center gap-2 sm:!flex">
                    <?php self::render_button($previous_url, 'Trước', 'fa-light fa-arrow-left', false, true); ?>

                    <?php if (is_array($number_links)) : ?>
                        <?php foreach ($number_links as $number_link) : ?>
                            <?php echo wp_kses_post(self::style_number_link($number_link)); ?>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <?php self::render_button($next_url, 'Sau', 'fa-light fa-arrow-right', true, true); ?>
                </div>
            </nav>
            <?php
        }

        /**
         * Render nút điều hướng trước hoặc sau.
         *
         * @param string $url        URL điều hướng.
         * @param string $label      Nhãn của nút.
         * @param string $icon_class Class Font Awesome.
         * @param bool   $icon_after Hiển thị icon sau nhãn.
         * @param bool   $desktop    Dùng kích thước desktop.
         *
         * @return void
         */
        private static function render_button($url, $label, $icon_class, $icon_after = false, $desktop = false)
        {
            $is_disabled = $url === '';
            $enabled_classes = $desktop
                ? '!inline-flex h-10 items-center justify-center gap-1 rounded-xl border border-ui bg-surface px-3 text-sm font-semibold !text-heading shadow-ui-card transition-colors hover:border-primary-300 hover:!text-primary'
                : '!inline-flex min-h-11 items-center gap-1.5 rounded-xl border border-ui bg-surface px-3 text-sm font-semibold !text-heading shadow-ui-card transition-colors active:bg-primary-50';
            $disabled_classes = $desktop
                ? '!inline-flex h-10 cursor-not-allowed select-none items-center justify-center gap-1 rounded-xl border border-ui bg-surface-muted px-3 text-sm font-semibold !text-disabled'
                : '!inline-flex min-h-11 cursor-not-allowed select-none items-center gap-1.5 rounded-xl border border-ui bg-surface-muted px-3 text-sm font-semibold !text-disabled';
            $icon_html = '<i class="' . esc_attr($icon_class) . '" aria-hidden="true"></i>';

            if ($is_disabled) {
            ?>
                <span class="<?php echo esc_attr($disabled_classes); ?>" aria-disabled="true">
                    <?php self::render_button_content($label, $icon_html, $icon_after); ?>
                </span>
            <?php
                return;
            }
            ?>
            <a class="<?php echo esc_attr($enabled_classes); ?>" href="<?php echo esc_url($url); ?>">
                <?php self::render_button_content($label, $icon_html, $icon_after); ?>
            </a>
<?php
        }

        /**
         * Render nội dung nút điều hướng.
         *
         * @param string $label      Nhãn của nút.
         * @param string $icon_html  HTML icon đã escape class.
         * @param bool   $icon_after Hiển thị icon sau nhãn.
         *
         * @return void
         */
        private static function render_button_content($label, $icon_html, $icon_after)
        {
            if (!$icon_after) {
                echo wp_kses_post($icon_html);
            }

            echo esc_html($label);

            if ($icon_after) {
                echo wp_kses_post($icon_html);
            }
        }

        /**
         * Gắn utility class cho liên kết số trang do WordPress tạo.
         *
         * @param string $number_link HTML liên kết số trang.
         *
         * @return string
         */
        private static function style_number_link($number_link)
        {
            if (strpos($number_link, 'current') !== false) {
                return str_replace(
                    'page-numbers current',
                    'page-numbers current !inline-flex h-10 min-w-10 items-center justify-center rounded-xl border border-primary bg-primary px-3 text-sm font-bold !text-primary-on shadow-ui-card',
                    $number_link
                );
            }

            if (strpos($number_link, 'dots') !== false) {
                return str_replace(
                    'page-numbers dots',
                    'page-numbers dots !inline-flex h-10 min-w-8 items-center justify-center text-sm font-semibold !text-disabled',
                    $number_link
                );
            }

            return str_replace(
                'page-numbers',
                'page-numbers !inline-flex h-10 min-w-10 items-center justify-center rounded-xl border border-ui bg-surface px-3 text-sm font-semibold !text-heading shadow-ui-card transition-colors hover:border-primary-300 hover:!text-primary',
                $number_link
            );
        }
    }
}
