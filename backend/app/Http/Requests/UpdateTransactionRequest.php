<?php

namespace App\Http\Requests;

use App\Enums\TransactionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $transaction = $this->route('transaction');
        return $transaction && $transaction->user_id === $this->user()->id;
    }

    public function rules(): array
    {
        return [
            'description' => ['sometimes', 'string', 'min:3', 'max:255'],
            'amount' => ['sometimes', 'numeric', 'min:0.01', 'max:100000'],
            'type' => ['sometimes', new Enum(TransactionType::class)],
            'category_id' => ['nullable', 'exists:categories,id'],
            'date' => ['sometimes', 'date', 'before_or_equal:today'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
