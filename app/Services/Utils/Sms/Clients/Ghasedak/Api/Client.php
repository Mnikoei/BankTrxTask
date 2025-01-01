<?php

namespace App\Services\Utils\Sms\Clients\Ghasedak\Api;

use Ghasedak\DataTransferObjects\Request\SingleMessageDTO;
use Ghasedak\DataTransferObjects\Response\SingleMessageResponseDTO;
use Ghasedak\GhasedaksmsApi;
use Illuminate\Support\Facades\Http;

class Client
{
    private string $apiKey;

    public function __construct(
        public string $receptors,
        public string $message
    ){}

    public function call(): bool
    {
        try {

            (new GhasedaksmsApi($this->apiKey))
                ->sendSingle(new SingleMessageDTO(
                    sendDate: now()->toDateTimeImmutable(),
                    lineNumber: config('services.sms.clients.ghasedak.line_umber'),
                    receptor: $this->receptors,
                    message: $this->message
                ));

            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function setApiKey(string $apiKey): self
    {
        $this->apiKey = $apiKey;

        return $this;
    }
}

