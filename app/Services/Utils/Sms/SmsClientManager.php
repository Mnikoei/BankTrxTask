<?php

namespace App\Services\Utils\Sms;

use App\Services\Utils\Sms\Clients\Ghasedak\Ghasedak;
use App\Services\Utils\Sms\Clients\Kavenegar\KaveNegar;
use Illuminate\Support\Manager;

class SmsClientManager extends Manager
{
    public function getDefaultDriver()
    {
        return $this->config->get('sms.default_driver', 'kaveNegar');
    }

    public function createKaveNegarDriver(): KaveNegar
    {
        return new KaveNegar();
    }

    public function createGhasedakDriver(): Ghasedak
    {
        return new Ghasedak();
    }
}
