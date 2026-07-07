<?php

namespace App\Http\Controllers;

use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class SystemMaintenanceController extends Controller
{
  public function __construct(private readonly ActivityLogService $activityLogService)
  {
  }

  public function index()
  {
    $backupDir = config('security.backup.path', storage_path('app/backups'));
    File::ensureDirectoryExists($backupDir);

    $backups = collect(File::files($backupDir))
      ->sortByDesc(fn ($f) => $f->getMTime())
      ->take(20)
      ->map(fn ($f) => [
        'name' => $f->getFilename(),
        'size' => $this->formatBytes($f->getSize()),
        'date' => date('d/m/Y H:i', $f->getMTime()),
      ]);

    return view('security.maintenance', compact('backups'));
  }

  public function backup(Request $request)
  {
    $exitCode = Artisan::call('db:backup');

    $this->activityLogService->log(
      action: 'database_backup',
      user: $request->user(),
      description: 'Backup database manual dijalankan',
      properties: ['exit_code' => $exitCode],
      request: $request,
    );

    if ($exitCode !== 0) {
      return back()->with('error', 'Backup gagal: ' . Artisan::output());
    }

    return back()->with('success', 'Backup database berhasil dibuat.');
  }

  public function downloadBackup(string $filename)
  {
    $path = config('security.backup.path') . '/' . basename($filename);

    abort_unless(File::exists($path), 404);

    return response()->download($path);
  }

  private function formatBytes(int $bytes): string
  {
    if ($bytes < 1024) {
      return "{$bytes} B";
    }
    if ($bytes < 1048576) {
      return round($bytes / 1024, 1) . ' KB';
    }

    return round($bytes / 1048576, 1) . ' MB';
  }
}
