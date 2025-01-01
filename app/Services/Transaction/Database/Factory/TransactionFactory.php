<?php

namespace App\Services\Transaction\Database\Factory;

use App\Services\Transaction\Models\Account;
use App\Services\Transaction\Models\Card;
use App\Services\Transaction\Models\Enums\TransactionType;
use App\Services\Transaction\Models\Enums\TransferType;
use App\Services\Transaction\Models\Transaction;
use App\Services\User\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    private static ?User $user = null;

    public function definition(): array
    {
        return [
            'transfer_type' => TransferType::DEBIT,
            'transaction_type' => TransactionType::CARD_TO_CARD,
            'amount' => mt_rand(),
            'card_id' => Card::factory()
                ->when(static::$user, fn($f) => $f->forUser(static::$user))
                ->create()
                ->id,
        ];
    }

    public function forUser(User $user): static
    {
        static::$user = $user;

        return $this;
    }
}
