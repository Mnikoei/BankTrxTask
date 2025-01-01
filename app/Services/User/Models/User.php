<?php

namespace App\Services\User\Models;

use App\Services\Transaction\Models\Account;
use App\Services\Transaction\Models\Card;
use App\Services\User\Database\Factory\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'name',
        'mobile',
        'password',
    ];

    protected $hidden = [
        'password'
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function cards(): HasManyThrough
    {
        return $this->hasManyThrough(Card::class, Account::class);
    }

    protected static function newFactory()
    {
        return UserFactory::new();
    }
}
