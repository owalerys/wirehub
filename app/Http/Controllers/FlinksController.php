<?php

namespace App\Http\Controllers;

use App\Flinks\Item;
use App\Http\Resources\Item as ResourcesItem;
use App\Jobs\UpdateItem;
use App\Jobs\UpdateTransactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class FlinksController extends Controller
{
    public function saveLogin(Request $request)
    {
        Gate::authorize('create-item');

        $this->validate($request, [
            'login_id' => 'required|uuid',
            'institution' => 'required|string'
        ]);

        /** @var Item|null $item */
        $item = Item::withTrashed()->where('login_id', $request->input('login_id'))->restore();

        if ($item !== null) {
            $item->accounts()->restore();
            $item->accounts()->transactions()->restore();
        }

        $item = Item::create([
            'login_id' => $request->input('login_id'),
            'institution' => $request->input('institution')
        ]);

        UpdateItem::withChain([
            new UpdateTransactions($item, true)
        ])->dispatch($item);

        return response()->json(new ResourcesItem($item));
    }
}
