# Hướng Dẫn Quy Chuẩn Thiết Kế Giao Diện (Design System & Guidelines)

Tài liệu này lưu trữ toàn bộ quy chuẩn giao diện, bảng màu, phông chữ, bố cục và thành phần UI chuẩn cho dự án **Ninh Bình POI**. Hãy dựa vào tài liệu này khi phát triển các trang giao diện tiếp theo.

---

## 1. Tông Màu Chủ Đạo (Color Palette)

Dự án áp dụng bảng màu của mẫu **"Trang chủ Khám phá Ninh Bình Premium"** (`stitch_kh_m_ph_du_l_ch_ninh_b_nh/trang_ch_kh_m_ph_ninh_b_nh_premium`): nền xám lạnh trung tính, mực gần đen, và **đúng một màu nhấn là vàng đồng**.

Đây là bảng màu **cố ý đơn sắc**. Vẻ cao cấp không đến từ việc nhiều màu mà đến từ ba thứ: một điểm vàng ấm duy nhất đặt trên nền xám lạnh, khoảng trắng rộng, và nhãn chữ hoa nhỏ có giãn ký tự lớn.

### 1.1. Màu nền tảng

| Thành phần | Mã màu CSS | Mô tả |
| :--- | :--- | :--- |
| **Nền trang Auth / Form** | `#f1f5f9` | Giữ nguyên, trùng mã nền tệp `nen02.png` |
| **Nền trang chính Client** | `#ffffff` / `#f7f9fb` / `#f2f4f6` | Ba bậc xám lạnh; `#f2f4f6` là dải section xen kẽ |
| **Nền ô ảnh chờ tải** | `#e0e3e5` | Xám đặc hơn, dùng làm nền placeholder |
| **Tiêu đề chính (Headings)** | `#000000` | Đen tuyền. Đây là điểm đặc trưng của mẫu |
| **Chữ nền / mặc định** | `#191c1e` | Gần đen, dùng cho `body` |
| **Văn bản nội dung (Body text)**| `#45464d` | Xám đậm trung tính, giãn dòng 1.5 - 1.75. Tương phản 8.9:1 |
| **Thông tin phụ / Nhãn nhỏ**| `#76777d` | Xám vừa |
| **Viền ô nhập / Phân cách** | `#c6c6cd` / `#e0e3e5` | Viền mảnh nhạt, không dùng viền đậm gắt |
| **Nút bấm chính (Primary Button)**| `#000000` *(Hover: `#565e74`)*| Nút đen, chữ trắng |

> **Quy tắc một họ xám**: bảng này dùng xám **trung tính lạnh**. Không trộn xám ám xanh (`#1e3a5f`, `#47607d`, `#5f7594`) của bản cũ vào cùng trang.

### 1.2. Màu nhấn (Accent) — vàng đồng, dùng duy nhất

| Vai trò | Mã màu CSS | Dùng ở đâu |
| :--- | :--- | :--- |
| **Accent chính** | `#735c00` | Nhãn eyebrow, link hover, gạch chân mục active, ngày tháng, viền thẻ khi hover, tiêu đề cột footer. Tương phản 6.44:1 trên nền trắng nên dùng được cho cả chữ nhỏ |
| **Accent sáng** | `#cba72f` | Chỉ dùng trên nền tối, ví dụ vạch mảnh trên dải CTA đen |

### 1.3. Nguyên tắc dùng màu

- **Vàng đồng không bao giờ tô nền.** Nó chỉ là màu chữ, màu icon và màu viền. Mọi mảng nền trong trang đều giữ xám trung tính. Đây là quy tắc quan trọng nhất tạo nên vẻ tiết chế của mẫu — tô nền vàng sẽ hỏng ngay.
- **Đen cho cấu trúc, vàng cho tương tác.** Tiêu đề, nút chính, số trang đang xem dùng đen. Nhãn, link hover, mục menu đang chọn dùng vàng.
- **Lớp phủ trên ảnh dùng đen, không dùng màu.** Mẫu dùng `rgba(0,0,0,0.3)` cho hero và `rgba(0,0,0,0.6→0.8)` cho lớp phủ thẻ ảnh. Bóng đổ chữ trên ảnh: `0 4px 12px rgba(0,0,0,0.5)`.
- **Chỉ một dải nền xen kẽ.** Mẫu chỉ có `nb-section--mist` (`#f2f4f6`) phẳng, không viền, không chuyển sắc. Các section còn lại để trắng và dựa vào khoảng trắng lớn để tách nhau.
- Các mã trên khai báo sẵn thành biến CSS: `--primary`, `--text-dark`, `--text-body`, `--text-muted`, `--accent`, `--accent-bright`, `--line`, `--line-soft`, `--surface-mist` trong `client/layouts/app.blade.php`, và bản `--nb-*` tương ứng trong `public/css/heritage.css`.

> **Cảnh báo font**: mẫu gốc dùng `Libre Caslon Text` cho tiêu đề, nhưng font này **không có bộ ký tự tiếng Việt** (chỉ `latin`, `latin-ext`), chữ có dấu sẽ rơi sang font dự phòng trông lỗi. Nếu sau này muốn dùng serif, hãy chọn `Playfair Display` hoặc `Lora` — cả hai đều có subset `vietnamese`.

---

## 2. Quy Chuẩn Phông Chữ & Kiểu Dáng (Typography)

* **Font family**: `'Be Vietnam Pro', 'Plus Jakarta Sans', system-ui, sans-serif`
* **Độ đậm chữ (Font weight)**:
  - Hạn chế sử dụng `font-weight: 700` hoặc `800` quá đậm gây thô.
  - Tiêu đề trang dùng `font-weight: 600` (Semi-bold) hoặc `500` (Medium).
  - Nhãn form / Menu dùng `font-weight: 500`.
  - Văn bản nội dung dùng `font-weight: 400`.
* **Biểu tượng (Icon)**: Hạn chế tối đa icon trang trí rườm rà. Dùng văn bản thuần hoặc icon mỏng nhẹ khi thật sự cần thiết.

---

## 3. Quy Chuẩn Trang Đăng Nhập / Đăng Ký (Auth Split-Screen Layout)

### Bố cục Chia Đôi 50/50 (Split Screen):
1. **Nửa bên Trái (50% Width - Form Panel)**:
   - Nền màu `#f1f5f9`.
   - Nút **"Quay lại bản đồ"** đặt cố định ở góc trên bên trái (`top: 24px; left: 28px;`).
   - Tiêu đề Form đơn giản: **"Đăng nhập"** hoặc **"Đăng ký"** (không để logo thương hiệu rườm rà).
   - Trang Đăng ký xếp dạng **Grid 2 cột** (`Tên đăng nhập + Email` hàng 1, `Mật khẩu + Xác nhận` hàng 3) để chiều cao vừa vặn 1 màn hình.

2. **Nửa bên Phải (50% Width - Image Panel)**:
   - Hiển thị ảnh `public/images/nen02.png` nguyên bản 100%, **không** đè chữ, **không** đè mảng màu tối.
   - Thêm lớp **Gradient Mask mờ rìa trái (`::before`)** để ảnh hòa tan mượt mà vào nền `#f1f5f9` của form:
     ```css
     .split-image-side::before {
         content: '';
         position: absolute;
         top: 0; left: 0; bottom: 0;
         width: 180px;
         background: linear-gradient(to right, #f1f5f9 0%, rgba(241, 245, 249, 0) 100%);
         z-index: 2;
         pointer-events: none;
     }
     ```

3. **Ô Nhập Liệu Dạng Gạch Chân (Underline Inputs)**:
   - Loại bỏ viền hộp chữ nhật 4 cạnh. Chỉ giữ đường kẻ gạch chân dưới (`border-bottom: 1px solid #cbdbe8`).
   - Khi focus: `border-bottom: 2px solid #1e3a5f; outline: none; background: transparent;`.

---

## 4. Quy Chuẩn Thanh Điều Hướng (Navbar Header)

- **Logo**: Ảnh `public/images/logo.png` (44px ở header, 52px ở footer) đặt cạnh chữ `Ninh Bình Travel Hub`.
- **Mục active**: Chữ đen `#000000` kèm gạch chân vàng đồng `#735c00` dày 2px; hover đổi chữ sang `#735c00`.
- **Footer**: Nền sáng `#f7f9fb` tách bằng viền trên `#c6c6cd` (mẫu dùng footer sáng, **không** dùng footer nền tối). Tiêu đề cột màu vàng đồng, chữ hoa, giãn ký tự `0.1em`.
- **Nút User Profile**: Dùng pill màu xám `#f8fafc` viền `#e2e8f0` kèm avatar tròn `<x-user-avatar>`.

---

## 5. Quy Chuẩn Trang Báo / Tin Tức (Editorial Newspaper Layout)

- **Trang danh sách (`client/news/index.blade.php`)**:
  - **Hero Block**: 1 bài tin lớn nhất bên trái (ảnh 16:9, tiêu đề `1.25rem`) + 2 bài tiêu điểm phụ bên phải.
  - **Stream bài viết**: Ảnh 4:3, tiêu đề 2 dòng, tóm tắt 2 dòng, ngày tháng nhạt màu (`#a1a1aa`).
  - **Sidebar**: Khối **Xem nhiều nhất** đánh số thứ tự `1.`, `2.`, `3.`, `4.`, `5.` màu xám nhạt (`#a1a1aa`).
- **Trang đọc chi tiết (`client/news/show.blade.php`)**:
  - Tiêu đề màu `#27272a` (`font-size: 1.6rem`, `font-weight: 600`).
  - Khối Sapo (Tóm tắt) nền `#fafafa` có vạch dọc bên trái (`border-left: 2.5px solid #27272a`).
  - Giãn dòng bài viết `1.75` dễ đọc.
