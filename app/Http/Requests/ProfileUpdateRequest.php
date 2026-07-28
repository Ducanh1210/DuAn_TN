<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
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
            'display_name' => 'required|string|max:120',
            'email' => ['required', 'string', 'email', 'max:120', Rule::unique('users')->ignore($this->user()->id)],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'display_name.required' => 'Vui lòng nhập tên hiển thị.',
            'display_name.max' => 'Tên hiển thị không vượt quá 120 ký tự.',
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email này đã được tài khoản khác sử dụng.',
            'avatar.image' => 'Ảnh đại diện phải là tệp hình ảnh.',
            'avatar.max' => 'Kích thước ảnh đại diện tối đa là 2MB.',
        ];
    }
}
