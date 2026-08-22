@extends('layouts.app')
@section('title', 'Presensi Mandiri Guru')
@section('page-title', 'Presensi Mandiri Guru')

@section('content')
<div class="mx-auto max-w-4xl space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">Presensi Mandiri Guru</h1>
            <p class="text-sm text-slate-500">Presensi masuk & pulang menggunakan verifikasi foto wajah dan lokasi GPS.</p>
        </div>
        <div class="text-right">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-brand-50 px-3 py-1 text-xs font-bold text-brand-700 border border-brand-200">
                <i class="bi bi-clock"></i> <span id="clockDisplay">--:--:--</span>
            </span>
        </div>
    </div>

    {{-- Status Cards (Masuk & Pulang) --}}
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        {{-- Card Absen Masuk --}}
        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200/70">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-3">
                <div class="flex items-center gap-2">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600 font-bold">
                        <i class="bi bi-box-arrow-in-right text-lg"></i>
                    </div>
                    <div>
                        <h6 class="text-sm font-bold text-slate-900">Absen Masuk</h6>
                        <p class="text-[11px] text-slate-500">Buka: {{ substr($checkinStart, 0, 5) }} | Batas Telat: {{ substr($lateLimit, 0, 5) }}</p>
                    </div>
                </div>
                @if($todayAttendance && $todayAttendance->check_in_at)
                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-800">Sudah Absen</span>
                @else
                    <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-800">Belum Absen</span>
                @endif
            </div>

            @if($todayAttendance && $todayAttendance->check_in_at)
                <div class="flex items-center gap-4">
                    @if($todayAttendance->check_in_photo)
                        <img src="{{ asset('storage/' . $todayAttendance->check_in_photo) }}" class="h-16 w-16 rounded-xl object-cover ring-2 ring-emerald-400 shadow-sm" alt="Foto Masuk">
                    @else
                        <div class="flex h-16 w-16 items-center justify-center rounded-xl bg-slate-100 text-slate-400">
                            <i class="bi bi-person text-2xl"></i>
                        </div>
                    @endif
                    <div>
                        <p class="text-xs text-slate-500">Waktu Masuk:</p>
                        <p class="text-base font-extrabold text-slate-900">{{ $todayAttendance->check_in_at->format('H:i:s') }} WIB</p>
                        <span class="inline-block rounded px-2 py-0.5 text-[10px] font-bold text-white bg-{{ $todayAttendance->status_badge }}-500">
                            {{ $todayAttendance->status_label }}
                        </span>
                    </div>
                </div>
            @else
                <div class="py-3 text-center">
                    @if(now()->format('H:i:s') < $checkinStart)
                        <p class="text-xs text-amber-700 mb-3 font-semibold">Absen masuk baru dibuka pada pukul {{ substr($checkinStart, 0, 5) }} WIB.</p>
                        <button disabled class="w-full rounded-xl bg-amber-50 border-2 border-amber-300 py-3 text-xs font-extrabold text-amber-700 cursor-not-allowed flex items-center justify-center gap-2">
                            <i class="bi bi-clock-history text-amber-600 text-sm"></i> Belum Jam Masuk (Buka {{ substr($checkinStart, 0, 5) }})
                        </button>
                    @else
                        <p class="text-xs text-slate-500 mb-3">Silakan ambil foto wajah dan lokasi GPS Anda untuk melakukan Absen Masuk.</p>
                        <button type="button" onclick="openCameraModal('check_in')" class="w-full rounded-xl py-3 text-xs font-bold text-white shadow-md transition hover:opacity-90 flex items-center justify-center gap-2" style="background-color: #059669; color: #ffffff;">
                            <i class="bi bi-camera-fill text-sm"></i> Presensi Masuk Sekarang
                        </button>
                    @endif
                </div>
            @endif
        </div>

        {{-- Card Absen Pulang --}}
        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200/70">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-3">
                <div class="flex items-center gap-2">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-sky-100 text-sky-600 font-bold">
                        <i class="bi bi-box-arrow-right text-lg"></i>
                    </div>
                    <div>
                        <h6 class="text-sm font-bold text-slate-900">Absen Pulang</h6>
                        <p class="text-[11px] text-slate-500">Rentang: {{ substr($checkoutStart, 0, 5) }} — {{ substr($checkoutEnd, 0, 5) }} WIB</p>
                    </div>
                </div>
                @if($todayAttendance && $todayAttendance->check_out_at)
                    <span class="rounded-full bg-sky-100 px-3 py-1 text-xs font-bold text-sky-800">Sudah Absen</span>
                @else
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">Belum Absen</span>
                @endif
            </div>

            @if($todayAttendance && $todayAttendance->check_out_at)
                <div class="flex items-center gap-4">
                    @if($todayAttendance->check_out_photo)
                        <img src="{{ asset('storage/' . $todayAttendance->check_out_photo) }}" class="h-16 w-16 rounded-xl object-cover ring-2 ring-sky-400 shadow-sm" alt="Foto Pulang">
                    @else
                        <div class="flex h-16 w-16 items-center justify-center rounded-xl bg-slate-100 text-slate-400">
                            <i class="bi bi-person text-2xl"></i>
                        </div>
                    @endif
                    <div>
                        <p class="text-xs text-slate-500">Waktu Pulang:</p>
                        <p class="text-base font-extrabold text-slate-900">{{ $todayAttendance->check_out_at->format('H:i:s') }} WIB</p>
                        <span class="inline-block rounded bg-sky-600 px-2.5 py-1 text-[10px] font-bold text-white">Selesai Tugas</span>
                    </div>
                </div>
            @else
                <div class="py-3 text-center">
                    @if(!$todayAttendance || !$todayAttendance->check_in_at)
                        <p class="text-xs text-slate-500 mb-3 font-medium">Harap selesaikan Absen Masuk terlebih dahulu untuk membuka Absen Pulang.</p>
                        <button disabled class="w-full rounded-xl bg-slate-100 border-2 border-slate-300 py-3 text-xs font-extrabold text-slate-500 cursor-not-allowed flex items-center justify-center gap-2">
                            <i class="bi bi-lock-fill text-slate-400 text-sm"></i> Absen Pulang Terkunci
                        </button>
                    @elseif(now()->format('H:i:s') < $checkoutStart)
                        <p class="text-xs text-amber-700 mb-3 font-semibold">Absen pulang baru dibuka pada pukul {{ substr($checkoutStart, 0, 5) }} WIB.</p>
                        <button disabled class="w-full rounded-xl bg-amber-50 border-2 border-amber-300 py-3 text-xs font-extrabold text-amber-700 cursor-not-allowed flex items-center justify-center gap-2">
                            <i class="bi bi-clock-history text-amber-600 text-sm"></i> Belum Jam Pulang (Mulai {{ substr($checkoutStart, 0, 5) }})
                        </button>
                    @elseif(now()->format('H:i:s') > $checkoutEnd)
                        <p class="text-xs text-rose-600 mb-3 font-bold">Waktu absen pulang telah berakhir (Batas: {{ substr($checkoutEnd, 0, 5) }} WIB). Anda sudah tidak dapat absen pulang hari ini.</p>
                        <button disabled class="w-full rounded-xl bg-rose-50 border-2 border-rose-300 py-3 text-xs font-extrabold text-rose-700 cursor-not-allowed flex items-center justify-center gap-2">
                            <i class="bi bi-exclamation-octagon-fill text-rose-600 text-sm"></i> Waktu Absen Pulang Telah Berakhir
                        </button>
                    @else
                        <p class="text-xs text-slate-500 mb-3 font-medium">Silakan ambil foto wajah dan lokasi GPS Anda untuk Absen Pulang.</p>
                        <button type="button" onclick="openCameraModal('check_out')" class="w-full rounded-xl py-3 text-xs font-bold text-white shadow-md transition hover:opacity-90 flex items-center justify-center gap-2" style="background-color: #0284c7; color: #ffffff;">
                            <i class="bi bi-camera-fill text-sm"></i> Presensi Pulang Sekarang
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
                <h6 class="text-sm font-bold">Informasi Lokasi & Geofence Sekitar</h6>
                <p class="text-xs text-slate-400 mt-1">Presensi guru hanya dapat dilakukan jika perangkat berada di area radius sekolah (Maks. {{ config('attendance.geofence.radius_meters', 150) }} meter).</p>
                <div id="gpsStatusContainer" class="mt-3 flex items-center gap-2 text-xs font-medium text-amber-400">
                    <div class="h-2 w-2 rounded-full bg-amber-400 animate-ping"></div>
                    <span>Mengecek lokasi GPS perangkat Anda...</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Presensi Kamera & Wajah --}}
<div id="cameraModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/80 backdrop-blur-sm p-4 flex items-center justify-center">
    <div class="w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-slate-200">
        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
            <h5 id="modalTitle" class="text-base font-bold text-slate-900">Presensi Wajah</h5>
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
    let activeType = 'check_in'; // 'check_in' or 'check_out'
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
                statusEl.innerHTML = `<i class="bi bi-exclamation-octagon-fill text-rose-400 me-1"></i> <span class="text-rose-400">Gagal mendapatkan lokasi GPS: ${err.message}. Harap izinkan akses lokasi.</span>`;
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    }
    initGPS();

    async function openCameraModal(type) {
        activeType = type;
        document.getElementById('modalTitle').textContent = type === 'check_in' ? 'Absen Masuk (Wajah + GPS)' : 'Absen Pulang (Wajah + GPS)';
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
            showAlert('Gagal membuka kamera: ' + e.message, 'danger');
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
        // Mirror horizontally
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
        }, 'image/jpeg', 0.9);
    }

    function retakePhoto() {
        document.getElementById('videoFeed').classList.remove('hidden');
        document.getElementById('photoPreview').classList.add('hidden');
        document.getElementById('captureControls').classList.remove('hidden');
        document.getElementById('confirmControls').classList.add('hidden');
        capturedBlob = null;
        hideAlert();
    }

    async function submitAttendance() {
        if (!capturedBlob) {
            showAlert('Harap ambil foto terlebih dahulu.', 'warning');
            return;
        }

        if (currentLat === null || currentLng === null) {
            showAlert('Koordinat GPS belum terdeteksi. Izinkan izin lokasi pada browser Anda.', 'warning');
            return;
        }

        const btn = document.getElementById('btnSubmitAttendance');
        btn.disabled = true;
        btn.innerHTML = `<i class="bi bi-arrow-repeat animate-spin me-1"></i> Mengirim...`;

        const formData = new FormData();
        formData.append('latitude', currentLat);
        formData.append('longitude', currentLng);
        formData.append('photo', capturedBlob, 'teacher_attendance.jpg');

        const endpoint = activeType === 'check_in' 
            ? '{{ route("api.attendance.teacher.check-in") }}' 
            : '{{ route("api.attendance.teacher.check-out") }}';

        try {
            const res = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
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
                showAlert(data.message || 'Gagal menyimpan presensi.', 'danger');
                btn.disabled = false;
                btn.innerHTML = `<i class="bi bi-check-circle me-1"></i> Kirim Presensi`;
            }
        } catch (e) {
            showAlert('Terjadi kesalahan koneksi: ' + e.message, 'danger');
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
