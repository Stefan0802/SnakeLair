<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditAdminUserRequest extends FormRequest
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
            'firstName' => 'sometimes|required|string|max:255',
            'lastName' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $this->route('id'),
            'role' => 'sometimes|required'
        ];
    }
    public function messages()
    {
        return [
            'firstName.required' => 'Имя обязательно для заполнения.',
            'lastName.required' => 'Фамилия обязательна для заполнения.',
            'email.required' => 'Email обязателен для заполнения.',
            'role.required' => 'Роль обязательна для заполнения.',
        ];
    }
}
