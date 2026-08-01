<?php

/**
 * Product filter request parsing and normalization.
 */

defined('ABSPATH') || exit;

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
