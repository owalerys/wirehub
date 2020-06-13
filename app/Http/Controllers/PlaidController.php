<?php

namespace App\Http\Controllers;

use App\Http\Resources\Item as ResourcesItem;
use App\Item;
use App\Services\Plaid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PlaidController extends Controller
{
    public function exchangeToken(Request $request, Plaid $service)
    {
        Gate::authorize('create-item');

        $this->validate($request, [
            'public_token' => 'required|string'
        ]);

        $item = $service->exchangePublicToken($request->input('public_token'));

        return response()->json(new ResourcesItem($item));
    }
}
