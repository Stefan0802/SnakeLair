<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePostRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ];
    }
    public function messages()
    {
        return [
            'title.required' => 'Заголовок обязателен.',
            'description.required' => 'Описание обязательно.',
            'photo.image' => 'Файл должен быть изображением.',
            'photo.mimes' => 'Поддерживаются только форматы: jpeg, png, jpg, gif.',
        ];
    }
}
