<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditCommentRequest extends FormRequest
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
            'comment' => 'required|string|max:255'
        ];
    }

    public function messages()
    {
        return [
            'comment.required' => 'Поле обязательно',
            'comment.string' => 'Поле должно быть строчкой',
            'comment.max' => 'Комментарий слишком большой',
        ];
    }
}
