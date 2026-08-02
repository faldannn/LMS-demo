<?php

namespace App\Console\Commands;

use App\Services\RiansaKartuService;
use Illuminate\Console\Command;

class RiansaKartuPullAttendance extends Command
{
    protected $signature = 'kartu:pull-attendance';
    protected $description = 'Tarik catatan absensi kartu dari server Riansa Kartu ke LMS (Polling Sync)';

    public function handle(RiansaKartuService $kartuService): int
    {
        $this->info('Memulai narik data absensi dari Riansa Kartu...');

        $result = $kartuService->pullAttendance();

        if ($result['success']) {
            $this->info($result['message']);
            return Command::SUCCESS;
        }

        $this->error($result['message']);
        return Command::FAILURE;
    }
}
