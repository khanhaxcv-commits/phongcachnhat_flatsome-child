<?php

defined('ABSPATH') || exit;

/**
 * Load cấu hình bộ lọc.
 */
function get_filter_config()
{
    static $config = null;

    if ($config !== null) {
        return $config;
    }

    $config = require get_stylesheet_directory()
        . '/inc/product-filter-config.php';

    return is_array($config) ? $config : [];
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
    static $bumped_this_request = false;

    if ($bumped_this_request) {
        return;
    }

    $version = get_product_filter_cache_version();

    update_option(
        'product_filter_cache_version',
        $version + 1,
        false
    );

    $bumped_this_request = true;
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
function get_product_filter_taxonomies_for_category($category_id)
{
    static $request_cache = [];

    $category_id = (int) $category_id;

    if ($category_id <= 0) {
        return [];
    }

    if (isset($request_cache[$category_id])) {
        return $request_cache[$category_id];
    }

    $category = get_term($category_id, 'product_cat');

    if (!$category || is_wp_error($category)) {
        $request_cache[$category_id] = [];

        return [];
    }

    $config = get_filter_config();

    if (!empty($config['override'][$category->slug])) {
        $taxonomies = $config['override'][$category->slug];
    } else {
        $taxonomies = get_category_attributes_auto($category_id);
    }

    $exclude = !empty($config['exclude'])
        ? (array) $config['exclude']
        : [];

    $taxonomies = array_values(array_unique(array_filter(
        array_map('sanitize_key', (array) $taxonomies)
    )));

    $request_cache[$category_id] = array_values(array_filter(
        $taxonomies,
        function ($taxonomy) use ($exclude) {
            return taxonomy_exists($taxonomy)
                && !in_array($taxonomy, $exclude, true);
        }
    ));

    return $request_cache[$category_id];
}

function prepare_product_filter_options_for_json($filters)
{
    $payload = [];

    foreach ((array) $filters as $filter) {
        $terms = [];

        foreach ((array) ($filter['terms'] ?? []) as $term) {
            if (!$term || empty($term->term_id)) {
                continue;
            }

            $terms[] = [
                'id'   => (int) $term->term_id,
                'name' => (string) $term->name,
                'slug' => (string) $term->slug,
            ];
        }

        $payload[] = [
            'key'   => (string) ($filter['key'] ?? ''),
            'label' => (string) ($filter['label'] ?? ''),
            'terms' => $terms,
        ];
    }

    return $payload;
}

function get_product_filter_term_ids($value, $taxonomy)
{
    $values = [];

    foreach (is_array($value) ? $value : [$value] as $raw_value) {
        if (is_string($raw_value) && strpos($raw_value, ',') !== false) {
            $values = array_merge($values, explode(',', $raw_value));
        } else {
            $values[] = $raw_value;
        }
    }

    $term_ids = [];

    foreach ($values as $single_value) {
        if (is_array($single_value) || is_object($single_value)) {
            continue;
        }

        $term_id = absint($single_value);
        $term = $term_id > 0
            ? get_term($term_id, $taxonomy)
            : get_term_by('slug', sanitize_title($single_value), $taxonomy);

        if ($term && !is_wp_error($term)) {
            $term_ids[] = (int) $term->term_id;
        }
    }

    return array_values(array_unique(array_filter($term_ids)));
}

function get_product_filter_active_filters(
    $raw_filters = null,
    $allowed_taxonomies = null,
    $allow_unprefixed = false
) {
    $raw_filters = $raw_filters === null ? $_GET : $raw_filters;

    if (!is_array($raw_filters)) {
        return [];
    }

    if ($allowed_taxonomies === null) {
        $allowed_taxonomies = wc_get_attribute_taxonomy_names();
    }

    $allowed_lookup = array_fill_keys(
        array_values(array_unique(array_filter(
            array_map('sanitize_key', (array) $allowed_taxonomies)
        ))),
        true
    );

    $active_filters = [];

    foreach ($raw_filters as $key => $value) {
        $key = sanitize_key(wp_unslash((string) $key));

        if (strpos($key, 'cs_') === 0) {
            $attribute_key = substr($key, 3);
        } elseif (strpos($key, 'filter_') === 0) {
            $attribute_key = substr($key, 7);
        } elseif ($allow_unprefixed) {
            $attribute_key = $key;
        } else {
            continue;
        }

        $attribute_key = sanitize_title($attribute_key);
        $taxonomy = 'pa_' . $attribute_key;

        if (
            !$attribute_key
            || !isset($allowed_lookup[$taxonomy])
            || !taxonomy_exists($taxonomy)
        ) {
            continue;
        }

        $term_ids = get_product_filter_term_ids($value, $taxonomy);

        if (!empty($term_ids)) {
            $active_filters[$taxonomy] = $term_ids;
        }
    }

    ksort($active_filters);

    return $active_filters;
}

function get_dynamic_product_filters($category_id = 0, $raw_active_filters = null)
{
    if (!$category_id) {
        if (!is_product_category()) {
            return [];
        }

        $category_id = get_queried_object_id();
    }

    $category_id = (int) $category_id;

    if ($category_id <= 0) {
        return [];
    }

    $taxonomies = get_product_filter_taxonomies_for_category($category_id);

    if (empty($taxonomies)) {
        return [];
    }

    $active_filters = get_product_filter_active_filters(
        $raw_active_filters,
        $taxonomies,
        $raw_active_filters !== null
    );

    $term_ids_by_taxonomy = get_product_filter_faceted_term_ids(
        $category_id,
        $taxonomies,
        $active_filters
    );

    $all_term_ids = [];

    foreach ($term_ids_by_taxonomy as $term_ids) {
        $all_term_ids = array_merge($all_term_ids, $term_ids);
    }

    $all_term_ids = array_values(array_unique(array_filter(
        array_map('absint', $all_term_ids)
    )));
    $terms_by_taxonomy = array_fill_keys($taxonomies, []);

    if (!empty($all_term_ids)) {
        $terms = get_terms([
            'taxonomy'   => $taxonomies,
            'include'    => $all_term_ids,
            'hide_empty' => false,
            'orderby'    => 'name',
            'order'      => 'ASC',
        ]);

        if (!is_wp_error($terms)) {
            foreach ($terms as $term) {
                if (isset($terms_by_taxonomy[$term->taxonomy])) {
                    $terms_by_taxonomy[$term->taxonomy][] = $term;
                }
            }
        }
    }

    $filters = [];

    foreach ($taxonomies as $taxonomy) {
        $filters[] = [
            'key'      => str_replace('pa_', '', $taxonomy),
            'taxonomy' => $taxonomy,
            'label'    => wc_attribute_label($taxonomy),
            'terms'    => $terms_by_taxonomy[$taxonomy],
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
function get_product_filter_excluded_visibility_tt_ids()
{
    static $visibility_tt_ids = null;

    if ($visibility_tt_ids !== null) {
        return $visibility_tt_ids;
    }

    if (!function_exists('wc_get_product_visibility_term_ids')) {
        $visibility_tt_ids = [];

        return $visibility_tt_ids;
    }

    $visibility_terms = wc_get_product_visibility_term_ids();
    $excluded_terms = [];

    if (!empty($visibility_terms['exclude-from-catalog'])) {
        $excluded_terms[] = absint(
            $visibility_terms['exclude-from-catalog']
        );
    }

    if (
        'yes' === get_option('woocommerce_hide_out_of_stock_items')
        && !empty($visibility_terms['outofstock'])
    ) {
        $excluded_terms[] = absint(
            $visibility_terms['outofstock']
        );
    }

    $visibility_tt_ids = array_values(array_unique(array_filter(
        $excluded_terms
    )));

    return $visibility_tt_ids;
}

function get_product_filter_faceted_term_ids(
    $category_id,
    $taxonomies,
    $active_filters = []
) {
    global $wpdb;

    $category_id = (int) $category_id;
    $taxonomies = array_values(array_unique(array_filter(
        array_map('sanitize_key', (array) $taxonomies)
    )));

    if ($category_id <= 0 || empty($taxonomies)) {
        return [];
    }

    sort($taxonomies);
    $normalized_active_filters = [];

    foreach ((array) $active_filters as $taxonomy => $term_ids) {
        $taxonomy = sanitize_key($taxonomy);
        $term_ids = array_values(array_unique(array_filter(
            array_map('absint', (array) $term_ids)
        )));

        if (
            !in_array($taxonomy, $taxonomies, true)
            || empty($term_ids)
            || !taxonomy_exists($taxonomy)
        ) {
            continue;
        }

        sort($term_ids);
        $normalized_active_filters[$taxonomy] = $term_ids;
    }

    ksort($normalized_active_filters);

    $cache_payload = [
        'taxonomies' => $taxonomies,
        'filters'    => $normalized_active_filters,
        'visibility' => get_product_filter_excluded_visibility_tt_ids(),
    ];
    $cache_version = get_product_filter_cache_version();
    $cache_key = sprintf(
        'product_filter_facets_%d_%s_v%d',
        $category_id,
        md5(wp_json_encode($cache_payload)),
        $cache_version
    );

    $cached = get_transient($cache_key);

    if ($cached !== false) {
        return is_array($cached) ? $cached : [];
    }

    $category_tt_ids = get_product_category_scope_tt_ids($category_id);
    $term_ids_by_taxonomy = array_fill_keys($taxonomies, []);

    if (empty($category_tt_ids)) {
        set_transient(
            $cache_key,
            $term_ids_by_taxonomy,
            DAY_IN_SECONDS
        );

        return $term_ids_by_taxonomy;
    }

    $query_groups = [];

    foreach ($taxonomies as $taxonomy) {
        $scope_filters = $normalized_active_filters;
        unset($scope_filters[$taxonomy]);

        $scope_key = md5(wp_json_encode($scope_filters));

        if (!isset($query_groups[$scope_key])) {
            $query_groups[$scope_key] = [
                'filters'    => $scope_filters,
                'taxonomies' => [],
            ];
        }

        $query_groups[$scope_key]['taxonomies'][] = $taxonomy;
    }

    foreach ($query_groups as $query_group) {
        $group_taxonomies = array_values(array_unique(
            $query_group['taxonomies']
        ));
        $category_placeholders = implode(
            ', ',
            array_fill(0, count($category_tt_ids), '%d')
        );
        $taxonomy_placeholders = implode(
            ', ',
            array_fill(0, count($group_taxonomies), '%s')
        );

        $sql = "
            SELECT DISTINCT
                attribute_taxonomy.taxonomy,
                attribute_taxonomy.term_id

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
            $group_taxonomies
        );

        $visibility_tt_ids = get_product_filter_excluded_visibility_tt_ids();

        if (!empty($visibility_tt_ids)) {
            $visibility_placeholders = implode(
                ', ',
                array_fill(0, count($visibility_tt_ids), '%d')
            );

            $sql .= "
              AND NOT EXISTS (
                  SELECT 1
                  FROM {$wpdb->term_relationships} AS visibility_relation
                  WHERE visibility_relation.object_id = products.ID
                    AND visibility_relation.term_taxonomy_id
                        IN ({$visibility_placeholders})
              )
            ";

            $prepare_args = array_merge(
                $prepare_args,
                $visibility_tt_ids
            );
        }

        foreach ($query_group['filters'] as $filter_taxonomy => $term_ids) {
            $term_placeholders = implode(
                ', ',
                array_fill(0, count($term_ids), '%d')
            );

            $sql .= "
              AND EXISTS (
                  SELECT 1
                  FROM {$wpdb->term_relationships} AS filter_relation
                  INNER JOIN {$wpdb->term_taxonomy} AS filter_taxonomy
                      ON filter_relation.term_taxonomy_id =
                         filter_taxonomy.term_taxonomy_id
                  WHERE filter_relation.object_id = products.ID
                    AND filter_taxonomy.taxonomy = %s
                    AND filter_taxonomy.term_id
                        IN ({$term_placeholders})
              )
            ";

            $prepare_args[] = $filter_taxonomy;
            $prepare_args = array_merge($prepare_args, $term_ids);
        }

        $prepared_sql = $wpdb->prepare($sql, ...$prepare_args);
        $rows = $wpdb->get_results($prepared_sql);

        foreach ((array) $rows as $row) {
            $taxonomy = sanitize_key($row->taxonomy ?? '');
            $term_id = absint($row->term_id ?? 0);

            if (
                $term_id > 0
                && isset($term_ids_by_taxonomy[$taxonomy])
            ) {
                $term_ids_by_taxonomy[$taxonomy][] = $term_id;
            }
        }
    }

    foreach ($term_ids_by_taxonomy as $taxonomy => $term_ids) {
        $term_ids = array_values(array_unique(array_filter(
            array_map('absint', $term_ids)
        )));

        if (!empty($normalized_active_filters[$taxonomy])) {
            $term_ids = array_values(array_unique(array_merge(
                $term_ids,
                $normalized_active_filters[$taxonomy]
            )));
        }

        sort($term_ids);
        $term_ids_by_taxonomy[$taxonomy] = $term_ids;
    }

    set_transient(
        $cache_key,
        $term_ids_by_taxonomy,
        DAY_IN_SECONDS
    );

    return $term_ids_by_taxonomy;
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
function maybe_bump_product_filter_cache_on_product_term_assignment(
    $object_id,
    $terms,
    $term_taxonomy_ids,
    $taxonomy
) {
    unset($terms, $term_taxonomy_ids);

    if (
        $taxonomy !== 'product_cat'
        && $taxonomy !== 'product_visibility'
        && strpos($taxonomy, 'pa_') !== 0
    ) {
        return;
    }

    if (get_post_type((int) $object_id) !== 'product') {
        return;
    }

    bump_product_filter_cache_version();
}

add_action(
    'set_object_terms',
    'maybe_bump_product_filter_cache_on_product_term_assignment',
    10,
    4
);

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
