<?php

/**
 * Load config
 */
function get_filter_config()
{
    return require get_stylesheet_directory() . '/inc/product-filter-config.php';
}

/**
 * Main function
 */
function get_dynamic_product_filters()
{
    if (!is_product_category()) {
        return [];
    }

    $category = get_queried_object();
    if (!$category || empty($category->term_id)) {
        return [];
    }

    $config = get_filter_config();

    // 1. Nếu category có override
    if (!empty($config['override'][$category->slug])) {
        $taxonomies = $config['override'][$category->slug];
    } else {
        // 2. Auto detect
        $taxonomies = get_category_attributes_auto($category->term_id);
    }

    if (empty($taxonomies)) {
        return [];
    }

    // Remove exclude
    $exclude = !empty($config['exclude']) ? $config['exclude'] : [];
    $filters = [];

    foreach ($taxonomies as $taxonomy) {
        if (in_array($taxonomy, $exclude) || !taxonomy_exists($taxonomy)) {
            continue;
        }

        $terms = get_attribute_terms_by_category($category->term_id, $taxonomy);
        if (empty($terms)) {
            continue;
        }

        $filters[] = [
            'key'      => str_replace('pa_', '', $taxonomy),
            'taxonomy' => $taxonomy,
            'label'    => wc_attribute_label($taxonomy),
            'terms'    => $terms,
        ];
    }

    return $filters;
}

/**
 * AUTO lấy attribute từ sản phẩm category
 */
function get_category_attributes_auto($category_id)
{
    $products = get_posts([
        'post_type'      => 'product',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'tax_query'      => [
            [
                'taxonomy' => 'product_cat',
                'field'    => 'term_id',
                'terms'    => $category_id
            ]
        ]
    ]);

    if (empty($products)) {
        return [];
    }

    $attributes = [];
    foreach ($products as $product_id) {
        $product = wc_get_product($product_id);
        if (!$product) {
            continue;
        }

        $product_attributes = $product->get_attributes();
        foreach ($product_attributes as $attribute) {
            // Chỉ lấy taxonomy attribute
            if (!$attribute->is_taxonomy()) {
                continue;
            }
            $attributes[] = $attribute->get_name();
        }
    }

    return array_unique($attributes);
}

/**
 * Lấy term thuộc attribute trong category
 */
function get_attribute_terms_by_category($category_id, $taxonomy)
{
    $products = get_posts([
        'post_type'      => 'product',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'tax_query'      => [
            [
                'taxonomy' => 'product_cat',
                'field'    => 'term_id',
                'terms'    => $category_id
            ]
        ]
    ]);

    if (empty($products)) {
        return [];
    }

    $terms = wp_get_object_terms($products, $taxonomy, ['fields' => 'all']);

    return is_wp_error($terms) ? [] : $terms;
}

/**
 * Render bộ sắp xếp sản phẩm.
 */
function render_custom_catalog_ordering()
{
    $current_order = isset($_GET['orderby'])
        ? wc_clean(wp_unslash($_GET['orderby']))
        : 'menu_order';

    $base_url = remove_query_arg('orderby');

    $orders = [
        'menu_order' => 'Nổi bật',
        'popularity' => 'Bán chạy',
        'date'       => 'Mới nhất',
        'price'      => 'Giá thấp - cao',
        'price-desc' => 'Giá cao - thấp',
    ];
?>

    <div class="custom-product-ordering flex items-center gap-3">

        <label class="ordering-label m-0 shrink-0 text-[14px] font-medium text-[var(--text-soft-ui)]">
            Sắp xếp theo:
        </label>

        <div class="ordering-select-wrap relative w-[240px]">

            <select
                class="ordering-select m-0 h-11 w-full rounded-[var(--radius-input)] border border-[var(--input-border)] bg-[var(--surface-bg-muted)] px-4 text-[14px] font-medium text-[var(--input-text)] outline-none transition duration-200 hover:border-[var(--border-accent)] hover:bg-[var(--surface-bg-accent)] focus:border-[var(--input-focus-border)] focus:bg-[var(--surface-bg)] focus:ring-4 focus:ring-[var(--focus-ring-ui)]"
                onchange="if(this.value){window.location.href=this.value;}"
                aria-label="Sắp xếp sản phẩm">

                <?php foreach ($orders as $key => $label) : ?>

                    <?php
                    $url = add_query_arg(
                        'orderby',
                        $key,
                        $base_url
                    );
                    ?>

                    <option
                        value="<?php echo esc_url($url); ?>"
                        <?php selected($current_order, $key); ?>>

                        <?php echo esc_html($label); ?>

                    </option>

                <?php endforeach; ?>

            </select>

        </div>

    </div>

<?php
}
