<?php

namespace App\Services\Transaction\Models;

use App\Services\Transaction\Database\Factory\AccountFactory;
use App\Services\User\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'number'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cards(): HasMany
    {
        return $this->hasMany(Card::class);
    }

    protected static function newFactory()
    {
        return AccountFactory::new();
    }
}
