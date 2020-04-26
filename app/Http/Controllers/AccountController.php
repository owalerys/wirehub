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
}
