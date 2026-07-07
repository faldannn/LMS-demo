@extends('layouts.app')
@section('title', 'Presensi Wajah & GPS')
@section('page-title', 'Presensi Mandiri')

@section('content')
@if($todayLog)
<div class="flex justify-center">
    <div class="w-full max-w-2xl">
        <div class="rounded-2xl bg-white p-10 text-center shadow-sm ring-1 ring-slate-200/70">
            <i class="bi bi-check-circle-fill mb-4 text-6xl text-emerald-500"></i>
            <h5 class="text-xl font-bold text-slate-900">Anda sudah presensi hari ini</h5>
            <p class="mb-1 text-slate-500">{{ $todayLog->checked_in_at->format('d M Y, H:i:s') }}</p>
            <p class="text-xs text-slate-400">{{ $todayLog->mode_label }}</p>
            <a href="{{ route('attendance.mine') }}" class="mt-6 inline-block rounded-xl border border-brand-600 px-6 py-2.5 text-sm font-semibold text-brand-600 transition hover:bg-brand-50">Lihat Riwayat Absensi</a>
        </div>
    </div>
</div>
@else
<div class="flex justify-center">
    <div class="w-full max-w-3xl">
        <div id="selfAttendanceApp" class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/70">
            <div class="px-6 pt-6 pb-2">
                <div class="mb-6 flex items-center justify-between" data-step-indicator>
                    @foreach(['Login', 'Kamera', 'Wajah', 'GPS', 'Selesai'] as $i => $label)
                    <div class="flex-1 text-center">
                        <div class="mx-auto mb-2 flex h-8 w-8 items-center justify-center rounded-full border-2 border-slate-200 bg-slate-50 text-xs font-bold text-slate-600 transition-colors {{ $i === 0 ? 'active' : '' }}"
                             data-step="{{ $i + 1 }}"> {{ $i+1 }} </div>
                        <div class="text-[10px] font-semibold text-slate-500">{{ $label }}</div>
                    </div>
                    @endforeach
                </div>
                <style>
                    [data-step-indicator] [data-step].active { background:var(--color-brand-500)!important;color:#fff;border-color:var(--color-brand-500)!important; }
                </style>
            </div>
            <div class="px-6 pb-6">
                <div data-status class="mb-4 rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-800">Memuat konfigurasi presensi...</div>

                <div data-step-panel="ready">
                    <div class="py-8 text-center">
                        <img src="{{ $student->photo_url }}" class="mx-auto mb-4 h-20 w-20 rounded-full object-cover shadow-sm ring-2 ring-white" alt="">
                        <h6 class="text-lg font-bold text-slate-900">{{ $student->name }}</h6>
                        <p class="text-sm text-slate-500">NIS: {{ $student->nis }}</p>
                        <p class="mx-auto mt-4 max-w-md text-sm text-slate-500">Sistem akan memverifikasi wajah Anda, lalu memastikan Anda berada dalam radius sekolah melalui GPS.</p>
                        <div class="mt-6 flex flex-wrap justify-center gap-2">
                            <span class="rounded-full bg-emerald-100 px-3 py-1.5 text-xs font-bold text-emerald-700">Face Recognition</span>
                            <span class="rounded-full bg-sky-100 px-3 py-1.5 text-xs font-bold text-sky-700">GPS Sekolah</span>
                            <span class="rounded-full bg-amber-100 px-3 py-1.5 text-xs font-bold text-amber-700">Audit Log</span>
                        </div>
                        <button data-btn-start class="mt-8 inline-flex items-center justify-center gap-2 rounded-xl bg-brand-600 px-8 py-3.5 text-sm font-bold text-white shadow-lg shadow-brand-500/30 transition hover:bg-brand-700">
                            <i class="bi bi-camera-video"></i> Mulai Presensi
                        </button>
                    </div>
                </div>

                <div data-step-panel="camera" class="hidden">
                    <div class="relative mx-auto mb-4 overflow-hidden rounded-xl bg-slate-900" style="aspect-ratio:4/3; max-width: 480px;">
                        <video id="attendanceVideo" class="h-full w-full object-cover" playsinline muted autoplay></video>
                        <canvas id="attendanceCanvas" class="hidden"></canvas>
                        <div data-face-overlay class="absolute bottom-0 left-0 right-0 bg-black/60 p-2 text-center text-xs font-medium text-white backdrop-blur-sm">Menunggu kamera...</div>
                    </div>
                </div>

                <div data-step-panel="gps" class="hidden py-10 text-center">
                    <i class="bi bi-geo-alt-fill mb-4 text-6xl text-brand-600"></i>
                    <h6 class="text-lg font-bold text-slate-900">Memvalidasi lokasi GPS...</h6>
                    <p class="mt-2 text-sm text-slate-500">Pastikan GPS aktif dan izin lokasi diberikan.</p>
                </div>

                <div data-step-panel="submitting" class="hidden py-10 text-center">
                    <div class="mx-auto mb-4 h-8 w-8 animate-spin rounded-full border-4 border-slate-200 border-t-brand-600"></div>
                    <p class="font-medium text-slate-600">Mengirim presensi...</p>
                </div>

                <div data-step-panel="success" class="hidden py-8 text-center" data-success></div>

                <div data-step-panel="fallback" class="hidden py-8 text-center">
                    <i class="bi bi-exclamation-triangle mb-4 text-5xl text-amber-500"></i>
                    <h6 class="text-lg font-bold text-slate-900">Mode Alternatif</h6>
                    <p class="mb-6 text-sm text-slate-500">Kamera atau GPS tidak tersedia. Pilih fallback yang diizinkan sekolah.</p>
                    <div class="mx-auto flex max-w-xs flex-col gap-3">
                        <button data-btn-fallback-gps class="rounded-xl border border-brand-600 px-4 py-2.5 text-sm font-semibold text-brand-600 hover:bg-brand-50">Lanjut dengan GPS saja</button>
                        <button data-btn-fallback-camera class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Lanjut dengan Kamera saja</button>
                        <button data-btn-fallback-manual class="rounded-xl border border-amber-500 px-4 py-2.5 text-sm font-semibold text-amber-600 hover:bg-amber-50">Presensi Manual (dengan alasan)</button>
                        <button data-btn-retry class="mt-2 text-sm font-medium text-slate-500 hover:text-slate-700 hover:underline">Coba lagi</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
@if(file_exists(public_path('build/manifest.json')))
@vite(['resources/js/self-attendance.js'])
@else
<script type="module" src="{{ asset('js/self-attendance.js') }}"></script>
@endif
@endpush
@endif
@endsection
