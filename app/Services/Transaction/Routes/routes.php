<?php

use App\Services\Transaction\Http\Controllers\TransactionController;
use App\Services\Transaction\Http\Controllers\TransferController;

Route::prefix('transaction')->group(function () {


});


Route::prefix('transaction')->middleware('throttle:10,1')->group(function () {

    Route::get('/', [TransactionController::class, 'index']);

    Route::post('/transfer', [TransferController::class, 'transfer']);
});
