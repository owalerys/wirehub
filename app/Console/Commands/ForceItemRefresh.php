<?php

namespace App\Console\Commands;

use App\Plaid\Item;
use App\Jobs\EagerUpdateItem;
use App\Services\Discovery;
use Illuminate\Console\Command;

class ForceItemRefresh extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'items:force {itemId?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh items with merchants connected to accounts';

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
        if ($itemId = $this->argument('itemId')) {
            $items = [$service->identifyItem($itemId)];
        } else {
            $items = $service->getAllItemsForRefresh();
        }

        foreach ($items as $item) {
            $this->info('Updating ' . $item->getResourceIdentifier());
            EagerUpdateItem::dispatch($item);
        }
    }
}
