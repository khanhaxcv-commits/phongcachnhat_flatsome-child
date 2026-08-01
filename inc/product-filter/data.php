<?php

/**
 * Product filter data providers and filter option builders.
 */

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

    $config = require __DIR__ . '/settings.php';

    return is_array($config) ? $config : [];
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

    $taxonomies = array_values(array_filter(
        $taxonomies,
        function ($taxonomy) use ($exclude) {
            return taxonomy_exists($taxonomy)
                && !in_array($taxonomy, $exclude, true);
        }
    ));

    $priority = array_values(array_unique(array_filter(
        array_map(
            'sanitize_key',
            (array) ($config['priority'] ?? [])
        )
    )));

    $ordered_taxonomies = [];

    foreach ($priority as $taxonomy) {
        if (in_array($taxonomy, $taxonomies, true)) {
            $ordered_taxonomies[] = $taxonomy;
        }
    }

    foreach ($taxonomies as $taxonomy) {
        if (!in_array($taxonomy, $ordered_taxonomies, true)) {
            $ordered_taxonomies[] = $taxonomy;
        }
    }

    $request_cache[$category_id] = $ordered_taxonomies;

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
    $config = get_filter_config();
    $custom_labels = !empty($config['labels'])
        && is_array($config['labels'])
        ? $config['labels']
        : [];

    foreach ($taxonomies as $taxonomy) {
        $filters[] = [
            'key'      => str_replace('pa_', '', $taxonomy),
            'taxonomy' => $taxonomy,
            'label'    => !empty($custom_labels[$taxonomy])
                ? (string) $custom_labels[$taxonomy]
                : wc_attribute_label($taxonomy),
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
