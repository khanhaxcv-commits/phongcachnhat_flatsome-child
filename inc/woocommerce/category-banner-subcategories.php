<?php

defined('ABSPATH') || exit;

// Đăng ký action hooks
add_action('category_banner', 'render_category_banner', 10);
add_action('category_subcategories', 'render_category_subcategories', 10);

/**
 * Render banner danh mục.
 */
function render_category_banner()
{
    $term = get_queried_object();

    if ($term && property_exists($term, 'term_id')) {
        $term_id  = $term->term_id;
        $taxonomy = $term->taxonomy;

        // Lấy banner từ ACF field
        $banner = get_field('banner', $taxonomy . '_' . $term_id);

        if ($banner) {
            echo '<div class="rounded-md overflow-hidden">';
            echo wp_get_attachment_image($banner, 'full');
            echo '</div>';
        }
    }
}


/**
 * Render các danh mục sản phẩm con.
 */
function render_category_subcategories()
{
    if (!is_product_category()) {
        return;
    }

    $current_category = get_queried_object();

    if (!$current_category || empty($current_category->term_id)) {
        return;
    }

    $subcategories = get_terms([
        'taxonomy'   => 'product_cat',
        'parent'     => (int) $current_category->term_id,
        'hide_empty' => true,
        'orderby'    => 'menu_order',
        'order'      => 'ASC',
    ]);

    if (empty($subcategories) || is_wp_error($subcategories)) {
        return;
    }
?>

    <div class="category-subcategories mt-5" aria-labelledby="category-subcategories-title">
        <div class="rounded-[var(--radius-lg)] border border-[var(--border-ui)] bg-[var(--surface-bg)] px-4 py-5 sm:px-5">

            <div class="mb-5 flex items-start gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-[var(--radius-input)] bg-[var(--surface-bg-accent)] text-[var(--accent)]">
                    <i class="fa-light fa-grid-2 text-[18px]" aria-hidden="true"></i>
                </div>

                <div class="min-w-0">
                    <h2 id="category-subcategories-title" class="m-0 text-[16px] font-semibold leading-tight text-[var(--text-heading)]">
                        Danh mục sản phẩm
                    </h2>

                    <p class="mb-0 mt-1 text-[13px] leading-tight text-[var(--text-soft-ui)]">
                        Khám phá thiết bị theo từng nhu cầu sử dụng
                    </p>
                </div>
            </div>

            <div class="flex snap-x snap-mandatory gap-3 overflow-x-auto pb-2 pr-4 lg:grid lg:grid-cols-3 lg:overflow-visible lg:pb-0 lg:pr-0 xl:grid-cols-4">
                <?php foreach ($subcategories as $subcategory) : ?>
                    <?php
                    $subcategory_link = get_term_link($subcategory);

                    if (is_wp_error($subcategory_link)) {
                        continue;
                    }

                    $thumbnail_id = (int) get_term_meta(
                        $subcategory->term_id,
                        'thumbnail_id',
                        true
                    );

                    $category_name = $subcategory->name;

                    $category_description = trim(
                        wp_strip_all_tags(
                            term_description(
                                $subcategory->term_id,
                                'product_cat'
                            )
                        )
                    );

                    if (!$category_description) {
                        $category_description = sprintf(
                            '%d sản phẩm đang có',
                            (int) $subcategory->count
                        );
                    }

                    $category_description = wp_trim_words(
                        $category_description,
                        8,
                        '...'
                    );
                    ?>

                    <a href="<?php echo esc_url($subcategory_link); ?>" class="group w-[280px] shrink-0 snap-start no-underline sm:w-[300px] lg:w-auto" aria-label="<?php echo esc_attr(sprintf('Xem danh mục %s', $category_name)); ?>">
                        <div class="relative flex h-full min-h-[116px] items-center overflow-hidden rounded-[var(--radius-sm)] bg-[var(--surface-bg-muted)] p-3 transition duration-200 hover:bg-[var(--primary-50)] hover:shadow-[var(--shadow-ui-card)] sm:min-h-[126px]">

                            <div class="flex h-[86px] w-[86px] shrink-0 items-center justify-center overflow-hidden rounded-[var(--radius-pill] bg-[var(--surface-bg)] sm:h-[96px] sm:w-[96px]">
                                <?php if ($thumbnail_id) : ?>
                                    <?php
                                    echo wp_get_attachment_image(
                                        $thumbnail_id,
                                        'woocommerce_thumbnail',
                                        false,
                                        [
                                            'class'    => 'h-full w-full object-contain p-2 transition duration-300 group-hover:scale-105',
                                            'loading'  => 'lazy',
                                            'decoding' => 'async',
                                            'alt'      => esc_attr($category_name),
                                        ]
                                    );
                                    ?>
                                <?php else : ?>
                                    <i class="fa-light fa-image text-[30px] text-[var(--text-light)]" aria-hidden="true"></i>
                                <?php endif; ?>
                            </div>

                            <div class="min-w-0 flex-1 px-3 pr-10">
                                <h3 class="m-0 line-clamp-2 text-[15px] font-semibold leading-[22px] text-[var(--text-heading)] transition-colors duration-200 group-hover:text-[var(--primary-hover)]">
                                    <?php echo esc_html($category_name); ?>
                                </h3>

                                <p class="m-0 mt-1 line-clamp-2 text-[12px] leading-[18px] text-[var(--text-sub)]">
                                    <?php echo esc_html($category_description); ?>
                                </p>
                            </div>

                            <span class="absolute bottom-3 right-3 flex h-8 w-8 items-center justify-center rounded-full bg-[var(--surface-bg)] text-[var(--primary)] shadow-sm transition duration-200 group-hover:bg-[var(--primary)] group-hover:text-[var(--primary-on)]">
                                <i class="fa-light fa-chevron-right text-[12px]" aria-hidden="true"></i>
                            </span>

                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

        </div>
    </div>

<?php
}
