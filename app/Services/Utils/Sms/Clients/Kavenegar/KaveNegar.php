<?php

namespace App\Services\Utils\Sms\Clients\Kavenegar;

use App\Services\Utils\Sms\Clients\Contracts\SmsClient;
use App\Services\Utils\Sms\Clients\Kavenegar\Api\Client;

class KaveNegar implements SmsClient
{
    public function send(string $mobile, string $message): bool
    {
        return (new Client([$mobile], $message))
            ->setApiKey($this->getApiKey())
            ->call();
    }

    public function getApiKey(): string
    {
        return config('services.sms.clients.kavenegar.api_key');
    }
}
