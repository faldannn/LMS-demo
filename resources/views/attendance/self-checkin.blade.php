@extends('layouts.app')
@section('title', 'Kartu QR Presensi Siswa')
@section('page-title', 'Kartu QR Presensi Siswa')

@push('styles')
<style>
    .qr-card-shadow {
        box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.08), 0 0 0 1px rgba(226, 232, 240, 0.9);
    }
    .pulse-radar {
        box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.5);
        animation: pulseRadarAnim 2s infinite;
    }
    @keyframes pulseRadarAnim {
        0% { transform: scale(0.97); box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 12px rgba(245, 158, 11, 0); }
        100% { transform: scale(0.97); box-shadow: 0 0 0 0 rgba(245, 158, 11, 0); }
    }
    .spin-icon {
        display: inline-block;
        animation: spinAnim 2s linear infinite;
    }
    @keyframes spinAnim {
        100% { transform: rotate(360deg); }
    }
</style>
@endpush

@section('content')
<div class="mx-auto max-w-5xl space-y-6">

    {{-- Header & Live Clock --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-1">
        <div>
            <div class="mb-1 flex items-center gap-2">
                <a href="{{ route('attendance.mine') }}" class="inline-flex items-center gap-1.5 text-xs font-extrabold text-slate-500 hover:text-brand-600 transition">
                    <i class="bi bi-arrow-left"></i> Riwayat Absensi
                </a>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-brand-600 text-white shadow-md shadow-brand-500/20 shrink-0">
                    <i class="bi bi-qr-code text-xl"></i>
                </span>
                Kartu QR Presensi Siswa
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1 mb-0">
                Tunjukkan Kode QR ini kepada Guru atau Petugas untuk dipindai saat absensi kelas.
            </p>
        </div>
        <div class="text-left sm:text-right shrink-0">
            <span class="inline-flex items-center gap-2 rounded-full bg-slate-900 px-3.5 py-1.5 text-xs font-mono font-bold text-white shadow-sm">
                <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span id="clockDisplay">--:--:-- WIB</span>
            </span>
        </div>
    </div>

    {{-- Main Container (Flex Layout - Stabil & Tidak Terjepit) --}}
    <div class="flex flex-col md:flex-row gap-6 items-start w-full">

        {{-- KOLOM KIRI: KARTU DIGITAL & KODE QR SISWA --}}
        <div class="w-full md:w-7/12" style="flex: 1 1 58%; min-width: 0;">
            <div class="rounded-3xl bg-white border border-slate-200/90 qr-card-shadow overflow-hidden">
                
                {{-- Header Kartu Pelajar Digital --}}
                <div style="background: linear-gradient(135deg, #1e40af, #3b82f6); padding: 18px 24px; color: white;" class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/15 border border-white/20">
                            <i class="bi bi-mortarboard-fill text-xl text-amber-300"></i>
                        </div>
                        <div>
                            <div class="text-xs font-black uppercase tracking-wider text-white">SMA Islam Assyafiiyah 02</div>
                            <div class="text-[10px] text-white/80 font-mono">KARTU DIGITAL PRESENSI SISWA</div>
                        </div>
                    </div>
                    <span class="text-[11px] font-mono px-3 py-1 rounded-full bg-white/15 border border-white/25 text-white font-bold">
                        {{ today()->translatedFormat('d M Y') }}
                    </span>
                </div>

                {{-- Body Kartu --}}
                <div class="p-6 text-center space-y-4">
                    
                    {{-- Profil Siswa --}}
                    <div class="flex flex-col items-center">
                        <div class="relative mb-2.5 h-16 w-16">
                            <img src="{{ $student->photo_url }}" class="h-16 w-16 rounded-full object-cover shadow-sm ring-4 ring-brand-50" alt="{{ $student->name }}">
                            <span class="absolute bottom-0 right-0 h-4 w-4 rounded-full bg-emerald-500 ring-2 ring-white flex items-center justify-center text-[8px] text-white">
                                <i class="bi bi-check"></i>
                            </span>
                        </div>
                        <h4 class="text-lg font-black text-slate-900 leading-snug mb-0">{{ $student->name }}</h4>
                        <div class="flex items-center gap-2 mt-1 flex-wrap justify-center">
                            <span class="text-xs font-mono text-slate-500 font-semibold">NIS: {{ $student->nis }}</span>
                            <span class="text-slate-300">•</span>
                            <span class="rounded-full bg-brand-50 px-2.5 py-0.5 text-xs font-bold text-brand-700 border border-brand-200">
                                {{ $student->class?->name ?? 'Tanpa Kelas' }}
                            </span>
                            <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-600 uppercase">
                                Sesi {{ ucfirst($session) }}
                            </span>
                        </div>
                    </div>

                    {{-- QR CODE UTAMA SISWA (BESAR & SANGAT JELAS) --}}
                    <div class="py-2 flex flex-col items-center justify-center">
                        <div style="width: 220px; height: 220px; background: white; border-radius: 20px; border: 2px solid #e2e8f0; padding: 12px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(0,0,0,0.04);">
                            {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(196)->generate('LMS-' . $student->nis) !!}
                        </div>
                        <div class="mt-3">
                            <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-slate-100 text-slate-800 font-mono text-xs font-bold border border-slate-200">
                                <i class="bi bi-upc-scan text-brand-600"></i> ID: LMS-{{ $student->nis }}
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-2 mb-0">
                            Arahkan layar ini ke kamera pemindai Guru / Admin
                        </p>
                    </div>

                </div>

                {{-- Footer Kartu --}}
                <div class="bg-slate-50 border-t border-slate-100 px-6 py-3.5 flex items-center justify-between text-xs text-slate-500">
                    <span class="flex items-center gap-1.5 font-semibold text-emerald-600">
                        <i class="bi bi-shield-fill-check"></i> Terverifikasi Sistem
                    </span>
                    <span class="font-mono text-slate-400 text-[11px]">
                        SMA Islam Assyafiiyah 02
                    </span>
                </div>

            </div>
        </div>

        {{-- KOLOM KANAN: STATUS KEHADIRAN & PANDUAN --}}
        <div class="w-full md:w-5/12 space-y-5" style="flex: 1 1 42%; min-width: 0;">

            {{-- Card Status Presensi --}}
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                
                <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-calendar-check text-brand-600 text-base"></i>
                        <h6 class="text-xs font-black text-slate-800 uppercase tracking-wider mb-0">Status Hari Ini</h6>
                    </div>
                    <span class="text-[11px] text-slate-500 font-mono">Buka: {{ substr($checkinStart, 0, 5) }} WIB</span>
                </div>

                <div id="attendanceStatusArea">
                    @if($todayLog)
                        {{-- STATUS: SUDAH PRESENSI --}}
                        <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-5 text-center space-y-3">
                            <div class="mx-auto h-12 w-12 rounded-2xl bg-emerald-600 text-white flex items-center justify-center text-2xl shadow-md shadow-emerald-600/30">
                                <i class="bi bi-check-lg"></i>
                            </div>
                            <div>
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1 text-xs font-black text-emerald-800 mb-1">
                                    🟢 Kehadiran Terverifikasi
                                </span>
                                <h3 class="text-xl font-black text-slate-900 mt-1">
                                    {{ is_string($todayLog->checked_in_at) ? $todayLog->checked_in_at : $todayLog->checked_in_at->format('H:i:s') }} WIB
                                </h3>
                                <p class="text-xs text-emerald-700 font-medium mb-0 mt-1">
                                    {{ $todayLog->notes ?: 'Telah diverifikasi Hadir' }}
                                </p>
                            </div>
                        </div>
                    @else
                        {{-- STATUS: BELUM PRESENSI --}}
                        <div class="rounded-2xl bg-amber-50/70 border border-amber-200 p-5 text-center space-y-3">
                            <div class="mx-auto h-12 w-12 rounded-2xl bg-amber-500 text-white flex items-center justify-center text-2xl shadow-md shadow-amber-500/20 pulse-radar">
                                <i class="bi bi-hourglass-split"></i>
                            </div>
                            <div>
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-3 py-1 text-xs font-black text-amber-800 mb-1">
                                    ⏳ Menunggu Scan Guru
                                </span>
                                <h4 class="text-base font-bold text-slate-800 mt-1">Belum Melakukan Absensi</h4>
                                <p class="text-xs text-slate-500 mb-0 mt-1">
                                    Tunjukkan QR Code di samping ke Guru Anda di kelas.
                                </p>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-slate-400 font-mono text-[11px]" id="livePollIndicator">
                        <i class="bi bi-arrow-repeat spin-icon"></i> Auto-check aktif
                    </span>
                    <a href="{{ route('attendance.mine') }}" class="font-bold text-brand-600 hover:text-brand-700">
                        Lihat Riwayat &rarr;
                    </a>
                </div>

            </div>

            {{-- Petunjuk Penggunaan Singkat --}}
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm space-y-3">
                <h6 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2 mb-0">
                    <i class="bi bi-info-circle-fill text-brand-600"></i> Cara Absensi Menggunakan QR
                </h6>
                <div class="space-y-2.5 text-xs text-slate-600 pt-1">
                    <div class="flex items-start gap-2.5">
                        <span class="h-5 w-5 rounded-full bg-brand-50 text-brand-700 font-bold text-[11px] flex items-center justify-center shrink-0 mt-0.5">1</span>
                        <p class="mb-0">Buka halaman ini dan pastikan kecerahan layar ponsel Anda mencukupi.</p>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <span class="h-5 w-5 rounded-full bg-brand-50 text-brand-700 font-bold text-[11px] flex items-center justify-center shrink-0 mt-0.5">2</span>
                        <p class="mb-0">Arahkan Kode QR kepada <strong>Guru / Admin</strong> di kelas atau gerbang.</p>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <span class="h-5 w-5 rounded-full bg-brand-50 text-brand-700 font-bold text-[11px] flex items-center justify-center shrink-0 mt-0.5">3</span>
                        <p class="mb-0">Setelah Guru memindai QR, status di layar ini akan <strong>otomatis berubah menjadi Hadir</strong>.</p>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>

@push('scripts')
<script>
    // Live Digital Clock
    function updateClock() {
        const el = document.getElementById('clockDisplay');
        if (el) el.textContent = new Date().toLocaleTimeString('id-ID') + ' WIB';
    }
    setInterval(updateClock, 1000);
    updateClock();

    // Polling status presensi real-time
    let isAlreadyCheckedIn = {{ $todayLog ? 'true' : 'false' }};
    const pollUrl = '{{ route("attendance.status-poll") }}';

    function checkAttendanceStatus() {
        if (isAlreadyCheckedIn) return;

        fetch(pollUrl, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.checked_in) {
                isAlreadyCheckedIn = true;
                renderSuccessStatus(data.time, data.notes);
            }
        })
        .catch(err => {
            console.log('Polling status check:', err);
        });
    }

    function renderSuccessStatus(timeStr, notesStr) {
        const area = document.getElementById('attendanceStatusArea');
        if (!area) return;

        area.innerHTML = `
            <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-5 text-center space-y-3">
                <div class="mx-auto h-12 w-12 rounded-2xl bg-emerald-600 text-white flex items-center justify-center text-2xl shadow-md shadow-emerald-600/30">
                    <i class="bi bi-check-lg"></i>
                </div>
                <div>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1 text-xs font-black text-emerald-800 mb-1">
                        🟢 Kehadiran Terverifikasi
                    </span>
                    <h3 class="text-xl font-black text-slate-900 mt-1">${timeStr || 'Baru Saja'}</h3>
                    <p class="text-xs text-emerald-700 font-medium mb-0 mt-1">
                        ${notesStr || 'Telah diverifikasi oleh Guru / Admin'}
                    </p>
                </div>
            </div>
        `;

        const indicator = document.getElementById('livePollIndicator');
        if (indicator) {
            indicator.innerHTML = '<span class="text-emerald-600 font-bold"><i class="bi bi-check-circle-fill"></i> Terkonfirmasi</span>';
        }
    }

    // Jalankan auto-check tiap 3.5 detik jika belum hadir
    if (!isAlreadyCheckedIn) {
        setInterval(checkAttendanceStatus, 3500);
    }
</script>
@endpush
@endsection
