<?php

namespace App\Http\Requests\Admin\User;

use Illuminate\Foundation\Http\FormRequest;

class AdjustUserPointsRequest extends FormRequest
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
            'amount' => 'required|integer',
            'description' => 'required|string|max:255',
        ];
    }

    /**
     * Custom error messages for validation.
     */
    public function messages(): array
    {
        return [
            'amount.required' => 'Vui lòng nhập số điểm cần điều chỉnh.',
            'amount.integer' => 'Số điểm phải là một số nguyên hợp lệ.',
            'description.required' => 'Vui lòng nhập lý do thay đổi điểm.',
            'description.max' => 'Lý do không được vượt quá 255 ký tự.',
        ];
    }
}
