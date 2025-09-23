<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ApiSyncService;
use Illuminate\Support\Facades\Log;

class SyncApiCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Run: php artisan nba:sync-all
     */
    protected $signature = 'nba:sync-all';

    /**
     * The console command description.
     */
    protected $description = 'Run ApiSyncService->sync() (players, teams, games, player details).';

    /**
     * Execute the console command.
     */
    public function handle(ApiSyncService $apiSync): int
    {
        $this->info('Starting NBA API sync (ApiSyncService->sync())');

        try {
            // ApiSyncService is Dependency Injected by the container
            $apiSync->sync();

            $this->info('NBA API sync finished successfully.');
            return 0;
        } catch (\Throwable $e) {
            $this->error('NBA API sync failed: ' . $e->getMessage());
            Log::error('SyncApiCommand failed', ['exception' => $e]);
            return 1;
        }
    }
}
