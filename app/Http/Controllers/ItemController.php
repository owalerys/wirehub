<?php

namespace App\Http\Controllers;

use App\Http\Resources\Account as ResourcesAccount;
use App\Http\Resources\Item as ResourcesItem;
use App\Item;
use App\Services\Discovery;
use App\Services\Plaid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ItemController extends Controller
{
    public function get(string $itemId, Discovery $service)
    {
        $item = $service->identifyItem($itemId);

        if (!$item) abort(404);

        Gate::authorize('view-item', $item);

        $item->load('accounts.teams');

        return response()->json(new ResourcesItem($item));
    }

    public function delete(string $itemId, Discovery $service)
    {
        $item = $service->identifyItem($itemId);

        if (!$item) {
            abort(404);
        }

        Gate::authorize('delete-item', $item);

        $item->load('accounts.teams');

        try {
            DB::beginTransaction();

            foreach ($item->accounts as $account) {
                $account->teams()->sync([]);

                $account->delete();
            }

            $item->delete();

            $item->disconnect();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            throw $e;
        }

        return response('', 200);
    }
}
