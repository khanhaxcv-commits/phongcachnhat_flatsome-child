<?php
/**
 * Posts single.
 *
 * Child theme override: custom post header with breadcrumbs, title, meta and share.
 */

if (have_posts()): ?>

    <?php while (have_posts()):
        the_post(); ?>

        <article id="post-<?php the_ID(); ?>" <?php post_class('cvn-single-post'); ?>>
            <div class="article-inner <?php flatsome_blog_article_classes(); ?>">
                <div class="cvn-single-meta-row">
                    <div class="cvn-single-meta-column">
                        <div class="cvn-single-meta">
                            <p class="cvn-single-author">
                                <?php echo get_avatar(get_the_author_meta('ID'), 42); ?>
                                <span>Phong Cách Nhật</span>
                            </p>

                            <time class="cvn-single-date" datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                <span class="cvn-single-date__icon" aria-hidden="true"></span>
                                <?php echo esc_html(get_the_date('d/m/Y')); ?>
                            </time>
                        </div>

                    <?php if (get_theme_mod('blog_share', 1)): ?>
                        <div class="cvn-single-share">
                            <!-- <span>Chia sẻ</span> -->
                            <?php echo do_shortcode('[share]'); ?>
                        </div>
                    <?php endif; ?>
                    </div>
                    <div class="cvn-single-form-column">
                        <!-- <?php echo do_shortcode('[contact-form-7 id="fc17c5b" title="Số điện thoại"]'); ?> -->
                    </div>
                </div>

                <?php get_template_part('template-parts/posts/content', 'single'); ?>
            </div>
        </article>

    <?php endwhile; ?>

<?php else: ?>

    <?php get_template_part('no-results', 'index'); ?>

<?php endif; ?>
