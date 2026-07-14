@extends('layouts.app')
@section('title', 'Presensi Mandiri')
@section('page-title', 'Presensi Mandiri')

@section('content')
<div class="mb-4 flex justify-center">
    <div class="w-full @if($todayLog) max-w-2xl @else max-w-3xl @endif flex justify-start">
        <a href="{{ route('attendance.mine') }}" class="inline-flex items-center gap-1.5 text-xs font-extrabold text-slate-500 hover:text-brand-600 transition">
            <i class="bi bi-arrow-left"></i> Kembali ke Halaman Absensi
        </a>
    </div>
</div>

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
                    @foreach(['Login', 'Ambil Foto', 'Lokasi GPS', 'Selesai'] as $i => $label)
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
                        <p class="mx-auto mt-4 max-w-md text-sm text-slate-500">Silakan klik tombol di bawah untuk mengambil foto wajah Anda dan mencatat lokasi GPS presensi Anda.</p>
                        <button data-btn-start class="mt-8 inline-flex items-center justify-center gap-2 rounded-xl bg-brand-600 px-8 py-3.5 text-sm font-bold text-white shadow-lg shadow-brand-500/30 transition hover:bg-brand-700">
                            <i class="bi bi-camera-video"></i> Mulai Presensi
                        </button>
                    </div>
                </div>

                <div data-step-panel="camera" class="hidden text-center">
                    <div class="relative mx-auto mb-4 overflow-hidden rounded-xl bg-slate-900" style="aspect-ratio:4/3; max-width: 480px;">
                        <video id="attendanceVideo" class="h-full w-full object-cover" playsinline muted autoplay style="transform: scaleX(-1);"></video>
                        <img id="attendancePreview" class="hidden h-full w-full object-cover" alt="Preview">
                        <canvas id="attendanceCanvas" class="hidden"></canvas>
                        
                        <!-- Face Box Overlay -->
                        <div id="faceOverlay" class="absolute hidden border-2 border-emerald-500 rounded-lg pointer-events-none" style="transition: all 0.05s ease-out;">
                            <span class="absolute inset-0 m-auto h-2 w-2 rounded-full bg-emerald-500 animate-ping"></span>
                        </div>
                    </div>

                    <!-- Fallback file input for non-secure contexts / HTTP / Permission Denied -->
                    <div id="cameraFallbackContainer" class="hidden py-6 text-center">
                        <i class="bi bi-camera-fill text-5xl text-slate-400 mb-3 d-block"></i>
                        <p class="text-sm text-slate-500 mb-4">Kamera langsung tidak tersedia.</p>
                        <label class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-xl bg-brand-600 px-6 py-2.5 text-sm font-bold text-white shadow-md transition hover:bg-brand-700">
                            <i class="bi bi-upload"></i> Ambil Foto / Pilih File
                            <input type="file" id="filePhoto" accept="image/*" capture="user" class="hidden">
                        </label>
                        <div id="previewContainer" class="mt-4 hidden">
                            <img id="fallbackPreview" class="mx-auto max-h-48 rounded-lg shadow-sm border border-slate-200" src="" alt="Preview">
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div id="captureActionContainer" class="mt-4">
                        <button id="btnCapturePhoto" class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-600 px-8 py-3.5 text-sm font-bold text-white shadow-lg shadow-brand-500/30 transition hover:bg-brand-700" disabled>
                            <i class="bi bi-camera"></i> Ambil Foto
                        </button>
                    </div>

                    <div id="confirmActionContainer" class="hidden mt-4 flex justify-center gap-3">
                        <button id="btnRetakePhoto" type="button" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-6 py-3 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50">
                            <i class="bi bi-arrow-counterclockwise"></i> Foto Ulang
                        </button>
                        <button id="btnConfirmPhoto" type="button" class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-brand-500/30 transition hover:bg-brand-700">
                            <i class="bi bi-check-lg"></i> Gunakan Foto & Lanjut
                        </button>
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
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/tracking.js/1.1.3/tracking-min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tracking.js/1.1.3/data/face-min.js"></script>
@if(file_exists(public_path('build/manifest.json')))
@vite(['resources/js/self-attendance.js'])
@else
<script type="module" src="{{ asset('js/self-attendance.js') }}"></script>
@endif
@endpush
@endif
@endsection
