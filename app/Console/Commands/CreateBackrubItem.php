<?php

namespace App\Console\Commands;

use App\Services\Backrub;
use Illuminate\Console\Command;

class CreateBackrubItem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backrub:create-item {username}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new backrub item';

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
    public function handle(Backrub $service)
    {
        $item = $service->connectItem($this->argument('username'));

        var_dump($item);
    }
}
