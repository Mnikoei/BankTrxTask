<?php

namespace App\Services\Transaction\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransferRequest extends FormRequest
{
    public function rules()
    {
        return [
            'src_card' => 'required|card_number',
            'dest_card' => 'required|card_number',
            'amount' => 'required|numeric|min:10000|max:500000000',
            'idempotency_key' => 'required|string|min:10|max:30',
        ];
    }

    protected function prepareForValidation()
    {
        $this->convertToEnCharacters(['src_card', 'dest_card', 'amount']);
    }
}
