<?php

namespace App\Http\Controllers;

use App\Jobs\RemoveTransactions;
use App\Jobs\UpdateItem;
use App\Jobs\UpdateTransactions;
use App\Plaid\Item;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function plaid(Request $request)
    {
        $this->validate($request, [
            'webhook_type' => 'required|string|in:TRANSACTIONS,ITEM',
            'webhook_code' => 'required|string'
        ]);

        $type = $request->input('webhook_type');
        $code = $request->input('webhook_code');
        switch ($type) {
            case 'TRANSACTIONS':
                if ($code === 'TRANSACTIONS_REMOVED') return $this->transactionsRemoved($request);
                return $this->transactionsUpdated($request);
            case 'ITEM':
                if ($code === 'WEBHOOK_UPDATE_ACKNOWLEDGED') return $this->itemWebhookUpdated($request);
                elseif ($code === 'PENDING_EXPIRATION') return $this->itemPendingExpiration($request);
                elseif ($code === 'ERROR') return $this->itemError($request);
        }

        return response()->json(['error' => 'Unsupported webhook_type/webhook_code combo.'], 400);
    }

    private function transactionsUpdated(Request $request)
    {
        $this->validate($request, [
            'webhook_code' => 'in:INITIAL_UPDATE,HISTORICAL_UPDATE,DEFAULT_UPDATE',
            'item_id' => 'required|string|exists:items,external_id',
            'new_transactions' => 'required|integer'
        ]);

        $item = Item::where('external_id', $request->input('item_id'))->first();

        $fullHistory = $request->input('webhook_code') === 'HISTORICAL_UPDATE';

        UpdateTransactions::withChain(
            new UpdateItem($item)
        )->dispatch($item, $fullHistory);

        return response()->json(['message' => 'Transaction update acknowledged.']);
    }

    private function transactionsRemoved(Request $request)
    {
        $this->validate($request, [
            'webhook_code' => 'in:TRANSACTIONS_REMOVED',
            'item_id' => 'required|string|exists:items,external_id',
            'removed_transactions' => 'required|array',
            'removed_transactions.*' => 'required|string|exists:transactions,external_id'
        ]);

        $item = Item::where('external_id', $request->input('item_id'));

        RemoveTransactions::withChain([
            new UpdateItem($item)
        ])->dispatch($request->input('removed_transactions'));

        return response()->json(['message' => 'Transaction removal acknowledged.']);
    }

    private function itemWebhookUpdated(Request $request)
    {
        $this->validate($request, [
            'webhook_code' => 'in:WEBHOOK_UPDATE_ACKNOWLEDGED',
            'item_id' => 'required|string|exists:items,external_id',
            'new_webhook_url' => 'required|string|url'
        ]);

        $item = Item::where('external_id', $request->input('item_id'));

        UpdateItem::dispatch($item);

        return response()->json(['message' => 'Item webhook update acknowledged.']);
    }

    private function itemPendingExpiration(Request $request)
    {
        $this->validate($request, [
            'webhook_code' => 'in:PENDING_EXPIRATION',
            'item_id' => 'required|string|exists:items,external_id',
            'consent_expiration_time' => 'required|string|date_format:c'
        ]);

        $item = Item::where('external_id', $request->input('item_id'));

        UpdateItem::dispatch($item);

        return response()->json(['message' => 'Item pending expiration acknowledged.']);
    }

    private function itemError(Request $request)
    {
        $this->validate($request, [
            'webhook_code' => 'in:ERROR',
            'item_id' => 'required|string|exists:items,external_id',
            'error.display_message' => 'required|string',
            'error.error_code' => 'required|string',
            'error.error_message' => 'required|string',
            'error.error_type' => 'required|string',
            'error.status' => 'required|integer'
        ]);

        $item = Item::where('external_id', $request->input('item_id'));

        UpdateItem::dispatch($item);

        return response()->json(['message' => 'Item error acknowledged.']);
    }
}
