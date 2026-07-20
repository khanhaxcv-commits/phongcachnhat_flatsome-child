<?php

defined('ABSPATH') || exit;

/*Sale price by sago media - sagomedia.vn*/
function sago_price_html($product, $is_variation = false)
{
    ob_start();

    // Khởi tạo biến để lưu thông tin thương hiệu và mã sản phẩm.
    $brand = '';
    $brand_link = '';
    $product_code = '';

    // --- Lấy thông tin Thương hiệu ---
    // Ưu tiên lấy từ taxonomy 'product_brand' (thường dùng cho các plugin Thương hiệu).
    $brand_terms = get_the_terms($product->get_id(), 'product_brand');

    // Nếu không tìm thấy từ 'product_brand' HOẶC có lỗi, thử lấy từ thuộc tính 'pa_thuong-hieu'.
    if (empty($brand_terms) || is_wp_error($brand_terms)) {
        $brand_terms = get_the_terms($product->get_id(), 'pa_thuong-hieu');
    }

    // Kiểm tra xem có bất kỳ term thương hiệu nào được tìm thấy và không có lỗi.
    if (! empty($brand_terms) && ! is_wp_error($brand_terms)) {
        // Lấy đối tượng term thương hiệu đầu tiên.
        // Thường một sản phẩm chỉ có một thương hiệu chính, hoặc bạn muốn hiển thị thương hiệu đầu tiên tìm được.
        $first_brand_term = reset($brand_terms);

        // Gán tên thương hiệu (đã được làm sạch HTML).
        $brand = esc_html($first_brand_term->name);

        // Lấy permalink của term thương hiệu đó.
        $link = get_term_link($first_brand_term);

        // Kiểm tra xem link có hợp lệ không (không phải đối tượng lỗi WordPress).
        if (! is_wp_error($link)) {
            // Gán URL thương hiệu (đã được làm sạch URL).
            $brand_link = esc_url($link);
        }
    }

    // Lấy mã sản phẩm (giả sử dùng SKU)
    $product_code = $product->get_sku() ? $product->get_sku() : 'Chờ cập nhật';

    // Hiển thị thương hiệu và mã sản phẩm
?>
    <div class="product-meta-info">
        <div class="product-brand product-code">

            <span class="label">Thương hiệu:</span>
            <span class="value">
                <b>
                    <a href="<?php echo $brand_link; ?>">
                        <?php echo $brand; ?>
                    </a>
                </b>
            </span>

            <span class="label">Mã sản phẩm:</span>
            <span class="value">
                <b><?php echo esc_html($product_code); ?></b>
            </span>
        </div>
    </div>
    <?php

    // Hiển thị giá sản phẩm
    if ($product->is_on_sale() && ($is_variation || $product->is_type('simple') || $product->is_type('external'))) {
        $sale_price = $product->get_sale_price();
        $regular_price = $product->get_regular_price();

        if ($regular_price) {
            $sale = round(((floatval($regular_price) - floatval($sale_price)) / floatval($regular_price)) * 100);
            $sale_amout = $regular_price - $sale_price;
    ?>
            <div class="price-text">
                <ul>
                    <li class="price">
                        <span class="km"><?php echo wc_price($sale_price); ?></span>
                        <span class="goc"><?php echo wc_price($regular_price); ?></span>
                    </li>
                </ul>
            </div>
            <?php
        }
    } elseif ($product->is_on_sale() && $product->is_type('variable')) {
        $prices = $product->get_variation_prices(true);

        if (empty($prices['price'])) {
            $price = apply_filters('woocommerce_variable_empty_price_html', '', $product);
        } else {
            $min_price = current($prices['price']);
            $max_price = end($prices['price']);
            $min_reg_price = current($prices['regular_price']);
            $max_reg_price = end($prices['regular_price']);

            if ($min_price !== $max_price) {
                $price = wc_format_price_range($min_price, $max_price) . $product->get_price_suffix();
            } elseif ($product->is_on_sale() && $min_reg_price === $max_reg_price) {
                $sale = round(((floatval($max_reg_price) - floatval($min_price)) / floatval($max_reg_price)) * 100);
                $sale_amout = $max_reg_price - $min_price;
            ?>
                <div class="price-text price">
                    <span class="km"><?php echo wc_price($min_price); ?></span>
                    <span class="goc"><?php echo wc_price($max_reg_price); ?></span>
                </div>
        <?php
            } else {
                $price = wc_price($min_price) . $product->get_price_suffix();
            }
        }

        echo $price;
    } else {
        ?>
        <div class="price-text">
            <ul>
                <li class="price">
                    Giá bán:
                    <span><?php echo $product->get_price_html(); ?></span>
                </li>
            </ul>
        </div>
<?php
    }

    // tình trạng hàng hóa	
    $khuyen_mai = get_field('khuyen_mai');

    if ($khuyen_mai) {
        echo '<div class="khuyen-mai">';
        echo $khuyen_mai;
        echo '</div>';
    }

    return ob_get_clean();
}

function woocommerce_template_single_price()
{
    global $product;

    echo sago_price_html($product);
}

add_filter('woocommerce_available_variation', 'sago_woocommerce_available_variation', 10, 3);

function sago_woocommerce_available_variation($args, $thisC, $variation)
{
    $old_price_html = $args['price_html'];

    if ($old_price_html) {
        $args['price_html'] = sago_price_html($variation, true);
    }

    return $args;
}
