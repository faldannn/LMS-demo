@extends('layouts.app')
@section('title', 'Keamanan Sistem')
@section('page-title', 'Keamanan Sistem')

@section('content')
    <div class="row g-4 mb-4">
        @foreach([
            ['route' => 'security.audit-logs', 'icon' => 'bi-shield-check', 'title' => 'Audit Log', 'desc' => 'Riwayat aktivitas pengguna di sistem', 'classes' => 'bg-brand-50 text-brand-600'],
            ['route' => 'security.login-history', 'icon' => 'bi-door-open', 'title' => 'Riwayat Login', 'desc' => 'Log percobaan login sukses dan gagal', 'classes' => 'bg-brand-50 text-brand-600'],
            ['route' => 'security.sessions', 'icon' => 'bi-pc-display', 'title' => 'Manajemen Sesi', 'desc' => 'Pantau dan cabut sesi aktif pengguna', 'classes' => 'bg-violet-50 text-violet-600'],
            ['route' => 'security.maintenance', 'icon' => 'bi-database-check', 'title' => 'Backup & Pemeliharaan', 'desc' => 'Backup database dan file cadangan', 'classes' => 'bg-amber-50 text-amber-600'],
        ] as $card)
            <div class="col-md-6 col-lg-3">
                <a href="{{ route($card['route']) }}" class="card text-decoration-none h-100 hover:shadow-lg transition-shadow">
                    <div class="card-body p-4 text-center">
                        <div class="mx-auto mb-4 grid h-12 w-12 place-items-center rounded-2xl {{ $card['classes'] }} text-xl shadow-sm">
                            <i class="bi {{ $card['icon'] }}"></i>
                        </div>
                        <h6 class="fw-bold text-slate-900 mb-1">{{ $card['title'] }}</h6>
                        <p class="text-slate-500 small mb-0">{{ $card['desc'] }}</p>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    <div class="card">
        <div class="card-header"><i class="bi bi-info-circle me-2"></i>Ringkasan Keamanan</div>
        <div class="card-body">
            <ul class="mb-0 small text-muted">
                <li>CSRF protection aktif pada semua form (Laravel @csrf)</li>
                <li>Rate limiting login: 10 percobaan/menit + lockout 5 gagal</li>
                <li>Output Blade di-escape (XSS protection)</li>
                <li>Header keamanan: X-Frame-Options, X-Content-Type-Options, HSTS (produksi)</li>
                <li>Sesi disimpan di database dengan regenerasi setelah login</li>
            </ul>
        </div>
    </div>
@endsection
