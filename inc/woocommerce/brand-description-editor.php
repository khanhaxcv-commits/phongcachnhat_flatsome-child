<?php

defined('ABSPATH') || exit;

// Khởi tạo biến.
$current_brand_taxonomy_slug = null;

// Kiểm tra nếu chúng ta đang ở trang admin và có tham số 'taxonomy' trong URL.
if (is_admin() && isset($_GET['taxonomy'])) {
    $current_brand_taxonomy_slug = sanitize_key($_GET['taxonomy']);

    // Tùy chọn: Bạn có thể thêm kiểm tra để đảm bảo đây là taxonomy của thương hiệu bạn muốn.
    // Ví dụ: chỉ chấp nhận nếu slug là 'product_brand' hoặc 'brand'.
    // if ( ! in_array( $current_brand_taxonomy_slug, ['product_brand', 'brand', 'shop_brand'] ) ) {
    //     $current_brand_taxonomy_slug = null; // Reset nếu không phải taxonomy mong muốn.
    // }
}

// Nếu chúng ta đã xác định được slug, thì mới định nghĩa hằng số.
if ($current_brand_taxonomy_slug) {
    define('BRAND_TAXONOMY_SLUG', $current_brand_taxonomy_slug);

    /**
     * Thêm trình soạn thảo Rich Text vào trường Mô tả trên màn hình chỉnh sửa Thương hiệu.
     * Điều này sẽ thay thế trường Mô tả mặc định.
     *
     * @param WP_Term $term Đối tượng term hiện tại đang được chỉnh sửa.
     */
    function custom_brand_description_editor($term)
    {
        // Lấy nội dung mô tả hiện có từ trường 'description' mặc định của term.
        $description = html_entity_decode($term->description);

?>
        <tr class="form-field term-description-wrap">
            <th scope="row">
                <label for="description">Mô tả Thương hiệu</label>
            </th>

            <td>
                <?php
                // Định nghĩa các cài đặt cho trình soạn thảo TinyMCE.
                $editor_settings = array(
                    'textarea_name' => 'description', // RẤT QUAN TRỌNG: Đây phải là 'description'
                    // để WordPress tự động lưu vào cột mô tả của term.
                    'textarea_rows' => 15,          // Số hàng hiển thị ban đầu.
                    'editor_height' => 300,         // Chiều cao của trình soạn thảo.
                    'media_buttons' => true,        // Cho phép nút thêm media (hình ảnh, video).
                    'teeny'         => false,       // false cho phép đầy đủ thanh công cụ, true cho thanh công cụ tối giản.
                    'tinymce'       => true,        // Kích hoạt TinyMCE (trình soạn thảo WYSIWYG).
                    'quicktags'     => true,        // Kích hoạt các thẻ nhanh HTML.
                );

                // Hiển thị trình soạn thảo WordPress.
                // Tham số đầu tiên là nội dung, tham số thứ hai là ID của textarea (độc nhất).
                wp_editor(
                    $description,
                    'brand_description_full_editor',
                    $editor_settings
                );
                ?>

                <p class="description">
                    Viết mô tả chi tiết và phong phú cho thương hiệu của bạn tại đây.
                    Bạn có thể sử dụng các công cụ định dạng văn bản và chèn media.
                </p>
            </td>
        </tr>
        <?php
    }

    /**
     * Hook hàm của chúng ta vào hành động chỉnh sửa form trường của phân loại thương hiệu.
     * Hook sẽ có dạng '{$taxonomy_slug}_edit_form_fields'.
     */
    add_action(
        BRAND_TAXONOMY_SLUG . '_edit_form_fields',
        'custom_brand_description_editor',
        10,
        2
    );

    /**
     * Ẩn trường Mô tả mặc định của WordPress trên trang chỉnh sửa,
     * vì chúng ta đã thêm một trình soạn thảo riêng đầy đủ chức năng.
     * Điều này ngăn chặn việc hiển thị hai trường mô tả.
     */
    function hide_default_brand_description_textarea()
    {
        // Chỉ chạy trong trang quản trị
        if (! is_admin()) {
            return;
        }

        // Lấy thông tin màn hình hiện tại
        $screen = get_current_screen();

        // Kiểm tra xem chúng ta có đang ở trang chỉnh sửa term của phân loại thương hiệu không.
        if (
            $screen &&
            ($screen->base === 'edit-tags' || $screen->base === 'term') &&
            $screen->taxonomy === BRAND_TAXONOMY_SLUG
        ) {
        ?>
            <script type="text/javascript">
                jQuery(document).ready(function($) {
                    // Tìm trường textarea mặc định có id 'description' và ẩn toàn bộ hàng (tr) của nó.
                    $('#description').closest('tr').hide();
                });
            </script>
<?php
        }
    }

    // Hook JavaScript vào cuối admin_footer để đảm bảo DOM đã tải đầy đủ.
    add_action('admin_footer', 'hide_default_brand_description_textarea');
}
