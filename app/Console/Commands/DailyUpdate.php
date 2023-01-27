<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class DailyUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'day:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete user that has quarantined for more than 19 days';

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
        $user = User::where('quarantine_day', '>', 19)->delete();
        $patient = User::wherein('role_id',  [1])->increment('quarantine_day');
        $this->info('Daily Update has been updated successfully');
    }
}
