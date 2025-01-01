<?php

namespace App\Services\Transaction\Http\Controllers\Actions;

use App\Services\Transaction\Http\Requests\TransferRequest;
use Illuminate\Support\Facades\Cache;

readonly class DuplicationCheckAction
{
    public const IDEMPOTENCY_EXP_IN_SEC = 20;

    public function __construct(private TransferRequest $request)
    {
    }

    public function check(): void
    {
        abort_if(
            $this->keyExists(),
            403,
            'Duplicate Trx, try ' . static::IDEMPOTENCY_EXP_IN_SEC . ' seconds later!'
        );

        $this->setKeyInCache();
    }

    private function keyExists(): bool
    {
        return Cache::tags('transfer')->has($this->getCacheKey());
    }

    public function setKeyInCache(): void
    {
        Cache::tags('transfer')->put(
            key: $this->getCacheKey(),
            value: true,
            ttl: static::IDEMPOTENCY_EXP_IN_SEC
        );
    }

    public function getCacheKey(): string
    {
        return 'idempotency_'
            . $this->request->user()->id
            . '_'
            . $this->request->idempotency_key;
    }
}
