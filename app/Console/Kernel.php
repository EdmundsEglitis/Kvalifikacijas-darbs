<?php

namespace App\Console;
use Illuminate\Console\Commands\SyncApiCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{

    protected $commands = [
        \App\Console\Commands\SyncApiCommand::class,
    ];
    
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {

        \Log::info('Registering scheduler at: ' . now());

        $schedule->command('nba:sync-all')
            ->everyThreeMinutes()	                    // once per day; change to ->dailyAt('02:00') if you want a specific time
            ->runInBackground()           // run command in background
            ->appendOutputTo(storage_path('logs/nba_sync.log')); // log output to file
    }
    

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
    
}
