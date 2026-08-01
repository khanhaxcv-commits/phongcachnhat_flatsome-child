# Project Instructions

File này là quy tắc cấp cao nhất của toàn bộ dự án WordPress child theme.

Mọi file `AGENTS.md` nằm trong thư mục con phải kế thừa các quy tắc tại đây. Quy tắc ở thư mục con chỉ được bổ sung hoặc làm rõ cho phạm vi riêng, không được lặp lại toàn bộ nội dung của file này.

## 0. Trước khi bắt đầu task

- Trước khi phân tích, sửa file hoặc chạy lệnh làm thay đổi source, phải xác định các thư mục nằm trong phạm vi task.
- Phải đọc đầy đủ file `AGENTS.md` này và mọi file `AGENTS.md` gần nhất áp dụng cho từng thư mục sẽ đọc hoặc sửa.
- Nếu task đồng thời liên quan nhiều khu vực như `inc/`, `assets/`, `template-parts/` hoặc `woocommerce/`, phải đọc quy tắc của tất cả khu vực liên quan trước khi thực hiện phần việc tương ứng.
- Không cần đọc `AGENTS.md` của thư mục hoàn toàn không liên quan đến task.
- Khi phát hiện thêm file liên quan ở một khu vực chưa được xác định ban đầu, phải đọc `AGENTS.md` áp dụng cho khu vực đó trước khi tiếp tục.
- Không được bắt đầu sửa rồi mới đọc quy tắc để hợp thức hóa thay đổi đã thực hiện.

## 1. Tổng quan dự án

- Đây là WordPress child theme sử dụng Flatsome.
- Dự án dùng PHP, HTML, Tailwind CSS, CSS thuần và JavaScript.
- Tailwind CSS được build local.
- `package.json` và `node_modules` chỉ phục vụ quá trình build Tailwind CSS, không dùng để quản lý thư viện JavaScript frontend của website.
- Không sửa trực tiếp Flatsome parent theme.
- Không sửa trực tiếp mã nguồn plugin bên thứ ba.
- Không thay đổi WordPress core.
- Ưu tiên mở rộng bằng child theme, hook, filter, template override hoặc module riêng.

## 2. Phạm vi thực hiện task

- Chỉ sửa những file liên quan trực tiếp đến task.
- Không tự ý refactor toàn bộ dự án.
- Không tự ý thay đổi cấu trúc thư mục.
- Không đổi tên hoặc di chuyển file nếu chưa kiểm tra tất cả nơi đang require, include, enqueue hoặc tham chiếu đến file đó.
- Không xóa code cũ chỉ vì chưa thấy nơi sử dụng.
- Trước khi xóa hoặc thay đổi code hiện có, phải kiểm tra hook, shortcode, template, JavaScript selector, CSS selector và nơi gọi liên quan.
- Không thay đổi hành vi ngoài phạm vi task.
- Không tự ý sửa nội dung, giao diện hoặc logic của component khác.
- Không thêm tính năng ngoài yêu cầu.
- Không tự ý thay đổi URL, slug, endpoint, permalink hoặc rewrite rule.
- Không tự ý thay đổi dữ liệu trong database.
- Không thao tác production nếu task không yêu cầu rõ ràng.

## 3. Nguyên tắc ưu tiên khi sửa code

Thứ tự ưu tiên:

1. Tận dụng cấu trúc và module hiện có.
2. Sửa đúng file chịu trách nhiệm cho chức năng.
3. Dùng WordPress, WooCommerce hoặc Flatsome API nếu đã có giải pháp phù hợp.
4. Chỉ tạo file mới khi chưa có vị trí hợp lý trong cấu trúc hiện tại.
5. Chỉ thêm dependency hoặc thư viện khi giải pháp hiện có không đáp ứng hợp lý.

- Không viết lại toàn bộ component nếu chỉ cần sửa một phần nhỏ.
- Không tạo code trùng với chức năng đã tồn tại.
- Không sao chép cùng một logic vào nhiều file.
- Khi có nhiều cách làm, ưu tiên cách ít ảnh hưởng nhất đến code hiện tại.
- Giữ khả năng bảo trì và tương thích với WordPress, Flatsome và WooCommerce.
- Comment và chú thích giải thích mới trong code ưu tiên viết bằng tiếng Việt rõ ràng, ngắn gọn và đúng ngữ cảnh.
- Không dịch tên biến, function, class, constant, hook, filter, API, CSS class, JavaScript selector, handle, key cấu hình hoặc thuật ngữ kỹ thuật cần giữ nguyên.
- Giữ nguyên cú pháp và ngôn ngữ cần thiết của comment đặc biệt hoặc comment để công cụ đọc, như PHPDoc tag (`@param`, `@return`), `translators:`, PHPCS, ESLint, Stylelint, Tailwind/build directive, comment bản quyền, license, version và compatibility note.
- Không dịch hoặc viết lại hàng loạt comment cũ ngoài phạm vi task; chỉ chuẩn hóa comment liên quan trực tiếp đến phần code đang sửa.

## 4. Cấu trúc dự án

Các khu vực chính:

```text
theme/
├── AGENTS.md
├── assets/
├── inc/
│   ├── admin/
│   ├── assets/
│   ├── blog/
│   ├── cleanup/
│   ├── integrations/
│   ├── modules/
│   ├── product-filter/
│   ├── rewrite/
│   └── woocommerce/
├── template-parts/
│   ├── components/
│   └── posts/
├── tests/
│   └── manual/
├── woocommerce/
├── functions.php
├── style.css
├── package.json
└── tailwind.config.js
```

Quy ước:

- `functions.php` chỉ nạp các `bootstrap.php` cấp nhóm trong `inc/` và thực hiện khởi tạo cấp theme khi thật sự cần thiết.
- `inc/` chứa logic PHP, hook, filter, module và chức năng tích hợp.
- Quy tắc tổ chức module PHP chi tiết nằm trong `inc/AGENTS.md`.
- `template-parts/` chứa template giao diện custom có thể tái sử dụng và template override của Flatsome theo đúng đường dẫn tương đối.
- Quy tắc tổ chức template giao diện chi tiết nằm trong `template-parts/AGENTS.md`.
- `tests/manual/` chứa công cụ kiểm thử thủ công và không được nạp trong production.
- `woocommerce/` chứa template WooCommerce override.
- `assets/` chứa CSS, JavaScript, font, hình ảnh và source Tailwind.
- Không đưa một khối logic lớn vào `functions.php` nếu có thể tách thành module phù hợp.
- Không đặt logic nghiệp vụ phức tạp trực tiếp trong template.
- Không đặt JavaScript dài trực tiếp trong file PHP nếu đã có nơi phù hợp trong `assets/js/`.
- Không đặt CSS dài trực tiếp trong template nếu đã có nơi phù hợp trong `assets/`.

### 4.1. Tổ chức khi thêm hoặc custom UI

Trước khi tạo file, phải xác định thay đổi thuộc một trong các loại: chỉnh component hiện có, UI dùng chung, UI riêng của một trang, UI riêng của blog, tích hợp WooCommerce hoặc template override. Ưu tiên sửa đúng component hiện có trước khi tạo cấu trúc mới.

| Trách nhiệm | Vị trí ưu tiên |
| --- | --- |
| Markup UI custom có thể tái sử dụng | `template-parts/components/` |
| Markup archive hoặc bài viết của blog | `template-parts/posts/` |
| Logic PHP dùng chung, độc lập với blog và WooCommerce | `inc/modules/` |
| Logic PHP chỉ dành cho blog | `inc/blog/` |
| Hook, filter hoặc integration chỉ dành cho WooCommerce | `inc/woocommerce/` |
| Template override WooCommerce | `woocommerce/` và giữ đúng đường dẫn tương đối của template gốc |
| CSS của component dùng lại được | `assets/css/components/` |
| CSS của khu vực layout lớn | `assets/css/layout/` |
| CSS chỉ dùng cho một trang hoặc template cụ thể | `assets/css/pages/` |
| JavaScript do theme tự viết | `assets/js/`, đặt tên theo component hoặc trang chịu trách nhiệm |

Quy tắc tạo cấu trúc:

- Không mặc định tạo đủ PHP, template, CSS và JavaScript cho mọi UI; chỉ tạo lớp thực sự cần thiết.
- Nếu chỉ thay đổi màu sắc, khoảng cách, kích thước hoặc trạng thái trình bày trên markup hiện có, chỉ sửa CSS hoặc Tailwind phù hợp; không tạo module PHP hay template mới.
- Nếu chỉ cần tách một khối markup dùng lại được, tạo template part; không tạo module PHP rỗng chỉ để gọi template.
- Chỉ tạo JavaScript khi UI có hành vi tương tác mà HTML, CSS hoặc API hiện có không đáp ứng được.
- Chỉ tạo module PHP khi component có xử lý dữ liệu, hook, filter, query, shortcode, endpoint hoặc logic render cần tái sử dụng.
- Component dùng chung nhưng có adapter riêng cho blog hoặc WooCommerce phải giữ phần dùng chung trong `inc/modules/` hoặc `template-parts/components/`; phần kết nối theo ngữ cảnh đặt trong `inc/blog/` hoặc `inc/woocommerce/`.
- Không đưa logic WooCommerce vào module dùng chung nếu module đó có thể hoạt động khi WooCommerce không được kích hoạt.
- Không dùng template override khi hook, filter hoặc template part custom đã đáp ứng đầy đủ và ổn định hơn.

Quy tắc đặt tên:

- Tên thư mục và file mới dùng tiếng Anh, dạng kebab-case, mô tả đúng trách nhiệm, ví dụ `pagination.php`, `product-card.php`, `pagination.css` và `pagination.js`.
- Các file thuộc cùng một component nên dùng cùng tên gốc để dễ tìm kiếm, trừ khi nằm trong thư mục component và cần tên cụ thể hơn.
- Không dùng tên mơ hồ như `custom.php`, `new.php`, `functions.php` bên trong component, `style-new.css` hoặc `script-final.js`; không áp dụng hạn chế này cho file `functions.php` bắt buộc ở gốc theme.
- Không gắn tên dự án hiện tại vào component có mục đích tái sử dụng cho nhiều dự án.
- Không lặp lại tên thư mục trong tên file khi ngữ cảnh đã rõ, ví dụ dùng `product-filter/filter-bar.php` thay vì `product-filter/product-filter-filter-bar.php`.

## 5. WordPress và PHP

- Tuân thủ WordPress Coding Standards ở mức hợp lý với cấu trúc hiện tại.
- Ưu tiên WordPress API thay vì tự viết lại chức năng hệ thống.
- Không viết cứng đường dẫn tuyệt đối.
- Dùng `get_stylesheet_directory()` để lấy đường dẫn filesystem của child theme.
- Dùng `get_stylesheet_directory_uri()` để lấy URL của child theme.
- Dùng `home_url()`, `site_url()`, `admin_url()`, `wp_upload_dir()` hoặc API tương ứng thay vì viết cứng URL.
- Không viết cứng database prefix.
- Không dùng truy vấn SQL trực tiếp nếu WordPress API hoặc WooCommerce API đã đáp ứng được.
- Khi bắt buộc dùng `$wpdb`, phải dùng `prepare()` cho dữ liệu động.
- Function, class, constant, hook helper hoặc global riêng của theme phải có prefix nhất quán để tránh xung đột.
- Kiểm tra khả năng trùng function hoặc class trước khi khai báo trong khu vực có nguy cơ xung đột.
- Không tắt warning hoặc error chỉ để che lỗi.
- Không dùng `@` để suppress lỗi PHP.
- Không để debug output như `var_dump()`, `print_r()`, `error_log()` hoặc `console.log()` khi hoàn thành task, trừ khi task yêu cầu giữ lại.

## 6. Bảo mật và dữ liệu

- Sanitize dữ liệu đầu vào.
- Validate dữ liệu theo đúng kiểu và phạm vi mong đợi.
- Escape dữ liệu đầu ra đúng ngữ cảnh.
- Dùng `esc_html()` cho văn bản thuần.
- Dùng `esc_attr()` cho HTML attribute.
- Dùng `esc_url()` cho URL.
- Dùng `wp_kses_post()` hoặc hàm phù hợp khi cần cho phép HTML an toàn.
- Dùng nonce cho thao tác tạo, sửa, xóa hoặc gửi dữ liệu.
- Kiểm tra capability đối với chức năng quản trị.
- Không tin tưởng dữ liệu từ `$_GET`, `$_POST`, `$_REQUEST`, cookie hoặc AJAX request.
- Không ghi thông tin nhạy cảm vào JavaScript frontend.
- Không để lộ secret, token, password hoặc thông tin môi trường trong repository.
- Không chỉnh sửa file `.env` nếu task không yêu cầu rõ ràng.
- Không commit file chứa thông tin nhạy cảm.

## 7. HTML và giao diện

- Dùng `div` cho wrapper chỉ phục vụ layout hoặc style; dùng `section`, `article`, `nav`, `main`, `aside` và phần tử semantic khác khi nội dung có ý nghĩa tương ứng.
- Giữ markup dễ đọc và không lồng cấp không cần thiết.
- Không để nhiều dòng trống liên tiếp trong HTML hoặc đoạn PHP/HTML hỗn hợp.
- Không đặt dòng trống ngay sau thẻ mở hoặc ngay trước thẻ đóng nếu không dùng để phân tách một khối logic rõ ràng.
- Các phần tử con đơn giản nằm liền nhau không cần xen dòng trống; chỉ dùng tối đa một dòng trống để phân tách các khối giao diện lớn hoặc các nhánh PHP khác nhau.
- Không minify hoặc dồn toàn bộ markup thành một dòng; vẫn giữ indentation nhất quán để dễ đọc và review.
- Không tự động xóa whitespace trong nội dung mà khoảng trắng có ý nghĩa, như `pre`, `textarea`, văn bản inline hoặc chuỗi được render cho người dùng.
- Khi task chỉ sửa một component, chỉ dọn khoảng trắng trong phạm vi component hoặc đoạn code liên quan; không format hàng loạt file ngoài phạm vi.
- Không thêm wrapper nếu không phục vụ layout, style, JavaScript hoặc accessibility.
- Không đổi tên class, ID hoặc data attribute hiện có khi chưa kiểm tra nơi sử dụng.
- Không dùng inline style nếu Tailwind hoặc CSS hiện có xử lý được.
- Không viết nội dung giả, placeholder hoặc dữ liệu mẫu vào production code nếu task không yêu cầu.
- Giữ nguyên nội dung tiếng Việt, dấu câu và ký tự đặc biệt khi task chỉ yêu cầu sửa giao diện.
- Không tự ý thay đổi text, URL, icon hoặc hình ảnh ngoài phạm vi task.
- Hình ảnh phải có `alt` phù hợp; ảnh trang trí có thể dùng `alt=""`.
- Nút chỉ có icon phải có `aria-label`.
- Icon trang trí phải có `aria-hidden="true"` khi phù hợp.
- Không làm mất khả năng sử dụng bằng bàn phím.
- Không vô hiệu hóa focus outline nếu chưa có focus style thay thế rõ ràng.

## 8. Responsive

- Thiết kế theo mobile-first.
- Class mặc định áp dụng cho mobile.
- Dùng breakpoint chuẩn của Tailwind và breakpoint tùy chỉnh `desktop: 850px` của dự án.
- Không tự tạo breakpoint arbitrary nếu chưa có lý do kỹ thuật rõ ràng.
- Không viết desktop trước rồi dùng `max-width` để vá lại mobile.
- Không để xuất hiện horizontal scroll ngoài chủ đích.
- Kiểm tra spacing hai bên trên mobile.
- Không để text, button, input hoặc table tràn khỏi viewport.
- Khi task chỉ có hai trạng thái mobile và desktop, dùng class mặc định cho mobile và `desktop:` cho desktop từ 850px.
- `lg:` chỉ dùng khi layout thật sự cần thay đổi từ 1024px, không dùng thay cho breakpoint desktop của Flatsome.
- Trước khi sửa class Tailwind trong PHP, template hoặc JavaScript ở bất kỳ thư mục nào của theme, phải đọc và áp dụng `assets/AGENTS.md`.

## 9. CSS và Tailwind

- Quy tắc chi tiết về Tailwind, CSS thuần, responsive frontend, Font Awesome và JavaScript nằm trong `assets/AGENTS.md` và áp dụng cho frontend markup trên toàn child theme.
- Không lặp lại các quy tắc frontend chi tiết tại file gốc này.
- Không chỉnh sửa trực tiếp file CSS được sinh tự động nếu thay đổi cần thực hiện từ source.
- Không thêm `!important` tràn lan.
- Không sửa CSS toàn cục khi task chỉ liên quan một component.
- Không ghi đè class hệ thống của Flatsome nếu chưa kiểm tra phạm vi ảnh hưởng.
- Không dùng Tailwind CDN.
- Sau khi thay đổi Tailwind hoặc source CSS liên quan, phải chạy lệnh build phù hợp.

## 10. JavaScript và thư viện frontend

- Quy tắc chi tiết nằm trong `assets/AGENTS.md`.
- JavaScript frontend được quản lý bằng file enqueue PHP và tài nguyên trong `assets/js/`.
- Không dùng `package.json` để quản lý thư viện JavaScript frontend; `package.json` chỉ phục vụ build Tailwind CSS.
- Không thêm thư viện mới nếu JavaScript thuần, CSS, Tailwind hoặc thư viện hiện có đã xử lý hợp lý.
- Có thể thêm GSAP hoặc thư viện tương tự khi task thực sự cần animation phức tạp.
- Trước khi thêm thư viện frontend, phải kiểm tra file enqueue PHP, `assets/js/` và thư viện theme hoặc plugin đang tải.
- Tránh tải trùng cùng một thư viện.
- Thư viện chỉ dùng cho một trang hoặc component phải được enqueue có điều kiện khi hợp lý.
- Không tải toàn site một thư viện nặng chỉ để phục vụ một hiệu ứng nhỏ.
- Không sử dụng nhiều thư viện có chức năng trùng nhau nếu không có lý do rõ ràng.

## 11. Flatsome và UX Builder

- Không sửa parent theme Flatsome.
- Không phụ thuộc quá sâu vào markup nội bộ của Flatsome nếu có thể dùng class riêng hoặc hook ổn định hơn.
- Khi sửa component từ UX Builder, phải cân nhắc HTML do shortcode sinh ra.
- Không thay đổi shortcode structure hoặc attribute nếu chưa kiểm tra ảnh hưởng đến UX Builder.
- Không xóa class hoặc wrapper do Flatsome dùng cho layout, responsive hoặc JavaScript nếu chưa xác định an toàn.
- Khi CSS mặc định của Flatsome xung đột với Tailwind, áp dụng quy tắc override trong `assets/AGENTS.md`.
- Không ghi đè CSS Flatsome ở phạm vi toàn site nếu chỉ cần xử lý một component.
- Ưu tiên thêm class riêng cho component để giới hạn phạm vi.

## 12. WooCommerce

- Không sửa trực tiếp plugin WooCommerce.
- Template override phải đặt trong thư mục `woocommerce/` của child theme.
- Không thay đổi logic nghiệp vụ khi task chỉ yêu cầu sửa giao diện.
- Không xóa hook, nonce, hidden field, form action hoặc input name khi chưa kiểm tra chức năng.
- Không viết cứng URL giỏ hàng, cửa hàng, tài khoản hoặc thanh toán.
- Dùng WooCommerce API để lấy URL, dữ liệu và trạng thái.
- Không làm hỏng AJAX cart, cart fragments, quantity update hoặc checkout flow.
- Không thay đổi cart item key, product ID, variation ID hoặc endpoint nếu task không yêu cầu.
- Khi override template, giữ thông tin version nếu file hiện tại đang có.
- Báo rõ nếu template override có dấu hiệu lỗi thời.
- Quy tắc chi tiết cho khu vực này có thể được bổ sung trong `woocommerce/AGENTS.md`.

## 13. Template và nội dung render

- Template chỉ nên chịu trách nhiệm chuẩn bị nhẹ và render giao diện.
- Logic truy vấn hoặc xử lý lớn phải đặt trong module phù hợp.
- Không thực hiện query nặng lặp lại trong loop.
- Không gọi API bên ngoài trực tiếp khi render trang nếu không có cache hoặc lý do rõ ràng.
- Escape dữ liệu trước khi xuất ra HTML.
- Không làm thay đổi global query nếu chưa khôi phục trạng thái.
- Sau custom query phải dùng `wp_reset_postdata()` khi cần.
- Không tạo N+1 query nếu có thể lấy dữ liệu theo batch.

## 14. Hiệu suất

- Không enqueue CSS hoặc JavaScript không cần thiết trên toàn website.
- Ưu tiên enqueue có điều kiện cho tài nguyên chỉ dùng ở một trang hoặc component.
- Không tải trùng thư viện đã có từ theme hoặc plugin.
- Không thêm ảnh quá lớn nếu có thể dùng kích thước phù hợp.
- Không bỏ qua lazy loading nếu ảnh không nằm ở vùng hiển thị đầu tiên.
- Không thêm animation nặng ảnh hưởng cuộn trang hoặc thiết bị yếu.
- Không tạo DOM quá lớn cho một component đơn giản.
- Không thêm query database trong vòng lặp nếu tránh được.
- Không thêm request AJAX liên tục khi không cần.
- Không đánh đổi khả năng bảo trì để tối ưu vi mô không đáng kể.

## 15. Khả năng tương thích

- Giữ tương thích với WordPress, Flatsome, WooCommerce và PHP version hiện tại của dự án.
- Không dùng API mới nếu chưa xác định môi trường hỗ trợ.
- Không thay đổi behavior hiện có trên desktop khi task chỉ sửa mobile và ngược lại.
- Không làm hỏng UX Builder preview.
- Không làm hỏng trang quản trị.
- Không giả định plugin luôn được kích hoạt; kiểm tra function hoặc class tồn tại khi tích hợp plugin tùy chọn.
- Không gọi WooCommerce function nếu chưa kiểm tra WooCommerce đang hoạt động trong khu vực có thể chạy độc lập.

## 16. File được sinh tự động và file không được sửa

- Không chỉnh sửa `node_modules/`.
- Không commit `node_modules/`.
- Không sửa trực tiếp file build nếu file đó phải được tạo lại từ source.
- `assets/css/generated/tailwind.css` là build artifact dùng trực tiếp trên website: không sửa tay, nhưng phải build lại và đưa file đã sinh vào thay đổi khi class Tailwind, cấu hình Tailwind hoặc source Tailwind thay đổi.
- Không sửa file thư viện bên thứ ba.
- Không sửa WordPress core.
- Không sửa parent theme.
- Không sửa plugin bên thứ ba.
- Không sửa file backup, export, database dump hoặc archive như `.zip`, `.sql`, `.wpress`, `.tar`, `.tar.gz`.
- Không tự ý thay đổi `.gitignore`.
- Không tự ý thay đổi cấu hình build nếu task không liên quan.

## 17. Git và thay đổi source

- Git repository của dự án nằm tại thư mục gốc child theme `Phongcachnhat/`; chạy lệnh Git với thư mục này làm working directory.
- Chỉ đưa vào commit các file liên quan đến task.
- Không commit file tạm, log, cache, backup hoặc build artifact không thuộc quy trình dự án.
- Không tự ý reset, revert hoặc xóa thay đổi của người khác.
- Không dùng lệnh Git phá hủy như `reset --hard`, `clean -fd` hoặc force push nếu chưa được yêu cầu rõ ràng.
- Trước khi sửa file đang có thay đổi chưa commit, phải bảo toàn nội dung hiện tại.
- Không thay đổi line ending hàng loạt nếu task không liên quan.
- Không format lại toàn bộ file chỉ vì sửa một đoạn nhỏ.
- Giữ diff nhỏ, rõ ràng và dễ review.

## 18. Kiểm tra sau khi hoàn thành

Tùy loại task, phải thực hiện các kiểm tra phù hợp:

- Kiểm tra syntax PHP.
- Kiểm tra lỗi JavaScript.
- Chạy build Tailwind khi có thay đổi class hoặc source CSS.
- Kiểm tra responsive ở mobile và desktop; nếu thay đổi liên quan breakpoint Flatsome, kiểm tra cả 849px và 850px.
- Kiểm tra không có horizontal scroll ngoài ý muốn.
- Kiểm tra link, button, form và interactive state.
- Kiểm tra hover, focus, active và disabled state khi liên quan.
- Kiểm tra console không có lỗi mới.
- Kiểm tra PHP log nếu task liên quan backend.
- Kiểm tra WooCommerce flow nếu sửa cart, checkout, account hoặc product.
- Không tuyên bố đã kiểm tra một bước nếu chưa thực sự chạy hoặc quan sát được.

## 19. Báo cáo khi hoàn thành task

Khi hoàn thành, báo ngắn gọn và chính xác:

- Các file đã sửa.
- Thay đổi chính.
- Lệnh build hoặc kiểm tra đã chạy.
- Kết quả kiểm tra.
- Lỗi, giới hạn hoặc phần chưa xác minh.
- Không trình bày dài dòng nếu task đơn giản.
- Không tuyên bố “hoàn tất” nếu vẫn còn lỗi đã biết.
- Không che giấu bước kiểm tra chưa chạy.

## 20. Quan hệ với `AGENTS.md` trong thư mục con

- File này áp dụng cho toàn bộ dự án.
- `assets/AGENTS.md` bổ sung quy tắc cho Tailwind, CSS, JavaScript, font và tài nguyên frontend.
- `inc/AGENTS.md` bổ sung quy tắc cho module PHP, bootstrap, dependency và đường dẫn nội bộ.
- `template-parts/AGENTS.md` bổ sung quy tắc phân loại, đặt tên và tổ chức template giao diện.
- `woocommerce/AGENTS.md` có thể bổ sung quy tắc cho WooCommerce override.
- Không cần lặp lại quy tắc cấp cao trong file con.
- Khi quy tắc file con chi tiết hơn và chỉ áp dụng trong thư mục đó, dùng quy tắc file con.
- File con không được vô hiệu hóa các quy tắc an toàn, bảo mật hoặc giới hạn phạm vi ở file gốc.
