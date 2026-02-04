<?php

namespace App\Console\Commands;

use App\Http\Controllers\DataCenterController;
use App\Http\Controllers\LeadController;
use Illuminate\Console\Command;

class InsertData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:insert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'data insert';

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
        //\Log::info("Cron is working fine.");
        (new DataCenterController())->importToData();
    }
}
