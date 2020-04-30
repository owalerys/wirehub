<?php

namespace App\Jobs;

use App\Item;
use App\Services\Plaid;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateItem implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $itemId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $itemId)
    {
        $this->itemId = $itemId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Plaid $service)
    {
        try {
            $item = Item::where('external_id', $this->itemId)->first();

            if (!$item) {
                $this->delete();
                return;
            }

            try {
                $updatedItem = $service->getItem($item->access_token);

                $service->getAccounts($updatedItem);
            } catch (\Exception $e) {
                $this->fail($e);
            }

            $this->delete();
            return;
        } catch (\Exception $e) {
            $this->fail($e);
            return;
        }
    }
}
