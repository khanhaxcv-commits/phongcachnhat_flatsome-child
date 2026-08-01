<?php
/**
 * Danh sách bài viết trong trang chuyên mục.
 *
 * Hiển thị năm bài đầu trong khối nổi bật, sau đó hiển thị các bài còn lại
 * dưới dạng thẻ.
 */

if (have_posts()) : ?>
    <div id="post-list" class="w-full">
        <?php
        $post_ids = array();
        while (have_posts()) :
            the_post();
            $post_ids[] = get_the_ID();
        endwhile;

        $featured_ids = array_slice($post_ids, 0, 5);
        $rest_ids = array_slice($post_ids, 5);
        $has_side_posts = count($featured_ids) > 1;
        $featured_layout_classes = $has_side_posts
            ? 'lg:grid-cols-[minmax(0,6fr)_minmax(360px,4fr)] lg:items-stretch'
            : '';
        $main_post_layout_classes = $has_side_posts
            ? 'lg:aspect-auto lg:self-stretch'
            : '';
        $main_image_layout_classes = $has_side_posts
            ? 'lg:!absolute lg:inset-0 lg:!h-auto'
            : '';
        ?>

        <?php if (!empty($featured_ids)) : ?>
            <section class="!grid grid-cols-1 items-start gap-3.5 mb-6 <?php echo esc_attr($featured_layout_classes); ?>" aria-label="Bài viết nổi bật">
                <?php
                $post = get_post($featured_ids[0]);
                setup_postdata($post);
                $category = Blog_Category_Archive::get_preferred_post_category($featured_ids[0]);
                $category_name = $category ? $category->name : 'Blog';
                $category_link = $category ? get_category_link($category->term_id) : '';
                ?>
                <article <?php post_class('relative z-[1] m-0 aspect-video min-h-0 w-full min-w-0 max-w-full overflow-hidden rounded-xl bg-surface p-0 shadow-ui-card sm:aspect-[16/8] ' . $main_post_layout_classes, $featured_ids[0]); ?>>
                    <a class="relative !block h-full min-w-0 overflow-hidden rounded-none bg-surface-muted <?php echo esc_attr($main_image_layout_classes); ?>" href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>">
                        <?php if (has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail('large', array('class' => '!block h-full w-full object-cover')); ?>
                        <?php endif; ?>
                    </a>
                    <div class="pointer-events-none absolute inset-0 !flex flex-col justify-end rounded-none px-3.5 pb-3.5 pt-12 [background:linear-gradient(180deg,rgba(var(--accent-950-rgb),0)_18%,rgba(var(--accent-950-rgb),0.94)_100%)] sm:px-6 sm:pb-[22px] sm:pt-[72px] sm:[background:linear-gradient(180deg,rgba(var(--accent-950-rgb),0)_28%,rgba(var(--accent-950-rgb),0.9)_100%)]">
                        <?php if ($category_link) : ?>
                            <a class="pointer-events-auto order-1 mb-[7px] !inline-flex self-start rounded-md border border-primary bg-primary px-[9px] py-1.5 !text-primary-on text-[10px] font-extrabold leading-none hover:bg-primary-hover hover:!text-primary-on sm:mb-2.5 sm:px-3 sm:py-2 sm:text-xs" href="<?php echo esc_url($category_link); ?>"><?php echo esc_html($category_name); ?></a>
                        <?php else : ?>
                            <span class="order-1 mb-[7px] !inline-flex self-start rounded-md border border-primary bg-primary px-[9px] py-1.5 !text-primary-on text-[10px] font-extrabold leading-none sm:mb-2.5 sm:px-3 sm:py-2 sm:text-xs"><?php echo esc_html($category_name); ?></span>
                        <?php endif; ?>
                        <time class="order-3 mt-2 !inline-flex items-center gap-1.5 !text-on-dark text-xs font-extrabold leading-[1.2] sm:mt-2.5 sm:gap-2 sm:text-sm" datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                            <span class="h-2.5 w-2.5 flex-none rounded border-2 border-primary bg-primary-100 sm:h-3 sm:w-3" aria-hidden="true"></span>
                            <?php echo esc_html(get_the_date('d.m.Y')); ?>
                        </time>
                        <h2 class="order-2 m-0 max-w-full !line-clamp-3 overflow-hidden [overflow-wrap:anywhere] !text-on-dark text-[19px] font-extrabold leading-[1.18] tracking-normal sm:max-w-[760px] sm:!line-clamp-none sm:text-[28px] sm:leading-[1.2]">
                            <a class="pointer-events-auto !text-on-dark hover:!text-on-dark" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h2>
                    </div>
                </article>

                <?php if ($has_side_posts) : ?>
                    <div class="relative z-[2] !grid min-h-0 w-full min-w-0 max-w-full auto-rows-auto grid-cols-1 content-start gap-y-1.5 sm:gap-y-1">
                <?php endif; ?>

                <?php foreach (array_slice($featured_ids, 1) as $post_id) : ?>
                    <?php
                    $post = get_post($post_id);
                    setup_postdata($post);
                    $category = Blog_Category_Archive::get_preferred_post_category($post_id);
                    $category_name = $category ? $category->name : 'Blog';
                    $category_link = $category ? get_category_link($category->term_id) : '';
                    ?>
                    <article <?php post_class('relative m-0 !flex min-h-32 w-full min-w-0 max-w-full items-stretch gap-0 overflow-hidden rounded-xl border border-ui bg-surface p-0 shadow-ui-card sm:min-h-0 sm:items-center', $post_id); ?>>
                        <a class="!block h-auto w-[132px] flex-[0_0_132px] self-stretch overflow-hidden rounded-none bg-surface-muted" href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('medium', array('class' => '!block h-full w-full object-cover')); ?>
                            <?php elseif (function_exists('wc_placeholder_img')) : ?>
                                <?php echo wc_placeholder_img('medium', array('class' => '!block h-full w-full object-cover')); ?>
                            <?php endif; ?>
                        </a>
                        <div class="!flex min-w-0 flex-[1_1_auto] flex-col justify-center bg-transparent px-3.5 py-3 sm:px-4 sm:py-3.5">
                            <?php if ($category_link) : ?>
                                <a class="mb-[7px] !inline-flex self-start rounded-md border border-primary bg-primary px-[9px] py-1.5 !text-primary-on text-[11px] font-extrabold leading-none hover:bg-primary-hover hover:!text-primary-on" href="<?php echo esc_url($category_link); ?>"><?php echo esc_html($category_name); ?></a>
                            <?php else : ?>
                                <span class="mb-[7px] !inline-flex self-start rounded-md border border-primary bg-primary px-[9px] py-1.5 !text-primary-on text-[11px] font-extrabold leading-none"><?php echo esc_html($category_name); ?></span>
                            <?php endif; ?>
                            <h3 class="m-0 mb-2 !line-clamp-2 overflow-hidden [overflow-wrap:anywhere] !text-heading text-base font-bold leading-[1.28] tracking-normal sm:mb-[9px] sm:text-lg">
                                <a class="!text-heading hover:!text-link-hover" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>
                            <time class="mt-0 !inline-flex items-center !text-sub text-[13px] font-normal leading-[1.2] sm:text-sm" datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                <?php echo esc_html(get_the_date('d.m.Y')); ?>
                            </time>
                        </div>
                    </article>
                <?php endforeach; ?>

                <?php if ($has_side_posts) : ?>
                    </div>
                <?php endif; ?>
                <?php wp_reset_postdata(); ?>
            </section>
        <?php endif; ?>

        <?php if (!empty($rest_ids)) : ?>
            <section class="!grid grid-cols-1 gap-x-4 gap-y-[22px] sm:grid-cols-2 desktop:grid-cols-3" aria-label="Danh sách bài viết">
                <?php foreach ($rest_ids as $post_id) : ?>
                    <?php
                    $post = get_post($post_id);
                    setup_postdata($post);
                    $category = Blog_Category_Archive::get_preferred_post_category($post_id);
                    $category_name = $category ? $category->name : 'Blog';
                    $category_link = $category ? get_category_link($category->term_id) : '';
                    ?>
                    <article <?php post_class('m-0 min-w-0 overflow-hidden rounded-xl border border-ui bg-surface-soft shadow-ui-card', $post_id); ?>>
                        <div class="relative !block aspect-video overflow-hidden rounded-none bg-surface-muted">
                            <a class="!block h-full w-full" href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail('large', array('class' => '!block h-full w-full object-cover')); ?>
                                <?php endif; ?>
                            </a>
                            <?php if ($category_link) : ?>
                                <a class="absolute bottom-2.5 right-2.5 min-w-[78px] rounded-md border border-primary bg-primary px-[11px] py-[7px] !text-primary-on text-center text-xs font-extrabold leading-none hover:bg-primary-hover hover:!text-primary-on" href="<?php echo esc_url($category_link); ?>"><?php echo esc_html($category_name); ?></a>
                            <?php else : ?>
                                <span class="absolute bottom-2.5 right-2.5 min-w-[78px] rounded-md border border-primary bg-primary px-[11px] py-[7px] !text-primary-on text-center text-xs font-extrabold leading-none"><?php echo esc_html($category_name); ?></span>
                            <?php endif; ?>
                        </div>

                        <h2 class="m-0 !line-clamp-2 overflow-hidden px-4 pt-3.5 !text-heading text-lg font-bold leading-[1.35] tracking-normal">
                            <a class="!text-heading hover:!text-link-hover" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h2>

                        <div class="mt-[7px] !flex flex-wrap items-center gap-1.5 px-4 !text-sub text-[13px] leading-[1.35]">
                            Đăng bởi: <a class="!text-primary hover:!text-primary-hover" href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>"><?php the_author(); ?></a>
                            <time class="!inline-flex items-center gap-1.5 !text-inherit" datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                <span class="h-1 w-1 rounded-full bg-primary" aria-hidden="true"></span>
                                <?php echo esc_html(get_the_date('d/m/Y')); ?>
                            </time>
                        </div>

                        <p class="m-0 mt-2 !line-clamp-3 overflow-hidden px-4 pb-4 !text-soft text-sm leading-6">
                            <?php echo esc_html(wp_trim_words(get_the_excerpt(), 26, '...')); ?>
                        </p>
                    </article>
                <?php endforeach; ?>
                <?php wp_reset_postdata(); ?>
            </section>
        <?php endif; ?>

        <?php if (class_exists('Blog_Category_Pagination')) : ?>
            <?php Blog_Category_Pagination::render(); ?>
        <?php else : ?>
            <?php flatsome_posts_pagination(); ?>
        <?php endif; ?>
    </div>
<?php else : ?>

    <?php get_template_part('template-parts/posts/content', 'none'); ?>

<?php endif; ?>
