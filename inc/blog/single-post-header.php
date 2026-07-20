<?php

defined('ABSPATH') || exit;

// ----------------------------

if (false && !function_exists('cvn_single_post_breadcrumb')) {
    function cvn_single_post_breadcrumb()
    {
        if (!is_singular('post')) {
            return;
        }

        $items = array(
            array(
                'label' => __('Trang chủ', 'flatsome'),
                'url'   => home_url('/'),
            ),
        );

        $categories = get_the_category();

        if (!empty($categories) && !is_wp_error($categories)) {
            $primary_category = $categories[0];
            $primary_category_id = (int) get_post_meta(
                get_the_ID(),
                'rank_math_primary_category',
                true
            );

            if ($primary_category_id) {
                foreach ($categories as $category) {
                    if ((int) $category->term_id === $primary_category_id) {
                        $primary_category = $category;
                        break;
                    }
                }
            }

            $category_link = get_category_link($primary_category);

            if (!is_wp_error($category_link)) {
                $items[] = array(
                    'label' => $primary_category->name,
                    'url'   => $category_link,
                );
            }
        }

        $items[] = array(
            'label' => get_the_title(),
            'url'   => '',
        );

        echo '<nav class="cvn-single-breadcrumbs" aria-label="' . esc_attr__('Breadcrumb', 'flatsome') . '">';

        $last_index = count($items) - 1;

        foreach ($items as $index => $item) {
            if ($index > 0) {
                echo '<span class="cvn-single-breadcrumbs__divider" aria-hidden="true">/</span>';
            }

            if ($index === $last_index || empty($item['url'])) {
                echo '<span>' . esc_html($item['label']) . '</span>';
                continue;
            }

            echo '<a href="' . esc_url($item['url']) . '">' . esc_html($item['label']) . '</a>';
        }

        echo '</nav>';
    }
}

function cvn_single_post_breadcrumb()
{
    if (function_exists('rank_math_get_breadcrumbs')) {
        $breadcrumb = trim(
            rank_math_get_breadcrumbs(
                array(
                    'wrap_before' => '<nav class="cvn-single-breadcrumbs" aria-label="Breadcrumb">',
                    'wrap_after' => '</nav>',
                    'separator' => '<span class="cvn-single-breadcrumbs__divider">›</span>',
                )
            )
        );

        if ($breadcrumb !== '') {
            echo $breadcrumb;
            return;
        }
    }

    $items = array(
        '<a href="' . esc_url(home_url('/')) . '">Trang chủ</a>',
    );

    $categories = get_the_category();

    if (!empty($categories)) {
        $category = $categories[0];
        $ancestors = array_reverse(
            get_ancestors((int) $category->term_id, 'category')
        );

        foreach ($ancestors as $ancestor_id) {
            $ancestor = get_category($ancestor_id);

            if (!$ancestor || is_wp_error($ancestor)) {
                continue;
            }

            $items[] = '<a href="' . esc_url(get_category_link($ancestor)) . '">' . esc_html($ancestor->name) . '</a>';
        }

        $items[] = '<a href="' . esc_url(get_category_link($category)) . '">' . esc_html($category->name) . '</a>';
    }

    $items[] = '<span>' . esc_html(get_the_title()) . '</span>';

    echo '<nav class="cvn-single-breadcrumbs" aria-label="Breadcrumb">' .
        implode(
            '<span class="cvn-single-breadcrumbs__divider">›</span>',
            $items
        ) .
        '</nav>';
}

add_action('flatsome_before_blog', 'cvn_render_single_post_header', 20);

function cvn_render_single_post_header()
{
    if (!is_singular('post')) {
        return;
    }

    $post_id = get_queried_object_id();
    $thumbnail_id = $post_id ? get_post_thumbnail_id($post_id) : 0;
    $hero_class = $thumbnail_id
        ? 'cvn-single-hero'
        : 'cvn-single-hero cvn-single-hero--no-image';

?>
    <div class="cvn-single-breadcrumb-bar">
        <?php cvn_single_post_breadcrumb(); ?>
    </div>

    <div class="cvn-single-header">
        <div class="<?php echo esc_attr($hero_class); ?>">
            <div class="cvn-single-hero__content">
                <h1 class="cvn-single-title"><?php the_title(); ?></h1>
            </div>

            <?php if ($thumbnail_id) : ?>
                <div class="cvn-single-hero__image">
                    <?php
                    echo wp_get_attachment_image(
                        $thumbnail_id,
                        'full',
                        false,
                        array(
                            'loading' => 'eager',
                        )
                    );
                    ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php
}
