@extends('layouts.app')
@section('title', 'Stasiun Tap Kartu Presensi / Pink RFID Gate')
@section('page-title', 'Stasiun Tap Kartu Presensi')

@push('styles')
<style>
    .pink-gate-bg {
        background: radial-gradient(circle at top right, rgba(244, 63, 94, 0.15), transparent 40%),
                    radial-gradient(circle at bottom left, rgba(217, 70, 239, 0.12), transparent 40%),
                    #0b0f19;
    }
    .pink-glow {
        box-shadow: 0 0 35px -5px rgba(244, 63, 94, 0.35), 0 0 15px rgba(217, 70, 239, 0.25);
    }
    .pulse-pink {
        animation: pulsePinkAnim 2.2s infinite ease-in-out;
    }
    @keyframes pulsePinkAnim {
        0%, 100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(244, 63, 94, 0.5); }
        50% { transform: scale(1.02); box-shadow: 0 0 25px 8px rgba(244, 63, 94, 0.25); }
    }
    .card-flip-enter {
        animation: cardFlipAnim 0.45s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
    }
    @keyframes cardFlipAnim {
        0% { transform: scale(0.85) translateY(20px); opacity: 0; }
        100% { transform: scale(1) translateY(0); opacity: 1; }
    }
    .neon-border-pink {
        border: 1px solid rgba(244, 63, 94, 0.3);
        background: rgba(15, 23, 42, 0.75);
        backdrop-filter: blur(16px);
    }
</style>
@endpush

@section('content')
<div class="mx-auto max-w-7xl space-y-6">

    {{-- Top Action Bar & Status --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2">
        <div>
            <div class="mb-1 flex items-center gap-2">
                <a href="{{ route('attendance.index') }}" class="inline-flex items-center gap-1.5 text-xs font-extrabold text-slate-500 hover:text-brand-600 transition">
                    <i class="bi bi-arrow-left"></i> Rekap Presensi
                </a>
                <span class="text-slate-300">•</span>
                <span class="text-xs font-bold text-rose-500 flex items-center gap-1">
                    <i class="bi bi-credit-card-2-front-fill"></i> Alat Pink / RFID Tap Gate
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight flex items-center gap-3">
                <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-tr from-rose-500 to-pink-600 text-white shadow-lg shadow-rose-500/30 shrink-0">
                    <i class="bi bi-broadcast-pin text-xl"></i>
                </span>
                Stasiun Tap Kartu Presensi
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1 mb-0">
                Hubungkan pembaca kartu (Alat Pink / USB RFID Reader / Scanner Kartu) untuk mencatat kehadiran siswa secara instan tanpa sentuh.
            </p>
        </div>

        <div class="flex items-center gap-3 shrink-0 flex-wrap">
            <span class="inline-flex items-center gap-2 rounded-full bg-slate-900 px-4 py-2 text-xs font-mono font-bold text-white shadow-sm border border-slate-800">
                <span class="h-2.5 w-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                <span id="liveClock">--:--:-- WIB</span>
            </span>
            <button type="button" onclick="openRegisterCardModal()" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 text-xs font-bold transition">
                <i class="bi bi-plus-circle-fill"></i> Tautkan Kartu Siswa
            </button>
            <a href="{{ route('attendance.barcode-center') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                <i class="bi bi-qr-code"></i> Pusat Barcode
            </a>
        </div>
    </div>

    {{-- Main Kiosk Container --}}
    <div class="flex flex-col lg:flex-row gap-6 items-start w-full">

        {{-- KOLOM KIRI & TENGAH: KIOSK DISPLAY TAP (65%) --}}
        <div class="w-full lg:w-8/12 space-y-5" style="flex: 1 1 65%; width: 100%; min-width: 0;">
            
            {{-- Kiosk Screen Monitor --}}
            <div class="pink-gate-bg rounded-3xl p-6 sm:p-8 text-white pink-glow border border-rose-500/30 relative overflow-hidden">
                
                {{-- Decorative light waves --}}
                <div class="absolute -right-20 -top-20 w-64 h-64 bg-rose-500/10 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute -left-20 -bottom-20 w-64 h-64 bg-pink-500/10 rounded-full blur-3xl pointer-events-none"></div>

                {{-- Monitor Header --}}
                <div class="flex items-center justify-between border-b border-white/10 pb-4 mb-6">
                    <div class="flex items-center gap-3">
                        <div class="h-3 w-3 rounded-full bg-rose-500 animate-ping"></div>
                        <div>
                            <div class="text-[11px] font-mono tracking-widest uppercase text-rose-300 font-bold">SMART ATTENDANCE GATEWAY</div>
                            <div class="text-xs text-white/70 font-semibold">{{ $schoolName }}</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span id="readerStatusBadge" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-[11px] font-mono font-bold">
                            <span class="h-2 w-2 rounded-full bg-emerald-400"></span> READER PINK SIAP
                        </span>
                        <button type="button" onclick="toggleSound()" id="soundToggleBtn" class="px-2.5 py-1 rounded-lg bg-white/10 hover:bg-white/20 text-white text-xs font-bold transition" title="Aktifkan/Matikan Suara">
                            <i class="bi bi-volume-up-fill" id="soundIcon"></i>
                        </button>
                    </div>
                </div>

                {{-- Interactive Tap Target Area --}}
                <div class="py-6 flex flex-col items-center justify-center text-center">
                    
                    {{-- Default State: Menunggu Tap Kartu --}}
                    <div id="waitingTapState" class="space-y-5 flex flex-col items-center">
                        <div class="relative cursor-pointer" onclick="focusTapInput()">
                            <div class="w-36 h-36 sm:w-44 sm:h-44 rounded-full bg-gradient-to-tr from-rose-500/20 via-pink-500/30 to-purple-500/20 flex items-center justify-center pulse-pink border-2 border-rose-400/50">
                                <div class="w-28 h-28 sm:w-36 sm:h-36 rounded-full bg-slate-950/80 flex flex-col items-center justify-center text-rose-400 border border-rose-400/40">
                                    <i class="bi bi-credit-card-2-front text-4xl sm:text-5xl text-rose-400 animate-bounce"></i>
                                    <span class="text-[10px] font-mono font-bold tracking-widest text-rose-300 mt-1 uppercase">TAP DI SINI</span>
                                </div>
                            </div>
                            <span class="absolute -bottom-2 bg-rose-600 text-white px-3 py-0.5 rounded-full text-[10px] font-bold shadow-lg">
                                ALAT PINK READY
                            </span>
                        </div>

                        <div>
                            <h3 class="text-xl sm:text-2xl font-black text-white tracking-tight">
                                Tempelkan Kartu Siswa ke Alat Pink
                            </h3>
                            <p class="text-xs sm:text-sm text-slate-400 max-w-md mx-auto mt-1">
                                Sensor RFID atau Barcode Reader akan otomatis mendeteksi chip kartu dan mencatat kehadiran tanpa klik tombol.
                            </p>
                        </div>

                        <div class="flex items-center gap-2 pt-2">
                            <span class="text-[11px] font-mono text-slate-400 bg-white/5 px-3 py-1.5 rounded-full border border-white/10">
                                <i class="bi bi-keyboard text-rose-400 me-1"></i> Auto-Focus Active
                            </span>
                            <span class="text-[11px] font-mono text-slate-400 bg-white/5 px-3 py-1.5 rounded-full border border-white/10">
                                <i class="bi bi-clock-history text-rose-400 me-1"></i> Batas Telat: {{ substr($lateLimit, 0, 5) }} WIB
                            </span>
                        </div>
                    </div>

                    {{-- Tap Success Card Popup (Muncul saat kartu terdeteksi) --}}
                    <div id="tapSuccessState" class="hidden w-full max-w-lg card-flip-enter">
                        <div class="rounded-3xl bg-slate-900/90 border-2 border-emerald-400/80 p-6 sm:p-7 text-left shadow-2xl relative">
                            <div class="absolute -top-3 right-6 bg-emerald-500 text-slate-950 px-3.5 py-0.5 rounded-full text-xs font-black tracking-wide uppercase shadow-md flex items-center gap-1">
                                <i class="bi bi-check-circle-fill"></i> PRESENSI BERHASIL
                            </div>

                            <div class="flex items-center gap-5">
                                <div class="relative shrink-0">
                                    <img id="scannedPhoto" src="https://ui-avatars.com/api/?name=Siswa&background=10b981&color=fff" 
                                        class="h-24 w-24 sm:h-28 sm:w-28 rounded-2xl object-cover ring-4 ring-emerald-400/40 shadow-lg" alt="Siswa">
                                    <span id="scannedBadgeIcon" class="absolute -bottom-1 -right-1 h-7 w-7 rounded-full bg-emerald-500 text-white flex items-center justify-center text-sm shadow">
                                        <i class="bi bi-check"></i>
                                    </span>
                                </div>
                                <div class="space-y-1 min-w-0 flex-1">
                                    <div class="flex items-center gap-2">
                                        <span id="scannedStatusBadge" class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold uppercase bg-emerald-400/20 text-emerald-300 border border-emerald-400/40">
                                            HADIR TEPAT WAKTU
                                        </span>
                                        <span id="scannedTime" class="text-xs font-mono text-white/70">--:-- WIB</span>
                                    </div>
                                    <h4 id="scannedName" class="text-lg sm:text-xl font-black text-white truncate mb-0 leading-tight">Nama Siswa</h4>
                                    <div class="text-xs text-slate-300 font-mono">NIS: <span id="scannedNis">20250001</span></div>
                                    <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg bg-white/10 text-xs font-bold text-rose-300">
                                        <i class="bi bi-mortarboard-fill"></i> <span id="scannedClass">Kelas 10-A</span>
                                    </div>
                                </div>
                            </div>

                            <div id="scannedMessage" class="mt-4 pt-3 border-t border-white/10 text-xs text-emerald-300 font-medium flex items-center gap-2">
                                <i class="bi bi-info-circle-fill"></i> Presensi berhasil dicatat via Tap Kartu Pink.
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Hidden Global RFID Input (Menangkap input dari alat USB pink / keyboard-wedge) --}}
                <div class="mt-2 pt-4 border-t border-white/10 flex items-center justify-between text-xs text-white/60">
                    <div class="flex items-center gap-2 w-full max-w-md">
                        <span class="text-rose-400 font-mono text-[11px] shrink-0"><i class="bi bi-input-cursor"></i> UID Input:</span>
                        <input type="text" id="rfidBufferInput" placeholder="Tap kartu pink atau ketik UID/NIS..." 
                            class="w-full bg-slate-900/70 border border-rose-500/30 rounded-xl px-3 py-1.5 text-xs text-rose-200 font-mono placeholder-white/30 focus:outline-none focus:border-rose-400 focus:ring-1 focus:ring-rose-400 transition" autocomplete="off">
                    </div>
                    <div class="text-[11px] font-mono text-white/50 hidden sm:block">
                        Device ID: <span class="text-rose-300 font-bold">GATE-PINK-01</span>
                    </div>
                </div>

            </div>

            {{-- Demo Mode: Simulator Tap Cepat (Uji Coba Demo Tanpa Alat Fisik) --}}
            <div class="rounded-3xl bg-white p-6 border border-slate-200/90 shadow-sm space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-amber-100 text-amber-700">
                            <i class="bi bi-lightning-charge-fill text-sm"></i>
                        </span>
                        <div>
                            <h6 class="text-sm font-black text-slate-900 mb-0">Simulator Tap Kartu (Demo Showcase)</h6>
                            <p class="text-[11px] text-slate-500 mb-0">Uji coba simulasi respons alat pink tap kartu untuk demo presentasi</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-full bg-amber-50 text-amber-800 border border-amber-200 text-[10px] font-bold">
                        Mode Simulasi
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 pt-1">
                    <div class="sm:col-span-8">
                        <select id="simStudentSelect" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3.5 py-2.5 text-xs font-semibold text-slate-700 focus:border-rose-500 focus:outline-none focus:bg-white transition">
                            <option value="">-- Pilih Siswa Untuk Uji Coba Tap Kartu --</option>
                            @foreach($students as $st)
                                <option value="{{ $st->rfid ?: $st->nis }}" data-name="{{ $st->name }}" data-nis="{{ $st->nis }}">
                                    {{ $st->name }} (NIS: {{ $st->nis }} | {{ $st->class?->name ?? 'Tanpa Kelas' }}) {{ $st->rfid ? '[RFID: ' . $st->rfid . ']' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-4">
                        <button type="button" onclick="triggerSimulatedTap()" class="w-full rounded-xl bg-gradient-to-r from-rose-500 to-pink-600 hover:from-rose-600 hover:to-pink-700 text-white font-bold py-2.5 px-4 text-xs shadow-md shadow-rose-500/20 transition flex items-center justify-center gap-1.5 cursor-pointer">
                            <i class="bi bi-broadcast"></i> Simulasikan Tap
                        </button>
                    </div>
                </div>
            </div>

        </div>

        {{-- KOLOM KANAN: LIVE ACTIVITY FEED (35%) --}}
        <div class="w-full lg:w-4/12 space-y-5" style="flex: 1 1 35%; width: 100%; min-width: 0;">
            
            {{-- Counter Card --}}
            <div class="rounded-3xl bg-white p-5 border border-slate-200/90 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xs text-slate-500 font-bold uppercase tracking-wider">Total Presensi Hari Ini</span>
                    <h3 class="text-3xl font-black text-slate-900 mt-1 mb-0" id="totalTapCounterDisplay">{{ $todayCount }}</h3>
                    <p class="text-[11px] text-emerald-600 font-bold mt-0.5 mb-0 flex items-center gap-1">
                        <i class="bi bi-check2-all"></i> Terhubung ke database LMS
                    </p>
                </div>
                <div class="h-14 w-14 rounded-2xl bg-rose-50 border border-rose-100 flex items-center justify-center text-rose-600 text-2xl font-bold shadow-inner">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>

            {{-- Live Feed Siswa yang Baru Saja Tap --}}
            <div class="rounded-3xl bg-white border border-slate-200/90 shadow-sm overflow-hidden">
                <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-activity text-rose-500"></i>
                        <h6 class="text-xs font-black text-slate-900 uppercase tracking-wider mb-0">Riwayat Tap Hari Ini</h6>
                    </div>
                    <span class="text-[10px] font-mono text-slate-400">Live Updates</span>
                </div>

                <div class="divide-y divide-slate-100 max-h-[460px] overflow-y-auto" id="recentTapsList">
                    @forelse($recentTaps as $tap)
                        <div class="p-3.5 hover:bg-slate-50 transition flex items-center gap-3">
                            <img src="{{ $tap['photo_url'] ?: 'https://ui-avatars.com/api/?name=' . urlencode($tap['name']) . '&background=f43f5e&color=fff' }}" 
                                class="h-10 w-10 rounded-xl object-cover shrink-0 ring-1 ring-slate-200" alt="{{ $tap['name'] }}">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between gap-1">
                                    <h6 class="text-xs font-bold text-slate-900 truncate mb-0">{{ $tap['name'] }}</h6>
                                    <span class="text-[10px] font-mono text-slate-400 shrink-0">{{ $tap['check_in'] }}</span>
                                </div>
                                <div class="flex items-center gap-2 text-[11px] text-slate-500 mt-0.5">
                                    <span>{{ $tap['class'] }}</span>
                                    <span>•</span>
                                    <span class="font-bold {{ $tap['status'] === 'terlambat' ? 'text-amber-600' : 'text-emerald-600' }}">
                                        {{ ucfirst($tap['status_label'] ?? $tap['status']) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-10 text-center text-slate-400 text-xs" id="emptyRecentTapPlaceholder">
                            <i class="bi bi-inbox text-3xl text-slate-300 block mb-2"></i>
                            Belum ada riwayat tap kartu hari ini.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Info Alat Pink --}}
            <div class="rounded-3xl bg-gradient-to-tr from-rose-50 to-pink-50 p-5 border border-rose-200/70 text-xs text-rose-900 space-y-2">
                <div class="font-extrabold flex items-center gap-1.5 text-rose-700">
                    <i class="bi bi-info-circle-fill"></i> Integrasi Mesin / Alat Pink:
                </div>
                <p class="text-[11px] text-rose-800/80 mb-0 leading-relaxed">
                    Alat Pink RFID dapat dihubungkan via kabel USB (otomatis membaca nomor kartu pada layar ini) ataupun via IoT Wi-Fi menggunakan REST API endpoint:
                </p>
                <div class="bg-white/80 p-2 rounded-xl font-mono text-[10px] text-rose-950 border border-rose-200/80 break-all select-all">
                    POST {{ url('/api/v1/attendance/tap') }}
                </div>
            </div>

        </div>

    </div>

</div>

{{-- Modal Tautkan Kartu RFID ke Siswa --}}
<div id="registerCardModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/70 backdrop-blur-sm p-4 flex items-center justify-center">
    <div class="w-full max-w-md overflow-hidden rounded-3xl bg-white shadow-2xl ring-1 ring-slate-200">
        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 bg-slate-50">
            <div class="flex items-center gap-2.5">
                <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-rose-100 text-rose-600 font-bold">
                    <i class="bi bi-credit-card-fill"></i>
                </span>
                <h5 class="text-base font-black text-slate-900 mb-0">Tautkan Kartu RFID Siswa</h5>
            </div>
            <button type="button" onclick="closeRegisterCardModal()" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-200 hover:text-slate-700">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <form onsubmit="event.preventDefault(); submitRegisterCard();" class="p-6 space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Pilih Siswa:</label>
                <select id="regStudentId" required class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-800 focus:border-rose-500 focus:outline-none">
                    <option value="">-- Cari atau Pilih Siswa --</option>
                    @foreach($students as $st)
                        <option value="{{ $st->id }}">
                            {{ $st->name }} (NIS: {{ $st->nis }} - {{ $st->class?->name ?? 'Tanpa Kelas' }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Nomor UID Kartu RFID (Alat Pink):</label>
                <div class="flex gap-2">
                    <input type="text" id="regRfidNumber" required placeholder="Tap kartu ke alat pink atau ketik UID..." 
                        class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2 font-mono text-xs text-slate-800 focus:border-rose-500 focus:outline-none">
                </div>
                <p class="text-[10px] text-slate-400 mt-1 mb-0">UID didapat saat kartu ditempelkan ke reader RFID pink.</p>
            </div>

            <div id="regAlert" class="hidden rounded-xl p-3 text-xs font-bold"></div>

            <div class="pt-2 flex justify-end gap-2">
                <button type="button" onclick="closeRegisterCardModal()" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100">
                    Batal
                </button>
                <button type="submit" id="btnSubmitRegCard" class="px-5 py-2 rounded-xl text-xs font-extrabold text-white bg-rose-600 hover:bg-rose-500 shadow-md shadow-rose-600/20">
                    Simpan Kartu
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    let soundEnabled = true;
    let isProcessing = false;
    let cardClearTimeout = null;

    // Live Clock
    function updateLiveClock() {
        const el = document.getElementById('liveClock');
        if (el) el.textContent = new Date().toLocaleTimeString('id-ID') + ' WIB';
    }
    setInterval(updateLiveClock, 1000);
    updateLiveClock();

    // Toggle Sound
    function toggleSound() {
        soundEnabled = !soundEnabled;
        const icon = document.getElementById('soundIcon');
        if (soundEnabled) {
            icon.className = 'bi bi-volume-up-fill';
        } else {
            icon.className = 'bi bi-volume-mute-fill';
        }
    }

    // Audio Synthesizer Beep
    function playBeep(success = true) {
        if (!soundEnabled) return;
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();

            osc.connect(gain);
            gain.connect(ctx.destination);

            if (success) {
                // Two-tone double beep
                osc.frequency.setValueAtTime(880, ctx.currentTime); // A5
                osc.frequency.setValueAtTime(1174.66, ctx.currentTime + 0.08); // D6
                gain.gain.setValueAtTime(0.3, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.25);
                osc.start();
                osc.stop(ctx.currentTime + 0.25);
            } else {
                // Low error buzz
                osc.frequency.setValueAtTime(220, ctx.currentTime);
                gain.gain.setValueAtTime(0.4, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.4);
                osc.start();
                osc.stop(ctx.currentTime + 0.4);
            }
        } catch (e) {
            console.log('AudioContext not allowed yet');
        }
    }

    // Text-to-speech voice
    function speakText(text) {
        if (!soundEnabled || !window.speechSynthesis) return;
        try {
            window.speechSynthesis.cancel();
            const utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = 'id-ID';
            utterance.rate = 1.05;
            window.speechSynthesis.speak(utterance);
        } catch (e) {
            console.log('Speech error:', e);
        }
    }

    // Keep RFID buffer focused for USB pink reader keystrokes
    const rfidInput = document.getElementById('rfidBufferInput');
    function focusTapInput() {
        if (rfidInput) rfidInput.focus();
    }

    // Document listener: If user types while not inside modal/select, focus RFID input
    document.addEventListener('keydown', (e) => {
        const modal = document.getElementById('registerCardModal');
        if (!modal.classList.contains('hidden')) return;
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'SELECT' || e.target.tagName === 'TEXTAREA') return;

        if (rfidInput && document.activeElement !== rfidInput) {
            rfidInput.focus();
        }
    });

    // Enter press on RFID Input
    rfidInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            const val = rfidInput.value.trim();
            if (val) {
                processTapAttendance(val);
                rfidInput.value = '';
            }
        }
    });

    // Send Tap to API
    async function processTapAttendance(identifier) {
        if (isProcessing) return;
        isProcessing = true;

        const badge = document.getElementById('readerStatusBadge');
        badge.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 text-[11px] font-mono font-bold';
        badge.innerHTML = `<span class="h-2 w-2 rounded-full bg-amber-400 animate-ping"></span> MEMPROSES KARTU...`;

        try {
            const res = await fetch('{{ route("api.attendance.tap") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Api-Key': '{{ $apiKey }}',
                },
                body: JSON.stringify({
                    card_id: identifier,
                    device_id: 'GATE-PINK-01',
                }),
            });

            const data = await res.json();

            if (res.ok && data.success) {
                playBeep(true);
                displayTapSuccess(data);
                prependTapFeed(data);
                speakText(`Selamat datang, ${data.student?.name || 'Siswa'}. Presensi berhasil dicatat.`);
            } else {
                playBeep(false);
                displayTapError(data.message || 'Kartu tidak dikenali atau belum terdaftar.');
            }
        } catch (e) {
            playBeep(false);
            displayTapError('Gagal terhubung ke server tap: ' + e.message);
        } finally {
            isProcessing = false;
            setTimeout(() => {
                badge.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-[11px] font-mono font-bold';
                badge.innerHTML = `<span class="h-2 w-2 rounded-full bg-emerald-400"></span> READER PINK SIAP`;
            }, 600);
        }
    }

    function displayTapSuccess(data) {
        const waitingState = document.getElementById('waitingTapState');
        const successState = document.getElementById('tapSuccessState');

        const student = data.student || {};
        const att = data.attendance || {};

        document.getElementById('scannedName').textContent = student.name || 'Siswa';
        document.getElementById('scannedNis').textContent = student.nis || '-';
        document.getElementById('scannedClass').textContent = student.class || 'Kelas';
        document.getElementById('scannedTime').textContent = att.check_in || new Date().toLocaleTimeString('id-ID');
        document.getElementById('scannedPhoto').src = student.photo_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(student.name || 'Siswa')}&background=10b981&color=fff`;

        const statusBadge = document.getElementById('scannedStatusBadge');
        if (att.status === 'terlambat') {
            statusBadge.className = 'px-2.5 py-0.5 rounded-full text-[11px] font-extrabold uppercase bg-amber-400/20 text-amber-300 border border-amber-400/40';
            statusBadge.textContent = 'TERLAMBAT';
        } else {
            statusBadge.className = 'px-2.5 py-0.5 rounded-full text-[11px] font-extrabold uppercase bg-emerald-400/20 text-emerald-300 border border-emerald-400/40';
            statusBadge.textContent = 'HADIR TEPAT WAKTU';
        }

        document.getElementById('scannedMessage').innerHTML = `<i class="bi bi-check-circle-fill"></i> ${data.message || 'Presensi berhasil dicatat via Tap Kartu Pink.'}`;

        waitingState.classList.add('hidden');
        successState.classList.remove('hidden');

        // Auto revert to waiting state after 4.5 seconds
        if (cardClearTimeout) clearTimeout(cardClearTimeout);
        cardClearTimeout = setTimeout(() => {
            successState.classList.add('hidden');
            waitingState.classList.remove('hidden');
        }, 4500);

        // Update counter
        const counter = document.getElementById('totalTapCounterDisplay');
        if (counter && data.action === 'recorded') {
            counter.textContent = parseInt(counter.textContent || 0) + 1;
        }
    }

    function displayTapError(msg) {
        alert(msg);
    }

    function prependTapFeed(data) {
        const list = document.getElementById('recentTapsList');
        const emptyPl = document.getElementById('emptyRecentTapPlaceholder');
        if (emptyPl) emptyPl.remove();

        const student = data.student || {};
        const att = data.attendance || {};
        const statusColor = att.status === 'terlambat' ? 'text-amber-600' : 'text-emerald-600';
        const photo = student.photo_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(student.name || 'S')}&background=f43f5e&color=fff`;

        const item = document.createElement('div');
        item.className = 'p-3.5 hover:bg-slate-50 transition flex items-center gap-3 bg-rose-50/40';
        item.innerHTML = `
            <img src="${photo}" class="h-10 w-10 rounded-xl object-cover shrink-0 ring-1 ring-slate-200" alt="${student.name}">
            <div class="min-w-0 flex-1">
                <div class="flex items-center justify-between gap-1">
                    <h6 class="text-xs font-bold text-slate-900 truncate mb-0">${student.name}</h6>
                    <span class="text-[10px] font-mono text-slate-400 shrink-0">${att.check_in || 'Baru Saja'}</span>
                </div>
                <div class="flex items-center gap-2 text-[11px] text-slate-500 mt-0.5">
                    <span>${student.class || 'Kelas'}</span>
                    <span>•</span>
                    <span class="font-bold ${statusColor}">
                        ${att.status_label || (att.status === 'terlambat' ? 'Terlambat' : 'Hadir')}
                    </span>
                </div>
            </div>
        `;

        list.insertBefore(item, list.firstChild);
    }

    // Simulation Tap
    function triggerSimulatedTap() {
        const sel = document.getElementById('simStudentSelect');
        const val = sel.value;
        if (!val) {
            alert('Pilih siswa terlebih dahulu untuk simulasi tap kartu.');
            return;
        }
        processTapAttendance(val);
    }

    // Polling for external IoT readers (reads every 3.5 seconds)
    let lastKnownTapId = null;
    async function pollLatestTaps() {
        try {
            const res = await fetch('{{ route("api.attendance.tap.latest") }}?limit=1', {
                headers: { 'Accept': 'application/json' }
            });
            if (res.ok) {
                const data = await res.json();
                if (data.taps && data.taps.length > 0) {
                    const latest = data.taps[0];
                    if (lastKnownTapId === null) {
                        lastKnownTapId = latest.id;
                    } else if (latest.id > lastKnownTapId) {
                        lastKnownTapId = latest.id;
                        // Another machine tapped!
                        playBeep(true);
                        displayTapSuccess({
                            success: true,
                            message: `Presensi baru terdeteksi dari Gate Tap: ${latest.name}`,
                            student: {
                                name: latest.name,
                                nis: latest.nis,
                                class: latest.class,
                                photo_url: latest.photo_url,
                            },
                            attendance: {
                                status: latest.status,
                                status_label: latest.status_label,
                                check_in: latest.check_in,
                            }
                        });
                    }
                }
            }
        } catch (e) {
            // Polling silent catch
        }
    }
    setInterval(pollLatestTaps, 3500);

    // Modal Card Registration
    function openRegisterCardModal() {
        document.getElementById('registerCardModal').classList.remove('hidden');
        document.getElementById('regRfidNumber').focus();
    }

    function closeRegisterCardModal() {
        document.getElementById('registerCardModal').classList.add('hidden');
        document.getElementById('regAlert').classList.add('hidden');
    }

    async function submitRegisterCard() {
        const studentId = document.getElementById('regStudentId').value;
        const rfid = document.getElementById('regRfidNumber').value.trim();
        const alertBox = document.getElementById('regAlert');
        const btn = document.getElementById('btnSubmitRegCard');

        if (!studentId || !rfid) {
            alertBox.className = 'rounded-xl p-3 text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200';
            alertBox.textContent = 'Harap pilih siswa dan isi nomor kartu RFID.';
            alertBox.classList.remove('hidden');
            return;
        }

        btn.disabled = true;
        btn.textContent = 'Menyimpan...';

        try {
            const res = await fetch('{{ route("attendance.update-student-rfid") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ student_id: studentId, rfid: rfid })
            });

            const data = await res.json();
            if (res.ok && data.success) {
                alertBox.className = 'rounded-xl p-3 text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200';
                alertBox.textContent = data.message;
                alertBox.classList.remove('hidden');
                setTimeout(() => {
                    closeRegisterCardModal();
                    window.location.reload();
                }, 1200);
            } else {
                alertBox.className = 'rounded-xl p-3 text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200';
                alertBox.textContent = data.message || 'Gagal menautkan kartu.';
                alertBox.classList.remove('hidden');
            }
        } catch (e) {
            alertBox.className = 'rounded-xl p-3 text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200';
            alertBox.textContent = 'Kesalahan koneksi: ' + e.message;
            alertBox.classList.remove('hidden');
        } finally {
            btn.disabled = false;
            btn.textContent = 'Simpan Kartu';
        }
    }
</script>
@endpush
@endsection
