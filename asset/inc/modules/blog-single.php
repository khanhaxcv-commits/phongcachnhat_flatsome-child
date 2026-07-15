<?php

/**
 * Blog Single
 *
 * Handles single post assets, breadcrumb bar, share links
 * and related posts shortcode.
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Blog_Single')) {
    class Blog_Single
    {
        public static function init()
        {
            add_action('wp_enqueue_scripts', array(__CLASS__, 'enqueue_styles'), 30);
            add_action('flatsome_after_header', array(__CLASS__, 'render_breadcrumb_bar'), 5);

            add_filter('flatsome_share_links', array(__CLASS__, 'filter_share_links'));

            add_shortcode('blog_single_related_posts', array(__CLASS__, 'related_posts_shortcode'));
        }

        /**
         * Enqueue single post CSS.
         */
        public static function enqueue_styles()
        {
            if (!is_singular('post')) {
                return;
            }

            $style_path = get_stylesheet_directory() . '/assets/css/blog-single.css';

            if (!file_exists($style_path)) {
                return;
            }

            $deps = wp_style_is('flatsome-child-style', 'registered')
                ? array('flatsome-child-style')
                : array();

            wp_enqueue_style(
                'blog-single-css',
                get_stylesheet_directory_uri() . '/assets/css/blog-single.css',
                $deps,
                filemtime($style_path)
            );
        }

        /**
         * Show only selected Flatsome share links on single posts.
         */
        public static function filter_share_links($share_links)
        {
            if (!is_singular('post')) {
                return $share_links;
            }

            $allowed_links = array('facebook', 'linkedin', 'x', 'email');

            foreach ($share_links as $key => $share_link) {
                $share_links[$key]['enabled'] = in_array($key, $allowed_links, true);
            }

            if (isset($share_links['linkedin'])) {
                $share_links['linkedin']['priority'] = 30;
            }

            if (isset($share_links['x'])) {
                $share_links['x']['priority'] = 40;
            }

            if (isset($share_links['email'])) {
                $share_links['email']['priority'] = 50;
            }

            return $share_links;
        }

        /**
         * Render breadcrumb bar after Flatsome header.
         */
        public static function render_breadcrumb_bar()
        {
            if (!is_singular('post')) {
                return;
            }
?>
            <div class="blog-single-breadcrumb-bar">
                <div class="container">
                    <?php echo self::get_breadcrumb_html(); ?>
                </div>
            </div>
        <?php
        }

        /**
         * Get breadcrumb HTML.
         */
        public static function get_breadcrumb_html()
        {
            if (function_exists('rank_math_get_breadcrumbs')) {
                $breadcrumb = trim(rank_math_get_breadcrumbs(array(
                    'wrap_before' => '<nav class="blog-single-breadcrumbs" aria-label="Breadcrumb">',
                    'wrap_after'  => '</nav>',
                )));

                if ($breadcrumb !== '') {
                    return $breadcrumb;
                }
            }

            if (function_exists('yoast_breadcrumb')) {
                $breadcrumb = yoast_breadcrumb(
                    '<nav class="blog-single-breadcrumbs" aria-label="Breadcrumb">',
                    '</nav>',
                    false
                );

                if (!empty($breadcrumb)) {
                    return $breadcrumb;
                }
            }

            $items = array();

            $items[] = '<a href="' . esc_url(home_url('/')) . '">Trang chủ</a>';

            $category = self::get_primary_category(get_the_ID());

            if ($category) {
                $category_link = get_category_link($category);

                if (!is_wp_error($category_link)) {
                    $items[] = '<a href="' . esc_url($category_link) . '">' . esc_html($category->name) . '</a>';
                }
            }

            $items[] = '<span>' . esc_html(get_the_title()) . '</span>';

            return '<nav class="blog-single-breadcrumbs" aria-label="Breadcrumb">' .
                implode('<span class="blog-single-breadcrumbs__divider">/</span>', $items) .
                '</nav>';
        }

        /**
         * Related posts shortcode.
         *
         * Usage:
         * [blog_single_related_posts]
         * [blog_single_related_posts limit="3" title="Bài viết liên quan"]
         * [blog_single_related_posts category="tin-tuc,suc-khoe" limit="3"]
         */
        public static function related_posts_shortcode($atts)
        {
            $atts = shortcode_atts(
                array(
                    'limit'             => 3,
                    'title'             => 'Bài viết liên quan',
                    'category'          => '',
                    'show_date'         => 'true',
                    'show_reading_time' => 'false',
                ),
                $atts,
                'blog_single_related_posts'
            );

            $post_id = get_the_ID();
            $category_ids = self::get_related_post_category_ids($atts['category'], $post_id);

            if (empty($category_ids)) {
                return '';
            }

            $related_posts = new WP_Query(array(
                'post_type'           => 'post',
                'post_status'         => 'publish',
                'posts_per_page'      => max(1, absint($atts['limit'])),
                'post__not_in'        => is_singular('post') ? array($post_id) : array(),
                'category__in'        => $category_ids,
                'ignore_sticky_posts' => true,
                'no_found_rows'       => true,
            ));

            if (!$related_posts->have_posts()) {
                return '';
            }

            ob_start();
        ?>
            <section class="blog-single-related-posts">
                <div class="blog-single-related-posts__inner">
                    <?php if (!empty($atts['title'])) : ?>
                        <div class="blog-single-related-posts__head">
                            <h3 class="blog-single-related-posts__title">
                                <?php echo esc_html($atts['title']); ?>
                            </h3>
                        </div>
                    <?php endif; ?>

                    <div class="blog-single-related-posts__grid">
                        <?php while ($related_posts->have_posts()) : ?>
                            <?php $related_posts->the_post(); ?>

                            <article class="blog-single-related-post">
                                <a
                                    class="blog-single-related-post__image"
                                    href="<?php the_permalink(); ?>"
                                    aria-label="<?php echo esc_attr(get_the_title()); ?>">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <?php the_post_thumbnail('medium_large', array('alt' => esc_attr(get_the_title()))); ?>
                                    <?php else : ?>
                                        <img
                                            src="<?php echo esc_url(self::get_default_image_url()); ?>"
                                            alt="<?php echo esc_attr(get_the_title()); ?>">
                                    <?php endif; ?>
                                </a>

                                <h3 class="blog-single-related-post__title">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_title(); ?>
                                    </a>
                                </h3>

                                <?php if ($atts['show_date'] === 'true' || $atts['show_reading_time'] === 'true') : ?>
                                    <div class="blog-single-related-post__meta">
                                        <?php if ($atts['show_date'] === 'true') : ?>
                                            <time datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                                <?php echo esc_html(get_the_date('d/m/Y')); ?>
                                            </time>
                                        <?php endif; ?>

                                        <?php if ($atts['show_reading_time'] === 'true') : ?>
                                            <span>
                                                <?php echo esc_html(self::get_reading_time(get_the_ID())); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </article>

                        <?php endwhile; ?>
                    </div>
                </div>
            </section>
<?php

            wp_reset_postdata();

            return ob_get_clean();
        }

        /**
         * Get category IDs for related posts.
         */
        public static function get_related_post_category_ids($category, $post_id)
        {
            if (!empty($category)) {
                $category_ids = array();
                $category_values = array_map('trim', explode(',', $category));

                foreach ($category_values as $category_value) {
                    $term = is_numeric($category_value)
                        ? get_category((int) $category_value)
                        : get_category_by_slug($category_value);

                    if ($term && !is_wp_error($term)) {
                        $category_ids[] = (int) $term->term_id;
                    }
                }

                return array_unique($category_ids);
            }

            $categories = get_the_category($post_id);

            if (empty($categories) || is_wp_error($categories)) {
                return array();
            }

            return wp_list_pluck($categories, 'term_id');
        }

        /**
         * Get primary category.
         */
        public static function get_primary_category($post_id = null)
        {
            $post_id = $post_id ? absint($post_id) : get_the_ID();

            if (!$post_id) {
                return null;
            }

            if (class_exists('Blog_Post') && method_exists('Blog_Post', 'get_primary_category')) {
                return Blog_Post::get_primary_category($post_id);
            }

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
                if ($primary_id > 0 && in_array($primary_id, $category_ids, true)) {
                    $primary_category = get_category($primary_id);

                    if ($primary_category && !is_wp_error($primary_category)) {
                        return $primary_category;
                    }
                }
            }

            return $categories[0];
        }

        /**
         * Get estimated reading time.
         */
        public static function get_reading_time($post_id = null)
        {
            $post_id = $post_id ? absint($post_id) : get_the_ID();

            if (!$post_id) {
                return '';
            }

            if (class_exists('Blog_Post') && method_exists('Blog_Post', 'get_reading_time')) {
                return Blog_Post::get_reading_time($post_id);
            }

            $content = get_post_field('post_content', $post_id);
            $content = strip_shortcodes($content);
            $content = wp_strip_all_tags($content);

            preg_match_all('/\p{L}+/u', $content, $words);

            $word_count = isset($words[0]) ? count($words[0]) : 0;
            $reading_time = max(1, (int) ceil($word_count / 200));

            return number_format_i18n($reading_time) . ' phút đọc';
        }

        /**
         * Default fallback image.
         */
        public static function get_default_image_url()
        {
            return get_stylesheet_directory_uri() . '/assets/images/blog-single-default.jpg';
        }
    }

    Blog_Single::init();
}
