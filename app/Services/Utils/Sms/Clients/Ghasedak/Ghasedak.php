<?php

namespace App\Services\Utils\Sms\Clients\Ghasedak;

use App\Services\Utils\Sms\Clients\Contracts\SmsClient;
use App\Services\Utils\Sms\Clients\Ghasedak\Api\Client;

class Ghasedak implements SmsClient
{

    public function send(string $mobile, string $message): bool
    {
        return (new Client($mobile, $message))
            ->setApiKey($this->getApiKey())
            ->call();
    }

    public function getApiKey(): string
    {
        return config('services.sms.clients.ghasedak.api_key');
    }
}
