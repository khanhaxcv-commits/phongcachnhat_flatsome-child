<?php

defined('ABSPATH') || exit;

function ishen_decode_vietnamese_text($text)
{
    $text = (string) $text;

    return html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/**
 * Shortcode hiển thị danh mục con sản phẩm dạng ảnh tròn
 *
 * Cách dùng:
 * [dmsp_con]
 * [dmsp_con columns="4"]
 * [dmsp_con columns="5"]
 * [dmsp_con columns="6"]
 * [dmsp_con ids="15,8,22,10" columns="5"]
 *
 * Ghi chú:
 * - Chỉ hoạt động ở trang danh mục sản phẩm WooCommerce.
 * - Không có danh mục con thì không hiển thị gì.
 * - Nếu truyền ids thì hiển thị đúng thứ tự ids.
 * - columns là số item trên 1 dòng ở desktop.
 */
remove_shortcode('dmsp_con');
add_shortcode('dmsp_con', 'ishen_dmsp_con_shortcode');

function ishen_dmsp_con_shortcode($atts)
{

    /**
     * Chỉ chạy ở trang danh mục sản phẩm WooCommerce.
     * Nếu shortcode bị chèn ở Page/Post/UX Block khác thì dừng ngay.
     */
    if (! function_exists('is_product_category') || ! is_product_category()) {
        return '';
    }

    $atts = shortcode_atts(
        array(
            'ids'        => '',
            'columns'    => 5,
            'hide_empty' => 0,
        ),
        $atts,
        'dmsp_con'
    );

    $taxonomy = 'product_cat';
    $terms    = array();

    /**
     * Xử lý số cột desktop.
     * Cho phép linh động: 1 đến 8 cột.
     */
    $columns = absint($atts['columns']);

    if ($columns < 1) {
        $columns = 5;
    }

    if ($columns > 8) {
        $columns = 8;
    }

    /**
     * Trường hợp 1:
     * Có truyền ids thì lấy đúng các danh mục đó
     * và giữ đúng thứ tự ids.
     *
     * Ví dụ:
     * [dmsp_con ids="15,8,22,10" columns="5"]
     */
    if (! empty($atts['ids'])) {

        $ids = array_filter(
            array_map(
                'absint',
                explode(',', $atts['ids'])
            )
        );

        if (empty($ids)) {
            return '';
        }

        $terms = get_terms(
            array(
                'taxonomy'   => $taxonomy,
                'include'    => $ids,
                'orderby'    => 'include',
                'hide_empty' => (bool) $atts['hide_empty'],
            )
        );
    } else {

        /**
         * Trường hợp 2:
         * Không truyền ids thì tự lấy danh mục hiện tại làm cha,
         * sau đó hiển thị danh mục con của nó.
         *
         * Ví dụ:
         * [dmsp_con columns="5"]
         */
        $current_term = get_queried_object();

        if (
            ! $current_term ||
            ! isset($current_term->term_id) ||
            ! isset($current_term->taxonomy) ||
            $current_term->taxonomy !== $taxonomy
        ) {
            return '';
        }

        $terms = get_terms(
            array(
                'taxonomy'   => $taxonomy,
                'parent'     => absint($current_term->term_id),
                'hide_empty' => (bool) $atts['hide_empty'],
                'orderby'    => 'menu_order',
                'order'      => 'ASC',
            )
        );
    }

    /**
     * Không có danh mục con / danh mục hợp lệ thì không hiển thị gì.
     */
    if (empty($terms) || is_wp_error($terms)) {
        return '';
    }

    ob_start();
?>

    <div class="ishen-dmsp-con-wrap container" style="--dmsp-columns: <?php echo esc_attr($columns); ?>;">

        <?php foreach ($terms as $term) : ?>

            <?php
            if (! $term instanceof WP_Term) {
                continue;
            }

            $term_link = get_term_link($term, $taxonomy);

            if (is_wp_error($term_link)) {
                continue;
            }

            // Decode tên danh mục trước khi đưa ra frontend để không bị hiện dạng Ch&#432;a c&#7853;p nh&#7853;t.
            $term_name = ishen_decode_vietnamese_text($term->name);

            $thumbnail_id = get_term_meta($term->term_id, 'thumbnail_id', true);

            if ($thumbnail_id) {
                $image_html = wp_get_attachment_image(
                    $thumbnail_id,
                    'medium',
                    false,
                    array(
                        'class'   => 'ishen-dmsp-con-img-tag',
                        'alt'     => esc_attr($term_name),
                        'loading' => 'lazy',
                    )
                );
            } else {
                $image_html = '<img class="ishen-dmsp-con-img-tag" src="' . esc_url(wc_placeholder_img_src()) . '" alt="' . esc_attr($term_name) . '" loading="lazy">';
            }
            ?>

            <a class="ishen-dmsp-con-item" href="<?php echo esc_url($term_link); ?>">

                <span class="ishen-dmsp-con-img">
                    <?php echo $image_html; ?>
                </span>

                <span class="ishen-dmsp-con-title">
                    <?php echo esc_html($term_name); ?>
                </span>

            </a>

        <?php endforeach; ?>

    </div>

<?php
    return ob_get_clean();
}
