<?php

namespace App\Http\Requests\Admin\Category;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $categoryId = $this->route('category') ? (is_object($this->route('category')) ? $this->route('category')->id : $this->route('category')) : null;

        return [
            'name' => 'required|string|max:80|unique:categories,name,' . $categoryId,
            'status' => 'required|in:active,hidden',
            'display_order' => 'required|integer|min:0|unique:categories,display_order,' . $categoryId,
            'icon_color' => 'nullable|string|max:20',
            'icon' => 'nullable|image|mimes:png,jpg,jpeg,svg,gif|max:2048',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập tên danh mục.',
            'name.max' => 'Tên danh mục không vượt quá 80 ký tự.',
            'name.unique' => 'Tên danh mục này đã tồn tại.',
            'display_order.required' => 'Vui lòng nhập thứ tự hiển thị.',
            'display_order.integer' => 'Thứ tự hiển thị phải là số nguyên.',
            'display_order.min' => 'Thứ tự hiển thị không được nhỏ hơn 0.',
            'display_order.unique' => 'Thứ tự hiển thị này đã tồn tại, vui lòng chọn thứ tự khác.',
            'icon.image' => 'File tải lên phải là hình ảnh.',
            'icon.mimes' => 'Hình ảnh icon phải có định dạng png, jpg, jpeg, svg hoặc gif.',
            'icon.max' => 'Kích thước ảnh icon không vượt quá 2MB.',
        ];
    }
}
