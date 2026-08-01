# Template Parts Instructions

File này bổ sung quy tắc cho toàn bộ template trong `template-parts/`.
Mọi quy tắc tại `../AGENTS.md` và quy tắc frontend liên quan tại `../assets/AGENTS.md` vẫn được áp dụng.

## 1. Phân loại template

- `components/` chứa markup custom có thể tái sử dụng ở nhiều trang hoặc nhiều ngữ cảnh.
- `posts/` chứa template liên quan đến bài viết và archive blog; file trùng đúng đường dẫn tương đối với Flatsome parent theme được xem là template override.
- Template override WooCommerce không đặt tại đây; phải đặt trong `../woocommerce/` và giữ đúng đường dẫn tương đối của template gốc.
- Trước khi thêm file vào một đường dẫn có khả năng override, phải kiểm tra cùng đường dẫn trong Flatsome parent theme để xác định đó là override hay template custom.
- Không đặt template custom vào đường dẫn override của parent theme nếu component không có mục đích thay thế template gốc.

## 2. Khi tạo template mới

- Chỉ tạo template part khi markup cần tái sử dụng, cần tách khỏi logic lớn hoặc cần làm rõ trách nhiệm của template đang chứa nó.
- Không tách một đoạn HTML rất nhỏ chỉ được dùng một lần nếu việc tách làm luồng render khó theo dõi hơn.
- Không tạo template chỉ để bọc một lệnh gọi khác mà không bổ sung trách nhiệm rõ ràng.
- Ưu tiên gọi template custom bằng `get_template_part()` và truyền dữ liệu qua tham số `$args`; không dùng `require` hoặc `include` trực tiếp nếu WordPress API đáp ứng được.
- Template không được tự đăng ký hook, filter, shortcode, enqueue asset hoặc thực hiện xử lý khởi tạo.
- Query, xử lý dữ liệu, phân quyền, cache và logic nghiệp vụ phải được chuẩn bị trong module phù hợp trước khi render.
- Template chỉ được chuẩn bị dữ liệu nhẹ phục vụ hiển thị và phải escape output đúng ngữ cảnh.

### 2.1. Render nội dung bài viết

- Profile UX Builder của source tắt `wpautop` toàn cục có chủ đích; template không được tự ý bật lại filter này cho toàn website.
- Bài viết Gutenberg phải được render bằng `the_content()` hoặc nội dung đã đi qua `apply_filters('the_content', $content)` để block và shortcode hoạt động đúng.
- Không dùng `echo get_the_content()` hoặc xuất trực tiếp giá trị `post_content` để render toàn bộ nội dung bài viết.
- Nếu cần hỗ trợ bài Classic cũ không có block, kiểm tra bằng `has_blocks()` và chỉ áp dụng `wpautop()` cho chính nội dung đó trước khi đưa qua `the_content` filter.
- Không gọi `wpautop()` lên nội dung Gutenberg sau khi block đã được render vì có thể phát sinh lại `<p>` hoặc `<br>` ngoài ý muốn.
- Renderer blog không thay thế cấu hình editor trong trang quản trị; dự án muốn soạn Post bằng Gutenberg phải đồng thời cho phép Block Editor đối với post type `post`.

## 3. Đặt tên

- Tên thư mục và file mới dùng tiếng Anh, dạng kebab-case và mô tả đúng UI hoặc vai trò render.
- UI dùng chung ưu tiên cấu trúc `components/<ten-component>.php`; component có nhiều phần mới dùng `components/<ten-component>/<ten-phan>.php`.
- Trong thư mục component, không lặp lại tên component trong từng file khi ngữ cảnh đã rõ, ví dụ `components/product-filter/filter-bar.php`.
- Không dùng tên chung chung hoặc theo trạng thái làm việc như `custom.php`, `new.php`, `test.php`, `final.php` hoặc `backup.php`.
- Template custom không được dùng tên và đường dẫn trùng với template parent nếu không có chủ đích override. Khi chuyển một override thành template custom, phải đổi sang tên mô tả trách nhiệm cụ thể và cập nhật mọi nơi gọi; chỉ giữ tên và đường dẫn của parent cho override thực sự.
- Các asset riêng của template nên dùng cùng tên gốc và đặt đúng nhóm trong `../assets/`.

## 4. Template override

- Không sửa template tương ứng trong Flatsome parent theme.
- Trước khi override, ưu tiên kiểm tra hook hoặc filter ổn định có thể đáp ứng yêu cầu hay không.
- Khi tạo hoặc sửa override, phải đối chiếu template gốc hiện tại và giữ các hook, attribute, nonce, class hệ thống hoặc hành vi cần thiết.
- Không sao chép thêm template parent không liên quan vào child theme.
- Ghi nhận nguồn và version khi template gốc có metadata version; báo rõ nếu override có dấu hiệu lỗi thời.
- Một file override vẫn giữ đúng tên và đường dẫn của parent theme, kể cả khi quy tắc đặt tên file custom có thể khác.

## 5. HTML, CSS và JavaScript

- Giữ markup semantic, gọn và không thêm wrapper không cần thiết.
- Không đặt CSS dài hoặc JavaScript dài trực tiếp trong template.
- Nếu chỉ cần style markup hiện có, sửa CSS hoặc Tailwind phù hợp; không tạo template mới.
- Nếu component cần CSS thuần riêng và có thể dùng lại, đặt tại `../assets/css/components/<ten-component>.css`.
- Nếu style gắn chặt với một trang hoặc template duy nhất, đặt tại `../assets/css/pages/`.
- Nếu component cần JavaScript riêng, đặt file có tên tương ứng trong `../assets/js/` và enqueue có điều kiện khi hợp lý.
- Selector JavaScript mới ưu tiên dùng `data-*`; không phụ thuộc vào chuỗi utility Tailwind.

## 6. Kiểm tra khi thay đổi

- Rà lại tất cả nơi gọi template và cấu trúc `$args` liên quan.
- Nếu là override, đối chiếu hook và markup bắt buộc với template parent hoặc plugin hiện tại.
- Chạy PHP lint cho template PHP đã sửa.
- Chạy build Tailwind nếu thêm hoặc thay đổi class Tailwind trong template.
- Kiểm tra giao diện và trạng thái tương tác ở các ngữ cảnh đang sử dụng template.
