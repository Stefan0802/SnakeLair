<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|min:6',
        ];
    }

    public function messages()
    {
        return [
            'firstName.required' => 'Имя обязательно.',
            'lastName.required' => 'Фамилия обязательна.',
            'email.required' => 'Email обязателен.',
            'email.unique' => 'Этот email уже используется.',
            'email.email' => 'Некорректный email.',
            'password.required' => 'Пароль обязателен.',
            'password.min' => 'Пароль должен содержать минимум 6 символов.',
        ];
    }
}
