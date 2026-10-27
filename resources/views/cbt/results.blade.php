@extends('layouts.app')

@section('title', 'Rekap Hasil Ujian CBT — ' . $quiz->title)

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/katex.min.css">
<link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Scheherazade+New:wght@400;700&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
<style>
    .math-content {
        line-height: 1.8;
    }
    .math-content .katex {
        font-size: 1.1em;
    }
    .font-arabic {
        font-family: 'Amiri', 'Scheherazade New', serif;
        font-size: 1.35rem;
        line-height: 2.2;
        direction: rtl;
        text-align: right;
    }
    @media print {
        header, aside, nav, .no-print {
            display: none !important;
        }
        body {
            background: white !important;
            color: black !important;
        }
    }
</style>
@endpush

@section('content')
<div class="space-y-6 p-4 sm:p-6 lg:p-8">
    {{-- BREADCRUMB & HEADER --}}
    <div class="no-print flex items-center gap-2 text-xs font-semibold text-slate-500">
        <a href="{{ route('cbt.index') }}" class="hover:text-emerald-600 transition">Ujian CBT</a>
        <i class="bi bi-chevron-right text-[10px]"></i>
        <a href="{{ route('cbt.show', $quiz->id) }}" class="hover:text-emerald-600 transition truncate max-w-xs">{{ $quiz->title }}</a>
        <i class="bi bi-chevron-right text-[10px]"></i>
        <span class="text-slate-900">Rekap Nilai Siswa</span>
    </div>

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Rekap Nilai Peserta Ujian CBT</h1>
            <p class="mt-1 text-xs text-slate-500 sm:text-sm">
                {{ $quiz->title }} &bull; Target Kelas: 
                <strong>
                    @php
                        $assignedClasses = $quiz->all_target_classes;
                    @endphp
                    @if($assignedClasses->isNotEmpty())
                        {{ $assignedClasses->pluck('name')->join(', ') }}
                    @else
                        {{ $quiz->classSubject?->class?->name ?? 'Semua Kelas' }}
                    @endif
                </strong> 
                &bull; KKM: <strong>{{ $quiz->pass_score }}</strong>
            </p>
        </div>

        <div class="no-print flex items-center gap-2">
            <button type="button" onclick="window.print()" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition">
                <i class="bi bi-printer"></i>
                <span>Cetak Rekap</span>
            </button>
            <a href="{{ route('cbt.show', $quiz->id) }}" class="inline-flex items-center gap-1.5 rounded-xl bg-slate-900 px-4 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-slate-800 transition">
                <i class="bi bi-arrow-left"></i>
                <span>Kembali ke Ujian</span>
            </a>
        </div>
    </div>

    {{-- STATISTIK SINGKAT --}}
    @php
        $completedAttempts = $quiz->attempts->where('status', 'completed');
        $avgScore = $completedAttempts->count() > 0 ? round($completedAttempts->avg('score'), 2) : 0;
        $maxScore = $completedAttempts->count() > 0 ? $completedAttempts->max('score') : 0;
        $passedCount = $completedAttempts->where('score', '>=', $quiz->pass_score)->count();
    @endphp

    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Peserta Ikut</div>
            <div class="mt-2 text-2xl font-bold text-slate-900">{{ $quiz->attempts->count() }} Siswa</div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Rata-Rata Kelas</div>
            <div class="mt-2 text-2xl font-bold text-blue-600">{{ $avgScore }}</div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Nilai Tertinggi</div>
            <div class="mt-2 text-2xl font-bold text-emerald-600">{{ $maxScore }}</div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Tuntas KKM</div>
            <div class="mt-2 text-2xl font-bold text-emerald-600">{{ $passedCount }} Siswa</div>
        </div>
    </div>

    {{-- TABEL HASIL SISWA --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-6 py-4 bg-slate-50/50 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-900">Daftar Pengerjaan Siswa</h3>
            <span class="rounded-full bg-slate-200/80 px-2.5 py-0.5 text-[11px] font-bold text-slate-700">
                {{ $quiz->attempts->count() }} Data
            </span>
        </div>

        @if($quiz->attempts->isEmpty())
        <div class="p-12 text-center text-slate-400 text-xs">
            <i class="bi bi-inbox text-3xl mb-2 block"></i>
            Belum ada siswa yang mengerjakan ujian ini.
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 text-[11px] font-bold uppercase tracking-wider text-slate-500 bg-slate-50/50">
                        <th class="py-3.5 pl-6 pr-3">No</th>
                        <th class="py-3.5 px-3">Nama Siswa &amp; NISN</th>
                        <th class="py-3.5 px-3 text-center">Kelas</th>
                        <th class="py-3.5 px-3 text-center">Waktu Mulai</th>
                        <th class="py-3.5 px-3 text-center">Waktu Selesai</th>
                        <th class="py-3.5 px-3 text-center">Status</th>
                        <th class="py-3.5 px-3 text-center">Nilai Akhir</th>
                        <th class="py-3.5 px-3 text-center">Keterangan</th>
                        <th class="py-3.5 pr-6 pl-3 text-center">Lembar Jawaban &amp; Kunci</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                    @foreach($quiz->attempts as $index => $attempt)
                    @php
                        $isPassed = $attempt->score >= $quiz->pass_score;
                    @endphp
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="py-4 pl-6 pr-3 font-semibold text-slate-400">{{ $index + 1 }}</td>
                        <td class="py-4 px-3">
                            <div class="font-bold text-slate-900">{{ $attempt->student?->user?->name ?? 'Siswa' }}</div>
                            <div class="text-[11px] text-slate-400">NISN: {{ $attempt->student?->nisn ?? '-' }}</div>
                        </td>
                        <td class="py-4 px-3 text-center font-medium text-slate-700">
                            <span class="inline-flex items-center gap-1 rounded-md bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-700">
                                {{ $attempt->student?->class?->name ?? ($quiz->classSubject?->class?->name ?? '-') }}
                            </span>
                        </td>
                        <td class="py-4 px-3 text-center text-slate-600">
                            {{ $attempt->start_time ? $attempt->start_time->format('d/m/Y H:i') : '-' }}
                        </td>
                        <td class="py-4 px-3 text-center text-slate-600">
                            {{ $attempt->end_time ? $attempt->end_time->format('d/m/Y H:i') : '-' }}
                        </td>
                        <td class="py-4 px-3 text-center">
                            @if($attempt->status === 'completed')
                                <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-[10px] font-bold text-emerald-700 border border-emerald-200">Selesai</span>
                            @elseif($attempt->status === 'ongoing')
                                <span class="rounded-full bg-amber-50 px-2.5 py-1 text-[10px] font-bold text-amber-700 border border-amber-200 animate-pulse">Mengerjakan</span>
                            @else
                                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[10px] font-bold text-slate-600">{{ $attempt->status }}</span>
                            @endif
                        </td>
                        <td class="py-4 px-3 text-center">
                            <span class="text-base font-black {{ $isPassed ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $attempt->score ?? 0 }}
                            </span>
                        </td>
                        <td class="py-4 px-3 text-center">
                            @if($attempt->status === 'completed')
                                <span class="rounded-md px-2 py-0.5 text-[10px] font-bold {{ $isPassed ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                    {{ $isPassed ? 'TUNTAS' : 'REMEDIAL' }}
                                </span>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="py-4 pr-6 pl-3 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <button type="button" onclick="openStudentAnswerModal({{ $attempt->id }})" class="inline-flex items-center gap-1.5 rounded-xl bg-purple-50 px-3 py-1.5 text-xs font-bold text-purple-700 border border-purple-200 hover:bg-purple-100 hover:border-purple-300 transition shadow-2xs" title="Lihat Nilai, Soal, Benar/Salah &amp; Kunci Jawaban">
                                    <i class="bi bi-file-earmark-check-fill text-purple-600"></i>
                                    <span>Lihat Jawaban</span>
                                </button>
                                <a href="{{ route('cbt.attempts.show', $attempt->id) }}" target="_blank" class="rounded-xl border border-slate-200 bg-white p-1.5 text-slate-500 hover:bg-slate-100 hover:text-slate-800 transition" title="Buka Halaman Penuh / Cetak Lembar">
                                    <i class="bi bi-box-arrow-up-right text-xs"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

{{-- MODAL LEMBAR JAWABAN & KUNCI SOAL SISWA --}}
<div id="studentAnswerModal" class="fixed inset-0 z-50 hidden bg-slate-950/70 backdrop-blur-md p-3 sm:p-6 flex items-start justify-center overflow-y-auto">
    <div class="relative w-full max-w-4xl rounded-3xl bg-white shadow-2xl flex flex-col max-h-[92vh] sm:max-h-[88vh] my-auto overflow-hidden animate-in fade-in zoom-in-95 duration-150">
        {{-- Sticky Header --}}
        <div class="shrink-0 border-b border-slate-100 px-6 py-4 flex items-center justify-between bg-slate-50">
            <div>
                <h3 id="modalStudentName" class="text-base font-bold text-slate-900">Lembar Jawaban Siswa &amp; Kunci Soal</h3>
                <p id="modalStudentMeta" class="text-xs text-slate-500">Memuat data lembar jawaban...</p>
            </div>
            <button type="button" onclick="closeStudentAnswerModal()" class="rounded-xl p-2 text-slate-400 hover:bg-slate-200 hover:text-slate-700 transition">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        {{-- Scrollable Content Body --}}
        <div id="modalBodyContent" class="flex-1 overflow-y-auto p-6 space-y-5 overscroll-contain">
            {{-- Loading Spinner --}}
            <div id="modalLoadingState" class="py-16 text-center">
                <div class="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-emerald-600 border-r-transparent"></div>
                <div class="mt-3 text-xs font-semibold text-slate-500">Mengambil data soal dan jawaban siswa...</div>
            </div>

            {{-- Main Content Loaded Dynamically via JS --}}
            <div id="modalRealContent" class="hidden space-y-5">
                {{-- Stat Summary --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/80 p-3 text-center">
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Nilai Akhir</div>
                        <div id="modalStatScore" class="text-2xl font-black text-slate-900 mt-0.5">0</div>
                        <div id="modalStatPassBadge" class="mt-1"></div>
                    </div>
                    <div class="rounded-2xl border border-emerald-200 bg-emerald-50/60 p-3 text-center">
                        <div class="text-[10px] font-bold uppercase tracking-wider text-emerald-600">Jawaban Benar</div>
                        <div id="modalStatCorrect" class="text-2xl font-black text-emerald-700 mt-0.5">0</div>
                        <div class="text-[10px] text-emerald-600 mt-0.5 font-medium">Soal Tepat</div>
                    </div>
                    <div class="rounded-2xl border border-rose-200 bg-rose-50/60 p-3 text-center">
                        <div class="text-[10px] font-bold uppercase tracking-wider text-rose-600">Jawaban Salah</div>
                        <div id="modalStatIncorrect" class="text-2xl font-black text-rose-700 mt-0.5">0</div>
                        <div class="text-[10px] text-rose-600 mt-0.5 font-medium">Kurang Tepat</div>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/80 p-3 text-center">
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Soal</div>
                        <div id="modalStatTotal" class="text-2xl font-black text-slate-800 mt-0.5">0</div>
                        <div id="modalStatDuration" class="text-[10px] text-slate-500 mt-0.5">Waktu Selesai</div>
                    </div>
                </div>

                {{-- Questions Container --}}
                <div class="space-y-4" id="modalQuestionsList">
                    {{-- Dynamically populated --}}
                </div>
            </div>
        </div>

        {{-- Sticky Footer --}}
        <div class="shrink-0 flex items-center justify-between px-6 py-3.5 border-t border-slate-100 bg-slate-50/90 backdrop-blur-xs">
            <a id="modalPrintLink" href="#" target="_blank" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100 transition shadow-2xs">
                <i class="bi bi-box-arrow-up-right"></i>
                <span>Buka Halaman Penuh / Cetak</span>
            </a>
            <button type="button" onclick="closeStudentAnswerModal()" class="rounded-xl bg-slate-900 px-5 py-2 text-xs font-semibold text-white hover:bg-slate-800 transition">
                Tutup
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/katex.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/contrib/auto-render.min.js"></script>
<script>
    const letters = ['A', 'B', 'C', 'D', 'E'];

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function openStudentAnswerModal(attemptId) {
        const modal = document.getElementById('studentAnswerModal');
        const loading = document.getElementById('modalLoadingState');
        const realContent = document.getElementById('modalRealContent');
        const printLink = document.getElementById('modalPrintLink');

        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        loading.classList.remove('hidden');
        realContent.classList.add('hidden');

        printLink.href = `/cbt/attempts/${attemptId}`;

        fetch(`/cbt/attempts/${attemptId}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                alert('Gagal mengambil data lembar jawaban.');
                closeStudentAnswerModal();
                return;
            }

            renderModalData(data);
            loading.classList.add('hidden');
            realContent.classList.remove('hidden');

            // Render rumus matematika jika ada
            if (typeof renderMathInElement === 'function') {
                renderMathInElement(document.getElementById('modalQuestionsList'), {
                    delimiters: [
                        {left: "$$", right: "$$", display: true},
                        {left: "$", right: "$", display: false},
                        {left: "\\(", right: "\\)", display: false},
                        {left: "\\[", right: "\\]", display: true}
                    ],
                    throwOnError: false
                });
            }
        })
        .catch(err => {
            console.error(err);
            alert('Terjadi kesalahan jaringan saat memuat lembar jawaban.');
            closeStudentAnswerModal();
        });
    }

    function closeStudentAnswerModal() {
        const modal = document.getElementById('studentAnswerModal');
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function renderModalData(data) {
        const att = data.attempt;
        const quiz = data.quiz;
        const questions = data.questions;

        document.getElementById('modalStudentName').innerText = `Lembar Jawaban: ${att.student_name}`;
        document.getElementById('modalStudentMeta').innerText = `NISN: ${att.student_nisn} • Kelas: ${att.class_name} • Paket: ${quiz.title}`;

        const scoreEl = document.getElementById('modalStatScore');
        scoreEl.innerText = att.score;
        scoreEl.className = `text-2xl font-black mt-0.5 ${att.is_passed ? 'text-emerald-600' : 'text-rose-600'}`;

        document.getElementById('modalStatPassBadge').innerHTML = att.is_passed
            ? `<span class="inline-flex items-center gap-1 rounded-md bg-emerald-100 text-emerald-800 px-2 py-0.5 text-[10px] font-bold"><i class="bi bi-check-circle-fill"></i> TUNTAS KKM (${att.pass_score})</span>`
            : `<span class="inline-flex items-center gap-1 rounded-md bg-rose-100 text-rose-800 px-2 py-0.5 text-[10px] font-bold"><i class="bi bi-x-circle-fill"></i> REMEDIAL (KKM: ${att.pass_score})</span>`;

        document.getElementById('modalStatCorrect').innerText = att.correct_count;
        document.getElementById('modalStatIncorrect').innerText = att.incorrect_count;
        document.getElementById('modalStatTotal').innerText = att.total_questions;
        document.getElementById('modalStatDuration').innerText = `Selesai: ${att.end_time}`;

        const listContainer = document.getElementById('modalQuestionsList');
        listContainer.innerHTML = '';

        questions.forEach((q, idx) => {
            const isCorrect = q.is_correct;
            const hasAnswered = q.has_answered;

            let typeName = 'Pilihan Ganda';
            if (q.type === 'complex_true_false') typeName = 'Kompleks (Benar/Salah)';
            else if (q.type === 'essay') typeName = 'Essay / Uraian';

            let statusBadge = '';
            let cardBorder = 'border-slate-200 bg-white';

            if (!hasAnswered) {
                statusBadge = `<span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-0.5 text-[11px] font-bold text-slate-600 border border-slate-200"><i class="bi bi-dash-circle"></i> Tidak Dijawab</span>`;
            } else if (isCorrect) {
                statusBadge = `<span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-[11px] font-bold text-emerald-700 border border-emerald-200"><i class="bi bi-check-circle-fill text-emerald-600"></i> BENAR (+${q.score_earned} Poin)</span>`;
                cardBorder = 'border-emerald-200 bg-white';
            } else {
                statusBadge = `<span class="inline-flex items-center gap-1 rounded-full bg-rose-50 px-2.5 py-0.5 text-[11px] font-bold text-rose-700 border border-rose-200"><i class="bi bi-x-circle-fill text-rose-600"></i> SALAH (0 Poin)</span>`;
                cardBorder = 'border-rose-200 bg-white';
            }

            let bodyHtml = '';

            // Image jika ada
            let imageHtml = '';
            if (q.image_path) {
                imageHtml = `<div class="my-2"><img src="/${q.image_path}" alt="Gambar Soal" class="max-h-56 rounded-xl border border-slate-200 object-contain bg-slate-50"></div>`;
            }

            if (q.type === 'multiple_choice') {
                let optionsHtml = '';
                q.options.forEach((opt, optIdx) => {
                    const letter = letters[optIdx] || String.fromCharCode(65 + optIdx);
                    const isStudentChoice = (q.student_option_id === opt.id);
                    const isKey = opt.is_correct;

                    let optClass = 'border-slate-200 bg-slate-50/50 text-slate-700';
                    let optBadge = '';
                    let letterClass = 'bg-white border border-slate-200 text-slate-600';

                    if (isStudentChoice && isKey) {
                        optClass = 'border-emerald-500 bg-emerald-50/70 text-emerald-950 font-semibold shadow-2xs';
                        letterClass = 'bg-emerald-600 text-white font-bold';
                        optBadge = `<span class="inline-flex items-center gap-1 rounded-md bg-emerald-600 px-2 py-0.5 text-[10px] font-bold text-white shrink-0"><i class="bi bi-check-lg"></i> Jawaban Siswa &amp; Kunci Benar</span>`;
                    } else if (isStudentChoice && !isKey) {
                        optClass = 'border-rose-400 bg-rose-50/70 text-rose-950 font-semibold shadow-2xs';
                        letterClass = 'bg-rose-600 text-white font-bold';
                        optBadge = `<span class="inline-flex items-center gap-1 rounded-md bg-rose-600 px-2 py-0.5 text-[10px] font-bold text-white shrink-0"><i class="bi bi-x-lg"></i> Jawaban Siswa (Salah)</span>`;
                    } else if (isKey) {
                        optClass = 'border-emerald-300 bg-emerald-50/40 text-emerald-900 font-semibold';
                        letterClass = 'bg-emerald-200 text-emerald-900 font-bold';
                        optBadge = `<span class="inline-flex items-center gap-1 rounded-md bg-emerald-100 text-emerald-800 border border-emerald-300 px-2 py-0.5 text-[10px] font-bold shrink-0"><i class="bi bi-key-fill text-emerald-600"></i> Kunci Jawaban Benar</span>`;
                    }

                    optionsHtml += `
                        <div class="flex items-center justify-between gap-3 rounded-xl border p-2.5 text-xs ${optClass} transition">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <span class="flex h-6 w-6 items-center justify-center rounded-lg ${letterClass} text-xs shrink-0">${letter}</span>
                                <span class="math-content break-words">${escapeHtml(opt.option_text)}</span>
                            </div>
                            ${optBadge}
                        </div>
                    `;
                });
                bodyHtml = `<div class="space-y-2 pt-1">${optionsHtml}</div>`;
            } else if (q.type === 'complex_true_false') {
                let rowsHtml = '';
                q.options.forEach((stmt, sIdx) => {
                    const studentVal = q.student_matching ? q.student_matching[sIdx] : null;
                    const actualCorrect = stmt.is_correct;
                    const isMatch = (studentVal !== null) && (((String(studentVal) === 'true') && actualCorrect) || ((String(studentVal) === 'false') && !actualCorrect));

                    let studentLabel = '<span class="text-slate-400">-</span>';
                    if (studentVal !== null) {
                        if (String(studentVal) === 'true') {
                            studentLabel = `<span class="rounded px-2 py-0.5 font-bold ${actualCorrect ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800'}">BENAR</span>`;
                        } else {
                            studentLabel = `<span class="rounded px-2 py-0.5 font-bold ${!actualCorrect ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800'}">SALAH</span>`;
                        }
                    }

                    rowsHtml += `
                        <tr class="${isMatch ? 'bg-emerald-50/20' : 'bg-rose-50/20'}">
                            <td class="py-2 px-3 text-center font-bold text-slate-400">${sIdx + 1}</td>
                            <td class="py-2 px-3 font-medium text-slate-800">${escapeHtml(stmt.option_text)}</td>
                            <td class="py-2 px-3 text-center">${studentLabel}</td>
                            <td class="py-2 px-3 text-center"><span class="rounded bg-emerald-100 text-emerald-800 px-2 py-0.5 font-bold border border-emerald-200">${actualCorrect ? 'BENAR' : 'SALAH'}</span></td>
                        </tr>
                    `;
                });

                bodyHtml = `
                    <div class="rounded-xl border border-slate-200 overflow-hidden text-xs mt-2">
                        <table class="w-full text-left">
                            <thead class="bg-slate-50 text-[11px] font-bold uppercase text-slate-600 border-b border-slate-200">
                                <tr>
                                    <th class="py-2 px-3 w-10 text-center">No</th>
                                    <th class="py-2 px-3">Pernyataan</th>
                                    <th class="py-2 px-3 text-center w-32">Jawaban Siswa</th>
                                    <th class="py-2 px-3 text-center w-32">Kunci Sebenarnya</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">${rowsHtml}</tbody>
                        </table>
                    </div>
                `;
            } else if (q.type === 'essay') {
                bodyHtml = `
                    <div class="rounded-xl border border-slate-200 bg-slate-50/70 p-3 text-xs space-y-1.5 mt-2">
                        <div class="font-bold text-slate-700 uppercase tracking-wider text-[10px]">Teks Jawaban Siswa:</div>
                        <div class="rounded-lg border border-slate-200 bg-white p-2.5 text-slate-900 whitespace-pre-wrap">${escapeHtml(q.student_essay || '(Siswa tidak menginput jawaban essay)')}</div>
                    </div>
                `;
            }

            const qCard = document.createElement('div');
            qCard.className = `rounded-2xl border ${cardBorder} p-4 shadow-2xs space-y-3`;
            qCard.innerHTML = `
                <div class="flex items-center justify-between pb-2.5 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-slate-900 text-xs font-bold text-white">${q.order_number || (idx + 1)}</span>
                        <span class="rounded-md bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-600">${typeName}</span>
                        <span class="text-[10px] text-slate-400 font-medium">Bobot: ${q.points} Poin</span>
                    </div>
                    <div>${statusBadge}</div>
                </div>
                <div class="math-content text-xs sm:text-sm text-slate-900 leading-relaxed font-medium">
                    ${escapeHtml(q.question_text).replace(/\n/g, '<br>')}
                </div>
                ${imageHtml}
                ${bodyHtml}
            `;

            listContainer.appendChild(qCard);
        });
    }
</script>
@endpush
