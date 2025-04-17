<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditPasswordRequest extends FormRequest
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
            'password' => 'required|string|max:255|min:6',
            'confPassword' => 'required|string|max:255|same:password'
        ];
    }

    public function messages()
    {
        return [
            'password.required' => 'Пароль должен быть обязательно',
            'password.string' => 'Пароль должен быть строкой',
            'password.max' => 'Пароль слишком большой',
            'password.min' => 'Пароль должен быть больше 6 символов',
            'confPassword.required' => 'Пароль должен быть обязательно',
            'confPassword.string' => 'Пароль должен быть строкой',
            'confPassword.max' => 'Пароль слишком большой',
            'confPassword.same' => 'Пароли не совпадают',
        ];
    }
}
