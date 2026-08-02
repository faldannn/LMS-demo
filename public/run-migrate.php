<?php

/**
 * Hostinger Web Migration Runner (Fail-safe Deployment Tool)
 * Hanya dapat dijalankan jika menyertakan token keamanan di URL.
 */

$secretToken = 'assyafiiyah2026';

if (empty($_GET['token']) || $_GET['token'] !== $secretToken) {
    http_response_code(403);
    echo "<h1>403 Forbidden</h1><p>Akses ditolak. Token keamanan tidak sesuai.</p>";
    exit;
}

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "<h2>🚀 Hostinger Web Migration Runner</h2>";
echo "<pre style='background:#1e1e1e; color:#00ff00; padding:15px; border-radius:8px;'>";

try {
    // 1. Jalankan artisan migrate --force
    \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    echo \Illuminate\Support\Facades\Artisan::output();

    // 2. Pastikan kolom password_changed_at ada pada tabel users
    if (!\Illuminate\Support\Facades\Schema::hasColumn('users', 'password_changed_at')) {
        \Illuminate\Support\Facades\Schema::table('users', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->timestamp('password_changed_at')->nullable()->after('password');
        });
        echo "\n[OK] Kolom password_changed_at berhasil ditambahkan ke tabel users.\n";
    }

    echo "\n✅ Migration & Schema Auto-Fix Berhasil Dijalankan 100%!";
} catch (\Throwable $e) {
    echo "❌ Error: " . $e->getMessage();
}

echo "</pre>";
