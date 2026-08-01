# PHP Module Instructions

File này bổ sung quy tắc cho toàn bộ code PHP trong `inc/`.
Mọi quy tắc tại `../AGENTS.md` vẫn được áp dụng.

## 1. Cấu trúc module

- Mỗi nhóm chức năng phải nằm trong thư mục riêng phù hợp với trách nhiệm của nhóm đó.
- Mỗi thư mục chức năng dùng `bootstrap.php` làm entry point.
- `functions.php` chỉ được nạp các `bootstrap.php` cấp nhóm.
- Không import trực tiếp file chức năng mới trong `functions.php`.
- Không đặt file PHP chức năng mới trực tiếp tại thư mục gốc `inc/`.
- Không chuyển file giữa các nhóm nếu chưa kiểm tra toàn bộ dependency và nơi tham chiếu.

## 2. Bootstrap

- `bootstrap.php` chỉ chịu trách nhiệm nạp module, không chứa logic nghiệp vụ.
- Khai báo file bằng danh sách rõ ràng và có thứ tự.
- Không dùng `glob()` hoặc cơ chế tự động require toàn bộ thư mục.
- Dùng `__DIR__` để tạo đường dẫn nội bộ.
- Kiểm tra `file_exists()` trước khi dùng `require_once`.
- Giữ nguyên thứ tự load khi module có dependency hoặc đăng ký hook cùng priority.
- Module đang tắt phải tiếp tục được comment trong bootstrap tương ứng.
- Khi thêm file chức năng mới, phải khai báo file đó trong bootstrap của đúng nhóm.

Mẫu bootstrap:

```php
<?php

defined('ABSPATH') || exit;

$group_modules = array(
    'example.php',
);

foreach ($group_modules as $group_module) {
    $group_module_file = __DIR__ . '/' . $group_module;

    if (file_exists($group_module_file)) {
        require_once $group_module_file;
    }
}
```

## 3. Phân loại file

- Logic quản trị đặt trong `admin/`.
- Logic đăng ký CSS, JavaScript, font và thư viện frontend đặt trong `assets/`.
- Logic bài viết và archive blog đặt trong `blog/`.
- Logic dọn dẹp hoặc vô hiệu hóa hành vi mặc định của WordPress đặt trong `cleanup/`.
- Tích hợp plugin hoặc dịch vụ bên thứ ba đặt trong `integrations/`.
- Module giao diện hoặc chức năng độc lập đặt trong `modules/`.
- Rewrite, permalink và URL routing đặt trong `rewrite/`.
- WooCommerce hook, filter và integration đặt trong `woocommerce/`.
- Chức năng lớn có nhiều thành phần phải có thư mục riêng, như `product-filter/`.
- Template override của WooCommerce vẫn đặt trong `../woocommerce/`, không đặt trong `inc/woocommerce/`.

## 4. Đặt tên và trách nhiệm

- Tên file phải diễn đạt trách nhiệm cụ thể.
- Tránh tên chung chung như `functions.php`, `helpers.php` hoặc `custom.php` khi có thể đặt tên rõ hơn.
- Không lặp lại tên thư mục trong tên file nếu ngữ cảnh thư mục đã đủ rõ.
- Mỗi file nên có một trách nhiệm chính và không trở thành nơi gom logic không liên quan.
- Function, class, constant và hook helper mới phải dùng prefix riêng của theme.

## 5. Config và template

- File config trả về mảng không được import trực tiếp trong bootstrap.
- Module cần config phải tự load bằng đường dẫn dựa trên `__DIR__`.
- Không đặt HTML lớn trong file xử lý dữ liệu, cache, request hoặc query.
- Markup có thể tái sử dụng nên đặt trong `../template-parts/`.
- Không đặt JavaScript hoặc CSS dài trực tiếp trong module PHP khi đã có vị trí phù hợp trong `../assets/`.

## 6. Khi di chuyển hoặc thêm file

- Kiểm tra tất cả `require`, `include`, hook, shortcode, callback và tham chiếu liên quan.
- Cập nhật bootstrap tương ứng và giữ đúng thứ tự load.
- Không thay đổi logic nghiệp vụ trong cùng task nếu yêu cầu chỉ là tổ chức lại cấu trúc.
- Không đổi tên function, class, constant hoặc hook nếu chưa kiểm tra toàn bộ nơi sử dụng.
- Chạy PHP lint cho file được thêm hoặc di chuyển, bootstrap liên quan và `functions.php`.
- Rà lại toàn dự án để bảo đảm không còn đường dẫn cũ.
- Với file chỉ di chuyển, ưu tiên đối chiếu nội dung trước và sau để bảo đảm không bị thay đổi ngoài ý muốn.
