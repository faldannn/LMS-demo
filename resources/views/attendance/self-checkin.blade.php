@extends('layouts.app')
@section('title', 'Presensi Mandiri Siswa')
@section('page-title', 'Presensi Mandiri Siswa')

@section('content')
<div class="mx-auto max-w-4xl space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <div class="mb-1">
                <a href="{{ route('attendance.mine') }}" class="inline-flex items-center gap-1.5 text-xs font-extrabold text-slate-500 hover:text-brand-600 transition">
                    <i class="bi bi-arrow-left"></i> Kembali ke Halaman Absensi
                </a>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900">Presensi Mandiri Siswa</h1>
            <p class="text-sm text-slate-500">Presensi masuk cepat dengan foto kamera langsung dan validasi lokasi GPS sekolah.</p>
        </div>
        <div class="text-right">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-brand-50 px-3 py-1 text-xs font-bold text-brand-700 border border-brand-200">
                <i class="bi bi-clock"></i> <span id="clockDisplay">--:--:--</span>
            </span>
        </div>
    </div>

    {{-- Kartu Info Siswa & Status Presensi --}}
    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        {{-- Profil Siswa --}}
        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200/70 md:col-span-1">
            <div class="flex flex-col items-center text-center">
                <img src="{{ $student->photo_url }}" class="mb-3 h-20 w-20 rounded-full object-cover shadow-sm ring-2 ring-brand-100" alt="{{ $student->name }}">
                <h6 class="text-base font-bold text-slate-900">{{ $student->name }}</h6>
                <p class="text-xs text-slate-500 font-mono mt-0.5">NIS: {{ $student->nis }}</p>
                <div class="mt-3 flex flex-wrap justify-center gap-1.5">
                    <span class="rounded-full bg-brand-50 px-2.5 py-0.5 text-[11px] font-bold text-brand-700">
                        {{ $student->class?->name ?? 'Tanpa Kelas' }}
                    </span>
                    <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-[11px] font-semibold text-slate-600 uppercase">
                        Sesi {{ $session }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Card Absen Masuk --}}
        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200/70 md:col-span-2">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-3">
                <div class="flex items-center gap-2">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600 font-bold">
                        <i class="bi bi-box-arrow-in-right text-lg"></i>
                    </div>
                    <div>
                        <h6 class="text-sm font-bold text-slate-900">Status Presensi Hari Ini</h6>
                        <p class="text-[11px] text-slate-500">Jam Buka Presensi: {{ substr($checkinStart, 0, 5) }} WIB</p>
                    </div>
                </div>
                @if($todayLog)
                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-800">Sudah Presensi</span>
                @else
                    <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-800">Belum Presensi</span>
                @endif
            </div>

            @if($todayLog)
                <div class="flex items-center gap-4 py-2">
                    @if($todayLog->photo_path)
                        <img src="{{ asset('storage/' . $todayLog->photo_path) }}" class="h-16 w-16 rounded-xl object-cover ring-2 ring-emerald-400 shadow-sm" alt="Foto Presensi">
                    @else
                        <div class="flex h-16 w-16 items-center justify-center rounded-xl bg-slate-100 text-slate-400">
                            <i class="bi bi-person text-2xl"></i>
                        </div>
                    @endif
                    <div class="flex-1">
                        <p class="text-xs text-slate-500">Waktu Masuk:</p>
                        <p class="text-base font-extrabold text-slate-900">{{ $todayLog->checked_in_at->format('H:i:s') }} WIB</p>
                        <div class="mt-1 flex items-center gap-2">
                            <span class="inline-block rounded bg-emerald-600 px-2 py-0.5 text-[10px] font-bold text-white">
                                Presensi Berhasil
                            </span>
                            <span class="text-[11px] font-medium {{ $todayLog->within_radius ? 'text-emerald-700' : 'text-amber-700' }}">
                                <i class="bi bi-geo-alt-fill"></i> {{ $todayLog->within_radius ? 'Di Radius Sekolah' : 'Di Luar Radius Sekolah' }}
                                @if($todayLog->distance_meters !== null)
                                    ({{ round($todayLog->distance_meters) }} m)
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100">
                    <a href="{{ route('attendance.mine') }}" class="inline-flex items-center gap-2 text-xs font-bold text-brand-600 hover:text-brand-700">
                        <i class="bi bi-calendar-week"></i> Lihat Seluruh Riwayat Absensi &rarr;
                    </a>
                </div>
            @else
                <div class="py-3 text-center">
                    @if(now()->format('H:i:s') < $checkinStart)
                        <p class="text-xs text-amber-700 mb-3 font-semibold">Presensi mandiri siswa baru dibuka pada pukul {{ substr($checkinStart, 0, 5) }} WIB.</p>
                        <button disabled class="w-full rounded-xl bg-amber-50 border-2 border-amber-300 py-3 text-xs font-extrabold text-amber-700 cursor-not-allowed flex items-center justify-center gap-2">
                            <i class="bi bi-clock-history text-amber-600 text-sm"></i> Belum Jam Masuk (Buka {{ substr($checkinStart, 0, 5) }} WIB)
                        </button>
                    @else
                        <p class="text-xs text-slate-500 mb-3">Ambil foto selfie langsung dan catat lokasi GPS sekolah untuk melakukan presensi hari ini.</p>
                        <button type="button" onclick="openCameraModal()" class="w-full rounded-xl py-3.5 text-xs font-bold text-white shadow-md transition hover:opacity-90 flex items-center justify-center gap-2" style="background-color: #059669; color: #ffffff;">
                            <i class="bi bi-camera-fill text-base"></i> Ambil Foto & Presensi Sekarang
                        </button>
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- Informasi Lokasi Geofence --}}
    <div class="rounded-2xl bg-slate-900 p-5 text-white shadow-sm">
        <div class="flex items-start gap-4">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/10 text-brand-400">
                <i class="bi bi-geo-alt-fill text-xl"></i>
            </div>
            <div class="flex-1">
                <h6 class="text-sm font-bold">Informasi Lokasi & Radius Geofence Sekolah</h6>
                <p class="text-xs text-slate-400 mt-1">Presensi siswa diverifikasi menggunakan titik koordinat GPS di area radius sekolah (Maks. {{ config('attendance.geofence.radius_meters', 150) }} meter).</p>
                <div id="gpsStatusContainer" class="mt-3 flex items-center gap-2 text-xs font-medium text-amber-400">
                    <div class="h-2 w-2 rounded-full bg-amber-400 animate-ping"></div>
                    <span>Mengecek lokasi GPS perangkat Anda...</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Presensi Cepat Kamera & Foto --}}
<div id="cameraModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/80 backdrop-blur-sm p-4 flex items-center justify-center">
    <div class="w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-slate-200">
        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
            <h5 id="modalTitle" class="text-base font-bold text-slate-900">Presensi Mandiri Siswa</h5>
            <button type="button" onclick="closeCameraModal()" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="p-6 text-center">
            <div class="relative mx-auto mb-4 overflow-hidden rounded-xl bg-slate-900" style="aspect-ratio:4/3; max-width: 440px;">
                <video id="videoFeed" class="h-full w-full object-cover" playsinline muted autoplay style="transform: scaleX(-1);"></video>
                <img id="photoPreview" class="hidden h-full w-full object-cover" alt="Preview">
                <canvas id="canvasCapture" class="hidden"></canvas>
            </div>

            <div id="modalAlert" class="mb-4 hidden rounded-xl px-4 py-2.5 text-xs font-medium"></div>

            <div id="captureControls" class="flex justify-center gap-3">
                <button type="button" onclick="closeCameraModal()" class="rounded-xl border border-slate-300 px-5 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50">
                    Batal
                </button>
                <button type="button" id="btnSnap" onclick="snapPhoto()" class="rounded-xl bg-brand-600 px-6 py-2.5 text-xs font-bold text-white shadow-md hover:bg-brand-700">
                    <i class="bi bi-camera me-1"></i> Ambil Foto
                </button>
            </div>

            <div id="confirmControls" class="hidden flex justify-center gap-3">
                <button type="button" onclick="retakePhoto()" class="rounded-xl border border-slate-300 px-5 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Foto Ulang
                </button>
                <button type="button" id="btnSubmitAttendance" onclick="submitAttendance()" class="rounded-xl bg-emerald-600 px-6 py-2.5 text-xs font-bold text-white shadow-md hover:bg-emerald-700">
                    <i class="bi bi-check-circle me-1"></i> Kirim Presensi
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let currentStream = null;
    let capturedBlob = null;
    let currentLat = null;
    let currentLng = null;

    function updateLiveClock() {
        const el = document.getElementById('clockDisplay');
        if (el) el.textContent = new Date().toLocaleTimeString('id-ID');
    }
    setInterval(updateLiveClock, 1000);
    updateLiveClock();

    // Check GPS on load
    function initGPS() {
        const statusEl = document.getElementById('gpsStatusContainer');
        if (!navigator.geolocation) {
            statusEl.innerHTML = `<i class="bi bi-exclamation-triangle text-rose-400 me-1"></i> <span class="text-rose-400">GPS tidak didukung oleh browser Anda.</span>`;
            return;
        }

        navigator.geolocation.getCurrentPosition(
            (pos) => {
                currentLat = pos.coords.latitude;
                currentLng = pos.coords.longitude;
                statusEl.innerHTML = `<i class="bi bi-check-circle-fill text-emerald-400 me-1"></i> <span class="text-emerald-400">GPS Aktif & Terhubung (${currentLat.toFixed(5)}, ${currentLng.toFixed(5)})</span>`;
            },
            (err) => {
                statusEl.innerHTML = `<i class="bi bi-exclamation-octagon-fill text-rose-400 me-1"></i> <span class="text-rose-400">Gagal mendapatkan lokasi GPS: ${err.message}. Harap aktifkan izin lokasi.</span>`;
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    }
    initGPS();

    async function openCameraModal() {
        document.getElementById('cameraModal').classList.remove('hidden');
        document.getElementById('captureControls').classList.remove('hidden');
        document.getElementById('confirmControls').classList.add('hidden');
        document.getElementById('videoFeed').classList.remove('hidden');
        document.getElementById('photoPreview').classList.add('hidden');
        hideAlert();

        try {
            currentStream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } },
                audio: false
            });
            document.getElementById('videoFeed').srcObject = currentStream;
        } catch (e) {
            showAlert('Gagal membuka kamera: ' + e.message + '. Harap izinkan akses kamera pada browser.', 'danger');
        }
    }

    function closeCameraModal() {
        if (currentStream) {
            currentStream.getTracks().forEach(track => track.stop());
            currentStream = null;
        }
        document.getElementById('cameraModal').classList.add('hidden');
    }

    function snapPhoto() {
        const video = document.getElementById('videoFeed');
        const canvas = document.getElementById('canvasCapture');
        const preview = document.getElementById('photoPreview');

        canvas.width = video.videoWidth || 640;
        canvas.height = video.videoHeight || 480;

        const ctx = canvas.getContext('2d');
        // Mirror horizontally for natural selfie orientation
        ctx.translate(canvas.width, 0);
        ctx.scale(-1, 1);
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        canvas.toBlob((blob) => {
            capturedBlob = blob;
            preview.src = URL.createObjectURL(blob);
            video.classList.add('hidden');
            preview.classList.remove('hidden');
            document.getElementById('captureControls').classList.add('hidden');
            document.getElementById('confirmControls').classList.remove('hidden');
        }, 'image/jpeg', 0.85);
    }

    function retakePhoto() {
        document.getElementById('videoFeed').classList.remove('hidden');
        document.getElementById('photoPreview').classList.add('hidden');
        document.getElementById('captureControls').classList.remove('hidden');
        document.getElementById('confirmControls').classList.add('hidden');
        capturedBlob = null;
        hideAlert();
    }

    function detectDevice() {
        const ua = navigator.userAgent;
        if (/mobile/i.test(ua)) return 'mobile';
        if (/tablet/i.test(ua)) return 'tablet';
        return 'desktop';
    }

    function detectBrowser() {
        const ua = navigator.userAgent;
        if (ua.includes('Chrome')) return 'Chrome';
        if (ua.includes('Firefox')) return 'Firefox';
        if (ua.includes('Safari')) return 'Safari';
        if (ua.includes('Edge')) return 'Edge';
        return 'Browser';
    }

    async function submitAttendance() {
        if (!capturedBlob) {
            showAlert('Harap ambil foto selfie terlebih dahulu.', 'warning');
            return;
        }

        if (currentLat === null || currentLng === null) {
            showAlert('Sedang membaca koordinat GPS... Jika muncul popup izin lokasi, klik Izinkan.', 'warning');
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    currentLat = pos.coords.latitude;
                    currentLng = pos.coords.longitude;
                    sendPayload();
                },
                (err) => {
                    showAlert('Gagal membaca GPS: ' + err.message + '. Harap aktifkan GPS Anda.', 'danger');
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
            return;
        }

        sendPayload();
    }

    async function sendPayload() {
        const btn = document.getElementById('btnSubmitAttendance');
        btn.disabled = true;
        btn.innerHTML = `<i class="bi bi-arrow-repeat animate-spin me-1"></i> Mengirim...`;

        const formData = new FormData();
        formData.append('latitude', currentLat);
        formData.append('longitude', currentLng);
        formData.append('check_in_mode', 'full');
        formData.append('face_detection_confidence', '1');
        formData.append('face_match_confidence', '1');
        formData.append('face_matched', '1');
        formData.append('device_type', detectDevice());
        formData.append('browser', detectBrowser());
        formData.append('photo', capturedBlob, 'student_attendance.jpg');

        try {
            const res = await fetch('{{ route("api.attendance.self.check-in") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            const data = await res.json();
            if (res.ok && data.success) {
                showAlert(data.message, 'success');
                setTimeout(() => {
                    closeCameraModal();
                    window.location.reload();
                }, 1200);
            } else {
                showAlert(data.message || 'Gagal melakukan presensi.', 'danger');
                btn.disabled = false;
                btn.innerHTML = `<i class="bi bi-check-circle me-1"></i> Kirim Presensi`;
            }
        } catch (e) {
            showAlert('Terjadi kesalahan jaringan: ' + e.message, 'danger');
            btn.disabled = false;
            btn.innerHTML = `<i class="bi bi-check-circle me-1"></i> Kirim Presensi`;
        }
    }

    function showAlert(msg, type) {
        const el = document.getElementById('modalAlert');
        el.className = `mb-4 rounded-xl px-4 py-2.5 text-xs font-medium bg-${type === 'danger' ? 'rose' : (type === 'success' ? 'emerald' : 'amber')}-100 text-${type === 'danger' ? 'rose' : (type === 'success' ? 'emerald' : 'amber')}-800 border border-${type === 'danger' ? 'rose' : (type === 'success' ? 'emerald' : 'amber')}-200`;
        el.textContent = msg;
        el.classList.remove('hidden');
    }

    function hideAlert() {
        document.getElementById('modalAlert').classList.add('hidden');
    }
</script>
@endpush
@endsection
