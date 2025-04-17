<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdAvatarRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif',
        ];
    }

    public function messages()
    {
        return [
            'avatar.required' => 'Поле обязательно для заполнения',
            'avatar.image' => 'Должно быть картинкой',
            'avatar.mimes' => 'Доступны только такие разрешения: jpeg, png, jpg, gif'
        ];
    }
}
