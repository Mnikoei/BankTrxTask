<?php

namespace App\Services\Transaction\Http\Controllers\Actions;

use App\Services\Transaction\Models\Transaction;
use App\Services\User\Models\User;
use Illuminate\Support\Collection;

class FetchTopUsersTrx
{
    public function get(int $usersCount = 3): Collection
    {
        $users = $this->getTopUsers($usersCount);

        return $users->map(fn (User $user) => $this->userData($user));
    }

    private function getTopUsers(int $usersCount): Collection
    {
        return User::find(
            Transaction::getTopActiveUserIds($usersCount)
        );
    }

    private function userData(User $user): array
    {
        return [
            'user_id' => $user->id,
            'latest_trx' => Transaction::query()
                ->whereIn('card_id', $user->cards->map->id)
                ->latest('id')
                ->take(10)
                ->get()
        ];
    }
}
