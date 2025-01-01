<?php

namespace App\Services\Transaction\Tests\Feature;

use App\Services\Transaction\Events\TransactionSubmitted;
use App\Services\Transaction\Models\Enums\TransferType;
use App\Services\Transaction\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SmsTest extends TestCase
{
    use RefreshDatabase;

    public function testSendsSmsMessageOnCredit()
    {
        Http::fake();

        $trx = Transaction::factory()->create([
            'transfer_type' => TransferType::CREDIT
        ]);

        event(new TransactionSubmitted($trx));

        $this->assertUrl();
        $this->assertRequestBody($trx, 'recharge +');
    }

    public function assertUrl(): void
    {
        Http::assertSent(function (Request $request) {
            return $request->url() === str_replace(
                    search: '{API-KEY}',
                    replace: config('services.sms.clients.kavenegar.api_key'),
                    subject: 'https://api.kavenegar.com/v1/{API-KEY}/sms/send.json'
                );
        });
    }

    public function testSendsSmsMessageOnDebit()
    {
        Http::fake();

        $trx = Transaction::factory()->create([
            'transfer_type' => TransferType::DEBIT
        ]);

        event(new TransactionSubmitted($trx));

        $this->assertUrl();
        $this->assertRequestBody($trx, 'withdraw -');
    }

    public function assertRequestBody($trx, $keyword): void
    {
        Http::assertSent(function (Request $request) use ($trx, $keyword) {
            $data = $request->data();
            $user = $trx->card->account->user;

            return $data['receptor'] === $user->mobile &&
                   $data['message'] === "{$user->name} $keyword{$trx->amount}";
        });
    }
}
