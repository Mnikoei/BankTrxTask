<?php

namespace App\Services\Utils\Sms\Clients\Kavenegar\Api;

use Illuminate\Support\Facades\Http;

class Client
{
    private string $apiKey;

    public function __construct(
        public array $receptors,
        public string $message
    ){}

    public function call(): bool
    {
        $response = Http::asForm()->throw()->post($this->getUrl(), [
            'receptor' => implode(',', $this->receptors),
            'message' => $this->message,
        ]);

        return $response->successful();
    }

    public function setApiKey(string $apiKey): self
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    public function getUrl(): string
    {
        return str_replace(
            search: '{API-KEY}',
            replace: $this->apiKey,
            subject: 'https://api.kavenegar.com/v1/{API-KEY}/sms/send.json'
        );
    }
}
