<?php

defined('ABSPATH') || exit;

/**
 * Load cấu hình bộ lọc.
 */
function get_filter_config()
{
    return require get_stylesheet_directory() . '/inc/product-filter-config.php';
}

/**
 * Lấy phiên bản cache hiện tại của bộ lọc sản phẩm.
 *
 * Khi dữ liệu sản phẩm, danh mục hoặc attribute thay đổi,
 * phiên bản này sẽ được tăng để vô hiệu hóa cache cũ.
 */
function get_product_filter_cache_version()
{
    return max(
        1,
        (int) get_option('product_filter_cache_version', 1)
    );
}

/**
 * Tăng phiên bản cache bộ lọc sản phẩm.
 */
function bump_product_filter_cache_version()
{
    $version = get_product_filter_cache_version();

    update_option(
        'product_filter_cache_version',
        $version + 1,
        false
    );
}

/**
 * Lấy term taxonomy ID của danh mục hiện tại và toàn bộ danh mục con.
 *
 * Giữ nguyên hành vi include_children của tax_query cũ.
 */
function get_product_category_scope_tt_ids($category_id)
{
    static $request_cache = [];

    $category_id = (int) $category_id;

    if ($category_id <= 0) {
        return [];
    }

    if (isset($request_cache[$category_id])) {
        return $request_cache[$category_id];
    }

    $term_ids = [$category_id];

    $child_ids = get_term_children(
        $category_id,
        'product_cat'
    );

    if (!is_wp_error($child_ids) && !empty($child_ids)) {
        $term_ids = array_merge(
            $term_ids,
            array_map('intval', $child_ids)
        );
    }

    $term_ids = array_values(
        array_unique(
            array_filter(
                array_map('intval', $term_ids)
            )
        )
    );

    $term_taxonomy_ids = get_terms([
        'taxonomy'   => 'product_cat',
        'hide_empty' => false,
        'include'    => $term_ids,
        'fields'     => 'tt_ids',
    ]);

    if (
        is_wp_error($term_taxonomy_ids)
        || empty($term_taxonomy_ids)
    ) {
        $request_cache[$category_id] = [];

        return [];
    }

    $request_cache[$category_id] = array_values(
        array_unique(
            array_map('intval', $term_taxonomy_ids)
        )
    );

    return $request_cache[$category_id];
}

/**
 * Lấy bộ lọc động cho danh mục sản phẩm hiện tại.
 */
function get_dynamic_product_filters()
{
    if (!is_product_category()) {
        return [];
    }

    $category = get_queried_object();

    if (
        !$category
        || empty($category->term_id)
        || empty($category->slug)
    ) {
        return [];
    }

    $category_id = (int) $category->term_id;
    $config      = get_filter_config();

    /**
     * Nếu danh mục có cấu hình override thì dùng cấu hình đó.
     * Ngược lại tự động phát hiện attribute đang được sử dụng.
     */
    if (!empty($config['override'][$category->slug])) {
        $taxonomies = $config['override'][$category->slug];
    } else {
        $taxonomies = get_category_attributes_auto($category_id);
    }

    if (empty($taxonomies)) {
        return [];
    }

    $exclude = !empty($config['exclude'])
        ? (array) $config['exclude']
        : [];

    $filters = [];

    foreach ($taxonomies as $taxonomy) {
        $taxonomy = sanitize_key($taxonomy);

        if (
            !$taxonomy
            || in_array($taxonomy, $exclude, true)
            || !taxonomy_exists($taxonomy)
        ) {
            continue;
        }

        $terms = get_attribute_terms_by_category(
            $category_id,
            $taxonomy
        );

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
 * Tự động lấy các taxonomy attribute đang được sử dụng
 * bởi sản phẩm trong danh mục hiện tại.
 */
function get_category_attributes_auto($category_id)
{
    global $wpdb;

    $category_id = (int) $category_id;

    if ($category_id <= 0) {
        return [];
    }

    $cache_version = get_product_filter_cache_version();

    $cache_key = sprintf(
        'category_filter_attributes_%d_v%d',
        $category_id,
        $cache_version
    );

    $cached = get_transient($cache_key);

    if ($cached !== false) {
        return is_array($cached) ? $cached : [];
    }

    $attribute_taxonomies = wc_get_attribute_taxonomy_names();

    if (empty($attribute_taxonomies)) {
        set_transient(
            $cache_key,
            [],
            DAY_IN_SECONDS
        );

        return [];
    }

    $category_tt_ids = get_product_category_scope_tt_ids(
        $category_id
    );

    if (empty($category_tt_ids)) {
        set_transient(
            $cache_key,
            [],
            DAY_IN_SECONDS
        );

        return [];
    }

    $category_placeholders = implode(
        ', ',
        array_fill(
            0,
            count($category_tt_ids),
            '%d'
        )
    );

    $taxonomy_placeholders = implode(
        ', ',
        array_fill(
            0,
            count($attribute_taxonomies),
            '%s'
        )
    );

    $sql = "
        SELECT DISTINCT attribute_taxonomy.taxonomy

        FROM {$wpdb->term_relationships} AS category_relation

        INNER JOIN {$wpdb->term_taxonomy} AS category_taxonomy
            ON category_relation.term_taxonomy_id =
               category_taxonomy.term_taxonomy_id

        INNER JOIN {$wpdb->posts} AS products
            ON products.ID = category_relation.object_id

        INNER JOIN {$wpdb->term_relationships} AS attribute_relation
            ON products.ID = attribute_relation.object_id

        INNER JOIN {$wpdb->term_taxonomy} AS attribute_taxonomy
            ON attribute_relation.term_taxonomy_id =
               attribute_taxonomy.term_taxonomy_id

        WHERE category_taxonomy.taxonomy = 'product_cat'

          AND category_taxonomy.term_taxonomy_id
              IN ({$category_placeholders})

          AND products.post_type = 'product'
          AND products.post_status = 'publish'

          AND attribute_taxonomy.taxonomy
              IN ({$taxonomy_placeholders})
    ";

    $prepare_args = array_merge(
        $category_tt_ids,
        $attribute_taxonomies
    );

    $prepared_sql = $wpdb->prepare(
        $sql,
        ...$prepare_args
    );

    $results = $wpdb->get_col($prepared_sql);

    $results = array_values(
        array_unique(
            array_filter(
                array_map(
                    'sanitize_key',
                    (array) $results
                )
            )
        )
    );

    set_transient(
        $cache_key,
        $results,
        DAY_IN_SECONDS
    );

    return $results;
}

/**
 * Lấy các term của một attribute đang được sử dụng
 * bởi sản phẩm trong danh mục hiện tại.
 */
function get_attribute_terms_by_category($category_id, $taxonomy)
{
    global $wpdb;

    $category_id = (int) $category_id;
    $taxonomy    = sanitize_key($taxonomy);

    if (
        $category_id <= 0
        || !$taxonomy
        || !taxonomy_exists($taxonomy)
    ) {
        return [];
    }

    $cache_version = get_product_filter_cache_version();

    $cache_key = sprintf(
        'category_filter_terms_%d_%s_v%d',
        $category_id,
        $taxonomy,
        $cache_version
    );

    $cached = get_transient($cache_key);

    if ($cached !== false) {
        return is_array($cached) ? $cached : [];
    }

    $category_tt_ids = get_product_category_scope_tt_ids(
        $category_id
    );

    if (empty($category_tt_ids)) {
        set_transient(
            $cache_key,
            [],
            DAY_IN_SECONDS
        );

        return [];
    }

    $category_placeholders = implode(
        ', ',
        array_fill(
            0,
            count($category_tt_ids),
            '%d'
        )
    );

    $sql = "
        SELECT DISTINCT attribute_taxonomy.term_id

        FROM {$wpdb->term_relationships} AS category_relation

        INNER JOIN {$wpdb->term_taxonomy} AS category_taxonomy
            ON category_relation.term_taxonomy_id =
               category_taxonomy.term_taxonomy_id

        INNER JOIN {$wpdb->posts} AS products
            ON products.ID = category_relation.object_id

        INNER JOIN {$wpdb->term_relationships} AS attribute_relation
            ON products.ID = attribute_relation.object_id

        INNER JOIN {$wpdb->term_taxonomy} AS attribute_taxonomy
            ON attribute_relation.term_taxonomy_id =
               attribute_taxonomy.term_taxonomy_id

        WHERE category_taxonomy.taxonomy = 'product_cat'

          AND category_taxonomy.term_taxonomy_id
              IN ({$category_placeholders})

          AND products.post_type = 'product'
          AND products.post_status = 'publish'

          AND attribute_taxonomy.taxonomy = %s
    ";

    $prepare_args = array_merge(
        $category_tt_ids,
        [$taxonomy]
    );

    $prepared_sql = $wpdb->prepare(
        $sql,
        ...$prepare_args
    );

    $term_ids = $wpdb->get_col($prepared_sql);

    $term_ids = array_values(
        array_unique(
            array_filter(
                array_map('intval', (array) $term_ids)
            )
        )
    );

    if (empty($term_ids)) {
        set_transient(
            $cache_key,
            [],
            DAY_IN_SECONDS
        );

        return [];
    }

    $terms = get_terms([
        'taxonomy'   => $taxonomy,
        'include'    => $term_ids,
        'hide_empty' => false,
        'orderby'    => 'name',
        'order'      => 'ASC',
    ]);

    if (is_wp_error($terms)) {
        return [];
    }

    $terms = array_values($terms);

    set_transient(
        $cache_key,
        $terms,
        DAY_IN_SECONDS
    );

    return $terms;
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

/**
 * Vô hiệu hóa cache bộ lọc khi sản phẩm được lưu.
 */
function maybe_bump_product_filter_cache_on_product_save($post_id)
{
    $post_id = (int) $post_id;

    if ($post_id <= 0) {
        return;
    }

    if (
        (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        || wp_is_post_revision($post_id)
        || wp_is_post_autosave($post_id)
    ) {
        return;
    }

    bump_product_filter_cache_version();
}

add_action(
    'save_post_product',
    'maybe_bump_product_filter_cache_on_product_save',
    10,
    1
);

/**
 * Vô hiệu hóa cache trước khi xóa sản phẩm.
 */
function maybe_bump_product_filter_cache_on_product_delete($post_id)
{
    $post_id = (int) $post_id;

    if (
        $post_id <= 0
        || get_post_type($post_id) !== 'product'
    ) {
        return;
    }

    bump_product_filter_cache_version();
}

add_action(
    'before_delete_post',
    'maybe_bump_product_filter_cache_on_product_delete',
    10,
    1
);

/**
 * Vô hiệu hóa cache khi danh mục sản phẩm
 * hoặc attribute term thay đổi.
 */
function maybe_bump_product_filter_cache_on_term_change(
    $term_id,
    $term_taxonomy_id,
    $taxonomy
) {
    unset($term_id, $term_taxonomy_id);

    if (
        $taxonomy !== 'product_cat'
        && strpos($taxonomy, 'pa_') !== 0
    ) {
        return;
    }

    bump_product_filter_cache_version();
}

add_action(
    'created_term',
    'maybe_bump_product_filter_cache_on_term_change',
    10,
    3
);

add_action(
    'edited_term',
    'maybe_bump_product_filter_cache_on_term_change',
    10,
    3
);

add_action(
    'delete_term',
    'maybe_bump_product_filter_cache_on_term_change',
    10,
    3
);
