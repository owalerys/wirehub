<?php

namespace App\Http\Controllers;

use App\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function getAccounts()
    {
        $this->authorize('viewAny', Account::class);

        if (Auth::user()->hasRole(['admin', 'super-admin'])) {
            $accounts = Account::with('item')->where('type', 'depository')->get();
        } else {
            $user = Auth::user()->load('team.accounts.item');

            $accounts = $user->team->accounts;

            $accounts = $accounts->filter(function ($account, $key) {
                return $account->type === 'depository';
            })->values();
        }

        return response()->json($accounts);
    }

    public function getAccount(string $accountId)
    {
        $account = Account::where('external_id', $accountId)->with(['item', 'teams.users.roles'])->first();

        $this->authorize('view', $account);

        return response()->json($account);
    }

    public function getAccountTransactions(string $accountId)
    {
        $account = Account::where('external_id', $accountId)->first();

        $this->authorize('view', $account);

        $transactions = $account->transactions()->orderBy('date', 'desc')->limit(200)->get();

        return response()->json($transactions);
    }

    public function getAccountTransactionsHistorical(string $accountId)
    {
        $account = Account::where('external_id', $accountId)->first();

        $this->authorize('view', $account);

        $output = [];

        foreach($account->transactions()->orderBy('date', 'desc')->cursor() as $transaction) {
            array_push($output, [
                'ID' => $transaction->id,
                'Date' => $transaction->date,
                'Name' => $transaction->name,
                'Amount' => $transaction->amount,
                'Pending' => $transaction->pending ? 'Pending' : '',
                'Currency' => $transaction->iso_currency_code,
                'Credited To User' => $transaction->confirmed ? 'Credited' : '',
                'Credited Changed At' => $transaction->confirmed_at ?: ''
            ]);
        }

        return response()->json($output);
    }

    public function putTeamLink(string $accountId, Request $request)
    {
        $this->validate($request, [
            'team_id' => 'required|exists:teams,id'
        ]);

        $account = Account::where('external_id', $accountId)->first();

        $this->authorize('update', $account);

        if (!$account) return response('', 404);

        $account->teams()->sync([$request->input('team_id')]);

        return response()->json([]);
    }
}
