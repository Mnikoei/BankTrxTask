<?php

namespace App\Services\Transaction\Models\Enums;

enum TransferType: string
{
    case CREDIT = 'credit';
    case DEBIT = 'debit';
}
