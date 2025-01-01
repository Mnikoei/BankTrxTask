<?php

namespace App\Services\Transaction\Models;

use App\Services\Transaction\Database\Factory\TransactionFactory;
use App\Services\Transaction\Models\Enums\TransactionType;
use App\Services\Transaction\Models\Enums\TransferType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_type',
        'transfer_type',
        'amount',
        'card_id',
        'created_at'
    ];
    protected $casts = [
        'transaction_type' => TransactionType::class,
        'transfer_type' => TransferType::class,
    ];

    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class);
    }

    public static function createBy(
        Card $card,
        TransferType $type,
        int $amount,
        TransactionType $trxType = TransactionType::CARD_TO_CARD
    ): static {

        return static::create([
            'transaction_type' => $trxType,
            'transfer_type' => $type,
            'amount' => $amount,
            'card_id' => $card->id,
        ]);
    }

    public static function createWage(Card $card, int $amount): Transaction
    {
        return static::createBy(
            card: $card,
            type: TransferType::DEBIT,
            amount: $amount,
            trxType: TransactionType::WAGE
        );
    }

    protected function getTopActiveUserIds(int $userCount): array
    {
        return static::query()
            ->where('transactions.created_at', '>', now()->subMinutes(10))
            ->join('cards', 'transactions.card_id', '=', 'cards.id')
            ->join('accounts', 'cards.account_id', '=', 'accounts.id')
            ->join('users', 'accounts.user_id', '=', 'users.id')
            ->selectRaw('users.id as uid, count(transactions.id) as trx_count')
            ->groupBy('users.id')
            ->orderByDesc('trx_count')
            ->take($userCount)
            ->get()
            ->map
            ->uid
            ->toArray();
    }

    protected static function newFactory()
    {
        return TransactionFactory::new();
    }
}
