<?php

namespace App\Services\Utils\Sms;

use Illuminate\Support\ServiceProvider;

class SmsServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('sms', fn($app) => new Sms(new SmsClientManager($app)));
    }
}
