# Hướng Dẫn Quy Chuẩn Thiết Kế Giao Diện (Design System & Guidelines)

Tài liệu này lưu trữ toàn bộ quy chuẩn giao diện, bảng màu, phông chữ, bố cục và thành phần UI chuẩn cho dự án **Ninh Bình POI**. Hãy dựa vào tài liệu này khi phát triển các trang giao diện tiếp theo.

---

## 1. Tông Màu Chủ Đạo (Color Palette)

Dự án áp dụng phong cách **Misty Ice-Blue & Slate Ink (Xanh sương mờ & Mực xám lam)** thanh lịch, dịu mắt,màu sắc đơn giản, hạn chế dùng icon trang trí rườm rà, hòa hợp với hình ảnh sông núi du lịch Ninh Bình:

| Thành phần | Mã màu CSS | Mô tả |
| :--- | :--- | :--- |
| **Nền trang Auth / Form** | `#f1f5f9` | Màu xanh sương mờ dịu mắt, trùng mã nền tệp `nen02.png` |
| **Nền trang chính Client** | `#ffffff` / `#f8fafc` | Trắng / Xám nhạt sạch sẽ, tương phản tốt với bài viết |
| **Tiêu đề chính (Headings)** | `#1e3a5f` / `#27272a` | Xanh lam xám sẫm mềm mại, **không** dùng màu đen tuyền `#000000` |
| **Văn bản nội dung (Body text)**| `#3b5980` / `#52525b` | Xám xanh dịu mắt, giãn dòng 1.5 - 1.75 |
| **Thông tin phụ / Ngày tháng**| `#6482a6` / `#a1a1aa` | Xám nhạt tinh tế |
| **Viền ô nhập / Phân cách** | `#cbdbe8` / `#e5e7eb` | Viền mảnh nhạt, không dùng viền đậm gắt |
| **Nút bấm chính (Primary Button)**| `#1e3a5f` *(Hover: `#2b4c7e`)*| Nút màu lam xám sang trọng, bo góc 8px |

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

- **Logo**: Chữ thuần `Ninh Bình POI` màu `#0f172a` (`font-weight: 700`), bỏ icon bản đồ & gradient lòe loẹt.
- **Mục active**: Dùng khối nền xám nhạt `#f1f5f9` nhẹ nhàng với màu chữ `#0f172a`, không dùng màu xanh dương rực.
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
