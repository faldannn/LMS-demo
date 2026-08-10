<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CbtSyncService;

class SyncAllToCbt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cbt:sync-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync all subjects, teachers, and students from LMS to CBT';

    /**
     * Execute the console command.
     */
    public function handle(CbtSyncService $cbtSyncService)
    {
        $this->info('Starting sync to CBT...');

        $result = $cbtSyncService->syncAll();

        if ($result['success']) {
            $this->info($result['message']);
            return Command::SUCCESS;
        } else {
            $this->error($result['message']);
            return Command::FAILURE;
        }
    }
}

