<?php

namespace App\Http\Requests\Admin\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
        $userId = $this->route('user') ? (is_object($this->route('user')) ? $this->route('user')->id : $this->route('user')) : null;

        return [
            'username' => 'required|string|max:50|unique:users,username,' . $userId,
            'email' => 'required|string|email|max:100|unique:users,email,' . $userId,
            'display_name' => 'required|string|max:100',
            'password' => 'nullable|string|min:6|confirmed',
            'role' => 'required|in:admin,moderator,user',
            'status' => 'required|in:active,inactive,banned',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ];
    }

    /**
     * Custom error messages for validation.
     */
    public function messages(): array
    {
        return [
            'username.required' => 'Vui lòng nhập tên tài khoản (username).',
            'username.unique' => 'Tên tài khoản này đã được sử dụng.',
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Địa chỉ email không hợp lệ.',
            'email.unique' => 'Email này đã được sử dụng.',
            'display_name.required' => 'Vui lòng nhập tên hiển thị.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
            'role.required' => 'Vui lòng chọn vai trò.',
            'status.required' => 'Vui lòng chọn trạng thái tài khoản.',
            'avatar.image' => 'File tải lên phải là hình ảnh.',
            'avatar.max' => 'Kích thước ảnh đại diện không vượt quá 4MB.',
        ];
    }
}
