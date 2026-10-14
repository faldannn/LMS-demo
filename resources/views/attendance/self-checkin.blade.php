@extends('layouts.app')
@section('title', 'Presensi Scan QR Code')
@section('page-title', 'Presensi Scan QR Code Siswa')

@push('styles')
<style>
    .scanner-viewport {
        position: relative;
        width: 100%;
        max-width: 380px;
        margin: 0 auto;
        border-radius: 1.5rem;
        overflow: hidden;
        background: #020617;
        aspect-ratio: 1 / 1;
        border: 2px solid #1e293b;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.4), 0 0 0 1px rgba(255, 255, 255, 0.05);
    }
    .scanner-laser {
        position: absolute;
        left: 8%;
        right: 8%;
        height: 3px;
        background: linear-gradient(90deg, transparent, #10b981, #06b6d4, transparent);
        box-shadow: 0 0 15px #10b981, 0 0 25px #06b6d4;
        animation: scanLaserAnim 2.2s infinite ease-in-out;
        z-index: 20;
        pointer-events: none;
    }
    @keyframes scanLaserAnim {
        0%, 100% { top: 10%; }
        50% { top: 88%; }
    }
    .scan-corner {
        position: absolute;
        width: 28px;
        height: 28px;
        border-color: #10b981;
        z-index: 15;
        pointer-events: none;
    }
    .corner-tl { top: 20px; left: 20px; border-top: 3.5px solid; border-left: 3.5px solid; border-top-left-radius: 10px; }
    .corner-tr { top: 20px; right: 20px; border-top: 3.5px solid; border-right: 3.5px solid; border-top-right-radius: 10px; }
    .corner-bl { bottom: 20px; left: 20px; border-bottom: 3.5px solid; border-left: 3.5px solid; border-bottom-left-radius: 10px; }
    .corner-br { bottom: 20px; right: 20px; border-bottom: 3.5px solid; border-right: 3.5px solid; border-bottom-right-radius: 10px; }
</style>
@endpush

@section('content')
<div class="mx-auto max-w-4xl space-y-6">

    {{-- Breadcrumb & Title --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-1">
        <div>
            <div class="mb-1 flex items-center gap-2">
                <a href="{{ route('attendance.mine') }}" class="inline-flex items-center gap-1.5 text-xs font-extrabold text-slate-500 hover:text-brand-600 transition">
                    <i class="bi bi-arrow-left"></i> Riwayat Absensi
                </a>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-brand-600 text-white shadow-md shadow-brand-500/20 shrink-0">
                    <i class="bi bi-qr-code-scan text-xl"></i>
                </span>
                Presensi Scan QR Code Siswa
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Pindai QR Code kelas menggunakan kamera perangkat untuk mencatat kehadiran hari ini.
            </p>
        </div>
        <div class="text-left sm:text-right shrink-0">
            <span class="inline-flex items-center gap-2 rounded-full bg-slate-900 px-3.5 py-1.5 text-xs font-mono font-bold text-white shadow-sm">
                <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span id="clockDisplay">--:--:-- WIB</span>
            </span>
        </div>
    </div>

    {{-- Main Grid: Profil & Status Presensi --}}
    <div class="grid grid-cols-1 gap-5 md:grid-cols-12">

        {{-- Profil & Kartu QR Pelajar (4 Kolom) --}}
        <div class="md:col-span-4 space-y-4">
            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200/80 text-center">
                <div class="relative mx-auto mb-3 h-20 w-20">
                    <img src="{{ $student->photo_url }}" class="h-20 w-20 rounded-full object-cover shadow-sm ring-4 ring-brand-50" alt="{{ $student->name }}">
                    <span class="absolute bottom-0 right-0 h-5 w-5 rounded-full bg-emerald-500 ring-2 ring-white flex items-center justify-center text-[10px] text-white">
                        <i class="bi bi-check"></i>
                    </span>
                </div>
                <h6 class="text-base font-extrabold text-slate-900 leading-snug">{{ $student->name }}</h6>
                <p class="text-xs text-slate-500 font-mono mt-0.5">NIS: {{ $student->nis }}</p>
                
                <div class="mt-3 flex flex-wrap justify-center gap-1.5">
                    <span class="rounded-full bg-brand-50 px-3 py-1 text-xs font-bold text-brand-700 border border-brand-200">
                        {{ $student->class?->name ?? 'Tanpa Kelas' }}
                    </span>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700 uppercase">
                        Sesi {{ $session }}
                    </span>
                </div>

                {{-- Digital QR Badge Siswa --}}
                <div class="mt-5 pt-4 border-t border-slate-100">
                    <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2 flex items-center justify-center gap-1">
                        <i class="bi bi-person-badge text-brand-600"></i> Kartu QR Siswa
                    </div>
                    <div class="mx-auto w-32 h-32 p-2 bg-white rounded-xl border border-slate-200 shadow-2xs flex items-center justify-center">
                        {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(112)->generate('LMS-' . $student->nis) !!}
                    </div>
                    <p class="text-[10px] text-slate-400 mt-1.5 font-mono">Kode ID: LMS-{{ $student->nis }}</p>
                </div>
            </div>
        </div>

        {{-- Card Status Presensi & Action Scan (8 Kolom) --}}
        <div class="md:col-span-8 space-y-4">

            {{-- Status Kehadiran Hari Ini --}}
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/80">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 font-bold border border-emerald-100">
                            <i class="bi bi-calendar-check text-xl"></i>
                        </div>
                        <div>
                            <h6 class="text-sm font-bold text-slate-900">Status Presensi Hari Ini</h6>
                            <p class="text-xs text-slate-500 font-mono">Buka Presensi: {{ substr($checkinStart, 0, 5) }} WIB</p>
                        </div>
                    </div>
                    @if($todayLog)
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3.5 py-1 text-xs font-black text-emerald-800">
                            <i class="bi bi-check-circle-fill"></i> Sudah Presensi
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-3.5 py-1 text-xs font-black text-amber-800">
                            <i class="bi bi-clock"></i> Belum Presensi
                        </span>
                    @endif
                </div>

                @if($todayLog)
                    {{-- Tampilan Ketika Sudah Presensi --}}
                    <div class="rounded-2xl bg-emerald-50/70 border border-emerald-200/80 p-5 space-y-3">
                        <div class="flex items-center justify-between flex-wrap gap-2">
                            <div class="flex items-center gap-3">
                                <div class="h-12 w-12 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-2xl shadow-md shadow-emerald-600/20">
                                    <i class="bi bi-check-lg"></i>
                                </div>
                                <div>
                                    <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Kehadiran Terverifikasi</span>
                                    <h4 class="text-xl font-black text-slate-900">{{ $todayLog->checked_in_at->format('H:i:s') }} WIB</h4>
                                </div>
                            </div>
                            <span class="inline-flex items-center gap-1 rounded-full bg-white px-3 py-1 text-xs font-bold text-emerald-800 border border-emerald-200 shadow-2xs">
                                <i class="bi bi-qr-code"></i> {{ $todayLog->mode_label }}
                            </span>
                        </div>
                        
                        <div class="pt-2 border-t border-emerald-200/60 flex items-center justify-between text-xs text-slate-600">
                            <span class="font-mono text-slate-500"><i class="bi bi-upc-scan me-1"></i>{{ $todayLog->notes ?: 'Scan QR Valid' }}</span>
                            <span class="font-semibold text-slate-700">{{ $todayLog->attendance_date->translatedFormat('l, d F Y') }}</span>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                        <a href="{{ route('attendance.mine') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-brand-600 hover:text-brand-700">
                            <i class="bi bi-calendar-week"></i> Lihat Seluruh Riwayat Absensi &rarr;
                        </a>
                    </div>
                @else
                    {{-- Tampilan Ketika Belum Presensi --}}
                    <div class="space-y-4 py-2">
                        <div class="rounded-xl bg-slate-50 border border-slate-200 p-4">
                            <div class="flex items-start gap-3">
                                <span class="h-8 w-8 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center shrink-0 mt-0.5">
                                    <i class="bi bi-info-circle-fill"></i>
                                </span>
                                <div class="text-xs text-slate-600 space-y-1">
                                    <p class="font-bold text-slate-800">Petunjuk Presensi QR:</p>
                                    <p>1. Klik tombol <strong>Buka Scanner QR</strong> di bawah.</p>
                                    <p>2. Arahkan kamera ke QR Code kelas yang ditampilkan guru di layar proyektor / meja.</p>
                                    <p>3. Kehadiran Anda akan langsung terverifikasi seketika.</p>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                            <button type="button" onclick="openQrModal()" class="w-full rounded-xl bg-emerald-600 py-3.5 px-4 text-xs font-extrabold text-white shadow-md hover:bg-emerald-500 transition active:scale-95 flex items-center justify-center gap-2 cursor-pointer">
                                <i class="bi bi-camera-fill text-base"></i> Buka Scanner Kamera QR
                            </button>
                            <button type="button" onclick="quickSimulateQr('{{ $sampleClassQr ?? 'QR-CLASS-1-' . today()->toDateString() }}')" class="w-full rounded-xl bg-slate-900 py-3.5 px-4 text-xs font-extrabold text-white shadow-md hover:bg-slate-800 transition active:scale-95 flex items-center justify-center gap-2 cursor-pointer">
                                <i class="bi bi-lightning-charge-fill text-amber-400"></i> Uji Coba Cepat (1-Klik)
                            </button>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Informasi Keamanan & Kemudahan --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm space-y-2">
                <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-slate-700">
                    <i class="bi bi-shield-check text-brand-600 text-sm"></i> Presensi Cepat Tanpa GPS & Bebas Kendala
                </div>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Sistem presensi QR Code bekerja langsung di browser tanpa memerlukan izin lokasi GPS atau foto wajah. Cukup pastikan kamera aktif saat memindai kode QR kelas.
                </p>
            </div>

        </div>

    </div>

</div>

{{-- MODAL SCANNER QR CODE --}}
<div id="qrScannerModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-950/80 backdrop-blur-md p-4 flex items-center justify-center">
    <div class="w-full max-w-md overflow-hidden rounded-3xl bg-slate-900 border border-slate-800 shadow-2xl text-white">
        
        {{-- Header Modal --}}
        <div class="flex items-center justify-between border-b border-slate-800 px-6 py-4">
            <div class="flex items-center gap-2.5">
                <span class="h-3 w-3 rounded-full bg-emerald-400 shadow-[0_0_10px_#10b981]"></span>
                <h5 class="text-sm font-black text-white uppercase tracking-wider">Pemindai QR Code</h5>
            </div>
            <button type="button" onclick="closeQrModal()" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-800 hover:text-white transition cursor-pointer">
                <i class="bi bi-x-lg text-lg"></i>
            </button>
        </div>

        {{-- Body Scanner --}}
        <div class="p-6 text-center space-y-4">
            
            {{-- Viewfinder Container --}}
            <div class="scanner-viewport">
                <div class="scanner-laser" id="scannerLaser"></div>
                <div class="scan-corner corner-tl"></div>
                <div class="scan-corner corner-tr"></div>
                <div class="scan-corner corner-bl"></div>
                <div class="scan-corner corner-br"></div>
                
                {{-- HTML5 QR Code Mount / Video Feed --}}
                <div id="qrReader" class="h-full w-full object-cover"></div>
                <video id="qrVideoFeed" class="h-full w-full object-cover" playsinline muted autoplay></video>
                <canvas id="qrCanvas" class="hidden"></canvas>
            </div>

            <p class="text-xs text-slate-400 font-mono">
                Arahkan kamera ke QR Code Presensi Kelas
            </p>

            <div id="scannerAlert" class="hidden rounded-xl p-3 text-xs font-bold text-center"></div>

            {{-- Input Manual / Fallback --}}
            <div class="pt-2 border-t border-slate-800 space-y-2">
                <label class="text-[11px] text-slate-400 font-semibold block text-left">Atau masukkan kode QR secara manual:</label>
                <div class="flex gap-2">
                    <input type="text" id="manualQrInput" placeholder="Contoh: QR-CLASS-1-..." class="w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-xs font-mono text-white placeholder-slate-500 focus:border-cyan-400 focus:outline-none uppercase">
                    <button type="button" onclick="submitManualQr()" class="rounded-xl bg-brand-600 px-4 text-xs font-bold text-white hover:bg-brand-500 transition cursor-pointer shrink-0">
                        Kirim
                    </button>
                </div>
            </div>

            <div class="flex justify-center gap-2 pt-1">
                <button type="button" onclick="quickSimulateQr('{{ $sampleClassQr ?? 'QR-CLASS-1-' . today()->toDateString() }}')" class="rounded-xl bg-slate-800 border border-slate-700 px-4 py-2 text-xs font-bold text-slate-300 hover:text-white transition">
                    <i class="bi bi-lightning text-amber-400"></i> Tes Scan Otomatis
                </button>
                <button type="button" onclick="closeQrModal()" class="rounded-xl border border-slate-700 px-4 py-2 text-xs font-bold text-slate-400 hover:text-white transition">
                    Tutup
                </button>
            </div>

        </div>

    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    let html5QrCode = null;
    let isProcessing = false;

    // Live Digital Clock
    function updateClock() {
        const el = document.getElementById('clockDisplay');
        if (el) el.textContent = new Date().toLocaleTimeString('id-ID') + ' WIB';
    }
    setInterval(updateClock, 1000);
    updateClock();

    // Audio Buzzer Synthesizer
    let audioCtx = null;
    function playBeep(success = true) {
        try {
            audioCtx = audioCtx || new (window.AudioContext || window.webkitAudioContext)();
            if (audioCtx.state === 'suspended') audioCtx.resume();
            
            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();
            osc.frequency.setValueAtTime(success ? 1400 : 350, audioCtx.currentTime);
            gain.gain.setValueAtTime(0.2, audioCtx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + (success ? 0.2 : 0.35));
            osc.connect(gain);
            gain.connect(audioCtx.destination);
            osc.start();
            osc.stop(audioCtx.currentTime + (success ? 0.2 : 0.35));
        } catch(e) {}
    }

    // Modal Control
    function openQrModal() {
        document.getElementById('qrScannerModal').classList.remove('hidden');
        document.getElementById('scannerAlert').classList.add('hidden');
        isProcessing = false;
        startCameraScanner();
    }

    function closeQrModal() {
        stopCameraScanner();
        document.getElementById('qrScannerModal').classList.add('hidden');
    }

    async function startCameraScanner() {
        const qrReaderDiv = document.getElementById('qrReader');
        const videoFeed = document.getElementById('qrVideoFeed');

        if (typeof Html5Qrcode !== 'undefined') {
            try {
                videoFeed.classList.add('hidden');
                qrReaderDiv.classList.remove('hidden');

                if (!html5QrCode) {
                    html5QrCode = new Html5Qrcode("qrReader");
                }

                await html5QrCode.start(
                    { facingMode: "environment" },
                    { fps: 15, qrbox: { width: 250, height: 250 } },
                    (decodedText) => {
                        if (!isProcessing) {
                            handleQrScanned(decodedText);
                        }
                    },
                    (errorMessage) => {
                        // scanning frame loop
                    }
                );
                return;
            } catch (err) {
                console.warn('Html5Qrcode error, falling back to direct video stream:', err);
            }
        }

        // Fallback Native WebRTC
        try {
            qrReaderDiv.classList.add('hidden');
            videoFeed.classList.remove('hidden');
            const stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'environment', width: { ideal: 640 }, height: { ideal: 640 } },
                audio: false
            });
            videoFeed.srcObject = stream;
        } catch (e) {
            showScannerAlert('Kamera tidak dapat diakses: ' + e.message + '. Gunakan opsi input kode manual di bawah.', 'danger');
        }
    }

    function stopCameraScanner() {
        if (html5QrCode && html5QrCode.isScanning) {
            html5QrCode.stop().catch(() => {});
        }
        const video = document.getElementById('qrVideoFeed');
        if (video && video.srcObject) {
            video.srcObject.getTracks().forEach(t => t.stop());
            video.srcObject = null;
        }
    }

    function showScannerAlert(msg, type = 'info') {
        const el = document.getElementById('scannerAlert');
        el.className = `rounded-xl p-3 text-xs font-bold text-center ${type === 'danger' ? 'bg-rose-950 text-rose-300 border border-rose-800' : 'bg-emerald-950 text-emerald-300 border border-emerald-800'}`;
        el.textContent = msg;
        el.classList.remove('hidden');
    }

    function submitManualQr() {
        const val = document.getElementById('manualQrInput').value.trim();
        if (!val) {
            alert('Silakan masukkan kode QR terlebih dahulu.');
            return;
        }
        handleQrScanned(val);
    }

    function quickSimulateQr(code) {
        handleQrScanned(code);
    }

    async function handleQrScanned(qrPayload) {
        if (isProcessing) return;
        isProcessing = true;
        playBeep(true);

        showScannerAlert('Memproses QR Code...', 'info');

        try {
            const res = await fetch('{{ route("api.attendance.self.check-in") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    qr_code: qrPayload,
                    check_in_mode: 'qr_scan',
                    device_type: /mobile/i.test(navigator.userAgent) ? 'Mobile' : 'Desktop',
                    browser: 'Browser'
                })
            });

            const data = await res.json();

            if (res.ok && data.success) {
                showScannerAlert(data.message || 'Presensi berhasil dicatat!', 'success');
                setTimeout(() => {
                    closeQrModal();
                    window.location.reload();
                }, 1200);
            } else {
                playBeep(false);
                showScannerAlert(data.message || 'Gagal melakukan presensi.', 'danger');
                setTimeout(() => { isProcessing = false; }, 2000);
            }
        } catch (e) {
            playBeep(false);
            showScannerAlert('Kesalahan koneksi: ' + e.message, 'danger');
            setTimeout(() => { isProcessing = false; }, 2000);
        }
    }
</script>
@endpush
@endsection
