<?php

namespace App\Console\Commands;

use App\Services\RiansaKartuService;
use Illuminate\Console\Command;

class RiansaKartuSyncMembers extends Command
{
    protected $signature = 'kartu:sync-members';
    protected $description = 'Sinkronkan data daftar siswa dari LMS ke sistem Riansa Kartu RFID';

    public function handle(RiansaKartuService $kartuService): int
    {
        $this->info('Memulai sinkronisasi data siswa ke Riansa Kartu...');

        $result = $kartuService->syncMembers();

        if ($result['success']) {
            $this->info($result['message']);
            return Command::SUCCESS;
        }

        $this->error($result['message']);
        return Command::FAILURE;
    }
}
