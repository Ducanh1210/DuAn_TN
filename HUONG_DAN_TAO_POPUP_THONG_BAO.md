# Hướng Dẫn Chuyển Đổi Thông Báo Mặc Định Trình Duyệt Sang Popup Modal (Ninh Bình POI)

Tài liệu này hướng dẫn chi tiết cách thay thế các thông báo `confirm()` / `alert()` mặc định của trình duyệt (`127.0.0.1:8000 cho biết`) thành **Popup Modal SweetAlert2** chuẩn thiết kế dự án Ninh Bình POI (**Slate Ink `#1e3a5f`** & **Misty Ice-Blue `#f1f5f9`**).

---

## 1. Cơ Chế Hoạt Động

Trong file layout Admin [`resources/views/admin/layouts/app.blade.php`](file:///d:/laragon/www/DuAn_TN-main/resources/views/admin/layouts/app.blade.php), dự án đã tích hợp sẵn:
- **Thư viện SweetAlert2** & Bộ giao diện chuẩn (Font *Be Vietnam Pro*, bo góc `16px`, màu sắc thiết kế).
- **Bộ bắt sự kiện tự động (Global Interceptor)**: Tự động chặn các `<form>` có thuộc tính `data-confirm` hoặc thuộc tính `onsubmit="return confirm(...)` cũ để hiển thị Popup Modal mượt mà.

---

## 2. Các Cách Áp Dụng Thực Tế

### Cách 1: Sử dụng thuộc tính `data-confirm-*` trên `<form>` (Khuyên dùng)

Cách này áp dụng cho các Form gửi lên Server (xóa bản ghi, đổi trạng thái, khóa tài khoản...).

#### Ví dụ Blade Form:
```html
<form action="{{ route('admin.categories.destroy', $item->id) }}" method="POST" class="d-inline"
      data-confirm-title="Xóa danh mục" 
      data-confirm-text="Bạn có chắc chắn muốn xóa danh mục <strong>&quot;{{ $item->name }}&quot;</strong> không? Thao tác này không thể hoàn tác." 
      data-confirm-btn="Xóa danh mục" 
      data-confirm-type="danger">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn-minimal py-1 px-2 text-danger">Xóa</button>
</form>
```

#### Bảng thông số thuộc tính `data-confirm-*`:

| Thuộc tính | Mô tả | Mặc định (Nếu bỏ trống) |
| :--- | :--- | :--- |
| `data-confirm-title` | Tiêu đề hiển thị trên Popup Modal | `"Xác nhận thao tác"` |
| `data-confirm-text` | Nội dung câu hỏi (hỗ trợ thẻ HTML như `<strong>`) | Nội dung từ `data-confirm` hoặc `confirm(...)` |
| `data-confirm-btn` | Nhãn hiển thị trên nút đồng ý | `"Đồng ý"` |
| `data-confirm-type` | Loại hành động: `"danger"` (Nút màu đỏ) hoặc để trống (Nút màu xanh Slate Ink) | Tự nhận diện nếu có từ "xóa" |

---

### Cách 2: Giữ nguyên `onsubmit="return confirm(...)"` cũ (Tự động chuyển đổi)

Nếu bạn chưa kịp thêm các thuộc tính `data-confirm-*`, hệ thống JS của Admin vẫn **tự động đọc câu thông báo** trong `confirm('...')` và biến thành Popup SweetAlert2 mà bạn **không cần chỉnh sửa JS nào thêm**.

#### Ví dụ Form cũ:
```html
<form action="{{ route('admin.users.toggle_status', $user->id) }}" method="POST" class="d-inline" 
      onsubmit="return confirm('Bạn có chắc chắn muốn khóa tài khoản này?');">
    @csrf
    <button type="submit" class="btn-minimal text-danger">Khóa tài khoản</button>
</form>
```
👉 *Hệ thống sẽ tự động bắt câu `"Bạn có chắc chắn muốn khóa tài khoản này?"` và bật Popup Modal.*

---

### Cách 3: Gọi Popup trong JavaScript thủ công (Cho nút bấm / AJAX Call)

Đối với các chức năng dùng JavaScript thuần, nút bấm không nằm trong `<form>` hoặc thực hiện gọi API Ajax, bạn có thể gọi trực tiếp `Swal.fire()` với style chuẩn dự án:

#### 3.1. Popup xác nhận (Confirm Dialog):
```javascript
Swal.fire({
    title: 'Xóa bài viết',
    html: 'Bạn có chắc muốn xóa bài viết <strong>"Hành trình Tràng An"</strong> không?',
    icon: 'warning',
    iconColor: '#eab308',
    showCancelButton: true,
    confirmButtonText: 'Đồng ý xóa',
    cancelButtonText: 'Hủy bỏ',
    reverseButtons: true,
    customClass: {
        popup: 'custom-swal-popup',
        title: 'custom-swal-title',
        htmlContainer: 'custom-swal-text',
        confirmButton: 'custom-swal-confirm-btn custom-swal-confirm-danger',
        cancelButton: 'custom-swal-cancel-btn'
    },
    buttonsStyling: false
}).then((result) => {
    if (result.isConfirmed) {
        // Thực hiện hành động xóa (Ví dụ: gọi API hoặc submit form)
        console.log("Người dùng đã xác nhận đồng ý!");
    }
});
```

#### 3.2. Popup thông báo thành công (Success Toast):
```javascript
Swal.fire({
    icon: 'success',
    iconColor: '#166534',
    title: 'Thành công',
    text: 'Cập nhật thông tin thành công!',
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3500,
    timerProgressBar: true,
    customClass: {
        popup: 'custom-swal-toast'
    }
});
```

#### 3.3. Popup thông báo lỗi (Error Alert):
```javascript
Swal.fire({
    icon: 'error',
    iconColor: '#dc2626',
    title: 'Có lỗi xảy ra',
    text: 'Không thể thực hiện thao tác này!',
    confirmButtonText: 'Đóng',
    customClass: {
        popup: 'custom-swal-popup',
        title: 'custom-swal-title',
        htmlContainer: 'custom-swal-text',
        confirmButton: 'custom-swal-confirm-btn custom-swal-confirm-danger'
    },
    buttonsStyling: false
});
```

---

## 3. Thông Báo Từ Laravel Controller (`session('success')`, `session('error')`)

Trong Controller, bạn chỉ cần trả về thông báo flash session bình thường:

```php
// Thông báo thành công (hiển thị Toast xanh ở góc phải)
return redirect()->route('admin.categories.index')->with('success', 'Xóa danh mục thành công!');

// Thông báo lỗi (hiển thị Popup cảnh báo đỏ)
return back()->with('error', 'Không thể xóa danh mục đang chứa địa điểm!');
```
Layout Admin sẽ **tự động bắt** hai session này và hiển thị Popup Toast / Modal đẹp mắt cho người dùng.

---

## 4. Tóm Tắt Quy Trình Nhanh (Cheatsheet)

1. **Nếu là Form bấm Xóa / Sửa / Thao tác trong Blade**:
   Thêm `data-confirm-title="..."` và `data-confirm-text="..."` vào thẻ `<form>`.
2. **Nếu là nút bấm JS / AJAX**:
   Dùng hàm `Swal.fire(...)` kết hợp bộ `customClass` (`custom-swal-popup`, `custom-swal-confirm-btn`, `custom-swal-cancel-btn`).
3. **Nếu từ Controller**:
   Dùng `->with('success', '...')` hoặc `->with('error', '...')`.
