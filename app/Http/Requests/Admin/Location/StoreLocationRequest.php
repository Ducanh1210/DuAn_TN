<?php

namespace App\Http\Requests\Admin\Location;

use Illuminate\Foundation\Http\FormRequest;

class StoreLocationRequest extends FormRequest
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
        return [
            'name' => 'required|string|max:200',
            'category_id' => 'required|exists:categories,id',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:20480',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập tên địa điểm.',
            'name.max' => 'Tên địa điểm không vượt quá 200 ký tự.',
            'category_id.required' => 'Vui lòng chọn danh mục cho địa điểm.',
            'category_id.exists' => 'Danh mục được chọn không hợp lệ.',
            'lat.required' => 'Vui lòng nhập vĩ độ (Lat).',
            'lat.numeric' => 'Vĩ độ phải là số hợp lệ.',
            'lng.required' => 'Vui lòng nhập kinh độ (Lng).',
            'lng.numeric' => 'Kinh độ phải là số hợp lệ.',
            'thumbnail.image' => 'Ảnh đại diện phải là tệp hình ảnh.',
            'thumbnail.mimes' => 'Ảnh đại diện phải có định dạng jpeg, png, jpg, gif, svg hoặc webp.',
            'thumbnail.max' => 'Kích thước ảnh đại diện không vượt quá 20MB.',
        ];
    }
}
