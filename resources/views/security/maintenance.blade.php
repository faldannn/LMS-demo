@extends('layouts.app')
@section('title', 'Backup & Pemeliharaan')
@section('page-title', 'Backup & Pemeliharaan')

@section('content')
    <div class="mb-3">
        <a href="{{ route('security.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-header"><i class="bi bi-database-down me-2"></i>Backup Database</div>
                <div class="card-body">
                    <p class="text-muted small">Buat salinan database secara manual. Backup otomatis dijadwalkan setiap hari pukul 02:00 (jika scheduler aktif).</p>
                    <form method="POST" action="{{ route('security.maintenance.backup') }}">
                        @csrf
                        <button type="submit" class="btn btn-primary" onclick="return confirm('Jalankan backup database sekarang?')">
                            <i class="bi bi-download me-1"></i>Buat Backup Sekarang
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-header"><i class="bi bi-archive me-2"></i>File Backup Terbaru</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr><th>Nama File</th><th>Ukuran</th><th>Tanggal</th><th></th></tr>
                            </thead>
                            <tbody>
                                @forelse($backups as $backup)
                                    <tr>
                                        <td class="small">{{ $backup['name'] }}</td>
                                        <td class="small">{{ $backup['size'] }}</td>
                                        <td class="small">{{ $backup['date'] }}</td>
                                        <td>
                                            <a href="{{ route('security.maintenance.download', $backup['name']) }}" class="btn btn-outline-success btn-sm">
                                                <i class="bi bi-download"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted py-4">Belum ada backup.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header"><i class="bi bi-terminal me-2"></i>Perintah Produksi</div>
        <div class="card-body">
            <pre class="bg-light p-3 rounded small mb-0"><code>php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link
php artisan db:backup</code></pre>
        </div>
    </div>
@endsection
