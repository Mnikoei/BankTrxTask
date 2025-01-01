<?php

namespace App\Services\Transaction\Tests\Feature;

use App\Services\Transaction\Events\TransactionSubmitted;
use App\Services\Transaction\Models\Card;
use App\Services\Transaction\Models\Enums\TransactionType;
use App\Services\Transaction\Models\Enums\TransferType;
use App\Services\Transaction\Models\Transaction;
use App\Services\Utils\Transformers\Str\ToEnglishNumber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class TransferAmountInoutValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Http::fake();
    }

    #[DataProvider('transferInputDataProvider')]
    public function testInputValidation($payload, $expectedStatus)
    {
        $user = $this->authenticatedUser();
        Card::factory()->forUser($user)->create(['number' => ToEnglishNumber::convert($payload['src_card'])]);
        Card::factory()->create(['number' => ToEnglishNumber::convert($payload['dest_card'])]);

        $response = $this->postJson('api/v1/transaction/transfer', $payload);

        $response->assertStatus($expectedStatus);
    }

    public static function transferInputDataProvider()
    {
        $validSrcCard = '5041721097177902';
        $validDestCard = '5892101554389433';
        $validAmount = 55000;
        $validIdempotencyKey = Str::random(20);

        return [
            // Valid data
            [[
                'src_card' => $validSrcCard,
                'dest_card' => $validDestCard,
                'amount' => $validAmount,
                'idempotency_key' => $validIdempotencyKey,
            ], 200],

             //Invalid src_card (not numeric)
            [[
                'src_card' => 'invalid_card',
                'dest_card' => $validDestCard,
                'amount' => $validAmount,
                'idempotency_key' => $validIdempotencyKey,
            ], 422],

            // Invalid dest_card (Luhn algorithm fails)
            [[
                'src_card' => $validSrcCard,
                'dest_card' => '6274129023456780',
                'amount' => $validAmount,
                'idempotency_key' => $validIdempotencyKey,
            ], 422],

            // Invalid amount (below minimum)
            [[
                'src_card' => $validSrcCard,
                'dest_card' => $validDestCard,
                'amount' => 9999,
                'idempotency_key' => $validIdempotencyKey,
            ], 422],

            // Invalid amount (above maximum)
            [[
                'src_card' => $validSrcCard,
                'dest_card' => $validDestCard,
                'amount' => 500000001,
                'idempotency_key' => $validIdempotencyKey,
            ], 422],

            // Missing idempotency_key
            [[
                'src_card' => $validSrcCard,
                'dest_card' => $validDestCard,
                'amount' => $validAmount,
            ], 422],

            // Invalid idempotency_key (too short)
            [[
                'src_card' => $validSrcCard,
                'dest_card' => $validDestCard,
                'amount' => $validAmount,
                'idempotency_key' => 'short',
            ], 422],

            // Invalid idempotency_key (too long)
            [[
                'src_card' => $validSrcCard,
                'dest_card' => $validDestCard,
                'amount' => $validAmount,
                'idempotency_key' => Str::random(31),
            ], 422],

            // Persian/Arabic characters in src_card (requires conversion)
            [[
                'src_card' => '۵۰۴۱۷۲۱۰۹۷۱۷۷۹۰۲',
                'dest_card' => $validDestCard,
                'amount' => $validAmount,
                'idempotency_key' => $validIdempotencyKey,
            ], 200],
        ];
    }
}
