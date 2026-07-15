<?php
/**
 * My Account Dashboard Custom
 *
 * Copy to:
 * yourtheme/woocommerce/myaccount/dashboard.php
 */


// ngăn truy cập trực tiếp file PHP.
defined('ABSPATH') || exit;

// lấy tên, email và thông tin người dùng hiện tại.
$current_user = wp_get_current_user();

// Mảng $account_links chứa dữ liệu các thẻ chức năng:
$account_links = array(
    array(
        'title' => 'Đơn hàng',
        'desc'  => 'Theo dõi đơn hàng đã mua và trạng thái xử lý.',
        'icon'  => 'fa-solid fa-bag-shopping',
        'url'   => wc_get_account_endpoint_url('orders'),
    ),
    array(
        'title' => 'Địa chỉ',
        'desc'  => 'Quản lý địa chỉ giao hàng và thanh toán.',
        'icon'  => 'fa-solid fa-location-dot',
        'url'   => wc_get_account_endpoint_url('edit-address'),
    ),
    array(
        'title' => 'Tài khoản',
        'desc'  => 'Cập nhật họ tên, email và mật khẩu đăng nhập.',
        'icon'  => 'fa-solid fa-user-gear',
        'url'   => wc_get_account_endpoint_url('edit-account'),
    ),
    array(
        'title' => 'Tệp tải xuống',
        'desc'  => 'Xem các tài liệu hoặc sản phẩm tải xuống nếu có.',
        'icon'  => 'fa-solid fa-download',
        'url'   => wc_get_account_endpoint_url('downloads'),
    ),
);
?>


<div class="pcn-account-dashboard">

    <div class="pcn-account-hero">
        <div class="pcn-account-hero__content">
            <span class="pcn-account-eyebrow">
                Tài khoản khách hàng
            </span>

            <h1>
                Xin chào, <?php echo esc_html($current_user->display_name); ?>
            </h1>

            <p>
                Tại đây bạn có thể theo dõi đơn hàng, quản lý địa chỉ giao hàng,
                cập nhật thông tin tài khoản và đăng xuất khi cần.
            </p>

            <div class="pcn-account-actions">
                <a href="<?php echo esc_url(wc_logout_url()); ?>" class="pcn-account-btn pcn-account-btn--primary">
                    Đăng xuất
                    <i class="fa-solid fa-right-from-bracket"></i>
                </a>

                <a href="<?php echo esc_url(wc_get_account_endpoint_url('edit-account')); ?>" class="pcn-account-btn pcn-account-btn--outline">
                    Sửa tài khoản
                </a>
            </div>
        </div>

        <div class="pcn-account-hero__box">
            <div class="pcn-account-avatar">
                <i class="fa-solid fa-user"></i>
            </div>

            <div>
                <strong><?php echo esc_html($current_user->display_name); ?></strong>
                <span><?php echo esc_html($current_user->user_email); ?></span>
            </div>
        </div>
    </div>

    <div class="pcn-account-grid">
        <?php foreach ($account_links as $item) : ?>
            <a class="pcn-account-card" href="<?php echo esc_url($item['url']); ?>">
                <span class="pcn-account-card__icon">
                    <i class="<?php echo esc_attr($item['icon']); ?>"></i>
                </span>

                <span class="pcn-account-card__body">
                    <strong><?php echo esc_html($item['title']); ?></strong>
                    <small><?php echo esc_html($item['desc']); ?></small>
                </span>

                <span class="pcn-account-card__arrow">
                    <i class="fa-solid fa-arrow-right"></i>
                </span>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="pcn-account-support">
        <div>
            <span>Cần hỗ trợ chọn thiết bị?</span>
            <strong>Phong Cách Nhật luôn sẵn sàng tư vấn cho bạn.</strong>
        </div>

        <a href="/lien-he" class="pcn-account-support__btn">
            Liên hệ tư vấn
            <i class="fa-solid fa-comments"></i>
        </a>
    </div>

</div>
