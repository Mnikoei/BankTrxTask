<?php

namespace App\Services\Transaction\DTOs;

use App\Services\Transaction\Models\Transaction;

readonly class TransferTrxDto
{
    public function __construct(
        public Transaction $debitTrx,
        public Transaction $creditTrx,
        public Transaction $wageTrx,
    )
    {
    }
}
