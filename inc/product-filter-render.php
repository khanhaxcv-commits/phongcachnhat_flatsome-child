<?php

defined('ABSPATH') || exit;

/**
 * Render thanh bộ lọc sản phẩm.
 */
function render_product_filter_bar()
{
    $filters = get_dynamic_product_filters();

    if (empty($filters)) {
        return;
    }

    /*
     * Lấy danh sách query thuộc bộ lọc custom.
     * Chỉ xóa các tham số bắt đầu bằng cs_.
     */
    $filter_query_keys = [];

    foreach (array_keys($_GET) as $query_key) {
        if (strpos($query_key, 'cs_') === 0) {
            $filter_query_keys[] = $query_key;
        }
    }

    $has_active_filters = !empty($filter_query_keys);

    $clear_filter_url = $has_active_filters
        ? remove_query_arg($filter_query_keys)
        : '';
?>

    <div class="product-filter-wrapper w-full">

        <!-- ======================================
             DESKTOP FILTER
        ======================================= -->
        <div class="product-filter-desktop">

            <div class="rounded-[var(--radius-lg)] border border-[var(--border-ui)] bg-[var(--surface-bg)] p-5 shadow-[var(--shadow-ui-card)]">

                <!-- HEADER -->
                <div class="mb-4 flex items-center justify-between gap-5">

                    <div class="flex items-center gap-3">

                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-[var(--radius-input)] bg-[var(--surface-bg-accent)] text-[var(--accent)]">
                            <i class="fa-light fa-filter-list text-[18px]"></i>
                        </div>

                        <div>

                            <h2 class="m-0 text-[16px] font-semibold leading-tight text-[var(--text-heading)]">
                                Bộ lọc sản phẩm
                            </h2>

                            <p class="mt-1 mb-0 text-[13px] leading-tight text-[var(--text-soft-ui)]">
                                Chọn tiêu chí phù hợp để thu hẹp sản phẩm
                            </p>

                        </div>

                    </div>

                    <button
                        type="button"
                        class="clear-desktop-filter inline-flex h-9 items-center gap-2 rounded-[var(--radius-input)] border border-[var(--border-primary)] bg-[var(--primary-50)] px-3.5 text-[13px] font-medium text-[var(--primary-700)] transition duration-200 hover:border-[var(--primary-300)] hover:bg-[var(--primary-100)] hover:text-[var(--primary-hover)] <?php echo $has_active_filters ? '' : 'hidden'; ?>">

                        <i class="fa-light fa-arrow-rotate-left text-[12px]"></i>

                        <span>Xóa bộ lọc</span>

                    </button>

                </div>

                <!-- FILTER GRID -->
                <div class="product-filter-options">

                    <div class="product-filter-grid grid grid-cols-2 gap-3 lg:grid-cols-3 xl:grid-cols-5">

                        <?php foreach ($filters as $filter) : ?>

                            <?php
                            $parameter_name = 'cs_' . $filter['key'];

                            $selected_value = isset($_GET[$parameter_name])
                                ? absint(wp_unslash($_GET[$parameter_name]))
                                : '';

                            $is_selected = !empty($selected_value);

                            $select_state_classes = $is_selected
                                ? 'border-[var(--border-accent)] bg-[var(--surface-bg-accent)] text-[var(--accent-800)]'
                                : 'border-[var(--input-border)] bg-[var(--surface-bg-muted)] text-[var(--input-text)]';
                            ?>

                            <div class="product-filter-item relative min-w-0">

                                <select
                                    class="product-filter-select m-0 h-11 w-full truncate rounded-[var(--radius-input)] border px-4 text-[14px] font-medium outline-none transition duration-200 <?php echo esc_attr($select_state_classes); ?> hover:border-[var(--border-accent)] hover:bg-[var(--surface-bg-accent)] focus:border-[var(--input-focus-border)] focus:bg-[var(--surface-bg)] focus:ring-4 focus:ring-[var(--focus-ring-ui)]"
                                    data-filter="<?php echo esc_attr($filter['key']); ?>"
                                    aria-label="<?php echo esc_attr($filter['label']); ?>">

                                    <option value="">
                                        <?php echo esc_html($filter['label']); ?>
                                    </option>

                                    <?php foreach ($filter['terms'] as $term) : ?>

                                        <option
                                            value="<?php echo esc_attr($term->term_id); ?>"
                                            <?php selected($selected_value, $term->term_id); ?>>

                                            <?php echo esc_html($term->name); ?>

                                        </option>

                                    <?php endforeach; ?>

                                </select>

                            </div>

                        <?php endforeach; ?>

                    </div>

                </div>

                <!-- SORTING -->
                <div class="product-filter-sort mt-4 flex items-center justify-end border-t border-[var(--border-ui)] pt-4">
                    <?php render_custom_catalog_ordering(); ?>
                </div>

            </div>

        </div>

        <!-- ======================================
             MOBILE BUTTON
        ======================================= -->
        <button type="button" class="open-filter-drawer">
            <i class="fa-light fa-sliders"></i>
            <span>Bộ lọc</span>
        </button>

        <!-- ======================================
             MOBILE OVERLAY
        ======================================= -->
        <div class="filter-drawer-overlay"></div>

        <!-- ======================================
             MOBILE DRAWER
        ======================================= -->
        <div class="filter-drawer">

            <div class="filter-drawer-header">

                <strong>Bộ lọc</strong>

                <button type="button" class="close-filter-drawer">
                    <i class="fa-light fa-xmark"></i>
                </button>

            </div>

            <div class="drawer-content">

                <?php
                $current_order = isset($_GET['orderby'])
                    ? wc_clean(wp_unslash($_GET['orderby']))
                    : 'menu_order';

                $mobile_orders = [
                    'menu_order' => 'Nổi bật',
                    'popularity' => 'Bán chạy',
                    'date'       => 'Mới nhất',
                    'price'      => 'Giá thấp - cao',
                    'price-desc' => 'Giá cao - thấp',
                ];
                ?>

                <!-- MOBILE SORTING -->
                <div class="drawer-item drawer-sorting">

                    <label for="mobile-product-ordering">
                        Sắp xếp theo
                    </label>

                    <select
                        id="mobile-product-ordering"
                        class="mobile-ordering-select"
                        data-orderby>

                        <?php foreach ($mobile_orders as $order_key => $order_label) : ?>

                            <option
                                value="<?php echo esc_attr($order_key); ?>"
                                <?php selected($current_order, $order_key); ?>>

                                <?php echo esc_html($order_label); ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <!-- MOBILE FILTERS -->
                <?php foreach ($filters as $filter) : ?>

                    <?php
                    $parameter_name = 'cs_' . $filter['key'];

                    $selected_value = isset($_GET[$parameter_name])
                        ? absint(wp_unslash($_GET[$parameter_name]))
                        : '';
                    ?>

                    <div class="drawer-item">

                        <label for="mobile-filter-<?php echo esc_attr($filter['key']); ?>">
                            <?php echo esc_html($filter['label']); ?>
                        </label>

                        <select
                            id="mobile-filter-<?php echo esc_attr($filter['key']); ?>"
                            class="mobile-filter-select"
                            data-filter="<?php echo esc_attr($filter['key']); ?>">

                            <option value="">
                                Chọn <?php echo esc_html(mb_strtolower($filter['label'])); ?>
                            </option>

                            <?php foreach ($filter['terms'] as $term) : ?>

                                <option
                                    value="<?php echo esc_attr($term->term_id); ?>"
                                    <?php selected($selected_value, $term->term_id); ?>>

                                    <?php echo esc_html($term->name); ?>

                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                <?php endforeach; ?>

            </div>

            <div class="drawer-footer">

                <button type="button" class="clear-mobile-filter">
                    Hủy bộ lọc
                </button>

                <button type="button" class="apply-mobile-filter">
                    Áp dụng
                </button>

            </div>

        </div>

    </div>

<?php
}
