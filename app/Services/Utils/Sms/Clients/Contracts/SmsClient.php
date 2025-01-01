<?php

namespace App\Services\Utils\Sms\Clients\Contracts;

interface SmsClient
{
    public function send(string $mobile, string $message): bool;
}
