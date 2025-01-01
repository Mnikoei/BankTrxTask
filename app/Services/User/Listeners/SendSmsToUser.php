<?php

namespace App\Services\User\Listeners;

use App\Services\Transaction\Events\TransactionSubmitted;
use App\Services\Transaction\Models\Enums\TransferType;
use App\Services\Transaction\Models\Transaction;
use App\Services\Transaction\VOs\TrxSmsMessage;
use App\Services\Utils\Sms\Sms;

class SendSmsToUser
{
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TransactionSubmitted $event): void
    {
        $mobile = $event->transaction->card->account->user->mobile;
        $message = $this->getMessage($event->transaction);

        sms()->to($mobile)->message($message)->send();
    }

    public function getMessage(Transaction $transaction): string
    {
        return TrxSmsMessage::for($transaction)->getMessage();
    }
}
