<?php

namespace App\Services\Transaction\Database\Factory;

use App\Services\Transaction\Models\Account;
use App\Services\User\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccountFactory extends Factory
{
    protected $model = Account::class;

    public function definition(): array
    {
        return [
            'number' => random_int(1000, 10000000000),
            'user_id' => User::factory()->create()->id
        ];
    }
}
