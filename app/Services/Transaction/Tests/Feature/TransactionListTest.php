<?php

namespace App\Services\Transaction\Tests\Feature;

use App\Services\Transaction\Database\Factory\CardFactory;
use App\Services\Transaction\Events\TransactionSubmitted;
use App\Services\Transaction\Models\Card;
use App\Services\Transaction\Models\Enums\TransferType;
use App\Services\Transaction\Models\Transaction;
use App\Services\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TransactionListTest extends TestCase
{
    use RefreshDatabase;

    public function testGetsTop3UserTrx()
    {
        $user1 = $this->user();
        $user2 = $this->user();
        $user3 = $this->user();
        $user4 = $this->user();
        $user5 = $this->user();

        // these items should be in list
        $this->createTrxFor($user1, 5, now());
        $this->createTrxFor($user2, 10, now());
        $this->createTrxFor($user3, 7, now());

        // these items should not be in list
        $this->createTrxFor($user4, 0, now());
        $this->createTrxFor($user5, 20, now()->subMinutes(11));

        $response = $this->getJson('api/v1/transaction');

        $response->assertJsonCount(3);
        $response->assertJsonCount(5, '0.latest_trx');
        $response->assertJsonCount(10, '1.latest_trx');
        $response->assertJsonCount(7, '2.latest_trx');

        $this->assertEquals(
            Arr::pluck($response->json(), 'user_id'),
            [$user1->id, $user2->id, $user3->id]
        );
    }

    public function createTrxFor(User $user, $trxCount, $createdAt)
    {
        Transaction::factory()
            ->forUser($user)
            ->count($trxCount)
            ->create([
                'created_at' => $createdAt
            ]);
    }
}
