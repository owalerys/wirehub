<?php

namespace App\Console\Commands;

use App\Item;
use App\Jobs\EagerUpdateItem;
use Illuminate\Console\Command;

class ForceItemRefresh extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'items:force';

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
    public function handle()
    {
        $items = Item::has('accounts.teams')->with('accounts.teams')->get();

        foreach ($items as $item) {
            $this->info('Updating ' . $item->external_id);
            EagerUpdateItem::dispatch($item->external_id);
        }
    }
}
