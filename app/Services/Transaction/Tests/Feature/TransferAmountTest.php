<?php

namespace App\Services\Transaction\Tests\Feature;

use App\Services\Transaction\Events\TransactionSubmitted;
use App\Services\Transaction\Models\Card;
use App\Services\Transaction\Models\Enums\TransactionType;
use App\Services\Transaction\Models\Enums\TransferType;
use App\Services\Transaction\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

class TransferAmountTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Http::fake();
    }


    public function testCanTransferMoney()
    {
        $user = $this->authenticatedUser();

        $srcCard = Card::factory()->forUser($user)->create();
        $destCard = Card::factory()->create();

        $response = $this->postJson('api/v1/transaction/transfer', [
            'src_card' => $srcCard->number,
            'dest_card' => $destCard->number,
            'amount' => $amount = 50000,
            'idempotency_key' => Str::random()
        ]);

        $response->assertOk();
        $this->assertStoresTrxCorrectly($amount, $srcCard, $destCard);
    }

    public function testGetsErrIfCardNumberBelongsToSomeoneElse()
    {
        $this->authenticatedUser();

        $anotherUser = $this->user();

        $srcCard = Card::factory()->forUser($anotherUser)->create();
        $destCard = Card::factory()->create();

        $response = $this->postJson('api/v1/transaction/transfer', [
            'src_card' => $srcCard->number,
            'dest_card' => $destCard->number,
            'amount' => 50000,
            'idempotency_key' => Str::random()
        ]);

        $response->assertForbidden();
        $response->assertJson([
            'message' => 'Src card is invalid!'
        ]);
    }

    public function testSrcCardShouldHaveEnoughBalance()
    {
        $user = $this->authenticatedUser();

        $balance = 50000;

        $srcCard = Card::factory()->forUser($user)->create(['balance' => $balance]);
        $destCard = Card::factory()->create();

        $response = $this->postJson('api/v1/transaction/transfer', [
            'src_card' => $srcCard->number,
            'dest_card' => $destCard->number,
            'amount' => $balance + 1,
            'idempotency_key' => Str::random()
        ]);

        $response->assertForbidden();
        $response->assertJson([
            'message' => 'Balance is not enough!'
        ]);
    }

    public function testBalanceTransfers()
    {
        $user = $this->authenticatedUser();

        $srcCard = Card::factory()->forUser($user)->create([
            'balance' => 50000 + 5000 // amount + wage
        ]);

        $destCard = Card::factory()->create(['balance' => 0]);

        $response = $this->postJson('api/v1/transaction/transfer', [
            'src_card' => $srcCard->number,
            'dest_card' => $destCard->number,
            'amount' => 50000,
            'idempotency_key' => Str::random()
        ]);

        $response->assertOk();

        $this->assertEquals($srcCard->refresh()->balance, 0);
        $this->assertEquals($destCard->refresh()->balance, 50000);
    }

    public function testCantRepeatSameRequestAtOnce()
    {
        $user = $this->authenticatedUser();

        $srcCard = Card::factory()->forUser($user)->create();
        $destCard = Card::factory()->create();

        $response = $this->postJson('api/v1/transaction/transfer', [
            'src_card' => $srcCard->number,
            'dest_card' => $destCard->number,
            'amount' => 10000,
            'idempotency_key' => $idempotencyKey = Str::random()
        ]);
        $response->assertOk();

        $response = $this->postJson('api/v1/transaction/transfer', [
            'src_card' => $srcCard->number,
            'dest_card' => $destCard->number,
            'amount' => 10000,
            'idempotency_key' => $idempotencyKey
        ]);

        $response->assertForbidden();
        $response->assertJson([
            'message' => 'Duplicate Trx, try 20 seconds later!'
        ]);
    }

    public function testRaisesEvents()
    {
        Event::fake();

        $user = $this->authenticatedUser();

        $srcCard = Card::factory()->forUser($user)->create();
        $destCard = Card::factory()->create();

        $this->postJson('api/v1/transaction/transfer', [
            'src_card' => $srcCard->number,
            'dest_card' => $destCard->number,
            'amount' => 10000,
            'idempotency_key' => Str::random()
        ])->assertOk();

        $trx = Transaction::get();

        $debitTrx = $trx->first();
        $creditTrx = $trx->skip(1)->first();
        $wageTrx = $trx->last();


        Event::assertDispatched(
            TransactionSubmitted::class, fn($event) => $event->transaction->is($debitTrx)
        );

        Event::assertDispatched(
            TransactionSubmitted::class, fn($event) => $event->transaction->is($creditTrx)
        );

        Event::assertDispatched(
            TransactionSubmitted::class, fn($event) => $event->transaction->is($wageTrx)
        );
    }

    public function assertStoresTrxCorrectly($amount, $srcCard, $destCard): void
    {
        $this->assertDatabaseCount('transactions', 3);

        $this->assertDatabaseHas('transactions', [
            'amount' => $amount,
            'card_id' => $srcCard->id,
            'transfer_type' => TransferType::DEBIT,
            'transaction_type' => TransactionType::CARD_TO_CARD,
        ]);

        $this->assertDatabaseHas('transactions', [
            'amount' => $amount,
            'card_id' => $destCard->id,
            'transfer_type' => TransferType::CREDIT,
            'transaction_type' => TransactionType::CARD_TO_CARD,
        ]);

        $this->assertDatabaseHas('transactions', [
            'amount' => 5000,
            'card_id' => $srcCard->id,
            'transfer_type' => TransferType::DEBIT,
            'transaction_type' => TransactionType::WAGE,
        ]);
    }
}
