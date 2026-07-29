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

    $active_filters = get_product_filter_active_filters(
        null,
        wp_list_pluck($filters, 'taxonomy')
    );

    /*
     * Lấy danh sách query thuộc bộ lọc custom.
     * Chỉ xóa các tham số bắt đầu bằng cs_.
     */
    $has_active_filters = !empty($active_filters);
?>

    <div class="product-filter-wrapper w-full">

        <!-- ======================================
             DESKTOP FILTER
        ======================================= -->
        <div class="product-filter-desktop">

            <div class="rounded-lg border border-ui bg-surface p-5 shadow-ui-card">

                <!-- HEADER -->
                <div class="mb-4 flex items-center justify-between gap-5">

                    <div class="flex items-center gap-3">

                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-surface-accent !text-accent">
                            <i class="fa-light fa-filter-list text-[18px]"></i>
                        </div>

                        <div>

                            <h2 class="m-0 text-[16px] font-semibold leading-tight !text-heading">
                                Bộ lọc sản phẩm
                            </h2>

                            <p class="mt-1 mb-0 text-[13px] leading-tight !text-soft">
                                Chọn tiêu chí phù hợp để thu hẹp sản phẩm
                            </p>

                        </div>

                    </div>

                    <button
                        type="button"
                        class="clear-desktop-filter inline-flex h-9 items-center gap-2 rounded-lg border border-brand bg-primary-50 px-3.5 text-[13px] font-medium !text-primary-700 transition duration-200 hover:border-primary-300 hover:bg-primary-100 hover:!text-primary-hover <?php echo $has_active_filters ? '' : 'hidden'; ?>">

                        <i class="fa-light fa-arrow-rotate-left text-[12px]"></i>

                        <span>Xóa bộ lọc</span>

                    </button>

                </div>

                <!-- FILTER GRID -->
                <div class="product-filter-options">

                    <div class="product-filter-grid grid grid-cols-2 gap-3 lg:grid-cols-3 xl:grid-cols-5">

                        <?php foreach ($filters as $filter) : ?>

                            <?php
                            $selected_value = !empty($active_filters[$filter['taxonomy']])
                                ? reset($active_filters[$filter['taxonomy']])
                                : '';

                            $is_selected = !empty($selected_value);

                            $select_state_classes = $is_selected
                                ? '!border-accent-soft !bg-surface-accent !text-accent-800'
                                : '!border-input !bg-surface-muted !text-input';
                            ?>

                            <div
                                class="product-filter-item relative min-w-0 <?php echo empty($filter['terms']) ? 'hidden' : ''; ?>"
                                data-filter-key="<?php echo esc_attr($filter['key']); ?>"
                                <?php echo empty($filter['terms']) ? 'hidden' : ''; ?>>

                                <select
                                    class="product-filter-select !m-0 !h-11 !w-full truncate !rounded-lg !border !px-4 !text-[14px] font-medium !shadow-none outline-none !transition !duration-200 <?php echo esc_attr($select_state_classes); ?> hover:!border-accent-soft hover:!bg-surface-accent focus:!border-input-focus focus:!bg-surface focus:!ring-4 focus:!ring-ui"
                                    data-filter="<?php echo esc_attr($filter['key']); ?>"
                                    aria-label="<?php echo esc_attr($filter['label']); ?>">

                                    <option value="">
                                        <?php echo esc_html($filter['label']); ?>
                                    </option>

                                    <?php foreach ($filter['terms'] as $term) : ?>

                                        <option
                                            value="<?php echo esc_attr($term->term_id); ?>"
                                            data-term-slug="<?php echo esc_attr($term->slug); ?>"
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
                <div class="product-filter-sort mt-4 flex items-center justify-end border-t border-ui pt-4">
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
                    $selected_value = !empty($active_filters[$filter['taxonomy']])
                        ? reset($active_filters[$filter['taxonomy']])
                        : '';
                    ?>

                    <div
                        class="drawer-item <?php echo empty($filter['terms']) ? 'hidden' : ''; ?>"
                        data-filter-key="<?php echo esc_attr($filter['key']); ?>"
                        <?php echo empty($filter['terms']) ? 'hidden' : ''; ?>>

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
                                    data-term-slug="<?php echo esc_attr($term->slug); ?>"
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
