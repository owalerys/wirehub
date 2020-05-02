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
        $account = Account::where('external_id', $accountId)->with('item')->first();

        return response()->json($account);
    }

    public function getAccountTransactions(string $accountId)
    {
        $account = Account::where('external_id', $accountId)->first();

        $paginated = $account->transactions()->orderBy('date', 'desc')->paginate();

        return response()->json($paginated);
    }
}
