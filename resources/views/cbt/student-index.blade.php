@extends('layouts.app')

@section('title', 'Ruang Ujian Siswa (CBT)')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@600;700;800&display=swap" rel="stylesheet">
<style>
    .token-input {
        font-family: 'JetBrains Mono', monospace;
        letter-spacing: 0.25em;
        text-transform: uppercase;
        font-size: 1.5rem;
    }
</style>
@endpush

@section('content')
<div class="mx-auto max-w-5xl space-y-6 p-4 sm:p-6 lg:p-8">
    {{-- WELCOME BANNER SISWA --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-emerald-800 via-teal-800 to-slate-900 p-6 sm:p-8 text-white shadow-xl">
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-emerald-300 border border-white/15">
                    <i class="bi bi-person-badge"></i>
                    <span>PORTAL UJIAN CBT &bull; {{ strtoupper(config('school.name', 'SEKOLAH DIGITAL')) }}</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Selamat Datang, {{ $student->user?->name ?? 'Siswa' }}</h1>
                <p class="text-xs sm:text-sm text-slate-300 max-w-xl">
                    Kelas: <strong>{{ $student->class?->name ?? '-' }}</strong> &bull; NISN: <strong>{{ $student->nisn ?? '-' }}</strong>.
                    Pastikan Anda telah meminta <strong>Token Ujian 5-Menit</strong> kepada pengawas di ruang ujian sebelum menekan tombol Mulai Ujian.
                </p>
            </div>

            <div class="flex items-center gap-3 rounded-2xl bg-white/10 p-4 border border-white/15 backdrop-blur-md shrink-0">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-500 text-slate-900 font-bold text-2xl">
                    <i class="bi bi-shield-lock-fill"></i>
                </div>
                <div>
                    <div class="text-[11px] font-bold uppercase text-emerald-300">Sistem Keamanan</div>
                    <div class="text-sm font-bold text-white">Dynamic Token 5M</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ALERT NOTIFIKASI --}}
    @if(session('error'))
    <div class="flex items-center gap-3 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800 shadow-sm">
        <i class="bi bi-x-circle-fill text-xl text-rose-600 shrink-0"></i>
        <div class="flex-1 font-medium">{{ session('error') }}</div>
    </div>
    @endif

    @if(session('success'))
    <div class="flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800 shadow-sm">
        <i class="bi bi-check-circle-fill text-xl text-emerald-600 shrink-0"></i>
        <div class="flex-1 font-medium">{{ session('success') }}</div>
    </div>
    @endif

    {{-- DAFTAR UJIAN YANG TERSEDIA --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="border-b border-slate-200 px-6 py-4 bg-slate-50/50 flex items-center justify-between">
            <div>
                <h2 class="text-sm font-bold text-slate-900">Paket Ujian CBT Tersedia</h2>
                <p class="text-xs text-slate-500">Ujian yang dijadwalkan untuk kelas {{ $student->class?->name ?? 'Anda' }}</p>
            </div>
            <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-800">
                {{ $quizzes->count() }} Paket
            </span>
        </div>

        @if($quizzes->isEmpty())
        <div class="p-12 text-center">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 mb-3">
                <i class="bi bi-journal-x text-2xl"></i>
            </div>
            <h4 class="text-base font-bold text-slate-800">Tidak Ada Ujian Aktif</h4>
            <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1">
                Saat ini belum ada paket ujian CBT yang sedang dibuka untuk kelas Anda. Silakan hubungi guru mata pelajaran terkait.
            </p>
        </div>
        @else
        <div class="divide-y divide-slate-100 p-6 space-y-4">
            @foreach($quizzes as $quiz)
            @php
                $attempt = $attempts->get($quiz->id);
                $isCompleted = $attempt && $attempt->isCompleted();
                $isOngoing = $attempt && $attempt->isOngoing();
            @endphp
            <div class="rounded-2xl border border-slate-200/80 bg-white p-5 hover:border-emerald-300 hover:shadow-md transition">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="space-y-1.5 flex-1">
                        <div class="flex items-center gap-2">
                            <span class="rounded-md bg-emerald-50 px-2 py-0.5 text-xs font-bold text-emerald-700 border border-emerald-200">
                                {{ $quiz->classSubject?->subject?->name ?? 'Mata Pelajaran' }}
                            </span>
                            <span class="text-xs text-slate-400">&bull;</span>
                            <span class="text-xs text-slate-500">
                                Guru: {{ $quiz->classSubject?->teacher?->user?->name ?? '-' }}
                            </span>
                        </div>

                        <h3 class="text-base font-bold text-slate-900">{{ $quiz->title }}</h3>

                        <div class="flex flex-wrap items-center gap-4 text-xs text-slate-500 pt-1">
                            <div class="flex items-center gap-1.5">
                                <i class="bi bi-clock-history text-slate-400"></i>
                                <span>Durasi: <strong>{{ $quiz->duration }} Menit</strong></span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <i class="bi bi-card-checklist text-slate-400"></i>
                                <span>Jumlah Soal: <strong>{{ $quiz->questions_count }} Butir</strong></span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <i class="bi bi-award text-slate-400"></i>
                                <span>KKM: <strong>{{ $quiz->pass_score }}</strong></span>
                            </div>
                        </div>

                        @if($quiz->description)
                        <p class="text-xs text-slate-600 bg-slate-50 p-2.5 rounded-xl border border-slate-100 mt-2">
                            {{ $quiz->description }}
                        </p>
                        @endif
                    </div>

                    {{-- STATUS & ACTION BUTTON --}}
                    <div class="flex sm:flex-col items-end justify-between sm:justify-center gap-3 shrink-0">
                        @if($isCompleted)
                            <div class="text-right">
                                <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700 border border-blue-200">
                                    <i class="bi bi-check2-all"></i> Selesai
                                </span>
                                @if($quiz->show_results)
                                <div class="mt-1 text-xs font-bold text-slate-800">
                                    Nilai: <span class="text-emerald-600 text-sm">{{ $attempt->score }}</span> / 100
                                </div>
                                @endif
                            </div>
                            <a href="{{ route('cbt.student.result', $attempt->id) }}" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100 transition">
                                <i class="bi bi-eye"></i>
                                <span>Lihat Bukti Tes</span>
                            </a>
                        @elseif($isOngoing)
                            <div class="text-right">
                                <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-700 border border-amber-200 animate-pulse">
                                    <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span> Sedang Dikerjakan
                                </span>
                                <div class="text-[11px] text-slate-500 mt-0.5">Sisa Waktu: {{ round($attempt->remaining_seconds / 60) }} Menit</div>
                            </div>
                            <a href="{{ route('cbt.student.exam', $attempt->id) }}" class="inline-flex items-center gap-1.5 rounded-xl bg-amber-500 px-5 py-2.5 text-xs font-bold text-slate-900 shadow-sm hover:bg-amber-400 transition">
                                <i class="bi bi-arrow-right-circle-fill"></i>
                                <span>Lanjutkan Ujian</span>
                            </a>
                        @else
                            <button type="button" onclick="openTokenModal({{ $quiz->id }}, '{{ addslashes($quiz->title) }}', {{ $quiz->duration }})" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-emerald-500 transition">
                                <i class="bi bi-key-fill"></i>
                                <span>Mulai Ujian</span>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

{{-- MODAL INPUT TOKEN UJIAN 5 MENIT --}}
<div id="tokenModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm overflow-y-auto p-4 flex items-center justify-center">
    <div class="relative w-full max-w-md rounded-3xl bg-white shadow-2xl overflow-hidden p-6 text-center">
        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-600 mb-3">
            <i class="bi bi-shield-lock-fill text-2xl"></i>
        </div>

        <h3 class="text-lg font-bold text-slate-900">Konfirmasi Masuk Ujian CBT</h3>
        <p id="modalExamTitle" class="text-xs font-medium text-emerald-700 mt-0.5">-</p>
        <p class="text-xs text-slate-500 mt-2">
            Silakan masukkan <strong>Token Ujian 5-Menit</strong> yang tertera di layar proyektor / diberikan oleh pengawas ujian di ruang kelas Anda.
        </p>

        <form id="tokenForm" method="POST" action="" class="mt-5 space-y-4">
            @csrf

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-2">
                    Masukkan Token Ujian
                </label>
                <input type="text" name="token" id="tokenInput" required maxlength="10" placeholder="CONTOH: 7F9-K2A" autocomplete="off" class="token-input w-full rounded-2xl border-2 border-emerald-300 bg-emerald-50/30 px-4 py-3 text-center font-bold text-slate-900 placeholder-slate-300 focus:border-emerald-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 shadow-inner">
                <span class="text-[10px] text-slate-400 block mt-1.5">Token bersifat sensitif dan diperbarui otomatis setiap 5 menit.</span>
            </div>

            <div class="rounded-xl bg-slate-50 p-3 text-[11px] text-slate-600 text-left space-y-1">
                <div class="font-bold flex items-center gap-1 text-slate-800">
                    <i class="bi bi-info-circle-fill text-emerald-600"></i>
                    <span>Ketentuan Pengerjaan:</span>
                </div>
                <div>&bull; Durasi ujian: <strong id="modalDurationText">-</strong> menit.</div>
                <div>&bull; Jawaban otomatis tersimpan ke server saat memilih opsi.</div>
                <div>&bull; Dilarang membuka tab lain atau keluar dari layar ujian.</div>
            </div>

            <div class="flex items-center justify-center gap-2 pt-2">
                <button type="button" onclick="closeTokenModal()" class="w-1/3 rounded-xl border border-slate-200 py-2.5 text-xs font-semibold text-slate-600 hover:bg-slate-100 transition">
                    Batal
                </button>
                <button type="submit" class="w-2/3 inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-emerald-500 transition">
                    <i class="bi bi-unlock-fill"></i>
                    <span>Verifikasi &amp; Mulai</span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openTokenModal(quizId, title, duration) {
        const form = document.getElementById('tokenForm');
        form.action = "{{ url('cbt/student/start') }}/" + quizId;
        document.getElementById('modalExamTitle').textContent = title;
        document.getElementById('modalDurationText').textContent = duration;
        document.getElementById('tokenInput').value = '';
        document.getElementById('tokenModal').classList.remove('hidden');
        setTimeout(() => document.getElementById('tokenInput').focus(), 150);
    }

    function closeTokenModal() {
        document.getElementById('tokenModal').classList.add('hidden');
    }

    // Auto uppercase dan format token
    document.getElementById('tokenInput').addEventListener('input', function(e) {
        this.value = this.value.toUpperCase();
    });
</script>
@endpush
@endsection
