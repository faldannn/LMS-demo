@extends('layouts.app')

@section('title', 'Pusat Ujian CBT & Monitor Token')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/katex.min.css">
<link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=JetBrains+Mono:wght@600;700;800&display=swap" rel="stylesheet">
<style>
    .token-display {
        font-family: 'JetBrains Mono', monospace;
        letter-spacing: 0.15em;
    }
    .pulse-glow {
        animation: pulseGlow 2.5s infinite;
    }
    @keyframes pulseGlow {
        0%, 100% { box-shadow: 0 0 15px rgba(16, 185, 129, 0.2); }
        50% { box-shadow: 0 0 25px rgba(16, 185, 129, 0.45); }
    }
</style>
@endpush

@section('content')
<div class="space-y-6 p-4 sm:p-6 lg:p-8">
    {{-- HEADER & BREADCRUMB --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-emerald-600">
                <i class="bi bi-shield-lock-fill"></i>
                <span>SISTEM CBT INTERNAL &bull; NEXUS LMS DEMO</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Pusat Ujian CBT &amp; Monitor Token</h1>
            <p class="mt-1 text-sm text-slate-500">Kelola ujian berbasis komputer, bank soal rumus MTK/Kimia/Fisika/Arab, dan pantau rotasi token 5-menit.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('cbt.template.download', 'excel') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                <i class="bi bi-file-earmark-excel text-emerald-600 text-sm"></i>
                <span>Template Excel</span>
            </a>
            <a href="{{ route('cbt.template.download', 'word') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                <i class="bi bi-file-earmark-word text-blue-600 text-sm"></i>
                <span>Template Word/Docs</span>
            </a>
            <a href="{{ route('cbt.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-xs font-semibold text-white shadow-sm transition hover:bg-emerald-500">
                <i class="bi bi-plus-circle-fill text-sm"></i>
                <span>Buat Ujian Baru</span>
            </a>
        </div>
    </div>

    {{-- ALERT NOTIFIKASI --}}
    @if(session('success'))
    <div class="flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50/80 p-4 text-sm text-emerald-800 shadow-sm">
        <i class="bi bi-check-circle-fill text-xl text-emerald-600 shrink-0"></i>
        <div class="flex-1 font-medium">{{ session('success') }}</div>
    </div>
    @endif

    @if(session('warning'))
    <div class="flex items-center gap-3 rounded-2xl border border-amber-200 bg-amber-50/80 p-4 text-sm text-amber-800 shadow-sm">
        <i class="bi bi-exclamation-triangle-fill text-xl text-amber-600 shrink-0"></i>
        <div class="flex-1 font-medium">{{ session('warning') }}</div>
    </div>
    @endif

    {{-- LIVE TOKEN MONITOR CARD (GENERATED EVERY 5 MINUTES) --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-slate-800 to-emerald-950 p-6 text-white shadow-xl">
        {{-- Background Geometric Patterns --}}
        <div class="absolute -right-16 -top-16 h-64 w-64 rounded-full bg-emerald-500/10 blur-3xl pointer-events-none"></div>
        <div class="absolute right-1/3 bottom-0 h-48 w-48 rounded-full bg-blue-500/10 blur-2xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="space-y-2 max-w-xl">
                <div class="inline-flex items-center gap-2 rounded-full border border-emerald-400/30 bg-emerald-500/20 px-3 py-1 text-xs font-semibold text-emerald-300">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span>SISTEM ROTASI TOKEN 5 MENIT AKTIF</span>
                </div>
                <h2 class="text-xl font-bold text-white sm:text-2xl">Token Ujian Siswa (Otomatis Berganti Tiap 5 Menit)</h2>
                <p class="text-xs text-slate-300 sm:text-sm">
                    Token ini diinput oleh siswa saat hendak memulai ujian CBT. Sistem memvalidasi token secara deterministik dengan toleransi transisi agar siswa tidak terputus saat pergantian waktu.
                </p>
            </div>

            {{-- TOKEN BADGE & COUNTDOWN --}}
            <div class="flex flex-col sm:flex-row items-center gap-4 rounded-2xl bg-white/10 p-5 backdrop-blur-md border border-white/15">
                <div class="text-center sm:text-left">
                    <div class="text-[11px] font-semibold uppercase tracking-wider text-emerald-300">Token Aktif Saat Ini</div>
                    <div id="liveTokenDisplay" class="token-display text-4xl sm:text-5xl font-extrabold text-white tracking-widest mt-1">
                        {{ $tokenInfo['token'] }}
                    </div>
                    <div class="mt-1 flex items-center justify-center sm:justify-start gap-1.5 text-xs text-slate-300">
                        <i class="bi bi-clock-history"></i>
                        <span>Ganti dalam: <strong id="countdownText" class="text-emerald-300 font-mono text-sm">--:--</strong></span>
                    </div>
                </div>

                {{-- Action Refresh Button --}}
                <div class="flex sm:flex-col gap-2 shrink-0">
                    <button id="btnCopyToken" type="button" onclick="copyCurrentToken()" class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-white/15 px-3 py-2 text-xs font-semibold text-white hover:bg-white/25 transition" title="Salin Token">
                        <i class="bi bi-clipboard"></i>
                        <span>Salin</span>
                    </button>
                    <button id="btnForceRefresh" type="button" onclick="forceRefreshToken()" class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-emerald-500 px-3.5 py-2 text-xs font-semibold text-slate-900 hover:bg-emerald-400 transition shadow-sm" title="Refresh Sekarang">
                        <i class="bi bi-arrow-repeat" id="refreshIcon"></i>
                        <span>Ganti Sekarang</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- STATISTIK KARTU CBT --}}
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
        <div class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Ujian</span>
                <i class="bi bi-file-earmark-text text-lg text-emerald-600"></i>
            </div>
            <div class="mt-2 text-2xl font-bold text-slate-900">{{ $quizzes->total() }}</div>
            <div class="text-[11px] text-slate-500 mt-0.5">Ujian tersimpan di sistem</div>
        </div>

        <div class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Ujian Aktif</span>
                <i class="bi bi-broadcast text-lg text-blue-600"></i>
            </div>
            <div class="mt-2 text-2xl font-bold text-blue-600">
                {{ $quizzes->where('is_published', true)->count() }}
            </div>
            <div class="text-[11px] text-slate-500 mt-0.5">Dapat diakses oleh siswa</div>
        </div>

        <div class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Dukungan Rumus</span>
                <i class="bi bi-calculator text-lg text-purple-600"></i>
            </div>
            <div class="mt-2 text-base font-bold text-slate-800">LaTeX KaTeX</div>
            <div class="text-[11px] text-slate-500 mt-0.5">MTK, Fisika, Kimia &amp; Arab</div>
        </div>

        <div class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Mode Sistem</span>
                <i class="bi bi-hdd-network text-lg text-emerald-600"></i>
            </div>
            <div class="mt-2 text-base font-bold text-emerald-600">In-System LMS</div>
            <div class="text-[11px] text-slate-500 mt-0.5">Tanpa link keluar (Direct)</div>
        </div>
    </div>

    {{-- FILTER & PENCARIAN --}}
    <div class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm">
        <form method="GET" action="{{ route('cbt.index') }}" class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex flex-1 flex-col gap-3 sm:flex-row sm:items-center">
                <div class="relative flex-1">
                    <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul ujian CBT..." class="w-full rounded-xl border border-slate-200 bg-slate-50/50 pl-9 pr-4 py-2 text-xs text-slate-800 placeholder-slate-400 focus:border-emerald-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-emerald-500">
                </div>

                <div class="w-full sm:w-56">
                    <select name="class_id" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-2 text-xs text-slate-800 focus:border-emerald-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-emerald-500">
                        <option value="">Semua Kelas</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" @selected(request('class_id') == $c->id)>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl bg-slate-900 px-4 py-2 text-xs font-semibold text-white transition hover:bg-slate-800">
                    <i class="bi bi-funnel"></i>
                    <span>Terapkan</span>
                </button>
                @if(request()->hasAny(['search', 'class_id']))
                    <a href="{{ route('cbt.index') }}" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-600 hover:bg-slate-50">Reset</a>
                @endif
            </div>
        </form>
    </div>

    {{-- DAFTAR UJIAN CBT --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200/80 px-6 py-4 bg-slate-50/50 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-900">Daftar Paket Ujian CBT</h3>
            <span class="rounded-full bg-slate-200/80 px-2.5 py-0.5 text-[11px] font-semibold text-slate-700">
                {{ $quizzes->total() }} Paket
            </span>
        </div>

        @if($quizzes->isEmpty())
        <div class="p-12 text-center">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 mb-3">
                <i class="bi bi-pencil-square text-2xl"></i>
            </div>
            <h4 class="text-base font-bold text-slate-800">Belum Ada Ujian CBT</h4>
            <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1">
                Silakan buat paket ujian CBT baru untuk memulai pengerjaan tes dengan token 5-menit dan dukungan rumus lengkap.
            </p>
            <a href="{{ route('cbt.create') }}" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-emerald-500 transition">
                <i class="bi bi-plus-circle"></i>
                <span>Buat Ujian CBT Sekarang</span>
            </a>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 text-[11px] font-bold uppercase tracking-wider text-slate-500 bg-slate-50/50">
                        <th class="py-3.5 pl-6 pr-3">Judul Ujian &amp; Mata Pelajaran</th>
                        <th class="py-3.5 px-3">Kelas &amp; Guru</th>
                        <th class="py-3.5 px-3 text-center">Durasi &amp; KKM</th>
                        <th class="py-3.5 px-3 text-center">Soal</th>
                        <th class="py-3.5 px-3 text-center">Peserta</th>
                        <th class="py-3.5 px-3 text-center">Status</th>
                        <th class="py-3.5 pr-6 pl-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                    @foreach($quizzes as $quiz)
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="py-4 pl-6 pr-3">
                            <div class="font-bold text-slate-900 text-sm">{{ $quiz->title }}</div>
                            <div class="text-[11px] text-emerald-600 font-medium mt-0.5">
                                {{ $quiz->classSubject?->subject?->name ?? 'Mata Pelajaran Umum' }}
                            </div>
                        </td>
                        <td class="py-4 px-3">
                            @php
                                $assignedClasses = $quiz->all_target_classes;
                            @endphp
                            @if($assignedClasses->isNotEmpty())
                                <div class="flex flex-wrap items-center gap-1.5 max-w-xs">
                                    @foreach($assignedClasses->take(3) as $cl)
                                        <span class="inline-flex items-center gap-1 rounded-md bg-emerald-50 px-2 py-0.5 text-[11px] font-bold text-emerald-700 border border-emerald-200">
                                            <i class="bi bi-mortarboard-fill text-[9px] text-emerald-600"></i>
                                            {{ $cl->name }}
                                        </span>
                                    @endforeach
                                    @if($assignedClasses->count() > 3)
                                        <span class="inline-flex items-center rounded-md bg-slate-100 px-1.5 py-0.5 text-[10px] font-bold text-slate-600 border border-slate-200" title="{{ $assignedClasses->pluck('name')->join(', ') }}">
                                            +{{ $assignedClasses->count() - 3 }} lainnya
                                        </span>
                                    @endif
                                </div>
                            @else
                                <div class="font-semibold text-slate-800">{{ $quiz->classSubject?->class?->name ?? 'Semua Kelas' }}</div>
                            @endif
                            <div class="text-[11px] text-slate-500 mt-1 flex items-center gap-1">
                                <i class="bi bi-person text-slate-400"></i>
                                <span>{{ $quiz->classSubject?->teacher?->user?->name ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="py-4 px-3 text-center">
                            <div class="font-bold text-slate-800">{{ $quiz->duration }} Menit</div>
                            <div class="text-[10px] text-slate-500">KKM: {{ $quiz->pass_score }}</div>
                        </td>
                        <td class="py-4 px-3 text-center">
                            <span class="inline-flex items-center gap-1 rounded-lg bg-slate-100 px-2 py-1 text-xs font-bold text-slate-700">
                                <i class="bi bi-card-checklist text-slate-500"></i>
                                {{ $quiz->questions_count }}
                            </span>
                        </td>
                        <td class="py-4 px-3 text-center">
                            <a href="{{ route('cbt.results', $quiz->id) }}" class="inline-flex items-center gap-1 rounded-lg bg-emerald-50 px-2 py-1 text-xs font-bold text-emerald-700 hover:bg-emerald-100 transition" title="Lihat Rekap Peserta">
                                <i class="bi bi-people-fill text-emerald-600"></i>
                                {{ $quiz->attempts_count }} Siswa
                            </a>
                        </td>
                        <td class="py-4 px-3 text-center">
                            @if($quiz->is_published)
                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-semibold text-emerald-700 border border-emerald-200">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-600 border border-slate-200">
                                    <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                                    Draft
                                </span>
                            @endif
                        </td>
                        <td class="py-4 pr-6 pl-3 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('cbt.show', $quiz->id) }}" class="inline-flex items-center gap-1 rounded-lg bg-slate-100 px-2.5 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-200 transition" title="Kelola Soal &amp; Import">
                                    <i class="bi bi-pencil-square"></i>
                                    <span>Kelola Soal</span>
                                </a>
                                <a href="{{ route('cbt.results', $quiz->id) }}" class="inline-flex items-center gap-1 rounded-lg bg-blue-50 px-2.5 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-100 transition" title="Hasil Nilai">
                                    <i class="bi bi-bar-chart-fill"></i>
                                    <span>Nilai</span>
                                </a>
                                <form method="POST" action="{{ route('cbt.destroy', $quiz->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus ujian CBT ini beserta seluruh butir soalnya?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-lg p-1.5 text-slate-400 hover:bg-rose-50 hover:text-rose-600 transition" title="Hapus Ujian">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($quizzes->hasPages())
        <div class="border-t border-slate-200 p-4">
            {{ $quizzes->links() }}
        </div>
        @endif
        @endif
    </div>
</div>

@push('scripts')
<script>
    // Konfigurasi Live Token 5-Menit
    let remainingSeconds = {{ $tokenInfo['remaining_seconds'] }};
    const intervalSeconds = {{ $tokenInfo['interval_seconds'] }};
    const tokenDisplay = document.getElementById('liveTokenDisplay');
    const countdownText = document.getElementById('countdownText');

    function updateTimerUI() {
        if (remainingSeconds < 0) remainingSeconds = 0;

        const minutes = Math.floor(remainingSeconds / 60);
        const seconds = remainingSeconds % 60;
        countdownText.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;

        // Warning color bila sisa waktu < 1 menit
        if (remainingSeconds <= 60) {
            countdownText.className = "text-rose-400 font-mono text-sm font-bold animate-pulse";
        } else {
            countdownText.className = "text-emerald-300 font-mono text-sm";
        }
    }

    // Interval Timer mundur setiap 1 detik
    const timerInterval = setInterval(() => {
        remainingSeconds--;
        if (remainingSeconds <= 0) {
            // Waktu 5 menit habis -> Fetch token baru dari server
            fetchNewToken();
        } else {
            updateTimerUI();
        }
    }, 1000);

    updateTimerUI();

    // Fetch Token Baru via AJAX
    function fetchNewToken() {
        fetch("{{ route('cbt.token.poll') }}", {
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(res => {
            if (res.success && res.data) {
                tokenDisplay.textContent = res.data.token;
                remainingSeconds = res.data.remaining_seconds;
                updateTimerUI();
            }
        })
        .catch(err => console.error("Error fetching token:", err));
    }

    // Force Refresh Token manual oleh admin
    function forceRefreshToken() {
        const icon = document.getElementById('refreshIcon');
        icon.classList.add('animate-spin');

        fetch("{{ route('cbt.token.refresh') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ quiz_id: 0 })
        })
        .then(res => res.json())
        .then(res => {
            icon.classList.remove('animate-spin');
            if (res.success && res.data) {
                tokenDisplay.textContent = res.data.token;
                remainingSeconds = res.data.remaining_seconds;
                updateTimerUI();
                alert('Token berhasil di-refresh ke periode baru: ' + res.data.token);
            }
        })
        .catch(err => {
            icon.classList.remove('animate-spin');
            console.error(err);
        });
    }

    // Salin token ke clipboard
    function copyCurrentToken() {
        const token = tokenDisplay.textContent.trim();
        navigator.clipboard.writeText(token).then(() => {
            alert('Token CBT "' + token + '" berhasil disalin ke clipboard!');
        });
    }
</script>
@endpush
@endsection
