<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use App\Models\LoginLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PruneAuditLogsCommand extends Command
{
  protected $signature = 'logs:prune';

  protected $description = 'Hapus log audit dan riwayat login yang sudah melewati retensi';

  public function handle(): int
  {
    $auditDays = config('security.audit.retention_days', 365);
    $loginDays = config('security.audit.login_retention_days', 180);

    $auditDeleted = ActivityLog::where('logged_at', '<', now()->subDays($auditDays))->delete();
    $loginDeleted = LoginLog::where('logged_at', '<', now()->subDays($loginDays))->delete();

    $sessions = DB::table('sessions')
      ->where('last_activity', '<', now()->subDays(30)->timestamp)
      ->delete();

    $this->info("Dihapus: {$auditDeleted} audit log, {$loginDeleted} login log, {$sessions} sesi kedaluwarsa.");

    return self::SUCCESS;
  }
}
