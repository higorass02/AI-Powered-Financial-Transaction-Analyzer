<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportTransactionsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:51200'], // 50MB
            'auto_categorize' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'O arquivo CSV é obrigatório.',
            'file.mimes' => 'O arquivo deve ser do tipo CSV.',
            'file.max' => 'O arquivo não pode exceder 50MB.',
        ];
    }
}
