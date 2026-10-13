@extends('layouts.app')
@section('title', 'Simulator Mesin Tap Kartu RFID')
@section('page-title', 'Simulator Mesin Tap Kartu RFID')

@push('styles')
<style>
    /* ── Simulator Master Layout ── */
    .sim-wrapper {
        width: 100%;
        max-width: 1200px;
        margin: 0 auto;
    }

    .sim-grid-layout {
        display: grid;
        grid-template-columns: 1fr;
        gap: 24px;
        width: 100%;
    }

    @media (min-width: 1024px) {
        .sim-grid-layout {
            grid-template-columns: minmax(0, 1.28fr) minmax(0, 1fr);
            align-items: start;
        }
        .sim-left-column {
            min-width: 0;
            width: 100%;
        }
        .sim-right-column {
            min-width: 0;
            width: 100%;
        }
    }

    /* ── Hardware Device Mockup Casing ── */
    .tap-terminal-box {
        background: #0f172a;
        border-radius: 28px;
        padding: 24px;
        color: #f8fafc;
        box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.4), 0 0 0 1px rgba(51, 65, 85, 0.8);
        position: relative;
        overflow: hidden;
    }

    /* Screw accents */
    .terminal-screw {
        position: absolute;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #334155;
        box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.6);
    }
    .screw-tl { top: 12px; left: 12px; }
    .screw-tr { top: 12px; right: 12px; }
    .screw-bl { bottom: 12px; left: 12px; }
    .screw-br { bottom: 12px; right: 12px; }

    /* LCD Screen */
    .lcd-screen {
        background: #020617;
        border: 2px solid #1e293b;
        border-radius: 20px;
        padding: 24px;
        min-height: 230px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
        position: relative;
        overflow: hidden;
        box-shadow: inset 0 2px 8px rgba(0, 0, 0, 0.8);
    }

    .led-ambient-glow {
        position: absolute;
        top: -40px;
        left: 0;
        right: 0;
        height: 80px;
        background: rgba(6, 182, 212, 0.15);
        filter: blur(24px);
        transition: all 0.4s ease;
        pointer-events: none;
    }

    /* Sensor Tap Area */
    .tap-sensor-zone {
        margin-top: 20px;
        border: 2px dashed #334155;
        border-radius: 20px;
        background: rgba(2, 6, 23, 0.6);
        padding: 16px;
    }

    /* Sample Cards Grid */
    .sample-student-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
        gap: 12px;
    }

    .student-sample-btn {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 12px 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        width: 100%;
        text-align: left;
        cursor: pointer;
        transition: all 0.15s ease-in-out;
    }
    .student-sample-btn:hover {
        background: #eff6ff;
        border-color: #93c5fd;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px -2px rgba(59, 130, 246, 0.12);
    }

    /* Animations */
    @keyframes pulseGlow {
        0%, 100% { opacity: 0.6; transform: scale(1); }
        50% { opacity: 1; transform: scale(1.05); }
    }
    .animate-pulseGlow {
        animation: pulseGlow 2.5s infinite;
    }

    @keyframes fadeInSlide {
        from { opacity: 0; transform: translateY(6px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-result {
        animation: fadeInSlide 0.25s ease-out forwards;
    }
</style>
@endpush

@section('content')
<div class="sim-wrapper space-y-6">

    {{-- Breadcrumb & Title --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2">
        <div>
            <div class="mb-1.5 flex items-center gap-2">
                <a href="{{ route('attendance.index') }}" class="inline-flex items-center gap-1.5 text-xs font-extrabold text-slate-500 hover:text-brand-600 transition">
                    <i class="bi bi-arrow-left"></i> Rekap Presensi
                </a>
                <span class="text-slate-300">•</span>
                <a href="{{ route('settings.index') }}#kartu-pane" class="text-xs font-semibold text-slate-500 hover:text-brand-600 transition">
                    Pengaturan Integrasi RFID
                </a>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-amber-500 text-white shadow-md shadow-amber-500/20 shrink-0">
                    <i class="bi bi-credit-card-2-front-fill text-xl"></i>
                </span>
                Simulator Mesin Tap Kartu RFID
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Simulasi interaktif alur: <strong class="text-slate-800">Kartu Siswa &rarr; Mesin Tap &rarr; API Layer &rarr; LMS &rarr; Data Kehadiran</strong>
            </p>
        </div>
        <div class="flex items-center gap-2.5 shrink-0">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3.5 py-1.5 text-xs font-bold text-emerald-700 border border-emerald-200 shadow-2xs">
                <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                API Endpoint Active
            </span>
            <a href="{{ route('attendance.index') }}" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-700 shadow-2xs hover:bg-slate-50 transition">
                <i class="bi bi-clipboard-data text-brand-600"></i> Buka Rekap Absen
            </a>
        </div>
    </div>

    {{-- Main Two-Column Simulator Layout --}}
    <div class="sim-grid-layout">

        {{-- ============================================================ --}}
        {{-- KOLOM KIRI: HARDWARE MESIN TAP & SAMPLE KARTU SISWA          --}}
        {{-- ============================================================ --}}
        <div class="sim-left-column space-y-5">

            {{-- 1. HARDWARE DEVICE TERMINAL BOX --}}
            <div class="tap-terminal-box">
                <div class="terminal-screw screw-tl"></div>
                <div class="terminal-screw screw-tr"></div>
                <div class="terminal-screw screw-bl"></div>
                <div class="terminal-screw screw-br"></div>

                {{-- Status Bar Atas Mesin --}}
                <div class="flex items-center justify-between border-b border-slate-800 pb-3 mb-4">
                    <div class="flex items-center gap-2.5">
                        <span class="h-3 w-3 rounded-full bg-emerald-400 shadow-[0_0_12px_#10b981] inline-block"></span>
                        <span class="font-mono text-xs font-extrabold tracking-wider text-slate-300 uppercase">NEXUS-TAP-SMART GATE</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="font-mono text-xs font-bold text-amber-400" id="liveClock">00:00:00 WIB</span>
                        <div class="flex items-center gap-1 text-[11px] font-semibold text-slate-400 bg-slate-800/80 px-2 py-0.5 rounded-md border border-slate-700">
                            <i class="bi bi-wifi text-emerald-400"></i> ONLINE
                        </div>
                    </div>
                </div>

                {{-- 2. LCD DISPLAY (Layar Digital Mesin) --}}
                <div id="lcdDisplay" class="lcd-screen">
                    <div id="ledGlow" class="led-ambient-glow"></div>

                    {{-- Layar Standby --}}
                    <div id="screenStandby" class="space-y-3 py-2 w-full">
                        <div class="relative inline-flex items-center justify-center">
                            <div class="h-16 w-16 rounded-2xl bg-cyan-500/10 border border-cyan-500/30 flex items-center justify-center text-cyan-400 text-3xl shadow-[0_0_24px_rgba(6,182,212,0.2)] animate-pulseGlow">
                                <i class="bi bi-reception-4"></i>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-base sm:text-lg font-black tracking-wide text-white uppercase">TEMPELKAN KARTU SISWA</h3>
                            <p class="text-xs text-slate-400 mt-1 font-mono">Dekatkan kartu RFID atau barcode NISN ke area sensor</p>
                        </div>
                        <div class="inline-flex items-center gap-2 rounded-full bg-slate-900 border border-slate-800 px-3.5 py-1 text-[11px] font-semibold text-slate-400">
                            <i class="bi bi-geo-alt-fill text-amber-400"></i> Lokasi: <span id="labelDeviceLocation" class="text-white font-bold font-mono">GATE-TAP-01</span>
                        </div>
                    </div>

                    {{-- Layar Memproses (Spinner) --}}
                    <div id="screenScanning" class="hidden space-y-3 py-6">
                        <div class="inline-block animate-spin rounded-full h-10 w-10 border-4 border-cyan-500 border-t-transparent"></div>
                        <p class="text-xs font-mono font-bold text-cyan-400 uppercase tracking-widest">MEMPROSES KARTU...</p>
                    </div>

                    {{-- Layar Hasil (Sukses / Terlambat / Sudah Absen / Gagal) --}}
                    <div id="screenResult" class="hidden w-full space-y-3 animate-result py-1">
                        <div class="flex items-center justify-center gap-3.5 flex-wrap sm:flex-nowrap">
                            <img id="resPhoto" src="" class="h-16 w-16 rounded-2xl object-cover ring-2 ring-emerald-400 shadow-md hidden shrink-0" alt="Foto">
                            <div id="resIconWrapper" class="h-14 w-14 rounded-2xl flex items-center justify-center text-2xl shadow-lg shrink-0"></div>
                            <div class="text-left min-w-0">
                                <span id="resBadge" class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-black uppercase tracking-wider mb-1"></span>
                                <h4 id="resName" class="text-base sm:text-lg font-black text-white leading-tight truncate">Nama Siswa</h4>
                                <p id="resDetails" class="text-xs font-mono text-slate-300 mt-0.5 truncate"></p>
                            </div>
                        </div>
                        <div id="resMessageBanner" class="rounded-xl p-2.5 text-xs font-bold font-mono border"></div>
                    </div>
                </div>

                {{-- 3. AREA SENSOR SCAN / FORM INPUT TAP --}}
                <div class="tap-sensor-zone">
                    <form id="tapForm" onsubmit="event.preventDefault(); submitTap();" class="space-y-3">
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500 pointer-events-none">
                                <i class="bi bi-upc-scan text-base text-amber-400"></i>
                            </span>
                            <input type="text" id="inputCardId" autocomplete="off" autofocus
                                class="w-full rounded-xl border border-slate-700 bg-slate-900 py-3 pl-10 pr-24 font-mono text-sm font-bold text-white placeholder-slate-500 focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/20 tracking-wider uppercase transition shadow-inner"
                                placeholder="Scan / ketik UID RFID atau NISN">
                            <button type="submit" id="btnSubmitTap"
                                class="absolute inset-y-1.5 right-1.5 inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-4 text-xs font-extrabold text-white shadow-md hover:bg-emerald-500 transition active:scale-95 cursor-pointer">
                                <i class="bi bi-hand-index-thumb"></i> TAP
                            </button>
                        </div>
                        <div class="flex flex-wrap items-center justify-between text-[11px] text-slate-400 gap-2 px-1">
                            <div class="flex items-center gap-3">
                                <label class="flex items-center gap-1.5 cursor-pointer hover:text-white select-none">
                                    <input type="checkbox" id="checkSound" checked class="rounded border-slate-700 bg-slate-900 text-cyan-500 focus:ring-0">
                                    <span>Buzzer Suara</span>
                                </label>
                                <span class="text-slate-600">•</span>
                                <label class="flex items-center gap-1.5 cursor-pointer hover:text-white select-none">
                                    <input type="checkbox" id="checkAutoFocus" checked class="rounded border-slate-700 bg-slate-900 text-cyan-500 focus:ring-0">
                                    <span>Auto-Focus Scanner USB</span>
                                </label>
                            </div>
                            <div class="flex items-center gap-1.5 font-mono">
                                <span>Mesin:</span>
                                <select id="selectDeviceId" onchange="document.getElementById('labelDeviceLocation').textContent = this.value;"
                                    class="rounded-md border border-slate-700 bg-slate-900 px-2 py-0.5 text-[11px] font-bold text-slate-300 focus:border-cyan-500">
                                    <option value="GATE-TAP-01">GATE-TAP-01 (Lobi Utama)</option>
                                    <option value="GATE-TAP-02">GATE-TAP-02 (Gerbang Belakang)</option>
                                    <option value="STAND-PERPUS">STAND-PERPUS (Perpustakaan)</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- 2. SAMPLE KARTU SISWA UNTUK TEST CEPAT --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-2">
                        <i class="bi bi-person-badge text-brand-600"></i> Sample Kartu Siswa untuk Tes Cepat:
                    </h3>
                    <span class="text-[11px] text-slate-400">Klik untuk langsung uji coba tap</span>
                </div>

                <div class="sample-student-grid">
                    @forelse($students->take(6) as $s)
                        <button type="button" onclick="quickTap('{{ $s->rfid ?: $s->nisn ?: $s->nis }}', '{{ addslashes($s->name) }}')"
                            class="student-sample-btn group">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="h-8 w-8 rounded-lg bg-brand-50 border border-brand-200 flex items-center justify-center font-black text-xs text-brand-600 shadow-2xs shrink-0 group-hover:scale-105 transition">
                                    {{ substr($s->name, 0, 1) }}
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs font-bold text-slate-800 truncate group-hover:text-brand-700">{{ $s->name }}</div>
                                    <div class="text-[10px] text-slate-500 font-mono truncate">
                                        {{ $s->rfid ? 'RFID: ' . $s->rfid : 'NISN: ' . $s->nisn }} · {{ $s->class?->name }}
                                    </div>
                                </div>
                            </div>
                            <span class="text-xs font-bold text-brand-600 shrink-0 group-hover:translate-x-0.5 transition">&rarr;</span>
                        </button>
                    @empty
                        <p class="text-xs text-slate-400 col-span-2">Belum ada data siswa.</p>
                    @endforelse
                </div>

                {{-- Action Bar: Test Invalid Card --}}
                <div class="pt-2 border-t border-slate-100 flex items-center justify-between flex-wrap gap-2">
                    <button type="button" onclick="quickTap('KARTU-PALSU-9999', 'Uji Coba Kartu Tidak Terdaftar')"
                        class="inline-flex items-center gap-1.5 text-xs font-bold text-rose-600 hover:text-rose-700 transition cursor-pointer">
                        <i class="bi bi-x-circle"></i> Tes Kartu Tidak Terdaftar (Error 404)
                    </button>
                    <a href="{{ route('students.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
                        Atur RFID di Data Siswa &rarr;
                    </a>
                </div>
            </div>

        </div>

        {{-- ============================================================ --}}
        {{-- KOLOM KANAN: LOG AKTIVITAS TAP & API INSPECTOR               --}}
        {{-- ============================================================ --}}
        <div class="sim-right-column space-y-5">

            {{-- 1. LIVE TAP ACTIVITY LOG TABLE --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="border-b border-slate-100 bg-slate-50/80 px-4 py-3 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-list-check text-brand-600"></i>
                        <span class="text-xs font-bold text-slate-800 uppercase tracking-wider">Log Tap Sesi Ini</span>
                    </div>
                    <span id="logCounter" class="text-xs font-bold font-mono px-2 py-0.5 rounded-full bg-slate-200 text-slate-700">0 Tap</span>
                </div>
                <div class="max-h-[260px] overflow-y-auto divide-y divide-slate-100" id="logContainer">
                    <div id="emptyLogState" class="p-8 text-center text-slate-400">
                        <i class="bi bi-clock-history text-2xl d-block mb-1 text-slate-300"></i>
                        <p class="text-xs">Belum ada riwayat tap pada sesi ini.</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">Lakukan tap kartu di sebelah kiri untuk melihat log.</p>
                    </div>
                </div>
            </div>

            {{-- 2. API INTEGRATION INSPECTOR --}}
            <div class="rounded-2xl border border-slate-800 bg-slate-900 p-4 text-white shadow-sm space-y-3 font-mono text-xs">
                <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                    <span class="text-[11px] font-bold text-cyan-400 uppercase tracking-wider flex items-center gap-1.5">
                        <i class="bi bi-code-slash"></i> API Integration Inspector
                    </span>
                    <span id="httpStatusBadge" class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-800 text-slate-400">IDLE</span>
                </div>

                {{-- Endpoint Information --}}
                <div class="space-y-1 text-[11px]">
                    <div class="text-slate-400">Endpoint:</div>
                    <div class="text-emerald-400 bg-slate-950 p-2 rounded-lg border border-slate-800 break-all select-all">
                        POST {{ $endpointUrl }}
                    </div>
                </div>

                <div class="space-y-1 text-[11px]">
                    <div class="flex items-center justify-between text-slate-400">
                        <span>Header:</span>
                        <button type="button" onclick="navigator.clipboard.writeText('{{ $apiKey }}'); alert('API Key disalin!');" class="text-cyan-400 hover:underline text-[10px] cursor-pointer">Salin Key</button>
                    </div>
                    <div class="text-slate-300 bg-slate-950 p-2 rounded-lg border border-slate-800 break-all">
                        X-Api-Key: {{ substr($apiKey, 0, 12) }}...
                    </div>
                </div>

                {{-- Server Response JSON --}}
                <div class="space-y-1 text-[11px]">
                    <div class="flex items-center justify-between text-slate-400">
                        <span>Server Response JSON:</span>
                        <span id="responseLatency" class="text-[10px] text-slate-500"></span>
                    </div>
                    <pre id="jsonResponseBox" class="bg-slate-950 p-2.5 rounded-xl border border-slate-800 text-slate-300 overflow-x-auto text-[11px] leading-relaxed max-h-[160px]">{ "status": "waiting_for_tap" }</pre>
                </div>
            </div>

            {{-- 3. PANDUAN UNTUK TEKNISI IOT / MESIN TAP --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm space-y-2">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                    <i class="bi bi-cpu text-brand-600"></i> Panduan Mesin Tap / IoT Developer:
                </h4>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Kirimkan HTTP POST ke endpoint LMS di atas dengan payload JSON:
                </p>
                <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-200 font-mono text-[11px] text-slate-700 leading-relaxed">
                    {<br>
                    &nbsp;&nbsp;<span class="text-brand-600 font-bold">"card_id"</span>: <span class="text-amber-700 font-bold">"04CDC2EB21"</span>, <span class="text-slate-400">// UID kartu atau NISN</span><br>
                    &nbsp;&nbsp;<span class="text-brand-600 font-bold">"device_id"</span>: <span class="text-amber-700 font-bold">"GATE-01"</span> <span class="text-slate-400">// ID Mesin (opsional)</span><br>
                    }
                </div>
            </div>

        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
    // Live Digital Clock
    function updateClock() {
        const now = new Date();
        const hrs = String(now.getHours()).padStart(2, '0');
        const mins = String(now.getMinutes()).padStart(2, '0');
        const secs = String(now.getSeconds()).padStart(2, '0');
        const clockEl = document.getElementById('liveClock');
        if (clockEl) clockEl.textContent = `${hrs}:${mins}:${secs} WIB`;
    }
    setInterval(updateClock, 1000);
    updateClock();

    // Web Audio API Synthesizer (Realistic hardware beeps)
    let audioCtx = null;
    function getAudioContext() {
        if (!audioCtx) {
            audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        }
        return audioCtx;
    }

    function playBeep(type) {
        if (!document.getElementById('checkSound').checked) return;
        try {
            const ctx = getAudioContext();
            if (ctx.state === 'suspended') {
                ctx.resume();
            }

            if (type === 'success') {
                // Double high beep (Success Hadir)
                beepTone(ctx, 1200, 0.08, 0);
                beepTone(ctx, 1600, 0.12, 0.1);
            } else if (type === 'late' || type === 'already') {
                // Warning beep
                beepTone(ctx, 800, 0.18, 0);
            } else {
                // Low buzz error (Reject / 404)
                beepTone(ctx, 350, 0.28, 0, 'sawtooth');
            }
        } catch(e) {
            console.log('Audio not allowed yet:', e);
        }
    }

    function beepTone(ctx, freq, duration, delay, wave = 'sine') {
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.type = wave;
        osc.frequency.setValueAtTime(freq, ctx.currentTime + delay);
        gain.gain.setValueAtTime(0.15, ctx.currentTime + delay);
        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + delay + duration);
        osc.connect(gain);
        gain.connect(ctx.destination);
        osc.start(ctx.currentTime + delay);
        osc.stop(ctx.currentTime + delay + duration);
    }

    let tapCount = 0;
    const apiUrl = "{{ $endpointUrl }}";
    const apiKey = "{{ $apiKey }}";

    function quickTap(identifier, name) {
        document.getElementById('inputCardId').value = identifier;
        submitTap();
    }

    async function submitTap() {
        const input = document.getElementById('inputCardId');
        const cardId = input.value.trim();
        if (!cardId) {
            input.focus();
            return;
        }

        const deviceId = document.getElementById('selectDeviceId').value;
        const btn = document.getElementById('btnSubmitTap');
        btn.disabled = true;

        // UI Scanning State
        document.getElementById('screenStandby').classList.add('hidden');
        document.getElementById('screenResult').classList.add('hidden');
        document.getElementById('screenScanning').classList.remove('hidden');

        const startTime = performance.now();

        try {
            const response = await fetch(apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Api-Key': apiKey,
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    card_id: cardId,
                    device_id: deviceId
                })
            });

            const latency = Math.round(performance.now() - startTime);
            document.getElementById('responseLatency').textContent = `${latency} ms`;

            const data = await response.json();

            // Display in Inspector
            document.getElementById('jsonResponseBox').textContent = JSON.stringify(data, null, 2);
            const statusBadge = document.getElementById('httpStatusBadge');
            statusBadge.textContent = `HTTP ${response.status}`;
            statusBadge.className = `px-2 py-0.5 rounded text-[10px] font-bold ${response.ok ? 'bg-emerald-900 text-emerald-300' : 'bg-rose-900 text-rose-300'}`;

            // Render on LCD Screen
            renderLcdResult(response.ok, data);

            // Add to Live Log
            addLiveLog(response.ok, data, cardId);

        } catch (error) {
            console.error('Tap Error:', error);
            document.getElementById('jsonResponseBox').textContent = JSON.stringify({ error: error.message }, null, 2);
            renderLcdResult(false, { message: 'Koneksi ke server LMS terputus / gagal.' });
            playBeep('error');
        } finally {
            btn.disabled = false;
            input.value = '';
            if (document.getElementById('checkAutoFocus').checked) {
                input.focus();
            }
        }
    }

    function renderLcdResult(isOk, data) {
        document.getElementById('screenScanning').classList.add('hidden');
        const screenResult = document.getElementById('screenResult');
        screenResult.classList.remove('hidden');

        const glow = document.getElementById('ledGlow');
        const iconWrapper = document.getElementById('resIconWrapper');
        const badge = document.getElementById('resBadge');
        const name = document.getElementById('resName');
        const details = document.getElementById('resDetails');
        const banner = document.getElementById('resMessageBanner');
        const photo = document.getElementById('resPhoto');

        if (isOk) {
            const isAlready = data.action === 'already_present';
            const isLate = data.status === 'terlambat' || data.attendance?.status === 'terlambat';

            if (data.student?.photo_url) {
                photo.src = data.student.photo_url;
                photo.classList.remove('hidden');
            } else {
                photo.classList.add('hidden');
            }

            name.textContent = data.student?.name || 'Siswa';
            details.textContent = `${data.student?.class || '-'} • NIS: ${data.student?.nis || '-'}`;

            if (isAlready) {
                playBeep('already');
                glow.style.background = "rgba(245, 158, 11, 0.25)";
                iconWrapper.className = "h-14 w-14 rounded-2xl bg-amber-500/20 text-amber-400 flex items-center justify-center text-3xl shadow-lg border border-amber-500/40";
                iconWrapper.innerHTML = '<i class="bi bi-info-circle-fill"></i>';
                badge.className = "inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-black uppercase tracking-wider bg-amber-500/20 text-amber-300 border border-amber-500/30";
                badge.textContent = "SUDAH ABSEN HARI INI";
                banner.className = "rounded-xl p-2.5 text-xs font-bold font-mono border bg-amber-950/40 text-amber-300 border-amber-500/30";
                banner.textContent = data.message;
            } else if (isLate) {
                playBeep('late');
                glow.style.background = "rgba(245, 158, 11, 0.35)";
                iconWrapper.className = "h-14 w-14 rounded-2xl bg-amber-500/20 text-amber-400 flex items-center justify-center text-3xl shadow-lg border border-amber-500/40";
                iconWrapper.innerHTML = '<i class="bi bi-clock-fill"></i>';
                badge.className = "inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-black uppercase tracking-wider bg-amber-500/20 text-amber-300 border border-amber-500/30";
                badge.textContent = `TERLAMBAT (${data.attendance?.check_in || ''})`;
                banner.className = "rounded-xl p-2.5 text-xs font-bold font-mono border bg-amber-950/40 text-amber-300 border-amber-500/30";
                banner.textContent = `Presensi Berhasil: Masuk pukul ${data.attendance?.check_in} (Terlambat)`;
            } else {
                playBeep('success');
                glow.style.background = "rgba(16, 185, 129, 0.35)";
                iconWrapper.className = "h-14 w-14 rounded-2xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-3xl shadow-lg border border-emerald-500/40";
                iconWrapper.innerHTML = '<i class="bi bi-check-circle-fill"></i>';
                badge.className = "inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-black uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-500/30";
                badge.textContent = `HADIR TEPAT WAKTU (${data.attendance?.check_in || ''})`;
                banner.className = "rounded-xl p-2.5 text-xs font-bold font-mono border bg-emerald-950/40 text-emerald-300 border-emerald-500/30";
                banner.textContent = `Presensi Berhasil: Masuk pukul ${data.attendance?.check_in} WIB`;
            }
        } else {
            playBeep('error');
            photo.classList.add('hidden');
            glow.style.background = "rgba(244, 63, 94, 0.35)";
            iconWrapper.className = "h-14 w-14 rounded-2xl bg-rose-500/20 text-rose-400 flex items-center justify-center text-3xl shadow-lg border border-rose-500/40";
            iconWrapper.innerHTML = '<i class="bi bi-x-circle-fill"></i>';
            badge.className = "inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-black uppercase tracking-wider bg-rose-500/20 text-rose-300 border border-rose-500/30";
            badge.textContent = "KARTU DITOLAK";
            name.textContent = "Identitas Tidak Dikenal";
            details.textContent = "Kartu / ID belum terdaftar di database siswa";
            banner.className = "rounded-xl p-2.5 text-xs font-bold font-mono border bg-rose-950/40 text-rose-300 border-rose-500/30";
            banner.textContent = data.message || "Gagal memproses absensi";
        }

        // Auto reset LCD back to standby after 4.5 seconds
        clearTimeout(window.lcdTimeout);
        window.lcdTimeout = setTimeout(() => {
            document.getElementById('screenResult').classList.add('hidden');
            document.getElementById('screenStandby').classList.remove('hidden');
            document.getElementById('ledGlow').style.background = "rgba(6, 182, 212, 0.15)";
        }, 4500);
    }

    function addLiveLog(isOk, data, rawId) {
        tapCount++;
        document.getElementById('logCounter').textContent = `${tapCount} Tap`;
        const emptyState = document.getElementById('emptyLogState');
        if (emptyState) emptyState.remove();

        const container = document.getElementById('logContainer');
        const now = new Date();
        const timeStr = String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0') + ':' + String(now.getSeconds()).padStart(2, '0');

        let badgeBg = 'bg-rose-100 text-rose-700';
        let badgeLabel = 'Ditolak';
        let studentName = `ID: ${rawId}`;
        let subText = data.message;

        if (isOk) {
            studentName = data.student?.name || 'Siswa';
            subText = `${data.student?.class || ''} • Jam: ${data.attendance?.check_in || timeStr}`;
            if (data.action === 'already_present') {
                badgeBg = 'bg-amber-100 text-amber-800';
                badgeLabel = 'Sudah Absen';
            } else if (data.status === 'terlambat' || data.attendance?.status === 'terlambat') {
                badgeBg = 'bg-amber-100 text-amber-700';
                badgeLabel = 'Terlambat';
            } else {
                badgeBg = 'bg-emerald-100 text-emerald-800';
                badgeLabel = 'Hadir';
            }
        }

        const row = document.createElement('div');
        row.className = "p-3 flex items-center justify-between text-xs hover:bg-slate-50 transition animate-result";
        row.innerHTML = `
            <div class="flex items-center gap-2.5 min-w-0">
                <span class="font-mono text-[11px] text-slate-400 font-bold shrink-0">${timeStr}</span>
                <div class="min-w-0">
                    <div class="font-bold text-slate-900 truncate">${studentName}</div>
                    <div class="text-[10px] text-slate-500 truncate">${subText}</div>
                </div>
            </div>
            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-black uppercase shrink-0 ${badgeBg}">${badgeLabel}</span>
        `;

        container.prepend(row);
    }
</script>
@endpush
