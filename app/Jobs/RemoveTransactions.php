<?php

namespace App\Jobs;

use App\Services\Plaid;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RemoveTransactions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $transactionIds;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $transactionIds)
    {
        $this->transactionIds = $transactionIds;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Plaid $service)
    {
        $service->removeTransactions($this->transactionIds);
    }
}
