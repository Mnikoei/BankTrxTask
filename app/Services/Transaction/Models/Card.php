<?php

namespace App\Services\Transaction\Models;

use App\Services\Transaction\Database\Factory\CardFactory;
use App\Services\User\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Card extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'number',
        'balance',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function scopeBelongsToUser(Builder $query, User $user): Builder
    {
        return $query->whereHas(
            'account.user', fn($userQ) => $userQ->where('id', $user->id)
        );
    }

    public function incBalance(int $amount): int
    {
        return $this->increment('balance', $amount);
    }

    public function decBalance(int $amount): int
    {
        return $this->decrement('balance', $amount);
    }
    protected static function newFactory()
    {
        return CardFactory::new();
    }
}
