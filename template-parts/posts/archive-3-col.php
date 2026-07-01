<?php
/**
 * Posts archive 3 column.
 *
 * Child theme override: render the first five posts as a featured block,
 * then render the remaining posts as custom cards.
 */

if (!function_exists('cvn_get_preferred_post_category')) {
    function cvn_get_preferred_post_category($post_id)
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
            if ($primary_id > 0 && in_array($primary_id, $category_ids, true)) {
                $primary_category = get_category($primary_id);

                if ($primary_category && !is_wp_error($primary_category)) {
                    return $primary_category;
                }
            }
        }

        return $categories[0];
    }
}

if (!function_exists('cvn_get_preferred_post_category_name')) {
    function cvn_get_preferred_post_category_name($post_id)
    {
        $category = cvn_get_preferred_post_category($post_id);

        return $category ? $category->name : 'Blog';
    }
}

if (have_posts()) : ?>
    <div id="post-list">
        <?php
        $post_ids = array();
        while (have_posts()) :
            the_post();
            $post_ids[] = get_the_ID();
        endwhile;

        $featured_ids = array_slice($post_ids, 0, 5);
        $rest_ids = array_slice($post_ids, 5);
        ?>

        <?php if (!empty($featured_ids)) : ?>
            <section class="cvn-featured-posts <?php echo count($featured_ids) > 1 ? 'cvn-featured-posts--has-side' : 'cvn-featured-posts--single'; ?>" aria-label="Bai viet noi bat">
                <?php
                $post = get_post($featured_ids[0]);
                setup_postdata($post);
                $category = cvn_get_preferred_post_category($featured_ids[0]);
                $category_name = $category ? $category->name : 'Blog';
                $category_link = $category ? get_category_link($category->term_id) : '';
                ?>
                <article <?php post_class('cvn-featured-post cvn-featured-post--main', $featured_ids[0]); ?>>
                    <a class="cvn-featured-post__image" href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>">
                        <?php if (has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail('large'); ?>
                        <?php endif; ?>
                    </a>
                    <div class="cvn-featured-post__overlay">
                        <?php if ($category_link) : ?>
                            <a class="cvn-featured-post__label" href="<?php echo esc_url($category_link); ?>"><?php echo esc_html($category_name); ?></a>
                        <?php else : ?>
                            <span class="cvn-featured-post__label"><?php echo esc_html($category_name); ?></span>
                        <?php endif; ?>
                        <time class="cvn-featured-post__date" datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                            <?php echo esc_html(get_the_date('d.m.Y')); ?>
                        </time>
                        <h2 class="cvn-featured-post__title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h2>
                    </div>
                </article>

                <?php if (count($featured_ids) > 1) : ?>
                    <div class="cvn-featured-posts__side">
                <?php endif; ?>

                <?php foreach (array_slice($featured_ids, 1) as $post_id) : ?>
                    <?php
                    $post = get_post($post_id);
                    setup_postdata($post);
                    $category = cvn_get_preferred_post_category($post_id);
                    $category_name = $category ? $category->name : 'Blog';
                    $category_link = $category ? get_category_link($category->term_id) : '';
                    ?>
                    <article <?php post_class('cvn-featured-post cvn-featured-post--side', $post_id); ?>>
                        <a class="cvn-featured-post__thumb" href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('medium'); ?>
                            <?php elseif (function_exists('wc_placeholder_img')) : ?>
                                <?php echo wc_placeholder_img('medium'); ?>
                            <?php endif; ?>
                        </a>
                        <div class="cvn-featured-post__body">
                            <?php if ($category_link) : ?>
                                <a class="cvn-featured-post__side-label" href="<?php echo esc_url($category_link); ?>"><?php echo esc_html($category_name); ?></a>
                            <?php else : ?>
                                <span class="cvn-featured-post__side-label"><?php echo esc_html($category_name); ?></span>
                            <?php endif; ?>
                            <h3 class="cvn-featured-post__side-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>
                            <time class="cvn-featured-post__side-date" datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                <?php echo esc_html(get_the_date('d.m.Y')); ?>
                            </time>
                        </div>
                    </article>
                <?php endforeach; ?>

                <?php if (count($featured_ids) > 1) : ?>
                    </div>
                <?php endif; ?>
                <?php wp_reset_postdata(); ?>
            </section>
        <?php endif; ?>

        <?php if (!empty($rest_ids)) : ?>
            <section class="cvn-post-card-grid" aria-label="Danh sach bai viet">
                <?php foreach ($rest_ids as $post_id) : ?>
                    <?php
                    $post = get_post($post_id);
                    setup_postdata($post);
                    $category = cvn_get_preferred_post_category($post_id);
                    $category_name = $category ? $category->name : 'Blog';
                    $category_link = $category ? get_category_link($category->term_id) : '';
                    ?>
                    <article <?php post_class('cvn-post-card', $post_id); ?>>
                        <div class="cvn-post-card__image">
                            <a class="cvn-post-card__image-link" href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail('large'); ?>
                                <?php endif; ?>
                            </a>
                            <?php if ($category_link) : ?>
                                <a class="cvn-post-card__category" href="<?php echo esc_url($category_link); ?>"><?php echo esc_html($category_name); ?></a>
                            <?php else : ?>
                                <span class="cvn-post-card__category"><?php echo esc_html($category_name); ?></span>
                            <?php endif; ?>
                        </div>

                        <h2 class="cvn-post-card__title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h2>

                        <div class="cvn-post-card__author">
                            Dang boi: <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>"><?php the_author(); ?></a>
                            <time class="cvn-post-card__meta-date" datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                <?php echo esc_html(get_the_date('d/m/Y')); ?>
                            </time>
                        </div>

                        <p class="cvn-post-card__excerpt">
                            <?php echo esc_html(wp_trim_words(get_the_excerpt(), 26, '...')); ?>
                        </p>
                    </article>
                <?php endforeach; ?>
                <?php wp_reset_postdata(); ?>
            </section>
        <?php endif; ?>

        <?php flatsome_posts_pagination(); ?>
    </div>
<?php else : ?>

    <?php get_template_part('template-parts/posts/content', 'none'); ?>

<?php endif; ?>
