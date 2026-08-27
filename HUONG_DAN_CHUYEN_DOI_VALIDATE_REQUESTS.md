# Hướng Dẫn Chuyển Đổi Validate Form Sang Laravel Form Requests (Ninh Bình POI)

Tài liệu này hướng dẫn cách khắc phục lỗi **thông báo bong bóng mặc định của trình duyệt** (`! Vui lòng điền vào trường này`) do thuộc tính `required` gây ra, đồng thời chuyển toàn bộ logic kiểm tra dữ liệu sang các lớp **Form Request** chuẩn theo đúng phân hệ thư mục trong dự án.

---

## 1. Nguyên Lý Chuyển Đổi

Khi thẻ `<input>` có thuộc tính `required`, trình duyệt sẽ chặn form trước khi gửi dữ liệu lên Server và hiện tooltip mặc định xấu. 

**Giải pháp chuẩn dự án:**
1. **Phía Giao diện (Blade):** Thêm `novalidate` vào `<form>`, xóa thuộc tính `required` ở `<input>`, hiển thị câu thông báo lỗi bằng `@error`.
2. **Phía Backend (Laravel):** Đưa toàn bộ luật kiểm tra (`rules`) và câu thông báo tiếng Việt (`messages`) vào lớp Form Request riêng trong thư mục `app/Http/Requests/Admin/<Phân_Hệ>/`.

---

## 2. Quy Trình 3 Bước Chuyển Đổi Nhanh

### 🔹 Bước 1: Chỉnh sửa Form trong View (Blade)

1. Thêm `novalidate` vào thẻ `<form>`:
   ```html
   <form action="{{ route('admin.module.store') }}" method="POST" novalidate>
       @csrf
   ```
2. Xóa bỏ thuộc tính `required` ở các ô `<input>`, `<select>`, `<textarea>`.
3. Thêm class `@error('ten_truong') is-invalid @enderror` và dòng hiển thị lỗi bên dưới:
   ```html
   <div class="mb-3">
       <label class="form-label">Tên trường</label>
       <input type="text" name="fieldname" 
              class="form-control @error('fieldname') is-invalid @enderror" 
              value="{{ old('fieldname') }}" 
              placeholder="Nhập dữ liệu...">
       @error('fieldname')
           <div class="invalid-feedback d-block mt-1" style="font-size: 0.725rem;">{{ $message }}</div>
       @enderror
   </div>
   ```

---

### 🔹 Bước 2: Tạo file Form Request theo thư mục Phân hệ

Tạo file PHP trong thư mục tương ứng với phân hệ (`app/Http/Requests/Admin/<PhanHe>/<TenRequest>.php`).

#### Mẫu chuẩn file Form Request:
```php
<?php

namespace App\Http\Requests\Admin\TenPhanHe;

use Illuminate\Foundation\Http\FormRequest;

class StoreExampleRequest extends FormRequest
{
    /**
     * Cho phép request thực thi.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Quy tắc validation (Rules).
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:200',
            'content' => 'required|string',
        ];
    }

    /**
     * Thông báo lỗi Tiếng Việt (Messages).
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Vui lòng nhập tiêu đề.',
            'title.max' => 'Tiêu đề không được vượt quá 200 ký tự.',
            'content.required' => 'Vui lòng nhập nội dung.',
        ];
    }
}
```

---

### 🔹 Bước 3: Cập nhật Controller

1. Import lớp Form Request ở đầu Controller:
   ```php
   use App\Http\Requests\Admin\TenPhanHe\StoreExampleRequest;
   ```
2. Đổi tham số trong phương thức từ `Request $request` thành `StoreExampleRequest $request` và lấy dữ liệu bằng `$request->validated()`:
   ```php
   public function store(StoreExampleRequest $request)
   {
       // Lấy dữ liệu đã qua validation
       $validated = $request->validated();

       // Xử lý tạo bản ghi mới
       ExampleModel::create($validated);

       return redirect()->back()->with('success', 'Thêm thành công!');
   }
   ```

---

## 3. Cấu Trúc Thư Mục Chuẩn Dự Án

Toàn bộ các file Form Request Admin bắt buộc đặt trong phân hệ tương ứng:

```text
app/Http/Requests/
├── Admin/
│   ├── User/                             <-- Phân hệ Quản lý Người dùng
│   │   ├── StoreUserRequest.php
│   │   ├── UpdateUserRequest.php
│   │   └── AdjustUserPointsRequest.php
│   │
│   ├── Category/                         <-- Phân hệ Quản lý Danh mục
│   │   ├── StoreCategoryRequest.php
│   │   └── UpdateCategoryRequest.php
│   │
│   ├── Location/                         <-- Phân hệ Quản lý Địa điểm
│   │   ├── StoreLocationRequest.php
│   │   └── UpdateLocationRequest.php
│   │
│   └── News/                             <-- Các phân hệ tiếp theo (News, Event, ...)
│       ├── StoreNewsRequest.php
│       └── UpdateNewsRequest.php
└── Auth/
    ├── LoginRequest.php
    └── RegisterRequest.php
```
