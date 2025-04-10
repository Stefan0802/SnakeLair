<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required ', 'email'],
            'password' => ['required ', 'string', 'min:6']
        ];
    }

    public function messages()
    {
        return [
          'email.request' => 'Поле обязательно для заполнения',
          'password.request' => 'Введите пароль содержащий минимум 6 символов'
        ];
    }
}
