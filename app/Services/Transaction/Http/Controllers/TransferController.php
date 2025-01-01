<?php

namespace App\Services\Transaction\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\Transaction\Http\Controllers\Actions\DuplicationCheckAction;
use App\Services\Transaction\Http\Controllers\Actions\TransferAction;
use App\Services\Transaction\Http\Requests\TransferRequest;

class TransferController extends Controller
{
    public function transfer(TransferRequest $request)
    {
        (new DuplicationCheckAction($request))->check();

        waitOnRace(
            key: $request->user()->id . 'transfer',
            ttlInSeconds: 20,
            callback: fn() => (new TransferAction($request))->transfer()
        );

        return response()->json();
    }
}
