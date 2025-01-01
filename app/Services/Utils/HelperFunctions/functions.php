<?php


use App\Services\Utils\Sms\Sms;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

function validateCardNum(string $cardNumber): bool
{

    $cardNumber = str_replace([' ', '-'], '', $cardNumber);

    if (!ctype_digit($cardNumber)) {
        return false;
    }

    $sum = 0;
    $isSecond = false;

    for ($i = strlen($cardNumber) - 1; $i >= 0; $i--) {
        $digit = (int)$cardNumber[$i];

        if ($isSecond) {
            $digit *= 2;
            if ($digit > 9) {
                $digit -= 9;
            }
        }

        $sum += $digit;
        $isSecond = !$isSecond;
    }

    return ($sum % 10 === 0);
}

function waitOnRace(string $key, int $ttlInSeconds, Closure $callback)
{
    $lock = Cache::lock($key);

    try {
        return $lock->block($ttlInSeconds, $callback);
    } catch (LockTimeoutException $e) {
        optional($lock)->forceRelease();
    }
}

function trx(Closure $callback, $attempts = 1)
{
    return DB::transaction($callback, $attempts);
}

function sms(): Sms
{
    return app('sms');
}
