<?php

namespace App\Console\Commands;

use App\Http\Controllers\TargetHistoryController;
use Illuminate\Console\Command;

class TargetHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'target_history:insert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'target history insert';

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
     * @return int
     */
    public function handle()
    {
        //return Command::SUCCESS;
        \Log::info("Target History Cron is working fine.");
        (new TargetHistoryController())->store();
    }
}
