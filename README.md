# Flatsome Child Theme Starter

Source child theme WordPress dùng chung cho các dự án phát triển trên Flatsome. WooCommerce và các chức năng bán hàng là module tùy chọn.

Source cung cấp khung tổ chức PHP module, template override, CSS, JavaScript và cấu hình Tailwind. Khi khởi tạo dự án mới, phải rà lại module có sẵn, nhận diện thương hiệu, prefix và cấu hình tích hợp trước khi sử dụng. Không chỉnh sửa trực tiếp WordPress core, Flatsome parent theme hoặc plugin bên thứ ba.

## Môi trường hiện tại

- WordPress.
- Flatsome parent theme; môi trường local hiện dùng Flatsome `3.19.13`.
- PHP; môi trường phát triển hiện dùng PHP `8.2`.
- Node.js và npm để build Tailwind CSS.
- Tailwind CSS `3.4.17`.

Profile bán hàng có thể dùng WooCommerce; môi trường local tham chiếu hiện dùng WooCommerce `10.9.4`. Dự án không bán hàng không cần cài hoặc kích hoạt WooCommerce.

Một số module có tích hợp tùy chọn với Contact Form 7, Rank Math và All-in-One WP Migration. Các tích hợp tùy chọn phải kiểm tra plugin hoặc API liên quan trước khi mở rộng.

## Cài đặt cho môi trường phát triển

1. Sao chép source vào thư mục child theme của dự án, ví dụ:

   ```text
   wp-content/themes/your-child-theme/
   ```

2. Cập nhật metadata trong `style.css`, tối thiểu gồm `Theme Name`, `Description`, `Author` và `Version`. Giữ `Template: flatsome` để WordPress nhận diện đúng parent theme.

3. Cài đặt và kích hoạt Flatsome parent theme.

4. Cài dependency phục vụ build frontend:

   ```bash
   npm ci
   ```

5. Build Tailwind CSS:

   ```bash
   npm run build
   ```

6. Kích hoạt child theme theo `Theme Name` đã cấu hình trong WordPress Admin.

Dự án hiện dùng npm làm quy trình build được tài liệu hóa. Không tạo thêm lockfile bằng package manager khác nếu nhóm chưa thống nhất thay đổi.

## Lệnh phát triển

Build Tailwind một lần:

```bash
npm run build
```

Theo dõi thay đổi Tailwind trong lúc phát triển:

```bash
npm run dev
```

`npm run dev` là tiến trình watch. Phải dừng tiến trình này trước khi kết thúc phiên làm việc.

Tailwind được build từ:

```text
assets/src/tailwind-input.css
```

File đầu ra dùng trực tiếp trên website:

```text
assets/css/generated/tailwind.css
```

Không chỉnh sửa thủ công file Tailwind đã build.

## Cấu trúc dự án

```text
your-child-theme/
├── assets/
│   ├── css/
│   │   ├── base/
│   │   ├── components/
│   │   ├── generated/
│   │   ├── layout/
│   │   ├── overrides/
│   │   └── pages/
│   ├── fontawesome-pro-v7/
│   ├── js/
│   └── src/
├── inc/
│   ├── admin/
│   ├── assets/
│   ├── blog/
│   ├── cleanup/
│   ├── integrations/
│   ├── modules/
│   ├── product-filter/       # Tùy chọn: bộ lọc sản phẩm
│   ├── rewrite/
│   └── woocommerce/          # Tùy chọn: hook và integration bán hàng
├── template-parts/
├── tests/
│   └── manual/
├── woocommerce/              # Tùy chọn: template override WooCommerce
├── AGENTS.md
├── functions.php
├── style.css
├── package.json
└── tailwind.config.js
```

## Tổ chức PHP

- `functions.php` chỉ nạp các `bootstrap.php` cấp nhóm trong `inc/`.
- Mỗi nhóm PHP quản lý danh sách module bằng bootstrap riêng.
- Bootstrap phải khai báo file theo thứ tự rõ ràng; không dùng `glob()`.
- Module đang tắt tiếp tục được comment trong bootstrap tương ứng.
- Không đặt file chức năng mới trực tiếp tại thư mục gốc `inc/`.
- File config trả về mảng được module cần dùng load trực tiếp bằng `__DIR__`, không nạp từ bootstrap.

Khi thêm module PHP:

1. Chọn đúng thư mục trách nhiệm trong `inc/`.
2. Tạo file có tên diễn đạt rõ chức năng.
3. Thêm file vào bootstrap của nhóm theo đúng thứ tự dependency.
4. Chạy PHP lint cho file mới, bootstrap và `functions.php`.

Quy tắc chi tiết nằm trong `AGENTS.md` và `inc/AGENTS.md`.

## Tổ chức CSS

- `base/`: reset và quy tắc nền tảng.
- `generated/`: CSS do công cụ build sinh ra.
- `layout/`: khung và khu vực lớn của website.
- `components/`: thành phần giao diện độc lập.
- `pages/`: CSS gắn với trang hoặc template cụ thể.
- `overrides/`: ghi đè cuối cùng có chủ đích.

Giữ nguyên tên `pages/trang-chu-1.css` và `pages/lien-he-1.css` nếu task không yêu cầu đổi tên rõ ràng.

CSS và JavaScript chỉ dùng cho một trang hoặc component nên được enqueue có điều kiện khi hợp lý. Sau khi thay đổi Tailwind class, source Tailwind hoặc cấu hình Tailwind, phải chạy lại `npm run build`.

Quy tắc frontend chi tiết nằm trong `assets/AGENTS.md`.

## Quy ước UX Builder và Gutenberg

Profile mặc định ưu tiên Flatsome UX Builder. Source chủ động tắt Block Editor, Widget Block Editor và `wpautop` để WordPress không tự sinh thêm `<p>` hoặc `<br>` làm thay đổi markup do UX Builder kiểm soát.

Nếu dự án dùng Gutenberg cho bài viết blog:

- Cho phép Block Editor đối với post type `post`; có renderer riêng không tự động bật Gutenberg trong trang quản trị.
- Có thể tiếp tục tắt Block Editor đối với Page dùng UX Builder.
- Không cần bật lại `wpautop` toàn site.
- Render nội dung bằng `the_content()` hoặc `apply_filters('the_content', $content)` để block, shortcode và filter WordPress hoạt động đúng.
- Nội dung Classic cũ không có block có thể được áp dụng `wpautop()` có điều kiện trong renderer blog.
- Không áp dụng `wpautop()` lần nữa lên nội dung Gutenberg đã render.

Các thay đổi editor phải được giới hạn theo post type hoặc profile dự án; không bật hoặc tắt đồng loạt nếu dự án dùng kết hợp UX Builder và Gutenberg.

## Profile dự án và module tùy chọn

Source được phép giữ các module tùy chọn trong repository để tái sử dụng. File chỉ nằm trên ổ đĩa gần như không tạo chi phí runtime; chi phí phát sinh khi module bị require hoặc plugin được kích hoạt.

Với dự án không bán hàng:

- Không cài hoặc kích hoạt WooCommerce.
- `inc/woocommerce/` và `inc/product-filter/` không được load khi class `WooCommerce` không tồn tại.
- Template trong `woocommerce/` không được WordPress sử dụng khi WooCommerce không hoạt động.
- Có thể giữ các thư mục commerce làm source tham khảo hoặc xóa khỏi bản triển khai riêng sau khi xác nhận không còn tham chiếu.

Với dự án bán hàng:

- Kích hoạt WooCommerce trước khi kiểm tra các module commerce.
- PHP hook, filter và integration đặt trong `inc/woocommerce/`.
- Bộ lọc sản phẩm đặt trong `inc/product-filter/`.
- Template override đặt trong `woocommerce/` của child theme.

## WooCommerce

- WooCommerce là dependency tùy chọn, chỉ bắt buộc với profile bán hàng.
- Không sửa trực tiếp plugin WooCommerce.
- Khi cập nhật WooCommerce, đối chiếu version và nội dung của template override với template nguồn trong plugin.
- Sau thay đổi liên quan WooCommerce, kiểm tra đúng flow như sản phẩm, giỏ hàng, thanh toán, tài khoản hoặc trang nhận đơn.

## Kiểm tra trước khi bàn giao

- Chạy PHP lint cho các file PHP đã thay đổi.
- Chạy `npm run build` khi thay đổi Tailwind hoặc cấu hình build.
- Kiểm tra không còn đường dẫn cũ sau khi di chuyển file.
- Kiểm tra responsive ở mobile và desktop; với giao diện Flatsome, kiểm tra cả `849px` và `850px` khi liên quan breakpoint.
- Kiểm tra console và PHP log không có lỗi mới.
- Không để credential, token, mật khẩu, debug output hoặc dữ liệu môi trường trong repository.
- Không đưa `node_modules/`, file backup, database dump hoặc file tạm vào commit.
- Đưa `assets/css/generated/tailwind.css` đã build vào thay đổi khi source hoặc Tailwind class thay đổi.

`tests/manual/` chỉ chứa công cụ kiểm thử thủ công và không được nạp trong production.

## Quy tắc làm việc

Đọc và tuân thủ:

- `AGENTS.md` cho quy tắc toàn dự án.
- `inc/AGENTS.md` khi sửa module PHP.
- `assets/AGENTS.md` khi sửa CSS, JavaScript, Tailwind hoặc frontend markup.

Giữ diff nhỏ, không refactor ngoài phạm vi task và không thay đổi hành vi hiện có nếu yêu cầu chỉ là tổ chức lại cấu trúc.
