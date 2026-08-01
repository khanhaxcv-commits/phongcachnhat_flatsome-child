<?php

/**
 * Blog Category Archive
 *
 * Handles blog category archive hero, category image field, breadcrumbs,
 * Flatsome default title removal and category administration.
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Blog_Category_Archive')) {
    class Blog_Category_Archive
    {
        const META_KEY = '_blog_category_hero_image_id';
        const NONCE_ACTION = 'save_blog_category_hero_image';
        const NONCE_NAME = 'blog_category_hero_image_nonce';

        public static function init()
        {
            add_action('wp_enqueue_scripts', array(__CLASS__, 'enqueue_assets'), 30);

            add_action('wp', array(__CLASS__, 'hide_default_archive_title'));

            add_action('category_add_form_fields', array(__CLASS__, 'add_admin_field'));
            add_action('category_edit_form_fields', array(__CLASS__, 'edit_admin_field'));

            add_action('created_category', array(__CLASS__, 'save_admin_field'));
            add_action('edited_category', array(__CLASS__, 'save_admin_field'));

            add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_admin_assets'));

            add_shortcode('blog_category_hero_content', array(__CLASS__, 'hero_content_shortcode'));
        }

        /**
         * Enqueue optional frontend assets for blog category archives.
         */
        public static function enqueue_assets()
        {
            if (!is_category()) {
                return;
            }

            $theme_dir = get_stylesheet_directory();
            $theme_uri = get_stylesheet_directory_uri();
            $css_path = $theme_dir . '/assets/css/pages/blog-category.css';
            $js_path = $theme_dir . '/assets/js/blog-category.js';

            if (file_exists($css_path) && filesize($css_path) > 0) {
                $css_dependencies = wp_style_is('tailwind-css', 'registered')
                    ? array('tailwind-css')
                    : array();

                wp_enqueue_style(
                    'blog-category-css',
                    $theme_uri . '/assets/css/pages/blog-category.css',
                    $css_dependencies,
                    filemtime($css_path)
                );
            }

            if (file_exists($js_path) && filesize($js_path) > 0) {
                wp_enqueue_script(
                    'blog-category-js',
                    $theme_uri . '/assets/js/blog-category.js',
                    array('jquery'),
                    filemtime($js_path),
                    true
                );
            }
        }

        /**
         * Hide Flatsome default archive title to avoid duplicated H1.
         */
        public static function hide_default_archive_title()
        {
            if (!is_category()) {
                return;
            }

            if (function_exists('flatsome_archive_title')) {
                remove_action('flatsome_before_blog', 'flatsome_archive_title', 15);
            }
        }

        /**
         * Enqueue WordPress media uploader and admin inline script.
         */
        public static function enqueue_admin_assets($hook)
        {
            if (!in_array($hook, array('edit-tags.php', 'term.php'), true)) {
                return;
            }

            $screen = get_current_screen();

            if (!$screen || $screen->taxonomy !== 'category') {
                return;
            }

            wp_enqueue_media();
            wp_enqueue_script('jquery');

            wp_add_inline_script('jquery', self::get_admin_media_script());
        }

        /**
         * Admin media uploader script.
         */
        private static function get_admin_media_script()
        {
            return "
                jQuery(function($) {
                    $(document).on('click', '.blog-category-hero-image__upload', function(e) {
                        e.preventDefault();

                        var button = $(this);
                        var field = button.closest('.blog-category-hero-image');
                        var frame = wp.media({
                            title: 'Chọn ảnh hero danh mục',
                            button: {
                                text: 'Dùng ảnh này'
                            },
                            multiple: false
                        });

                        frame.on('select', function() {
                            var attachment = frame.state().get('selection').first().toJSON();
                            var imageUrl = attachment.sizes && attachment.sizes.medium ? attachment.sizes.medium.url : attachment.url;

                            field.find('.blog-category-hero-image__id').val(attachment.id);
                            field.find('.blog-category-hero-image__preview').html(
                                '<img src=\"' + imageUrl + '\" alt=\"\" style=\"max-width:260px;height:auto;display:block;margin-top:10px;border-radius:6px;\" />'
                            );
                            field.find('.blog-category-hero-image__remove').show();
                        });

                        frame.open();
                    });

                    $(document).on('click', '.blog-category-hero-image__remove', function(e) {
                        e.preventDefault();

                        var button = $(this);
                        var field = button.closest('.blog-category-hero-image');

                        field.find('.blog-category-hero-image__id').val('');
                        field.find('.blog-category-hero-image__preview').empty();
                        button.hide();
                    });
                });
            ";
        }

        /**
         * Render shared image field UI.
         */
        private static function render_image_field($selected_image_id = 0)
        {
            $selected_image_id = absint($selected_image_id);
            $image_url = $selected_image_id ? wp_get_attachment_image_url($selected_image_id, 'medium') : '';
?>
            <div class="blog-category-hero-image">
                <input
                    type="hidden"
                    class="blog-category-hero-image__id"
                    name="blog_category_hero_image_id"
                    value="<?php echo esc_attr($selected_image_id); ?>">

                <button type="button" class="button blog-category-hero-image__upload">
                    Chọn ảnh
                </button>

                <button
                    type="button"
                    class="button blog-category-hero-image__remove"
                    style="<?php echo $image_url ? '' : 'display:none;'; ?>">
                    Xóa ảnh
                </button>

                <div class="blog-category-hero-image__preview">
                    <?php if ($image_url) : ?>
                        <img
                            src="<?php echo esc_url($image_url); ?>"
                            alt=""
                            style="max-width:260px;height:auto;display:block;margin-top:10px;border-radius:6px;">
                    <?php endif; ?>
                </div>
            </div>
        <?php
        }

        /**
         * Add image field on category create screen.
         */
        public static function add_admin_field()
        {
            wp_nonce_field(self::NONCE_ACTION, self::NONCE_NAME);
        ?>
            <div class="form-field term-blog-category-hero-image-wrap">
                <label>Ảnh hero danh mục</label>

                <?php self::render_image_field(); ?>

                <p>
                    Ảnh này sẽ hiển thị ở phần hero đầu trang danh mục bài viết.
                </p>
            </div>
        <?php
        }

        /**
         * Add image field on category edit screen.
         */
        public static function edit_admin_field($term)
        {
            $selected_image_id = (int) get_term_meta($term->term_id, self::META_KEY, true);

            wp_nonce_field(self::NONCE_ACTION, self::NONCE_NAME);
        ?>
            <tr class="form-field term-blog-category-hero-image-wrap">
                <th scope="row">
                    <label>Ảnh hero danh mục</label>
                </th>
                <td>
                    <?php self::render_image_field($selected_image_id); ?>

                    <p class="description">
                        Ảnh này sẽ hiển thị ở phần hero đầu trang danh mục bài viết.
                    </p>
                </td>
            </tr>
        <?php
        }

        /**
         * Save category hero image ID.
         */
        public static function save_admin_field($term_id)
        {
            $nonce = isset($_POST[self::NONCE_NAME])
                ? sanitize_text_field(wp_unslash($_POST[self::NONCE_NAME]))
                : '';

            if (!$nonce || !wp_verify_nonce($nonce, self::NONCE_ACTION)) {
                return;
            }

            if (!current_user_can('manage_categories') && !current_user_can('manage_options')) {
                return;
            }

            $image_id = isset($_POST['blog_category_hero_image_id'])
                ? absint(wp_unslash($_POST['blog_category_hero_image_id']))
                : 0;

            if ($image_id > 0) {
                update_term_meta($term_id, self::META_KEY, $image_id);
            } else {
                delete_term_meta($term_id, self::META_KEY);
            }
        }

        /**
         * Get category hero image URL.
         */
        public static function get_hero_image_url($term_id)
        {
            $image_id = (int) get_term_meta($term_id, self::META_KEY, true);
            $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'full') : '';

            if ($image_url) {
                return $image_url;
            }

            $fallback_path = get_stylesheet_directory() . '/assets/images/blog-category-default.jpg';

            if (file_exists($fallback_path)) {
                return get_stylesheet_directory_uri() . '/assets/images/blog-category-default.jpg';
            }

            return '';
        }

        /**
         * Get the preferred category for a post.
         */
        public static function get_preferred_post_category($post_id)
        {
            $categories = get_the_category($post_id);

            if (empty($categories) || is_wp_error($categories)) {
                return null;
            }

            $category_ids = wp_list_pluck($categories, 'term_id');
            $primary_ids = array(
                (int) get_post_meta($post_id, 'rank_math_primary_category', true),
                (int) get_post_meta($post_id, '_yoast_wpseo_primary_category', true),
            );

            foreach ($primary_ids as $primary_id) {
                if ($primary_id <= 0 || !in_array($primary_id, $category_ids, true)) {
                    continue;
                }

                $primary_category = get_category($primary_id);

                if ($primary_category && !is_wp_error($primary_category)) {
                    return $primary_category;
                }
            }

            return $categories[0];
        }

        /**
         * Render category hero in the custom category template.
         */
        public static function render_hero()
        {
            if (!is_category()) {
                return;
            }

            $term = get_queried_object();

            if (!$term || empty($term->term_id) || is_wp_error($term)) {
                return;
            }

            $hero_image_url = self::get_hero_image_url((int) $term->term_id);
            $hero_background = $hero_image_url
                ? "url('" . esc_url($hero_image_url) . "')"
                : 'none';
            $description = term_description((int) $term->term_id, 'category');
        ?>
            <section
                class="relative min-h-[132px] w-auto bg-cover bg-center sm:min-h-[230px] [background-image:linear-gradient(90deg,rgba(var(--white-rgb),0.9)_0%,rgba(var(--white-rgb),0.72)_48%,rgba(var(--white-rgb),0.2)_100%),var(--blog-category-banner-image)]"
                style="--blog-category-banner-image: <?php echo esc_attr($hero_background); ?>;">
                <div class="container !flex min-h-[132px] flex-col justify-center px-[15px] pb-6 pt-[22px] sm:min-h-[230px]">
                    <?php echo self::get_breadcrumb_html($term); ?>

                    <h1 class="m-0 !text-heading-primary text-[clamp(32px,4vw,46px)] font-extrabold leading-[1.05] tracking-normal [text-shadow:0_1px_2px_rgba(var(--white-rgb),0.55)]">
                        <?php echo esc_html(single_cat_title('', false)); ?>
                    </h1>

                    <?php if ($description) : ?>
                        <div class="mt-2.5 max-w-[680px] !text-soft text-[15px] leading-relaxed [&>*:last-child]:!mb-0">
                            <?php echo wp_kses_post($description); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        <?php
        }

        /**
         * Get breadcrumb HTML.
         */
        public static function get_breadcrumb_html($term)
        {
            if (function_exists('rank_math_get_breadcrumbs')) {
                $breadcrumb = trim(rank_math_get_breadcrumbs(array(
                    'wrap_before' => '<nav class="breadcrumbs !mb-1 !flex flex-wrap items-center gap-1.5 !text-sub leading-[1.4] [&_.divider]:!text-inherit [&_a]:!text-link [&_a:hover]:!text-link-hover [&_span]:!text-inherit" aria-label="Breadcrumb">',
                    'wrap_after'  => '</nav>',
                )));

                if ($breadcrumb !== '') {
                    return $breadcrumb;
                }
            }

            $items = array(
                '<a href="' . esc_url(home_url('/')) . '">Trang chủ</a>',
            );

            $ancestors = array_reverse(get_ancestors((int) $term->term_id, 'category'));

            foreach ($ancestors as $ancestor_id) {
                $ancestor = get_category($ancestor_id);

                if (!$ancestor || is_wp_error($ancestor)) {
                    continue;
                }

                $items[] = '<a href="' . esc_url(get_category_link($ancestor)) . '">' . esc_html($ancestor->name) . '</a>';
            }

            $items[] = '<span>' . esc_html($term->name) . '</span>';

            return '<nav class="breadcrumbs !mb-1 !flex flex-wrap items-center gap-1.5 !text-sub leading-[1.4] [&_.divider]:!text-inherit [&_a]:!text-link [&_a:hover]:!text-link-hover [&_span]:!text-inherit" aria-label="Breadcrumb">' . implode('<span class="divider">/</span>', $items) . '</nav>';
        }

        /**
         * Shortcode for custom hero content if needed.
         *
         * Usage:
         * [blog_category_hero_content]
         * [blog_category_hero_content show_breadcrumb="yes" show_description="yes" desc_words="32"]
         */
        public static function hero_content_shortcode($atts)
        {
            if (!is_category()) {
                return '';
            }

            $term = get_queried_object();

            if (!$term || empty($term->term_id) || is_wp_error($term)) {
                return '';
            }

            $atts = shortcode_atts(array(
                'desc_words'       => 32,
                'show_breadcrumb'  => 'yes',
                'show_description' => 'yes',
            ), $atts, 'blog_category_hero_content');

            $description = term_description((int) $term->term_id, 'category');
            $description = $description
                ? wp_trim_words(wp_strip_all_tags($description), absint($atts['desc_words']), '...')
                : '';

            ob_start();
        ?>
            <div>
                <?php if ($atts['show_breadcrumb'] === 'yes') : ?>
                    <?php echo self::get_breadcrumb_html($term); ?>
                <?php endif; ?>

                <h1 class="m-0 !text-heading text-[clamp(32px,4vw,46px)] font-extrabold leading-tight tracking-normal">
                    <?php echo esc_html(single_cat_title('', false)); ?>
                </h1>

                <?php if ($atts['show_description'] === 'yes' && $description !== '') : ?>
                    <p class="mt-2.5 !text-soft text-[15px] leading-relaxed">
                        <?php echo esc_html($description); ?>
                    </p>
                <?php endif; ?>
            </div>
<?php

            return ob_get_clean();
        }
    }

    Blog_Category_Archive::init();
}
