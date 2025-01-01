<?php

namespace App\Services\Transaction\Http\Controllers\Actions;

use App\Services\Transaction\Models\Card;
use App\Services\Transaction\Models\Transaction;

class WageAction
{
    public function __construct(private readonly Card $card)
    {
    }

    public function create(): Transaction
    {
        $amount = $this->getAmount();

        $trx = Transaction::createWage($this->card, $amount);

        return tap($trx, fn() => $this->card->decBalance($amount));
    }

    public function getAmount(): int
    {
        return $this->getTariff();
    }

    /**
     * @notice
     * This can be calculated by an advanced
     * logic for wage in real project
     */
    public function getTariff(): int
    {
        return 5000;
    }
}
