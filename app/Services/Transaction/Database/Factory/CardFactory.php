<?php

namespace App\Services\Transaction\Database\Factory;

use App\Services\Transaction\Models\Account;
use App\Services\Transaction\Models\Card;
use App\Services\User\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CardFactory extends Factory
{
    protected $model = Card::class;

    private static ?User $user = null;

    public function definition(): array
    {
        return [
            'number' => fake()->creditCardNumber,
            'balance' => random_int(10000, 500000000),
            'account_id' => Account::factory()->when(static::$user, fn($f) => $f->for(static::$user))->create()->id
        ];
    }

    public function forUser(User $user): static
    {
        static::$user = $user;

        return $this;
    }
}
