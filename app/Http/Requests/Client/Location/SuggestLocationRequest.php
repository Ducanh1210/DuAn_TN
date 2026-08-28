<?php

namespace App\Http\Requests\Client\Location;

use Illuminate\Foundation\Http\FormRequest;

class SuggestLocationRequest extends FormRequest
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
            'category_suggest' => 'required|string|max:80',
            'address' => 'nullable|string|max:500',
            'description' => 'nullable|string|max:2000',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'images' => 'nullable|array|max:10',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:10240',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập tên địa điểm.',
            'name.max' => 'Tên địa điểm không được vượt quá 200 ký tự.',
            'category_suggest.required' => 'Vui lòng chọn danh mục cho địa điểm.',
            'category_suggest.max' => 'Danh mục không hợp lệ.',
            'address.max' => 'Địa chỉ không được vượt quá 500 ký tự.',
            'description.max' => 'Mô tả không được vượt quá 2000 ký tự.',
            'lat.required' => 'Vui lòng chọn vị trí địa điểm trên bản đồ.',
            'lat.numeric' => 'Tọa độ vĩ độ phải là số hợp lệ.',
            'lng.required' => 'Vui lòng chọn vị trí địa điểm trên bản đồ.',
            'lng.numeric' => 'Tọa độ kinh độ phải là số hợp lệ.',
            'images.max' => 'Chỉ được đính kèm tối đa 10 ảnh.',
            'images.*.image' => 'File đính kèm phải là hình ảnh.',
            'images.*.mimes' => 'Ảnh chỉ hỗ trợ định dạng JPG, PNG hoặc WEBP.',
            'images.*.max' => 'Mỗi ảnh không được lớn hơn 10MB. Hãy nén ảnh hoặc chọn ảnh nhỏ hơn.',
        ];
    }
}
