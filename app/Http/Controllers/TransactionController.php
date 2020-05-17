<?php

namespace App\Http\Controllers;

use App\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function confirm(string $transactionId, Request $request)
    {
        $this->validate($request, [
            'confirmed' => 'required|boolean'
        ]);

        $transaction = Transaction::where('external_id', $transactionId)->with('account.teams')->first();

        if (!$transaction) return response('', 404);

        $this->authorize('confirm', $transaction);

        $transaction->confirmed = (bool) $request->input('confirmed');
        $transaction->confirmed_at = now();

        $transaction->save();

        return response()->json($transaction);
    }
}
