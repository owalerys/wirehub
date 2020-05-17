<?php

namespace App\Http\Controllers;

use App\Item;
use App\Services\Plaid;
use Illuminate\Http\Request;

class PlaidController extends Controller
{
    public function exchangeToken(Request $request, Plaid $service)
    {
        $this->authorize('create', Item::class);

        $this->validate($request, [
            'public_token' => 'required|string'
        ]);

        $item = $service->exchangePublicToken($request->input('public_token'));

        return response()->json($item);
    }
}
