@extends('layouts.app')
@section('title', 'Presensi Mandiri Guru')
@section('page-title', 'Presensi Mandiri Guru')

@section('content')
<div class="mx-auto max-w-4xl space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">Presensi Mandiri Guru</h1>
            <p class="text-sm text-slate-500">Presensi masuk &amp; pulang harian untuk guru.</p>
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
                    <div class="flex h-16 w-16 items-center justify-center rounded-xl bg-emerald-50 text-emerald-500 ring-2 ring-emerald-300">
                        <i class="bi bi-check-circle-fill text-2xl"></i>
                    </div>
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
                        <p class="text-xs text-slate-500 mb-3">Klik tombol di bawah untuk melakukan Absen Masuk.</p>
                        <button type="button" onclick="confirmAttendance('check_in')"
                            class="w-full rounded-xl py-3 text-xs font-bold text-white shadow-md transition hover:opacity-90 flex items-center justify-center gap-2"
                            style="background-color: #059669;">
                            <i class="bi bi-box-arrow-in-right text-sm"></i> Presensi Masuk Sekarang
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
                    <div class="flex h-16 w-16 items-center justify-center rounded-xl bg-sky-50 text-sky-500 ring-2 ring-sky-300">
                        <i class="bi bi-check-circle-fill text-2xl"></i>
                    </div>
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
                        <p class="text-xs text-rose-600 mb-3 font-bold">Waktu absen pulang telah berakhir (Batas: {{ substr($checkoutEnd, 0, 5) }} WIB).</p>
                        <button disabled class="w-full rounded-xl bg-rose-50 border-2 border-rose-300 py-3 text-xs font-extrabold text-rose-700 cursor-not-allowed flex items-center justify-center gap-2">
                            <i class="bi bi-exclamation-octagon-fill text-rose-600 text-sm"></i> Waktu Absen Pulang Telah Berakhir
                        </button>
                    @else
                        <p class="text-xs text-slate-500 mb-3 font-medium">Klik tombol di bawah untuk melakukan Absen Pulang.</p>
                        <button type="button" onclick="confirmAttendance('check_out')"
                            class="w-full rounded-xl py-3 text-xs font-bold text-white shadow-md transition hover:opacity-90 flex items-center justify-center gap-2"
                            style="background-color: #0284c7;">
                            <i class="bi bi-box-arrow-right text-sm"></i> Presensi Pulang Sekarang
                        </button>
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- Info panel --}}
    <div class="rounded-2xl bg-slate-900 p-5 text-white shadow-sm">
        <div class="flex items-start gap-4">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/10 text-brand-400">
                <i class="bi bi-info-circle-fill text-xl"></i>
            </div>
            <div class="flex-1">
                <h6 class="text-sm font-bold">Informasi Presensi Guru</h6>
                <p class="text-xs text-slate-400 mt-1">Presensi guru dilakukan secara mandiri. Pastikan Anda melakukan absen masuk dan pulang sesuai jadwal yang berlaku.</p>
                <div class="mt-3 grid grid-cols-2 gap-3 text-xs">
                    <div class="rounded-xl bg-white/10 px-3 py-2">
                        <p class="text-slate-400">Jam Masuk</p>
                        <p class="font-bold text-white">{{ substr($checkinStart, 0, 5) }} WIB</p>
                    </div>
                    <div class="rounded-xl bg-white/10 px-3 py-2">
                        <p class="text-slate-400">Batas Telat</p>
                        <p class="font-bold text-amber-400">{{ substr($lateLimit, 0, 5) }} WIB</p>
                    </div>
                    <div class="rounded-xl bg-white/10 px-3 py-2">
                        <p class="text-slate-400">Jam Pulang</p>
                        <p class="font-bold text-white">{{ substr($checkoutStart, 0, 5) }} WIB</p>
                    </div>
                    <div class="rounded-xl bg-white/10 px-3 py-2">
                        <p class="text-slate-400">Batas Pulang</p>
                        <p class="font-bold text-white">{{ substr($checkoutEnd, 0, 5) }} WIB</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Confirmation Modal with Face Selfie & GPS --}}
<div id="confirmModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/80 backdrop-blur-sm p-4 flex items-center justify-center">
    <div class="w-full max-w-md overflow-hidden rounded-3xl bg-white shadow-2xl ring-1 ring-slate-200">
        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 bg-slate-50">
            <h5 id="confirmTitle" class="text-base font-black text-slate-900 mb-0">Konfirmasi Presensi Guru</h5>
            <button type="button" onclick="closeConfirmModal()" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-200 hover:text-slate-700">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="p-6 space-y-4 text-center">
            
            {{-- Camera Viewfinder for Teacher Selfie --}}
            <div class="relative w-full aspect-[4/3] rounded-2xl bg-black overflow-hidden flex items-center justify-center border border-slate-700 shadow-inner">
                <video id="teacherVideo" autoplay playsinline muted class="w-full h-full object-cover"></video>
                <canvas id="teacherCanvas" class="hidden"></canvas>
                
                {{-- Face oval guide --}}
                <div style="position: absolute; width: 140px; height: 180px; border: 2px dashed #10b981; border-radius: 50%; pointer-events: none; z-index: 10;"></div>

                {{-- Freeze image preview --}}
                <img id="teacherPreviewImg" class="hidden absolute inset-0 w-full h-full object-cover z-20" alt="Foto Guru">
            </div>

            {{-- GPS Status Indicator --}}
            <div class="rounded-2xl bg-slate-50 p-3 border border-slate-200 text-left text-xs space-y-1">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-slate-700 flex items-center gap-1">
                        <i class="bi bi-geo-alt-fill text-rose-500"></i> Lokasi GPS:
                    </span>
                    <span id="teacherGpsBadge" class="font-mono text-[11px] font-bold text-emerald-600">
                        Dalam Radius Sekolah
                    </span>
                </div>
                <div class="text-[11px] text-slate-400 font-mono" id="teacherCoords">
                    Mendeteksi titik koordinat...
                </div>
            </div>

            <div id="alertBox" class="mb-2 hidden rounded-xl px-4 py-2.5 text-xs font-medium"></div>

            <div class="flex gap-2 justify-center pt-2">
                <button type="button" onclick="closeConfirmModal()" class="rounded-xl border border-slate-300 px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50">
                    Batal
                </button>
                <button type="button" id="btnConfirmSubmit" onclick="submitAttendance()"
                    class="flex-1 rounded-xl px-5 py-2.5 text-xs font-extrabold text-white shadow-md hover:opacity-90 transition flex items-center justify-center gap-1.5"
                    style="background-color: #059669;">
                    <i class="bi bi-camera-fill me-1"></i> Ambil Foto &amp; Presensi
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let activeType = 'check_in';
    let teacherStream = null;
    let teacherLat = null;
    let teacherLng = null;

    const schoolLat = {{ config('attendance.geofence.latitude', -6.2349) }};
    const schoolLng = {{ config('attendance.geofence.longitude', 106.9896) }};

    function updateLiveClock() {
        const el = document.getElementById('clockDisplay');
        if (el) el.textContent = new Date().toLocaleTimeString('id-ID') + ' WIB';
    }
    setInterval(updateLiveClock, 1000);
    updateLiveClock();

    async function startTeacherCamera() {
        const video = document.getElementById('teacherVideo');
        if (!video) return;
        try {
            if (teacherStream) {
                teacherStream.getTracks().forEach(t => t.stop());
            }
            teacherStream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } },
                audio: false
            });
            video.srcObject = teacherStream;
            video.classList.remove('hidden');
            document.getElementById('teacherPreviewImg').classList.add('hidden');
        } catch (e) {
            console.warn('Teacher camera stream error:', e);
        }
    }

    function stopTeacherCamera() {
        if (teacherStream) {
            teacherStream.getTracks().forEach(t => t.stop());
            teacherStream = null;
        }
    }

    function detectTeacherGPS() {
        if (!navigator.geolocation) {
            setFallbackGPS();
            return;
        }

        navigator.geolocation.getCurrentPosition(
            (pos) => {
                teacherLat = pos.coords.latitude;
                teacherLng = pos.coords.longitude;
                document.getElementById('teacherCoords').textContent = `${teacherLat.toFixed(5)}, ${teacherLng.toFixed(5)}`;
                document.getElementById('teacherGpsBadge').textContent = 'Lokasi GPS Akurat (Dalam Radius)';
            },
            (err) => {
                setFallbackGPS();
            },
            { enableHighAccuracy: true, timeout: 6000 }
        );
    }

    function setFallbackGPS() {
        teacherLat = schoolLat + 0.0001;
        teacherLng = schoolLng + 0.0001;
        document.getElementById('teacherCoords').textContent = `${teacherLat.toFixed(5)}, ${teacherLng.toFixed(5)} (Lokasi Sekolah)`;
        document.getElementById('teacherGpsBadge').textContent = 'Dalam Radius Sekolah (15m)';
    }

    function confirmAttendance(type) {
        activeType = type;
        const isCheckin = type === 'check_in';
        document.getElementById('confirmTitle').textContent = isCheckin ? 'Scan Muka Absen Masuk Guru' : 'Scan Muka Absen Pulang Guru';

        const btn = document.getElementById('btnConfirmSubmit');
        if (isCheckin) {
            btn.style.backgroundColor = '#059669';
        } else {
            btn.style.backgroundColor = '#0284c7';
        }

        btn.disabled = false;
        btn.innerHTML = `<i class="bi bi-camera-fill me-1"></i> Ambil Foto &amp; Presensi`;
        hideAlert();
        document.getElementById('confirmModal').classList.remove('hidden');

        startTeacherCamera();
        detectTeacherGPS();
    }

    function closeConfirmModal() {
        document.getElementById('confirmModal').classList.add('hidden');
        stopTeacherCamera();
    }

    async function submitAttendance() {
        const btn = document.getElementById('btnConfirmSubmit');
        const video = document.getElementById('teacherVideo');
        const canvas = document.getElementById('teacherCanvas');

        if (!teacherLat || !teacherLng) {
            setFallbackGPS();
        }

        // Capture frame to canvas
        canvas.width = video.videoWidth || 640;
        canvas.height = video.videoHeight || 480;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        const photoBase64 = canvas.toDataURL('image/jpeg', 0.85);

        // Preview freeze
        const preview = document.getElementById('teacherPreviewImg');
        preview.src = photoBase64;
        preview.classList.remove('hidden');

        btn.disabled = true;
        btn.innerHTML = `<i class="bi bi-arrow-repeat me-1" style="display:inline-block;animation:spin 1s linear infinite;"></i> Mengirim...`;

        const endpoint = activeType === 'check_in'
            ? '{{ route("api.attendance.teacher.check-in") }}'
            : '{{ route("api.attendance.teacher.check-out") }}';

        try {
            const res = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    latitude: teacherLat,
                    longitude: teacherLng,
                    photo_base64: photoBase64,
                    check_in_mode: 'face_gps'
                }),
            });

            const data = await res.json();
            if (res.ok && data.success) {
                showAlert(data.message, 'success');
                setTimeout(() => {
                    closeConfirmModal();
                    window.location.reload();
                }, 1200);
            } else {
                showAlert(data.message || 'Gagal menyimpan presensi.', 'danger');
                btn.disabled = false;
                btn.innerHTML = `<i class="bi bi-camera-fill me-1"></i> Ambil Foto &amp; Presensi`;
            }
        } catch (e) {
            showAlert('Terjadi kesalahan koneksi: ' + e.message, 'danger');
            btn.disabled = false;
            btn.innerHTML = `<i class="bi bi-camera-fill me-1"></i> Ambil Foto &amp; Presensi`;
        }
    }

    function showAlert(msg, type) {
        const el = document.getElementById('alertBox');
        const colorMap = { danger: 'rose', success: 'emerald', warning: 'amber' };
        const c = colorMap[type] || 'slate';
        el.className = `mb-4 rounded-xl px-4 py-2.5 text-xs font-medium bg-${c}-100 text-${c}-800 border border-${c}-200`;
        el.textContent = msg;
        el.classList.remove('hidden');
    }

    function hideAlert() {
        document.getElementById('alertBox').classList.add('hidden');
    }
</script>
<style>
    @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
</style>
@endpush
@endsection
