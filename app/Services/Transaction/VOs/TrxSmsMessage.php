<?php

namespace App\Services\Transaction\VOs;

use App\Services\Transaction\Models\Enums\TransferType;
use App\Services\Transaction\Models\Transaction;

readonly class TrxSmsMessage
{
    public function __construct(public Transaction $transaction)
    {
    }

    public static function for(Transaction $transaction): self
    {
        return new self($transaction);
    }

    public function getMessage(): string
    {
        return $this->transaction->transfer_type->value === TransferType::DEBIT->value
            ? $this->getDebitMessage()
            : $this->getCreditMessage();
    }

    private function getDebitMessage(): string
    {
        return __('transaction.notification.sms.debit_message', [
            'user' => $this->transaction->card->account->user->name,
            'amount' => $this->transaction->amount
        ]);
    }

    private function getCreditMessage(): string
    {
        return __('transaction.notification.sms.credit_message', [
            'user' => $this->transaction->card->account->user->name,
            'amount' => $this->transaction->amount
        ]);
    }
}
