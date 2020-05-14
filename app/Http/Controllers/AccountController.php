<?php

namespace App\Http\Controllers;

use App\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function getAccounts()
    {
        $accounts = Account::with('item')->get();

        return response()->json($accounts);
    }

    public function getAccount(string $accountId)
    {
        $account = Account::where('external_id', $accountId)->with(['item', 'teams.users.roles'])->first();

        return response()->json($account);
    }

    public function getAccountTransactions(string $accountId)
    {
        $account = Account::where('external_id', $accountId)->first();

        $transactions = $account->transactions()->orderBy('date', 'desc')->limit(200)->get();

        return response()->json($transactions);
    }

    public function putTeamLink(string $accountId, Request $request)
    {
        $this->validate($request, [
            'team_id' => 'required|exists:teams,id'
        ]);

        $account = Account::where('external_id', $accountId)->first();

        if (!$account) return response('', 404);

        $account->teams()->sync([$request->input('team_id')]);

        return response()->json([]);
    }
}
