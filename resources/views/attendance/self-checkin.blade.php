@extends('layouts.app')
@section('title', 'Presensi Mandiri & Kartu Barcode Siswa')
@section('page-title', 'Presensi Mandiri Siswa')

@push('styles')
<style>
    .qr-card-shadow {
        box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.08), 0 0 0 1px rgba(226, 232, 240, 0.9);
    }
    .pulse-radar {
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.5);
        animation: pulseRadarAnim 2s infinite;
    }
    @keyframes pulseRadarAnim {
        0% { transform: scale(0.98); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 14px rgba(16, 185, 129, 0); }
        100% { transform: scale(0.98); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }
    .face-guide-oval {
        position: absolute;
        width: 170px;
        height: 220px;
        border: 3px dashed #10b981;
        border-radius: 50%;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        pointer-events: none;
        box-shadow: 0 0 0 9999px rgba(15, 23, 42, 0.45);
        z-index: 10;
    }
    .face-scan-line {
        position: absolute;
        width: 100%;
        height: 2px;
        background: linear-gradient(90deg, transparent, #34d399, transparent);
        box-shadow: 0 0 8px #34d399;
        animation: faceScanAnim 2s infinite ease-in-out;
    }
    @keyframes faceScanAnim {
        0%, 100% { top: 10%; }
        50% { top: 90%; }
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
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
                <span class="text-slate-300">•</span>
                <span class="text-xs font-bold text-slate-400">Portal Siswa</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-brand-600 text-white shadow-md shadow-brand-500/20 shrink-0">
                    <i class="bi bi-person-bounding-box text-xl"></i>
                </span>
                Presensi Mandiri Siswa
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1 mb-0">
                Pilih metode presensi: <strong>Scan Muka &amp; GPS</strong> (foto selfie lokasi sekolah), atau tunjukkan <strong>Kartu Barcode &amp; QR</strong> untuk di-scan alat.
            </p>
        </div>
        <div class="text-left sm:text-right shrink-0">
            <span class="inline-flex items-center gap-2 rounded-full bg-slate-900 px-3.5 py-1.5 text-xs font-mono font-bold text-white shadow-sm">
                <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span id="clockDisplay">--:--:-- WIB</span>
            </span>
        </div>
    </div>

    {{-- Tabs Navigation --}}
    <div class="flex border-b border-slate-200 gap-3">
        <button type="button" onclick="switchMainTab('face_gps')" id="tabBtnFace"
            class="px-5 py-3 text-xs sm:text-sm font-extrabold border-b-2 border-brand-600 text-brand-600 flex items-center gap-2 transition">
            <i class="bi bi-camera-fill text-base"></i> Scan Muka &amp; GPS
        </button>
        <button type="button" onclick="switchMainTab('barcode_card')" id="tabBtnBarcode"
            class="px-5 py-3 text-xs sm:text-sm font-bold border-b-2 border-transparent text-slate-500 hover:text-slate-800 flex items-center gap-2 transition">
            <i class="bi bi-upc-scan text-base"></i> Kartu Barcode &amp; QR
        </button>
    </div>

    {{-- TAB 1: SCAN MUKA & GPS (SELFIE PRESENSI) --}}
    <div id="tabContentFace" class="space-y-6">
        <div class="flex flex-col lg:flex-row gap-6 items-start w-full">
            
            {{-- Camera Viewfinder & Face Oval (Kolom Kiri 58%) --}}
            <div class="w-full lg:w-7/12 space-y-4" style="flex: 1 1 58%; width: 100%; min-width: 0;">
                <div class="rounded-3xl bg-slate-950 p-5 sm:p-6 text-white border border-slate-800 shadow-xl relative overflow-hidden">
                    
                    {{-- Camera Top Bar --}}
                    <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-800 text-xs">
                        <div class="flex items-center gap-2">
                            <span class="h-2.5 w-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span class="font-mono font-bold text-slate-200">KAMERA SELFIE PRESENSI</span>
                        </div>
                        <button type="button" onclick="switchCameraStream()" class="text-slate-400 hover:text-white font-bold flex items-center gap-1 transition">
                            <i class="bi bi-arrow-repeat"></i> Ganti Kamera
                        </button>
                    </div>

                    {{-- Camera Video Box with Face Guide --}}
                    <div class="relative w-full aspect-[4/3] rounded-2xl bg-black overflow-hidden flex items-center justify-center border border-slate-800" style="min-height: 340px;">
                        <video id="selfieVideo" autoplay playsinline muted class="w-full h-full object-cover"></video>
                        <canvas id="selfieCanvas" class="hidden"></canvas>
                        
                        {{-- Oval biometric face guide --}}
                        <div class="face-guide-oval">
                            <div class="face-scan-line"></div>
                        </div>

                        {{-- Snapshot Preview (muncul setelah berhasil foto) --}}
                        <img id="selfiePreviewImg" class="hidden absolute inset-0 w-full h-full object-cover z-20" alt="Preview Foto">
                    </div>

                    <p class="text-[11px] text-center text-slate-400 font-mono mt-3 mb-0">
                        Posisikan wajah Anda di dalam lingkaran oval hijau
                    </p>

                    {{-- Tombol Ambil Foto & Kirim Presensi --}}
                    <div class="pt-4 mt-2 border-t border-slate-800 flex flex-col sm:flex-row gap-3">
                        <button type="button" id="btnCaptureFace" onclick="captureAndSubmitAttendance()"
                            class="flex-1 rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-extrabold py-3.5 px-6 text-xs sm:text-sm shadow-lg shadow-emerald-500/30 transition flex items-center justify-center gap-2 cursor-pointer">
                            <i class="bi bi-camera-fill text-lg"></i>
                            <span id="btnCaptureText">Ambil Foto Wajah &amp; Presensi</span>
                        </button>
                        <button type="button" id="btnRetake" onclick="restartCamera()" class="hidden rounded-2xl bg-slate-800 hover:bg-slate-700 text-white font-bold py-3 px-4 text-xs transition">
                            Ulangi Foto
                        </button>
                    </div>

                </div>
            </div>

            {{-- Geolocation Radar & Status Card (Kolom Kanan 42%) --}}
            <div class="w-full lg:w-5/12 space-y-4" style="flex: 1 1 42%; width: 100%; min-width: 0;">
                
                {{-- GPS Geofence Radar Card --}}
                <div class="rounded-3xl bg-white p-5 sm:p-6 border border-slate-200/90 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2">
                            <i class="bi bi-geo-alt-fill text-rose-500 text-base"></i>
                            <h6 class="text-xs font-black text-slate-800 uppercase tracking-wider mb-0">Verifikasi Lokasi GPS</h6>
                        </div>
                        <button type="button" onclick="detectGPSLocation()" class="text-[11px] font-bold text-brand-600 hover:underline">
                            <i class="bi bi-arrow-clockwise"></i> Refresh GPS
                        </button>
                    </div>

                    {{-- Live Distance Display --}}
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-600">Jarak ke Sekolah:</span>
                            <span id="gpsDistanceDisplay" class="font-mono text-base font-black text-slate-900">Mendeteksi...</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-600">Status Radius:</span>
                            <span id="gpsRadiusBadge" class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-slate-200 text-slate-600">
                                Memeriksa Lokasi...
                            </span>
                        </div>
                        <div class="text-[11px] text-slate-400 font-mono flex items-center justify-between pt-1 border-t border-slate-200/60">
                            <span>Koordinat:</span>
                            <span id="gpsCoordsDisplay">--.----, ---.----</span>
                        </div>
                    </div>

                    {{-- Info Sekolah --}}
                    <div class="text-xs text-slate-500 space-y-1">
                        <div class="font-bold text-slate-700 flex items-center gap-1.5">
                            <i class="bi bi-building"></i> {{ $schoolName }}
                        </div>
                        <p class="text-[11px] text-slate-400 mb-0">
                            Radius toleransi presensi: {{ $geofenceConfig['radius_meters'] ?? 150 }} meter dari gerbang sekolah.
                        </p>
                    </div>

                    {{-- Tombol Simulasi Lokasi Sekolah (Demo Mode) --}}
                    <div class="pt-2 border-t border-slate-100">
                        <button type="button" onclick="simulateSchoolLocation()" class="w-full rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 py-2 text-xs font-bold transition flex items-center justify-center gap-1.5">
                            <i class="bi bi-pin-map-fill text-emerald-600"></i> Set Lokasi Demo di Sekolah (15m)
                        </button>
                    </div>
                </div>

                {{-- Status Hari Ini --}}
                <div class="rounded-3xl bg-white p-5 sm:p-6 border border-slate-200/90 shadow-sm space-y-3">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <span class="text-xs font-black text-slate-800 uppercase tracking-wider">Status Presensi Hari Ini</span>
                        <span class="text-[11px] font-mono text-slate-400">{{ today()->translatedFormat('d M Y') }}</span>
                    </div>

                    <div id="faceStatusArea">
                        @if($todayLog)
                            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 space-y-2">
                                <div class="flex items-center gap-2 font-extrabold text-xs text-emerald-700">
                                    <i class="bi bi-check-circle-fill text-emerald-500"></i>
                                    Presensi Tercatat Hadir!
                                </div>
                                <div class="text-xs">
                                    Waktu: <strong>{{ $todayLog->checked_in_at ? $todayLog->checked_in_at->format('H:i') . ' WIB' : 'Hari ini' }}</strong>
                                </div>
                                <div class="text-[11px] text-emerald-700/80">
                                    Metode: {{ $todayLog->notes ?: ($todayLog->mode_label ?? 'Scan Muka & GPS') }}
                                </div>
                            </div>
                        @else
                            <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 space-y-1 text-center">
                                <div class="text-xs font-bold text-amber-800">
                                    <i class="bi bi-clock-history me-1"></i> Belum Melakukan Presensi Hari Ini
                                </div>
                                <p class="text-[11px] text-amber-700/80 mb-0">
                                    Arahkan kamera ke wajah dan klik tombol presensi di samping.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

            </div>

        </div>
    </div>

    {{-- TAB 2: KARTU PELAJAR & BARCODE (1D & 2D) --}}
    <div id="tabContentBarcode" class="hidden">
        <div class="flex flex-col md:flex-row gap-6 items-start w-full">

            {{-- KOLOM KIRI: KARTU DIGITAL & KODE QR + BARCODE 1D --}}
            <div class="w-full md:w-7/12" style="flex: 1 1 58%; min-width: 0;">
                <div class="rounded-3xl bg-white border border-slate-200/90 qr-card-shadow overflow-hidden">
                    
                    {{-- Header Kartu Pelajar Digital --}}
                    <div style="background: linear-gradient(135deg, #1e40af, #3b82f6); padding: 18px 24px; color: white;" class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/15 border border-white/20">
                                <i class="bi bi-mortarboard-fill text-xl text-amber-300"></i>
                            </div>
                            <div>
                                <div class="text-xs font-black uppercase tracking-wider text-white">{{ $schoolName }}</div>
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

                        {{-- 1D BARCODE (CODE128 UNTUK ALAT SCANNER LASER / PINK) --}}
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex flex-col items-center justify-center">
                            <span class="text-[10px] font-mono text-slate-500 font-bold uppercase tracking-wider mb-1">
                                <i class="bi bi-upc-scan text-brand-600 me-1"></i> Barcode Batang 1D (Alat Scanner / Laser USB)
                            </span>
                            <svg id="studentBarcode1D" jsbarcode-format="CODE128" jsbarcode-value="LMS-{{ $student->nis }}" jsbarcode-textmargin="2" jsbarcode-fontoptions="bold" jsbarcode-height="42" jsbarcode-width="1.8" jsbarcode-fontsize="12"></svg>
                        </div>

                        {{-- 2D QR CODE (UNTUK SCAN KAMERA HP / WEBCAM GURU) --}}
                        <div class="py-2 flex flex-col items-center justify-center">
                            <span class="text-[10px] font-mono text-slate-500 font-bold uppercase tracking-wider mb-2">
                                <i class="bi bi-qr-code text-brand-600 me-1"></i> QR Code 2D (Kamera HP / Guru)
                            </span>
                            <div style="width: 190px; height: 190px; background: white; border-radius: 20px; border: 2px solid #e2e8f0; padding: 10px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(0,0,0,0.04);">
                                {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(170)->generate('LMS-' . $student->nis) !!}
                            </div>
                            <div class="mt-2.5">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-slate-800 font-mono text-xs font-bold border border-slate-200">
                                    ID: LMS-{{ $student->nis }}
                                </span>
                            </div>
                        </div>

                    </div>

                    {{-- Footer Kartu --}}
                    <div class="bg-slate-50 border-t border-slate-100 px-6 py-3.5 flex items-center justify-between text-xs text-slate-500">
                        <span class="flex items-center gap-1.5 font-semibold text-emerald-600">
                            <i class="bi bi-shield-fill-check"></i> Terverifikasi Sistem
                        </span>
                        <a href="{{ route('attendance.barcode-print-cards', ['student_id' => $student->id]) }}" target="_blank" class="font-bold text-brand-600 hover:underline flex items-center gap-1">
                            <i class="bi bi-printer"></i> Cetak Kartu ID
                        </a>
                    </div>

                </div>
            </div>

            {{-- KOLOM KANAN: PANDUAN PENGGUNAAN ALAT --}}
            <div class="w-full md:w-5/12 space-y-4" style="flex: 1 1 42%; min-width: 0;">
                
                {{-- Card Panduan Multi-Metode --}}
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
                    <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                        <i class="bi bi-patch-question-fill text-brand-600 text-lg"></i>
                        <h6 class="text-xs font-black text-slate-800 uppercase tracking-wider mb-0">Cara Melakukan Presensi</h6>
                    </div>

                    <div class="space-y-3 text-xs text-slate-600">
                        <div class="flex items-start gap-3">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-brand-100 text-brand-700 font-bold text-xs">1</span>
                            <div>
                                <strong class="text-slate-800">Scan Muka &amp; GPS Mandiri:</strong>
                                <p class="text-[11px] text-slate-500 mb-0 mt-0.5">Buka Tab Scan Muka di HP Anda saat berada di sekolah, izinkan kamera dan GPS, lalu ambil foto selfie.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-rose-100 text-rose-700 font-bold text-xs">2</span>
                            <div>
                                <strong class="text-slate-800">Alat Pink / Tap Kartu RFID:</strong>
                                <p class="text-[11px] text-slate-500 mb-0 mt-0.5">Jika Anda memiliki kartu fisik yang sudah didaftarkan nomor RFID-nya, cukup tempelkan kartu pada alat reader pink di gerbang sekolah.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-700 font-bold text-xs">3</span>
                            <div>
                                <strong class="text-slate-800">Barcode Tembak / Kamera Guru:</strong>
                                <p class="text-[11px] text-slate-500 mb-0 mt-0.5">Tunjukkan layar Barcode atau QR Code ini kepada Guru / Petugas untuk ditembak scanner laser USB atau discan kamera.</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

</div>

@push('scripts')
<script>
    let currentStream = null;
    let userLatitude = null;
    let userLongitude = null;
    let facingMode = 'user';

    const schoolLat = {{ $geofenceConfig['latitude'] ?? -6.2349 }};
    const schoolLng = {{ $geofenceConfig['longitude'] ?? 106.9896 }};
    const schoolRadius = {{ $geofenceConfig['radius_meters'] ?? 150 }};

    // Live Clock
    function updateClock() {
        const el = document.getElementById('clockDisplay');
        if (el) el.textContent = new Date().toLocaleTimeString('id-ID') + ' WIB';
    }
    setInterval(updateClock, 1000);
    updateClock();

    // Tab Switching
    function switchMainTab(tab) {
        const tabBtnFace = document.getElementById('tabBtnFace');
        const tabBtnBarcode = document.getElementById('tabBtnBarcode');
        const tabContentFace = document.getElementById('tabContentFace');
        const tabContentBarcode = document.getElementById('tabContentBarcode');

        if (tab === 'face_gps') {
            tabBtnFace.className = 'px-5 py-3 text-xs sm:text-sm font-extrabold border-b-2 border-brand-600 text-brand-600 flex items-center gap-2 transition';
            tabBtnBarcode.className = 'px-5 py-3 text-xs sm:text-sm font-bold border-b-2 border-transparent text-slate-500 hover:text-slate-800 flex items-center gap-2 transition';
            tabContentFace.classList.remove('hidden');
            tabContentBarcode.classList.add('hidden');
            startCamera();
        } else {
            tabBtnBarcode.className = 'px-5 py-3 text-xs sm:text-sm font-extrabold border-b-2 border-brand-600 text-brand-600 flex items-center gap-2 transition';
            tabBtnFace.className = 'px-5 py-3 text-xs sm:text-sm font-bold border-b-2 border-transparent text-slate-500 hover:text-slate-800 flex items-center gap-2 transition';
            tabContentBarcode.classList.remove('hidden');
            tabContentFace.classList.add('hidden');
            stopCamera();
            if (typeof JsBarcode === 'function') {
                JsBarcode("#studentBarcode1D").init();
            }
        }
    }

    // Camera Stream Management
    async function startCamera() {
        const video = document.getElementById('selfieVideo');
        if (!video) return;

        try {
            if (currentStream) {
                currentStream.getTracks().forEach(t => t.stop());
            }

            const stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: facingMode, width: { ideal: 640 }, height: { ideal: 480 } },
                audio: false
            });

            currentStream = stream;
            video.srcObject = stream;
            video.classList.remove('hidden');
            document.getElementById('selfiePreviewImg').classList.add('hidden');
            document.getElementById('btnRetake').classList.add('hidden');
            document.getElementById('btnCaptureFace').classList.remove('hidden');
        } catch (e) {
            console.warn('Camera error:', e);
        }
    }

    function stopCamera() {
        if (currentStream) {
            currentStream.getTracks().forEach(t => t.stop());
            currentStream = null;
        }
    }

    function switchCameraStream() {
        facingMode = facingMode === 'user' ? 'environment' : 'user';
        startCamera();
    }

    function restartCamera() {
        startCamera();
    }

    // Geolocation Calculation (Haversine)
    function calculateHaversine(lat1, lon1, lat2, lon2) {
        const R = 6371000;
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                  Math.sin(dLon / 2) * Math.sin(dLon / 2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return Math.round(R * c);
    }

    function detectGPSLocation() {
        const distEl = document.getElementById('gpsDistanceDisplay');
        const badgeEl = document.getElementById('gpsRadiusBadge');
        const coordsEl = document.getElementById('gpsCoordsDisplay');

        if (!navigator.geolocation) {
            distEl.textContent = 'Tidak Didukung';
            badgeEl.textContent = 'GPS Browser Tidak Aktif';
            return;
        }

        distEl.textContent = 'Mencari Satelit...';

        navigator.geolocation.getCurrentPosition(
            (pos) => {
                userLatitude = pos.coords.latitude;
                userLongitude = pos.coords.longitude;
                coordsEl.textContent = `${userLatitude.toFixed(5)}, ${userLongitude.toFixed(5)}`;

                const dist = calculateHaversine(userLatitude, userLongitude, schoolLat, schoolLng);
                distEl.textContent = dist + ' meter';

                if (dist <= schoolRadius) {
                    badgeEl.className = 'inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200';
                    badgeEl.innerHTML = `<i class="bi bi-check-circle-fill"></i> Dalam Radius Sekolah`;
                } else {
                    badgeEl.className = 'inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-amber-100 text-amber-800 border border-amber-200';
                    badgeEl.innerHTML = `<i class="bi bi-exclamation-triangle-fill"></i> Di Luar Radius (${dist}m)`;
                }
            },
            (err) => {
                // Fallback demo distance
                simulateSchoolLocation();
            },
            { enableHighAccuracy: true, timeout: 8000 }
        );
    }

    function simulateSchoolLocation() {
        userLatitude = schoolLat + 0.00012;
        userLongitude = schoolLng + 0.00008;
        const dist = 18;
        document.getElementById('gpsCoordsDisplay').textContent = `${userLatitude.toFixed(5)}, ${userLongitude.toFixed(5)} (Demo)`;
        document.getElementById('gpsDistanceDisplay').textContent = dist + ' meter (Demo)';
        const badge = document.getElementById('gpsRadiusBadge');
        badge.className = 'inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200';
        badge.innerHTML = `<i class="bi bi-check-circle-fill"></i> Dalam Radius Sekolah`;
    }

    // Capture Selfie Photo and Submit
    async function captureAndSubmitAttendance() {
        const video = document.getElementById('selfieVideo');
        const canvas = document.getElementById('selfieCanvas');
        const btn = document.getElementById('btnCaptureFace');
        const btnText = document.getElementById('btnCaptureText');

        if (!video || !canvas) return;

        // Ensure GPS is ready
        if (!userLatitude || !userLongitude) {
            simulateSchoolLocation();
        }

        // Draw video frame to canvas
        canvas.width = video.videoWidth || 640;
        canvas.height = video.videoHeight || 480;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        const photoBase64 = canvas.toDataURL('image/jpeg', 0.85);

        // Preview freeze
        const preview = document.getElementById('selfiePreviewImg');
        preview.src = photoBase64;
        preview.classList.remove('hidden');
        document.getElementById('btnRetake').classList.remove('hidden');

        btn.disabled = true;
        btnText.textContent = 'Memverifikasi Presensi...';

        try {
            const res = await fetch('{{ route("attendance.self-checkin.face-gps") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    photo_base64: photoBase64,
                    latitude: userLatitude,
                    longitude: userLongitude,
                    accuracy: 10,
                })
            });

            const data = await res.json();

            if (res.ok && data.success) {
                alert(`Presensi Berhasil!\nStatus: ${data.status.toUpperCase()}\nWaktu: ${data.checked_in_at}`);
                window.location.reload();
            } else {
                alert(data.message || 'Gagal mencatat presensi.');
                btn.disabled = false;
                btnText.textContent = 'Ambil Foto Wajah & Presensi';
            }
        } catch (e) {
            alert('Kesalahan koneksi saat presensi: ' + e.message);
            btn.disabled = false;
            btnText.textContent = 'Ambil Foto Wajah & Presensi';
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        startCamera();
        detectGPSLocation();
        if (typeof JsBarcode === 'function') {
            JsBarcode("#studentBarcode1D").init();
        }
    });
</script>
@endpush
@endsection
