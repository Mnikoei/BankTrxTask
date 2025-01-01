<?php

namespace App\Services\Utils\Sms;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class Sms implements ShouldQueue
{
    use Queueable;
    public string $mobile;
    public string $message;
    public ?string $driver = null;
    public bool $viaQueue = false;

    public function __construct(public readonly SmsClientManager $clientManager)
    {
    }

    public function message(string $msg): static
    {
        $this->message = $msg;

        return $this;
    }

    public function to(string $mobile): static
    {
        $this->mobile = $mobile;

        return $this;
    }

    public function driver(string $driver): static
    {
        $this->driver = $driver;

        return $this;
    }

    public function viaQueue(): static
    {
        $this->viaQueue = true;

        return $this;
    }

    public function send(): void
    {
        $this->viaQueue
            ? dispatch($this)
            : $this->handle();
    }

    public function handle(): bool
    {
       return $this->clientManager->driver($this->driver)->send(
           $this->mobile,
           $this->message
       );
    }
}
