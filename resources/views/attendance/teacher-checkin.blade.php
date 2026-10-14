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

{{-- Confirmation Modal --}}
<div id="confirmModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/80 backdrop-blur-sm p-4 flex items-center justify-center">
    <div class="w-full max-w-sm overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-slate-200">
        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
            <h5 id="confirmTitle" class="text-base font-bold text-slate-900">Konfirmasi Presensi</h5>
            <button type="button" onclick="closeConfirmModal()" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="p-6 text-center">
            <div id="confirmIconWrap" class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                <i id="confirmIcon" class="bi bi-box-arrow-in-right text-3xl"></i>
            </div>
            <p id="confirmDesc" class="text-sm text-slate-600 mb-6">Apakah Anda yakin ingin melakukan Absen Masuk sekarang?</p>

            <div id="alertBox" class="mb-4 hidden rounded-xl px-4 py-2.5 text-xs font-medium"></div>

            <div class="flex gap-3 justify-center">
                <button type="button" onclick="closeConfirmModal()" class="rounded-xl border border-slate-300 px-5 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50">
                    Batal
                </button>
                <button type="button" id="btnConfirmSubmit" onclick="submitAttendance()"
                    class="rounded-xl px-6 py-2.5 text-xs font-bold text-white shadow-md hover:opacity-90 transition"
                    style="background-color: #059669;">
                    <i class="bi bi-check-circle me-1"></i> Ya, Presensi Sekarang
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let activeType = 'check_in';

    function updateLiveClock() {
        const el = document.getElementById('clockDisplay');
        if (el) el.textContent = new Date().toLocaleTimeString('id-ID');
    }
    setInterval(updateLiveClock, 1000);
    updateLiveClock();

    function confirmAttendance(type) {
        activeType = type;
        const isCheckin = type === 'check_in';
        document.getElementById('confirmTitle').textContent = isCheckin ? 'Konfirmasi Absen Masuk' : 'Konfirmasi Absen Pulang';
        document.getElementById('confirmDesc').textContent = isCheckin
            ? 'Apakah Anda yakin ingin melakukan Absen Masuk sekarang?'
            : 'Apakah Anda yakin ingin melakukan Absen Pulang sekarang?';

        const iconWrap = document.getElementById('confirmIconWrap');
        const icon = document.getElementById('confirmIcon');
        const btn = document.getElementById('btnConfirmSubmit');

        if (isCheckin) {
            iconWrap.className = 'mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 text-emerald-600';
            icon.className = 'bi bi-box-arrow-in-right text-3xl';
            btn.style.backgroundColor = '#059669';
        } else {
            iconWrap.className = 'mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-sky-100 text-sky-600';
            icon.className = 'bi bi-box-arrow-right text-3xl';
            btn.style.backgroundColor = '#0284c7';
        }

        btn.disabled = false;
        btn.innerHTML = `<i class="bi bi-check-circle me-1"></i> Ya, Presensi Sekarang`;
        hideAlert();
        document.getElementById('confirmModal').classList.remove('hidden');
    }

    function closeConfirmModal() {
        document.getElementById('confirmModal').classList.add('hidden');
    }

    async function submitAttendance() {
        const btn = document.getElementById('btnConfirmSubmit');
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
                body: JSON.stringify({ check_in_mode: 'manual' }),
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
                btn.innerHTML = `<i class="bi bi-check-circle me-1"></i> Ya, Presensi Sekarang`;
            }
        } catch (e) {
            showAlert('Terjadi kesalahan koneksi: ' + e.message, 'danger');
            btn.disabled = false;
            btn.innerHTML = `<i class="bi bi-check-circle me-1"></i> Ya, Presensi Sekarang`;
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
