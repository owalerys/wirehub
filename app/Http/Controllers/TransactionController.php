<?php

namespace App\Http\Controllers;

use App\Services\Discovery;
use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TransactionController extends Controller
{
    public function confirm(string $transactionId, Request $request, Discovery $service)
    {
        $this->validate($request, [
            'confirmed' => 'required|boolean'
        ]);

        $transaction = $service->identifyTransaction($transactionId);

        if (!$transaction) return abort(404);

        $transaction->load('account.teams');

        Gate::authorize('confirm-transaction', $transaction);

        $transaction->confirm($request->input('confirmed'));

        return response()->json($transaction);
    }
}
