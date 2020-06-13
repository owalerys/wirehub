<?php

namespace App\Jobs;

use App\Contracts\Item as ContractsItem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateTransactions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @property ContractsItem $item */
    protected $item;
    /** @property bool $fullHistory */
    protected $fullHistory;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(ContractsItem $item, bool $fullHistory = false)
    {
        $this->item = $item;
        $this->fullHistory = $fullHistory;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $this->item->detailRefresh($this->fullHistory);

            $this->delete();
        } catch (\Exception $e) {
            $this->fail($e);
        }
    }
}
