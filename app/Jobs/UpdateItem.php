<?php

namespace App\Jobs;

use App\Contracts\Item as ContractsItem;
use Bugsnag\BugsnagLaravel\Facades\Bugsnag;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateItem implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @property ContractsItem $item */
    protected $item;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(ContractsItem $item)
    {
        $this->item = $item;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $this->item->summaryRefresh();

            $this->delete();
        } catch (\Exception $e) {
            Bugsnag::notifyException($e);
            $this->fail($e);
            return;
        }
    }
}
