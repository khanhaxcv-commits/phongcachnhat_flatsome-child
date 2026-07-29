# Assets Instructions

Các quy tắc trong file `AGENTS.md` ở thư mục gốc vẫn được áp dụng.

File này quy định cách làm việc với toàn bộ tài nguyên frontend trong thư mục `assets/`, bao gồm Tailwind CSS, CSS thuần, JavaScript, hình ảnh, font và Font Awesome. Theo yêu cầu của file gốc, các quy tắc về Tailwind và frontend markup trong file này cũng áp dụng khi sửa PHP hoặc template ở thư mục khác của child theme.

## 1. Cấu trúc thư mục

```text
assets/
├── css/
├── fontawesome-pro-v7/
├── js/
└── src/
```

Quy ước:

- `assets/src/` chứa mã nguồn Tailwind hoặc CSS nguồn.
- `assets/css/` chứa CSS được build hoặc CSS dùng trực tiếp trên website.
- `assets/js/` chứa JavaScript của theme.
- `assets/fontawesome-pro-v7/` chứa Font Awesome Pro và webfont.
- Không chỉnh sửa `node_modules/`.
- Không di chuyển hoặc đổi tên file nếu chưa kiểm tra nơi file đang được enqueue.
- Không tạo file mới nếu đã có file phù hợp với chức năng đang sửa.

## 2. Tailwind CSS

- Dự án sử dụng Tailwind CSS 3.4.17.
- Ưu tiên Tailwind utility class trước CSS thuần.
- Class Tailwind trong thuộc tính `class` phải viết trên một dòng.
- Không dùng Tailwind CDN.
- Không chỉnh sửa trực tiếp file CSS build nếu thay đổi đó phải được tạo từ source Tailwind.
- Các token màu, nền, border, focus ring và shadow đã ánh xạ trong `tailwind.config.js`; ưu tiên utility semantic như `text-main`, `bg-surface`, `border-ui`, `ring-ui`, `shadow-ui-card` thay cho arbitrary value tương ứng.
- Chỉ dùng dạng `text-[var(--*)]`, `bg-[var(--*)]`, `border-[var(--*)]`, `ring-[var(--*)]` hoặc `shadow-[var(--*)]` khi token chưa có mapping semantic hoặc giá trị là biểu thức riêng của component.
- Thang bo góc trong `tailwind.config.js` phải ánh xạ tới các token `--radius-*` trong `style.css` và giữ đúng tên chuẩn của Tailwind: `rounded-none`, `rounded`, `rounded-sm`, `rounded-md`, `rounded-lg`, `rounded-xl`, `rounded-2xl`, `rounded-3xl`, `rounded-full`.
- Ưu tiên utility bo góc chuẩn; không dùng arbitrary value như `rounded-[var(--radius-lg)]` khi đã có `rounded-lg` tương ứng.
- Không tạo token bo góc theo tên component như `--radius-input` nếu giá trị đã có trong thang chuẩn. Chỉ tạo token riêng khi đó là một quyết định thiết kế độc lập, không phải alias của token hiện có.

Ví dụ đúng:

```html
<div class="custom-card relative rounded-xl bg-white p-4 shadow-sm desktop:sticky desktop:top-3"></div>
```

## 3. Responsive

Toàn bộ giao diện phải viết theo hướng mobile-first.

Breakpoint chuẩn:

```text
Mặc định: mobile
sm: 640px
md: 768px
desktop: 850px
lg: 1024px
xl: 1280px
2xl: 1536px
```

Quy ước:

- Mobile dùng class mặc định, không có prefix.
- `sm:` chỉ dùng khi giao diện cần thay đổi từ 640px.
- Tablet ưu tiên `md:`.
- Giao diện desktop cần đồng bộ với breakpoint của Flatsome phải dùng `desktop:` từ 850px.
- `lg:` chỉ dùng khi layout thật sự cần thay đổi từ 1024px, không dùng thay cho breakpoint desktop của Flatsome.
- Màn hình lớn dùng `xl:` hoặc `2xl:` khi thật sự cần.
- Nếu thiết kế chỉ có hai trạng thái mobile và desktop, dùng class mặc định cho mobile và `desktop:` cho desktop.
- Phải khai báo đầy đủ style mobile trước, sau đó dùng breakpoint để ghi đè cho màn hình lớn hơn.
- Không viết desktop trước rồi dùng `max-width` để vá lại mobile.
- Không tự tạo breakpoint arbitrary như `min-[850px]:`, `max-[849px]:` hoặc `@media (max-width: 849px)` nếu task không yêu cầu tương thích chính xác với Flatsome hoặc một breakpoint đặc biệt đã được xác nhận.
- Khi thay đổi responsive liên quan breakpoint Flatsome, kiểm tra giao diện ở cả 849px và 850px.
- Không dùng CSS thuần hoặc `@media` cho padding, margin, gap, flex, grid, width, height, background, color, border, border-radius, shadow, position hoặc typography khi Tailwind xử lý được.
- Responsive cho các thuộc tính Tailwind xử lý được phải đặt trực tiếp trong thuộc tính `class`.

Ví dụ đúng:

```html
<div class="rounded-none border-b border-gray-200 bg-white p-2 shadow-[0_-6px_20px_rgba(17,24,39,0.1)] desktop:sticky desktop:top-3 desktop:rounded-[14px] desktop:border-b-0 desktop:p-4 desktop:shadow-none"></div>
```

Ví dụ không đúng:

```css
@media (max-width: 849px) {
    .custom-cart-summary-card {
        padding: 8px;
        border-radius: 0;
        background: #fff;
    }
}
```

## 4. Xung đột Tailwind và Flatsome

Dự án sử dụng Tailwind cùng CSS mặc định của Flatsome.

Flatsome có thể ghi đè:

- `display`
- màu chữ
- màu chữ khi hover
- style của `input`, `select`, `textarea` và Select2
- style của thẻ liên kết
- style được cấu hình trong WordPress Admin

Vì vậy phải tuân thủ các quy tắc bên dưới.

### 4.1. Form control bắt buộc kiểm tra CSS của Flatsome

- Trước khi style `input`, `select`, `textarea`, `.select2-choice` hoặc `.select2-selection`, phải kiểm tra selector tương ứng trong `wp-content/themes/flatsome/assets/css/flatsome.css`. Khi kiểm tra giao diện trên trình duyệt, đối chiếu thêm computed style để xác định rule đang thắng.
- Selector mặc định của Flatsome có specificity cao hơn một utility Tailwind đơn lẻ. Vì vậy utility dùng để ghi đè các thuộc tính Flatsome đã khai báo như background, border, border-radius, box-shadow, color, font-size, height, max-width, padding, transition, vertical-align hoặc width phải dùng important modifier `!`.
- Important modifier phải đặt đúng sau variant, ví dụ `focus:!border-*`, `hover:!shadow-*`, `disabled:!bg-*` và `desktop:!h-*`.
- Chỉ thêm `!` vào thuộc tính thực sự cần ghi đè; không thêm vào utility không xung đột với Flatsome.

Ví dụ đúng:

```html
<input class="!h-11 !w-full !rounded-lg !border-input !bg-input !px-3 !text-sm !text-input !shadow-none placeholder:!text-input focus:!border-input-focus focus:!ring-4 focus:!ring-ui">
```

Không viết:

```html
<input class="h-11 rounded-lg border-gray-300 bg-white px-3 text-gray-900 shadow-none">
```

## 5. Display bắt buộc dùng `!`

Mọi utility Tailwind điều khiển `display` phải dùng important modifier `!`.

Áp dụng cho:

- `hidden`
- `block`
- `inline`
- `inline-block`
- `flex`
- `inline-flex`
- `grid`
- `inline-grid`
- `table`
- `table-row`
- `table-cell`
- `contents`
- `flow-root`

Ví dụ đúng:

```html
<div class="!hidden desktop:!block"></div>
<div class="!flex items-center gap-3"></div>
<div class="!grid grid-cols-2 gap-4 desktop:grid-cols-4"></div>
<a class="!inline-flex items-center gap-2"></a>
```

Không viết:

```html
<div class="hidden desktop:block"></div>
<div class="flex"></div>
<a class="inline-flex"></a>
```

Với responsive, dấu `!` phải đặt sau variant:

```html
<div class="!hidden md:!flex desktop:!grid"></div>
```

Ngoại lệ: không dùng display utility có `!` theo cách làm hỏng trạng thái được điều khiển bởi thuộc tính `hidden`, inline style, jQuery hoặc JavaScript của WordPress, WooCommerce và Flatsome. Với các phần tử này, phải kiểm tra cơ chế ẩn/hiện hiện có trước khi thêm important modifier.

## 6. Màu chữ bắt buộc dùng `!`

Khi Tailwind quy định màu chữ cụ thể, phải dùng important modifier `!` để tránh bị Flatsome hoặc thiết lập trong WordPress Admin ghi đè.

Ví dụ đúng:

```html
<p class="!text-gray-900"></p>
<a class="!text-white"></a>
<span class="!text-[#ed1c24]"></span>
```

Không viết:

```html
<p class="text-gray-900"></p>
<a class="text-white"></a>
```

Quy tắc này đặc biệt áp dụng cho:

- thẻ `a`
- button
- menu item
- tiêu đề có liên kết
- icon kế thừa màu
- text nằm trong component Flatsome
- text chịu ảnh hưởng từ Theme Options

## 7. Màu chữ hover bắt buộc dùng `!`

Khi định nghĩa màu chữ hover, phải dùng `hover:!text-*`.

Ví dụ đúng:

```html
<a class="!text-gray-900 hover:!text-red-600"></a>
<a class="!text-white hover:!text-yellow-300"></a>
<button class="!text-gray-700 hover:!text-[#ed1c24]"></button>
```

Không viết:

```html
<a class="text-gray-900 hover:text-red-600"></a>
<a class="!text-gray-900 hover:text-red-600"></a>
```

Kết hợp responsive:

```html
<a class="!text-gray-900 hover:!text-red-600 desktop:!text-white desktop:hover:!text-yellow-300"></a>
```

## 8. Màu icon

Nếu icon kế thừa màu từ phần tử cha, ưu tiên đặt màu tại phần tử cha.

```html
<a class="!inline-flex items-center gap-2 !text-gray-900 hover:!text-red-600">
    <i class="fa-light fa-chevron-left text-xs" aria-hidden="true"></i>
    <span>Tiếp tục mua sắm</span>
</a>
```

Chỉ thêm `!text-*` trực tiếp vào icon khi Flatsome ghi đè màu của icon.

## 9. Không dùng `!` tràn lan

Important modifier chỉ bắt buộc cho:

- utility điều khiển `display`
- màu chữ
- màu chữ khi hover
- utility đã xác định bị Flatsome ghi đè
- trường hợp task yêu cầu ghi đè CSS hiện có

Không tự động thêm `!` vào toàn bộ utility.

Không nên viết:

```html
<div class="!relative !w-full !rounded-xl !bg-white !p-4 !shadow-sm"></div>
```

Ưu tiên:

```html
<div class="relative w-full rounded-xl bg-white p-4 shadow-sm"></div>
```

## 10. CSS thuần

Chỉ viết CSS thuần khi:

- Tailwind không xử lý hợp lý.
- Component có trạng thái phức tạp.
- Cần selector theo markup WordPress hoặc WooCommerce.
- Cần pseudo-element.
- Cần tương thích với cấu trúc HTML do Flatsome sinh ra.

Quy tắc:

- Không dùng `!important` tràn lan.
- Không tạo selector phụ thuộc quá sâu vào cấu trúc HTML.
- Ưu tiên class riêng của component với tiền tố `custom-`.
- Class `custom-*` chỉ dùng cho phần CSS mà Tailwind không xử lý hợp lý, ví dụ pseudo-element, animation phức tạp, selector quan hệ, trạng thái phức tạp hoặc markup do WordPress, WooCommerce, Flatsome sinh ra.
- Không dùng class `custom-*` để viết lại các utility đơn giản đã có thể đặt bằng Tailwind trong HTML.
- Không tạo CSS trùng hoặc ghi đè utility Tailwind đang có trên cùng phần tử, trừ khi task yêu cầu override có chủ đích và đã xác định rõ lý do.
- Có thể kết hợp class `custom-*` với Tailwind trên cùng phần tử; Tailwind chịu trách nhiệm cho layout, spacing, màu sắc, border, radius, shadow, typography và responsive thông thường.
- Không sửa CSS toàn cục nếu task chỉ liên quan một component.
- Không ghi đè class hệ thống của Flatsome nếu chưa kiểm tra phạm vi ảnh hưởng.
- Với component hiện hữu đang dùng CSS thuần, ưu tiên bản sửa tối thiểu theo kiến trúc hiện tại; không tự ý chuyển toàn bộ sang Tailwind nếu task không yêu cầu refactor.

## 11. JavaScript

- Không thay đổi hành vi toàn site khi task chỉ liên quan một component.
- Kiểm tra element tồn tại trước khi khởi tạo logic, gắn event hoặc animation.
- Không khai báo biến global nếu không thật sự cần.
- Giữ nguyên tên class, ID hoặc data attribute đang được JavaScript sử dụng.
- Khi tạo selector DOM mới, ưu tiên dùng `data-*` thay vì phụ thuộc vào class Tailwind hoặc class `custom-*`.
- Class `custom-*` chủ yếu dùng làm selector CSS của component, không mặc định được xem là selector JavaScript.
- Không viết JavaScript inline trong template nếu có thể đặt logic vào file phù hợp trong `assets/js/`.
- Ưu tiên JavaScript thuần hoặc thư viện frontend đã có sẵn trong theme trước khi thêm thư viện mới.
- Trước khi thêm thư viện JavaScript mới, phải kiểm tra các file PHP enqueue, thư mục `assets/js/` và các thư viện đã được theme hoặc plugin tải sẵn.
- Không sử dụng `package.json` để quản lý thư viện JavaScript frontend; `package.json` của dự án chỉ phục vụ build Tailwind CSS.
- Có thể thêm thư viện như GSAP khi task yêu cầu animation phức tạp mà CSS hoặc JavaScript thuần không xử lý hợp lý.
- Không thêm thư viện chỉ để xử lý hiệu ứng đơn giản như fade, slide, hover hoặc toggle nếu CSS, Tailwind hoặc JavaScript thuần đã đáp ứng được.
- Khi thêm thư viện mới, phải nêu rõ lý do sử dụng, file enqueue liên quan và phạm vi trang hoặc component cần tải.
- Thư viện chỉ phục vụ một trang hoặc một component phải được enqueue có điều kiện, không tải trên toàn website.
- Không sử dụng đồng thời nhiều thư viện có chức năng trùng nhau trong cùng một component.
- Phải hỗ trợ `prefers-reduced-motion` đối với animation có chuyển động đáng kể.
- Phải cleanup event listener, observer, timer, ScrollTrigger hoặc animation instance khi component được khởi tạo lại.

## 12. Font Awesome

- Font Awesome Pro nằm trong `assets/fontawesome-pro-v7/`.
- Mặc định ưu tiên style `fa-light` để tận dụng bộ Font Awesome Pro và giữ giao diện thanh thoát.
- Không tự động sử dụng `fa-solid` chỉ vì icon đó phổ biến hoặc dễ tìm.
- Chỉ dùng `fa-solid`, `fa-regular`, `fa-thin`, `fa-duotone` hoặc style khác khi mockup, thiết kế hoặc task yêu cầu rõ ràng.
- Trước khi dùng icon, ưu tiên kiểm tra icon tương ứng có hỗ trợ `fa-light`.
- Không dùng thêm icon library khác nếu Font Awesome Pro đã có icon tương ứng.
- Không thay đổi, xóa hoặc di chuyển webfont.
- Không sửa trực tiếp file thư viện Font Awesome.

Ví dụ mặc định:

```html
<i class="fa-light fa-chevron-left text-xs" aria-hidden="true"></i>
```

Chỉ dùng style khác khi có chủ đích:

```html
<i class="fa-solid fa-cart-shopping" aria-hidden="true"></i>
```

## 13. Build

Sau khi thay đổi class Tailwind trong PHP hoặc JavaScript, `tailwind.config.js` hay source trong `assets/src/`, chạy:

```bash
npm run build
```

Trong lúc phát triển có thể chạy:

```bash
npm run dev
```

`npm run dev` là tiến trình watch; chỉ chạy khi cần theo dõi thay đổi liên tục và phải dừng trước khi kết thúc task. Chỉ sửa file CSS dùng trực tiếp trong `assets/css/` thì không cần build Tailwind, trừ khi task đồng thời thay đổi class, cấu hình hoặc source Tailwind.

Sau khi hoàn thành phải báo rõ:

- file đã sửa
- thay đổi chính
- lệnh build đã chạy
- lỗi hoặc giới hạn còn tồn tại
