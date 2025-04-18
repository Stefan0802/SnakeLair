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
            'email' => 'required|email',
            'password' => 'required|string|min:6'
        ];
    }

    public function messages()
    {
        return [
            'email.request' => 'Неверная почта',
            'email.email' => 'Логин должен быть почтой',
            'password.request' => 'Неверный пароль',
        ];
    }
}
