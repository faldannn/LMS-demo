@extends('layouts.app')
@section('title', 'Scanner QR Presensi Siswa')
@section('page-title', 'Scanner QR Code Siswa')

@push('styles')
<style>
    .scanner-box {
        position: relative;
        width: 100%;
        max-width: 440px;
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
        left: 6%;
        right: 6%;
        height: 3px;
        background: linear-gradient(90deg, transparent, #10b981, #06b6d4, transparent);
        box-shadow: 0 0 15px #10b981, 0 0 25px #06b6d4;
        animation: scanLaserAnim 2s infinite ease-in-out;
        z-index: 20;
        pointer-events: none;
    }
    @keyframes scanLaserAnim {
        0%, 100% { top: 8%; }
        50% { top: 90%; }
    }
    .scan-corner {
        position: absolute;
        width: 32px;
        height: 32px;
        border-color: #10b981;
        z-index: 15;
        pointer-events: none;
    }
    .corner-tl { top: 16px; left: 16px; border-top: 4px solid; border-left: 4px solid; border-top-left-radius: 12px; }
    .corner-tr { top: 16px; right: 16px; border-top: 4px solid; border-right: 4px solid; border-top-right-radius: 12px; }
    .corner-bl { bottom: 16px; left: 16px; border-bottom: 4px solid; border-left: 4px solid; border-bottom-left-radius: 12px; }
    .corner-br { bottom: 16px; right: 16px; border-bottom: 4px solid; border-right: 4px solid; border-bottom-right-radius: 12px; }
</style>
@endpush

@section('content')
<div class="mx-auto max-w-6xl space-y-6">

    {{-- Breadcrumb & Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-1">
        <div>
            <div class="mb-1 flex items-center gap-2">
                <a href="{{ route('attendance.create') }}" class="inline-flex items-center gap-1.5 text-xs font-extrabold text-slate-500 hover:text-brand-600 transition">
                    <i class="bi bi-arrow-left"></i> Presensi Kelas
                </a>
                <span class="text-slate-300">•</span>
                <span class="text-xs font-bold text-slate-400">Scanner Guru & Admin</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-emerald-600 text-white shadow-md shadow-emerald-600/20 shrink-0">
                    <i class="bi bi-camera-fill text-xl"></i>
                </span>
                Scanner QR Code Presensi Siswa
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Arahkan kamera ke Kartu QR Siswa untuk memverifikasi kehadiran secara cepat dan otomatis.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-bold">
                <i class="bi bi-person-check-fill text-emerald-600"></i>
                <span id="todayCountDisplay">{{ $todayCount }}</span> Siswa Hadir Hari Ini
            </span>
            <a href="{{ route('attendance.self-logs') }}" class="btn btn-sm btn-outline-secondary rounded-xl font-bold px-3 py-1.5 text-xs">
                <i class="bi bi-clock-history me-1"></i> Log Lengkap
            </a>
        </div>
    </div>

    {{-- Main 2 Columns Layout --}}
    <div class="flex flex-col lg:flex-row gap-6 items-start w-full">

        {{-- KOLOM KIRI: KAMERA SCANNER --}}
        <div class="w-full lg:w-7/12 space-y-4" style="flex: 1 1 58%; min-width: 0;">
            
            <div class="rounded-3xl bg-slate-950 p-6 border border-slate-800 shadow-xl text-white">
                
                {{-- Top Control Bar Kamera --}}
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-800 flex-wrap gap-2">
                    <div class="flex items-center gap-2">
                        <span class="h-2.5 w-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span class="text-xs font-bold font-mono tracking-wider text-slate-300">KAMERA PEMINDAI AKTIF</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" id="btnToggleCamera" onclick="switchCameraFacing()" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-bold border border-slate-700 transition">
                            <i class="bi bi-arrow-repeat"></i> Ganti Kamera
                        </button>
                    </div>
                </div>

                {{-- Viewfinder Kamera --}}
                <div class="scanner-box">
                    <div class="scanner-laser" id="scannerLaser"></div>
                    <div class="scan-corner corner-tl"></div>
                    <div class="scan-corner corner-tr"></div>
                    <div class="scan-corner corner-bl"></div>
                    <div class="scan-corner corner-br"></div>

                    {{-- HTML5 QR Code Mount / Video Feed --}}
                    <div id="qrReader" class="h-full w-full object-cover"></div>
                    <video id="qrVideoFeed" class="h-full w-full object-cover hidden" playsinline muted autoplay></video>
                </div>

                <div class="mt-4 text-center">
                    <p class="text-xs text-slate-400 font-mono mb-2">
                        Posisikan QR Code Siswa di dalam kotak hijau
                    </p>
                    <div id="scannerAlert" class="hidden rounded-xl p-3 text-xs font-bold text-center"></div>
                </div>

                {{-- Input USB Barcode Scanner / Manual NIS --}}
                <div class="mt-5 pt-4 border-t border-slate-800 space-y-2">
                    <div class="flex items-center justify-between text-xs text-slate-400">
                        <span class="font-bold flex items-center gap-1.5">
                            <i class="bi bi-upc-scan text-emerald-400"></i> Barcode Scanner USB / Input Manual NIS:
                        </span>
                    </div>
                    <form onsubmit="event.preventDefault(); submitManualQr();" class="flex gap-2">
                        <input type="text" id="manualQrInput" placeholder="Scan Barcode USB atau ketik NIS (misal: 2025001)..." 
                            class="w-full rounded-xl border border-slate-700 bg-slate-900 px-3.5 py-2 text-xs font-mono text-white placeholder-slate-500 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 focus:outline-none">
                        <button type="submit" class="rounded-xl bg-emerald-600 hover:bg-emerald-500 px-4 py-2 text-xs font-extrabold text-white transition shrink-0 cursor-pointer">
                            Verifikasi
                        </button>
                    </form>
                </div>

                {{-- Fitur Simulasi Uji Coba Cepat (Untuk Demo Showcase) --}}
                <div class="mt-4 pt-3 border-t border-slate-800/80">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[11px] font-bold text-amber-400 flex items-center gap-1">
                            <i class="bi bi-lightning-charge-fill"></i> Uji Coba Cepat (Demo Presensi):
                        </span>
                    </div>
                    <div class="flex gap-2">
                        <select id="simStudentSelect" class="w-full rounded-xl border border-slate-700 bg-slate-900 px-3 py-2 text-xs font-medium text-slate-200 focus:border-amber-400 focus:outline-none">
                            <option value="">-- Pilih Siswa Untuk Demo Scan --</option>
                            @foreach($students as $st)
                                <option value="LMS-{{ $st->nis }}">{{ $st->name }} (NIS: {{ $st->nis }} - {{ $st->class?->name ?? 'Kelas' }})</option>
                            @endforeach
                        </select>
                        <button type="button" onclick="submitSimulationScan()" class="rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black px-4 py-2 text-xs transition shrink-0 cursor-pointer">
                            Simulasi Scan
                        </button>
                    </div>
                </div>

            </div>

        </div>

        {{-- KOLOM KANAN: HASIL SCAN TERAKHIR & FEED REAL-TIME --}}
        <div class="w-full lg:w-5/12 space-y-5" style="flex: 1 1 42%; min-width: 0;">

            {{-- KARTU HASIL SCAN TERAKHIR --}}
            <div class="rounded-3xl bg-white border border-slate-200 p-5 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h6 class="text-xs font-black uppercase tracking-wider text-slate-800 flex items-center gap-2 mb-0">
                        <i class="bi bi-patch-check-fill text-emerald-600"></i> Hasil Scan Terakhir
                    </h6>
                    <span id="scanStatusBadge" class="text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-500">
                        Menunggu Scan...
                    </span>
                </div>

                {{-- Tampilan Siswa yang Ter-scan --}}
                <div id="latestScanDisplay">
                    @if($recentLogs->isNotEmpty())
                        @php $last = $recentLogs->first(); @endphp
                        <div class="flex items-center gap-3.5 p-3.5 rounded-2xl bg-emerald-50/70 border border-emerald-200">
                            <img src="{{ $last->student?->photo_url ?? 'https://ui-avatars.com/api/?name=Siswa' }}" class="h-14 w-14 rounded-2xl object-cover ring-2 ring-emerald-400 shadow-sm shrink-0" alt="Foto">
                            <div class="min-w-0 flex-1">
                                <span class="inline-flex items-center gap-1 text-[10px] font-black uppercase text-emerald-800 bg-emerald-100 px-2 py-0.5 rounded-full mb-1">
                                    <i class="bi bi-check-circle-fill"></i> Terverifikasi Hadir
                                </span>
                                <h5 class="text-sm font-black text-slate-900 truncate mb-0">{{ $last->student?->name }}</h5>
                                <div class="text-xs text-slate-500 font-mono mt-0.5">
                                    NIS: {{ $last->student?->nis }} • {{ $last->student?->class?->name ?? 'Kelas' }}
                                </div>
                                <div class="text-[11px] text-emerald-700 font-bold mt-1">
                                    Pukul: {{ $last->checked_in_at?->format('H:i:s') ?? '-' }} WIB
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="py-8 text-center text-slate-400 font-medium text-xs">
                            <i class="bi bi-upc-scan text-3xl text-slate-300 block mb-2"></i>
                            Belum ada siswa yang discan pada sesi ini.
                        </div>
                    @endif
                </div>
            </div>

            {{-- FEED SISWA YANG TELAH HADIR HARI INI --}}
            <div class="rounded-3xl bg-white border border-slate-200 p-5 shadow-sm space-y-3">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h6 class="text-xs font-black uppercase tracking-wider text-slate-800 flex items-center gap-2 mb-0">
                        <i class="bi bi-list-check text-brand-600"></i> Riwayat Scan Hari Ini
                    </h6>
                    <span class="text-xs font-bold text-slate-400" id="feedCount">{{ $recentLogs->count() }} Terdata</span>
                </div>

                <div class="space-y-2 max-h-[350px] overflow-y-auto pr-1" id="scannedFeedList">
                    @forelse($recentLogs as $log)
                        <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 hover:bg-slate-100/80 transition text-xs border border-slate-100">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <img src="{{ $log->student?->photo_url ?? 'https://ui-avatars.com/api/?name=Siswa' }}" class="h-8 w-8 rounded-lg object-cover border border-slate-200 shrink-0" alt="">
                                <div class="min-w-0">
                                    <div class="font-bold text-slate-900 truncate">{{ $log->student?->name }}</div>
                                    <div class="text-[11px] text-slate-500 font-mono">{{ $log->student?->class?->name ?? 'Kelas' }} • {{ $log->student?->nis }}</div>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="font-black text-emerald-600 font-mono">{{ $log->checked_in_at?->format('H:i:s') }}</span>
                                <div class="text-[10px] text-slate-400">WIB</div>
                            </div>
                        </div>
                    @empty
                        <div class="py-6 text-center text-slate-400 text-xs" id="emptyFeedText">
                            Riwayat pemindaian akan muncul di sini.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

</div>

@push('scripts')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    const SCAN_API_URL = '{{ route("attendance.scan-student-qr") }}';
    const CSRF_TOKEN = '{{ csrf_token() }}';

    let html5QrCode = null;
    let isProcessing = false;
    let currentFacingMode = "environment"; // "environment" (belakang) atau "user" (depan)
    let audioCtx = null;

    // Audio Beep Synthesizer
    function playBeep(success = true) {
        try {
            audioCtx = audioCtx || new (window.AudioContext || window.webkitAudioContext)();
            if (audioCtx.state === 'suspended') audioCtx.resume();
            
            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();
            osc.frequency.setValueAtTime(success ? 1350 : 320, audioCtx.currentTime);
            gain.gain.setValueAtTime(0.25, audioCtx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + (success ? 0.22 : 0.4));
            osc.connect(gain);
            gain.connect(audioCtx.destination);
            osc.start();
            osc.stop(audioCtx.currentTime + (success ? 0.22 : 0.4));
        } catch(e) {}
    }

    // Start Camera Scanner
    async function startScanner() {
        const qrReaderDiv = document.getElementById('qrReader');
        const alertEl = document.getElementById('scannerAlert');

        if (typeof Html5Qrcode === 'undefined') {
            showScannerAlert('Library scanner sedang dimuat...', 'warning');
            setTimeout(startScanner, 1000);
            return;
        }

        try {
            if (!html5QrCode) {
                html5QrCode = new Html5Qrcode("qrReader");
            }

            await html5QrCode.start(
                { facingMode: currentFacingMode },
                { fps: 15, qrbox: { width: 280, height: 280 } },
                (decodedText) => {
                    if (!isProcessing) {
                        handleQrPayload(decodedText);
                    }
                },
                (errorMessage) => {
                    // Frame scan loop
                }
            );

            if (alertEl) alertEl.classList.add('hidden');
        } catch (err) {
            console.error('Kamera error:', err);
            showScannerAlert('Kamera tidak dapat diakses (' + err.message + '). Silakan gunakan input Barcode USB / Manual.', 'danger');
        }
    }

    // Toggle Camera Facing
    async function switchCameraFacing() {
        if (!html5QrCode) return;
        try {
            await html5QrCode.stop();
            currentFacingMode = currentFacingMode === "environment" ? "user" : "environment";
            startScanner();
        } catch (e) {
            console.error(e);
        }
    }

    // Handle Payload QR Code
    async function handleQrPayload(code) {
        if (isProcessing) return;
        isProcessing = true;

        showScannerAlert('Memproses: ' + code + '...', 'info');

        try {
            const res = await fetch(SCAN_API_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    qr_code: code,
                    device_type: 'Scanner Kamera Guru/Admin',
                    browser: navigator.userAgent.includes('Mobile') ? 'Mobile Camera' : 'Desktop/Webcam'
                })
            });

            const data = await res.json();

            if (res.ok && data.success) {
                playBeep(true);
                showScannerAlert(data.message, 'success');
                renderLatestScan(data.student, data.already_checked_in);
                prependToFeed(data.student);
                incrementTodayCount();
            } else {
                playBeep(false);
                showScannerAlert(data.message || 'Gagal memverifikasi siswa.', 'danger');
            }
        } catch (e) {
            playBeep(false);
            showScannerAlert('Kesalahan koneksi: ' + e.message, 'danger');
        } finally {
            // Delay 1.8 detik sebelum mengizinkan scan berikutnya agar tidak multi-trigger
            setTimeout(() => {
                isProcessing = false;
            }, 1800);
        }
    }

    // Render Latest Scan Result Card
    function renderLatestScan(student, alreadyChecked) {
        const container = document.getElementById('latestScanDisplay');
        const badge = document.getElementById('scanStatusBadge');

        if (badge) {
            badge.className = 'text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800';
            badge.textContent = alreadyChecked ? 'Sudah Hadir' : 'Berhasil Hadir';
        }

        if (container) {
            container.innerHTML = `
                <div class="flex items-center gap-3.5 p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 transition-all transform scale-100 animate-fade-in">
                    <img src="${student.photo_url || 'https://ui-avatars.com/api/?name=Siswa'}" class="h-14 w-14 rounded-2xl object-cover ring-2 ring-emerald-500 shadow-sm shrink-0" alt="Foto">
                    <div class="min-w-0 flex-1">
                        <span class="inline-flex items-center gap-1 text-[10px] font-black uppercase text-emerald-800 bg-emerald-100 px-2 py-0.5 rounded-full mb-1">
                            <i class="bi bi-check-circle-fill text-emerald-600"></i> ${alreadyChecked ? 'Sudah Tercatat' : 'Verifikasi Sukses'}
                        </span>
                        <h5 class="text-sm font-black text-slate-900 truncate mb-0">${student.name}</h5>
                        <div class="text-xs text-slate-500 font-mono mt-0.5">
                            NIS: ${student.nis} • ${student.class_name}
                        </div>
                        <div class="text-[11px] text-emerald-700 font-bold mt-1 font-mono">
                            Jam Hadir: ${student.check_in_time}
                        </div>
                    </div>
                </div>
            `;
        }
    }

    // Prepend to Live Feed List
    function prependToFeed(student) {
        const feed = document.getElementById('scannedFeedList');
        const emptyText = document.getElementById('emptyFeedText');
        if (emptyText) emptyText.remove();

        const row = document.createElement('div');
        row.className = 'flex items-center justify-between p-2.5 rounded-xl bg-emerald-50/60 border border-emerald-100 text-xs animate-fade-in';
        row.innerHTML = `
            <div class="flex items-center gap-2.5 min-w-0">
                <img src="${student.photo_url || 'https://ui-avatars.com/api/?name=Siswa'}" class="h-8 w-8 rounded-lg object-cover border border-slate-200 shrink-0" alt="">
                <div class="min-w-0">
                    <div class="font-bold text-slate-900 truncate">${student.name}</div>
                    <div class="text-[11px] text-slate-500 font-mono">${student.class_name} • ${student.nis}</div>
                </div>
            </div>
            <div class="text-right shrink-0">
                <span class="font-black text-emerald-600 font-mono">${student.check_in_time.split(' ')[0]}</span>
                <div class="text-[10px] text-slate-400">WIB</div>
            </div>
        `;

        if (feed) {
            feed.insertBefore(row, feed.firstChild);
        }
    }

    function incrementTodayCount() {
        const el = document.getElementById('todayCountDisplay');
        if (el) {
            el.textContent = parseInt(el.textContent || 0) + 1;
        }
    }

    function showScannerAlert(msg, type = 'info') {
        const el = document.getElementById('scannerAlert');
        if (!el) return;

        const colorMap = {
            info: 'bg-slate-800 text-slate-200 border border-slate-700',
            success: 'bg-emerald-950/80 text-emerald-300 border border-emerald-800',
            danger: 'bg-rose-950/80 text-rose-300 border border-rose-800',
            warning: 'bg-amber-950/80 text-amber-300 border border-amber-800',
        };

        el.className = `rounded-xl p-3 text-xs font-bold text-center mt-2 ${colorMap[type] || colorMap.info}`;
        el.textContent = msg;
        el.classList.remove('hidden');
    }

    // Manual Input Handler
    function submitManualQr() {
        const input = document.getElementById('manualQrInput');
        if (!input || !input.value.trim()) return;
        const code = input.value.trim();
        input.value = '';
        handleQrPayload(code);
    }

    // Simulation Scan Handler
    function submitSimulationScan() {
        const select = document.getElementById('simStudentSelect');
        if (!select || !select.value) {
            showScannerAlert('Pilih siswa dari daftar simulasi terlebih dahulu.', 'warning');
            return;
        }
        handleQrPayload(select.value);
    }

    // Auto Focus input for USB scanner
    document.addEventListener('DOMContentLoaded', () => {
        startScanner();
        const manualInput = document.getElementById('manualQrInput');
        if (manualInput) {
            manualInput.focus();
        }
    });
</script>
@endpush
@endsection
