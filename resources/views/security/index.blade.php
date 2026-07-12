@extends('layouts.app')
@section('title', 'Keamanan Sistem')
@section('page-title', 'Keamanan Sistem')

@section('content')
<div class="space-y-6">
    {{-- Security Cards Grid --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach([
            ['route' => 'security.audit-logs', 'icon' => 'bi-shield-check', 'title' => 'Audit Log', 'desc' => 'Riwayat aktivitas pengguna di sistem', 'bg' => 'bg-brand-50', 'text' => 'text-brand-600', 'gradient' => 'from-brand-500 to-brand-600'],
            ['route' => 'security.login-history', 'icon' => 'bi-door-open', 'title' => 'Riwayat Login', 'desc' => 'Log percobaan login sukses dan gagal', 'bg' => 'bg-brand-50', 'text' => 'text-brand-600', 'gradient' => 'from-brand-500 to-brand-600'],
            ['route' => 'security.sessions', 'icon' => 'bi-pc-display', 'title' => 'Manajemen Sesi', 'desc' => 'Pantau dan cabut sesi aktif pengguna', 'bg' => 'bg-violet-50', 'text' => 'text-violet-600', 'gradient' => 'from-violet-500 to-violet-600'],
            ['route' => 'security.maintenance', 'icon' => 'bi-database-check', 'title' => 'Backup & Pemeliharaan', 'desc' => 'Backup database dan file cadangan', 'bg' => 'bg-amber-50', 'text' => 'text-amber-600', 'gradient' => 'from-amber-500 to-amber-600'],
        ] as $card)
            <a href="{{ route($card['route']) }}" class="group block rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200/70 transition hover:shadow-md hover:ring-brand-200">
                <div class="flex flex-col items-center text-center">
                    <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl {{ $card['bg'] }} {{ $card['text'] }} text-xl shadow-sm transition group-hover:scale-110">
                        <i class="bi {{ $card['icon'] }}"></i>
                    </div>
                    <h3 class="text-base font-extrabold text-slate-900 group-hover:text-brand-600 transition-colors">{{ $card['title'] }}</h3>
                    <p class="mt-2 text-sm text-slate-500">{{ $card['desc'] }}</p>
                </div>
            </a>
        @endforeach
    </div>

    {{-- Security Summary Card --}}
    <!--<div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
        <div class="flex items-center gap-2 border-b border-slate-100 pb-4 mb-4">
            <i class="bi bi-info-circle text-brand-600 text-lg"></i>
            <h3 class="text-base font-extrabold text-slate-900">Ringkasan Keamanan</h3>
        </div>
        <ul class="space-y-3 text-sm text-slate-600">
            <li class="flex items-start gap-2">
                <i class="bi bi-check-circle-fill text-emerald-500 mt-0.5 shrink-0"></i>
                <span>CSRF protection aktif pada semua form (Laravel @csrf)</span>
            </li>
            <li class="flex items-start gap-2">
                <i class="bi bi-check-circle-fill text-emerald-500 mt-0.5 shrink-0"></i>
                <span>Rate limiting login: 10 percobaan/menit + lockout 5 gagal</span>
            </li>
            <li class="flex items-start gap-2">
                <i class="bi bi-check-circle-fill text-emerald-500 mt-0.5 shrink-0"></i>
                <span>Output Blade di-escape (XSS protection)</span>
            </li>
            <li class="flex items-start gap-2">
                <i class="bi bi-check-circle-fill text-emerald-500 mt-0.5 shrink-0"></i>
                <span>Header keamanan: X-Frame-Options, X-Content-Type-Options, HSTS (produksi)</span>
            </li>
            <li class="flex items-start gap-2">
                <i class="bi bi-check-circle-fill text-emerald-500 mt-0.5 shrink-0"></i>
                <span>Sesi disimpan di database dengan regenerasi setelah login</span>
            </li>
        </ul>
    </div>-->
</div>
@endsection
