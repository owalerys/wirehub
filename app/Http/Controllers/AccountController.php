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
            $accounts = Account::with('item')->get();
        } else {
            $user = Auth::user()->load('team.accounts.item');

            $accounts = $user->team->accounts;
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
