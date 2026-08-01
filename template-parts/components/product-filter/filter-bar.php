<?php

/**
 * Giao diện thanh bộ lọc sản phẩm.
 *
 * @var array $filters Danh sách bộ lọc đã chuẩn bị cho giao diện.
 * @var bool $has_active_filters Trạng thái có bộ lọc đang được áp dụng.
 * @var string $current_order Giá trị sắp xếp hiện tại.
 * @var array $mobile_orders Danh sách tùy chọn sắp xếp trên mobile.
 */

defined('ABSPATH') || exit;
?>

<div class="product-filter-wrapper w-full">
    <!-- Bộ lọc desktop -->
    <div class="product-filter-desktop">
        <div class="rounded-lg border border-ui bg-surface p-5 shadow-ui-card">
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

            <div class="product-filter-options">
                <div class="product-filter-grid grid grid-cols-2 gap-3 lg:grid-cols-3 xl:grid-cols-5">
                    <?php foreach ($filters as $filter) : ?>
                        <div
                            class="product-filter-item relative min-w-0 <?php echo $filter['has_terms'] ? '' : 'hidden'; ?>"
                            data-filter-key="<?php echo esc_attr($filter['key']); ?>"
                            <?php echo $filter['has_terms'] ? '' : 'hidden'; ?>>
                            <select
                                class="product-filter-select !m-0 !h-11 !w-full truncate !rounded-lg !border !px-4 !text-[14px] font-medium !shadow-none outline-none !transition !duration-200 <?php echo esc_attr($filter['select_state_classes']); ?> hover:!border-accent-soft hover:!bg-surface-accent focus:!border-input-focus focus:!bg-surface focus:!ring-4 focus:!ring-ui"
                                data-filter="<?php echo esc_attr($filter['key']); ?>"
                                aria-label="<?php echo esc_attr($filter['label']); ?>">
                                <option value="">
                                    <?php echo esc_html($filter['label']); ?>
                                </option>
                                <?php foreach ($filter['terms'] as $term) : ?>
                                    <option
                                        value="<?php echo esc_attr($term->term_id); ?>"
                                        data-term-slug="<?php echo esc_attr($term->slug); ?>"
                                        <?php selected($filter['selected_value'], $term->term_id); ?>>
                                        <?php echo esc_html($term->name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="product-filter-sort mt-4 flex items-center justify-end border-t border-ui pt-4">
                <?php render_custom_catalog_ordering(); ?>
            </div>
        </div>
    </div>

    <!-- Nút mở bộ lọc mobile -->
    <button type="button" class="open-filter-drawer">
        <i class="fa-light fa-sliders"></i>
        <span>Bộ lọc</span>
    </button>

    <div class="filter-drawer-overlay"></div>

    <!-- Bộ lọc mobile -->
    <div class="filter-drawer">
        <div class="filter-drawer-header">
            <strong>Bộ lọc</strong>
            <button type="button" class="close-filter-drawer">
                <i class="fa-light fa-xmark"></i>
            </button>
        </div>

        <div class="drawer-content">
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

            <?php foreach ($filters as $filter) : ?>
                <div
                    class="drawer-item <?php echo $filter['has_terms'] ? '' : 'hidden'; ?>"
                    data-filter-key="<?php echo esc_attr($filter['key']); ?>"
                    <?php echo $filter['has_terms'] ? '' : 'hidden'; ?>>
                    <label for="mobile-filter-<?php echo esc_attr($filter['key']); ?>">
                        <?php echo esc_html($filter['label']); ?>
                    </label>
                    <select
                        id="mobile-filter-<?php echo esc_attr($filter['key']); ?>"
                        class="mobile-filter-select"
                        data-filter="<?php echo esc_attr($filter['key']); ?>">
                        <option value="">
                            <?php echo esc_html($filter['mobile_placeholder']); ?>
                        </option>
                        <?php foreach ($filter['terms'] as $term) : ?>
                            <option
                                value="<?php echo esc_attr($term->term_id); ?>"
                                data-term-slug="<?php echo esc_attr($term->slug); ?>"
                                <?php selected($filter['selected_value'], $term->term_id); ?>>
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
