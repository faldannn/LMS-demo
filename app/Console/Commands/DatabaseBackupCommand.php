<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class DatabaseBackupCommand extends Command
{
  protected $signature = 'db:backup {--keep= : Jumlah hari retensi backup}';

  protected $description = 'Backup database ke storage/app/backups';

  public function handle(): int
  {
    $backupDir = config('security.backup.path', storage_path('app/backups'));
    File::ensureDirectoryExists($backupDir);

    $connection = config('database.default');
    $driver = config("database.connections.{$connection}.driver");
    $timestamp = now()->format('Y-m-d_His');
    $filename = "backup_{$connection}_{$timestamp}";

    try {
      $path = match ($driver) {
        'sqlite' => $this->backupSqlite($backupDir, $filename),
        'mysql', 'mariadb' => $this->backupMysql($backupDir, $filename, $connection),
        default => throw new \RuntimeException("Driver {$driver} belum didukung untuk backup otomatis."),
      };

      $this->info("Backup berhasil: {$path}");
      Log::channel('security')->info('Database backup created', ['path' => $path]);

      $this->pruneOldBackups($backupDir);

      return self::SUCCESS;
    } catch (\Throwable $e) {
      $this->error('Backup gagal: ' . $e->getMessage());
      Log::channel('security')->error('Database backup failed', ['error' => $e->getMessage()]);

      return self::FAILURE;
    }
  }

  private function backupSqlite(string $dir, string $filename): string
  {
    $source = config('database.connections.sqlite.database');
    $dest = "{$dir}/{$filename}.sqlite";

    if ($source === ':memory:') {
      try {
        $pdo = \Illuminate\Support\Facades\DB::connection()->getPdo();
        $quotedDest = str_replace("'", "''", $dest);
        $pdo->exec("VACUUM INTO '{$quotedDest}'");

        if (file_exists($dest)) {
          return $dest;
        }
      } catch (\Throwable) {
        // fallback below
      }

      return $this->backupSqliteMemoryAsSql($dir, $filename);
    }

    if (!file_exists($source)) {
      throw new \RuntimeException("File SQLite tidak ditemukan: {$source}");
    }

    if (!copy($source, $dest)) {
      throw new \RuntimeException('Gagal menyalin file SQLite.');
    }

    return $dest;
  }

  private function backupSqliteMemoryAsSql(string $dir, string $filename): string
  {
    $dest = "{$dir}/{$filename}.sql";
    $lines = [];

    $tables = \Illuminate\Support\Facades\DB::select(
      "SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' ORDER BY name"
    );

    foreach ($tables as $table) {
      $name = $table->name;
      $create = \Illuminate\Support\Facades\DB::selectOne(
        'SELECT sql FROM sqlite_master WHERE type = ? AND name = ?',
        ['table', $name]
      );
      if ($create?->sql) {
        $lines[] = $create->sql . ';';
      }

      foreach (\Illuminate\Support\Facades\DB::table($name)->get() as $row) {
        $data = (array) $row;
        $cols = implode(', ', array_map(fn ($c) => '"' . $c . '"', array_keys($data)));
        $vals = implode(', ', array_map(function ($v) {
          if ($v === null) {
            return 'NULL';
          }
          if (is_numeric($v)) {
            return $v;
          }

          return "'" . str_replace("'", "''", (string) $v) . "'";
        }, array_values($data)));
        $lines[] = "INSERT INTO \"{$name}\" ({$cols}) VALUES ({$vals});";
      }
    }

    file_put_contents($dest, implode("\n", $lines));

    return $dest;
  }

  private function backupMysql(string $dir, string $filename, string $connection): string
  {
    $config = config("database.connections.{$connection}");
    $dest = "{$dir}/{$filename}.sql";

    $command = sprintf(
      'mysqldump --host=%s --port=%s --user=%s %s %s > %s',
      escapeshellarg($config['host'] ?? '127.0.0.1'),
      escapeshellarg((string) ($config['port'] ?? 3306)),
      escapeshellarg($config['username'] ?? 'root'),
      !empty($config['password']) ? '--password=' . escapeshellarg($config['password']) : '',
      escapeshellarg($config['database']),
      escapeshellarg($dest),
    );

    exec($command, $output, $exitCode);

    if ($exitCode !== 0 || !file_exists($dest)) {
      throw new \RuntimeException('mysqldump gagal. Pastikan mysqldump terpasang di server.');
    }

    return $dest;
  }

  private function pruneOldBackups(string $dir): void
  {
    $days = (int) ($this->option('keep') ?? config('security.backup.retention', 14));
    $cutoff = now()->subDays($days)->getTimestamp();

    foreach (File::files($dir) as $file) {
      if ($file->getMTime() < $cutoff) {
        File::delete($file->getPathname());
        $this->line('Dihapus backup lama: ' . $file->getFilename());
      }
    }
  }
}
