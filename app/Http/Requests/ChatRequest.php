<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChatRequest extends FormRequest
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
            'message' => 'required|string|max:255'
        ];
    }

    public function messages()
    {
        return [
            'message.required' => 'Сообщение обязательно для заполнения',
            'message.string' => 'Сообщение должно быть строкой',
            'message.max' => 'Сообщение не должно превышать 255 символов',
        ];
    }
}
