<?php

namespace App\Services\Transaction\Models\Enums;

enum TransactionType: string
{
    case CARD_TO_CARD = 'card_to_card';
    case WAGE = 'wage';
}
