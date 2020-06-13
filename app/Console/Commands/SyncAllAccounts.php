<?php

namespace App\Console\Commands;

use App\Jobs\UpdateItem;
use App\Jobs\UpdateTransactions;
use App\Services\Discovery;
use Illuminate\Console\Command;

class SyncAllAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'accounts:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(Discovery $service)
    {
        $items = $service->getAllItems();

        foreach ($items as $item) {
            $this->info('Syncing: ' . $item->getResourceIdentifier());
            UpdateItem::withChain([
                new UpdateTransactions($item, true)
            ])->dispatch($item);
        }
    }
}
