<?php

namespace App\Http\Controllers;

use App\Item;
use App\Services\Plaid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{
    public function get(string $itemId)
    {
        $item = Item::where('external_id', $itemId)->with(['accounts' => function ($query) {
            $query->where('type', 'depository');
            $query->with('teams');
        }])->first();

        if (!$item) {
            return response('', 404);
        }

        $this->authorize('view', $item);

        return response()->json($item);
    }

    public function delete(string $itemId, Plaid $service)
    {
        $item = Item::where('external_id', $itemId)->with('accounts.teams')->first();

        if (!$item) {
            return response('', 404);
        }

        $this->authorize('delete', $item);

        try {
            DB::beginTransaction();

            foreach ($item->accounts as $account) {
                foreach ($account->teams as $team) {
                    $team->pivot->deleted_at = now();
                    $team->save();
                }

                $account->delete();
            }

            $item->delete();

            $service->removeItem($item);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            throw $e;
        }

        return response('', 200);
    }
}
