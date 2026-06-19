<?php

namespace App\Http\Requests;

use App\Enums\TransactionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'description' => ['required', 'string', 'min:3', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:100000'],
            'type' => ['required', new Enum(TransactionType::class)],
            'category_id' => ['nullable', 'exists:categories,id'],
            'date' => ['required', 'date', 'before_or_equal:today'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'metadata' => ['nullable', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'description.required' => 'A descrição é obrigatória.',
            'description.min' => 'A descrição deve ter pelo menos 3 caracteres.',
            'amount.required' => 'O valor é obrigatório.',
            'amount.min' => 'O valor deve ser maior que zero.',
            'amount.max' => 'O valor não pode exceder R$ 100.000.',
            'type.required' => 'O tipo da transação é obrigatório.',
            'date.required' => 'A data é obrigatória.',
            'date.before_or_equal' => 'A data não pode ser no futuro.',
        ];
    }
}
