<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditUserRequest extends FormRequest
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
        ];
    }
    public function messages()
    {
        return [
            'firstName.required' => 'Имя обязательно для заполнения',
            'firstName.string' => 'Имя должно быть строчкой',
            'firstName.max' => 'Имя слишком большое',
            'lastName.required' => 'Фамилия обязательна для заполнения',
            'lastName.string' => 'Фамилия должна быть строкой',
            'lastName.max' => 'Фамилия слишком большая',
            'email.required' => 'Почта обязательна для заполнения',
            'email.string' => 'Почта должна быть строкой',
            'email.email' => 'Почта неверна',
            'email.max' => 'Почта слишком большая',
            'email.unique' => 'Эта почта уже занята'
        ];
    }
}
