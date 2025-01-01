<?php

namespace App\Services\Transaction\Http\Controllers\Actions;

use App\Services\Transaction\DTOs\TransferTrxDto;
use App\Services\Transaction\Events\TransactionSubmitted;
use App\Services\Transaction\Http\Requests\TransferRequest;
use App\Services\Transaction\Models\Card;
use App\Services\Transaction\Models\Enums\TransferType;
use App\Services\Transaction\Models\Transaction;
use Illuminate\Database\Eloquent\Model;

class TransferAction
{
    public bool $withEvents = true;

    public function __construct(private TransferRequest $request)
    {
    }

    public function transfer(): TransferTrxDto
    {
        $srcCard = $this->getSrcCardModel();

        $this->validateBalance($srcCard);

        $destCard = $this->getDestCard();

        $transferTrxDto = $this->createTrx($srcCard, $destCard);

        $this->raiseEvents($transferTrxDto);

        return $transferTrxDto;
    }

    private function getSrcCardModel(): Card
    {
        $card = Card::query()
            ->whereNumber($this->request->src_card)
            ->belongsToUser($this->request->user())
            ->first();

        abort_if(
            boolean: !$card,
            code: 403,
            message: 'Src card is invalid!'
        );

        return $card;
    }

    private function getDestCard(): Card
    {
        return Card::query()
            ->whereNumber($this->request->dest_card)
            ->first();
    }

    private function validateBalance(Card $card): void
    {
        abort_if(
            boolean: $card->balance < $this->request->amount,
            code: 403,
            message: 'Balance is not enough!'
        );
    }

    private function createTrx(Card $srcCard, Card $destCard): TransferTrxDto
    {
        return trx(function () use ($srcCard, $destCard) {

            $amount = $this->request->amount;

            $debitTrx = Model::withoutEvents(
                fn() => Transaction::createBy(card: $srcCard, type: TransferType::DEBIT, amount: $amount)
            );

            $creditTrx = Model::withoutEvents(
                fn() => Transaction::createBy(card: $destCard, type: TransferType::CREDIT, amount: $amount)
            );

            $this->handleBalance($srcCard, $destCard, $amount);

            $wageTrx = $this->createWage($srcCard);

            return new TransferTrxDto($debitTrx, $creditTrx, $wageTrx);
        });
    }

    private function raiseEvents(TransferTrxDto $trx): void
    {
        if ($this->withEvents) {
            event(new TransactionSubmitted($trx->debitTrx));
            event(new TransactionSubmitted($trx->creditTrx));
            event(new TransactionSubmitted($trx->wageTrx));
        }
    }

    public function handleBalance(Card $srcCard, Card $destCard, int $amount): void
    {
        $srcCard->decBalance($amount);
        $destCard->incBalance($amount);
    }

    private function createWage(Card $card): Transaction
    {
        return (new WageAction($card))->create();
    }
}
