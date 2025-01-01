<?php

namespace App\Services\Transaction\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\Transaction\Http\Controllers\Actions\DuplicationCheckAction;
use App\Services\Transaction\Http\Controllers\Actions\FetchTopUsersTrx;
use App\Services\Transaction\Http\Controllers\Actions\TransferAction;
use App\Services\Transaction\Http\Requests\TransferRequest;
use App\Services\Transaction\Models\Transaction;
use App\Services\User\Models\User;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        return response()->json((new FetchTopUsersTrx())->get());
    }
}
