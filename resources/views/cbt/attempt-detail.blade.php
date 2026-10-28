@extends('layouts.app')

@section('title', 'Lembar Jawaban: ' . ($attempt->student?->user?->name ?? 'Siswa') . ' — ' . $quiz->title)

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
        .print-container {
            max-width: 100% !important;
            padding: 0 !important;
        }
    }
</style>
@endpush

@section('content')
<div class="print-container mx-auto max-w-5xl space-y-6 p-4 sm:p-6 lg:p-8">
    {{-- BREADCRUMB --}}
    <div class="no-print flex items-center gap-2 text-xs font-semibold text-slate-500">
        <a href="{{ route('cbt.index') }}" class="hover:text-emerald-600 transition">Ujian CBT</a>
        <i class="bi bi-chevron-right text-[10px]"></i>
        <a href="{{ route('cbt.results', $quiz->id) }}" class="hover:text-emerald-600 transition">Rekap Nilai Siswa</a>
        <i class="bi bi-chevron-right text-[10px]"></i>
        <span class="text-slate-900 truncate max-w-xs">{{ $attempt->student?->user?->name ?? 'Siswa' }}</span>
    </div>

    {{-- HEADER KARTU HASIL SISWA --}}
    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div class="space-y-1.5">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="rounded-lg bg-purple-100 px-2.5 py-0.5 text-xs font-bold text-purple-800">
                        {{ $quiz->classSubject?->subject?->name ?? 'Mata Pelajaran' }}
                    </span>
                    <span class="rounded-lg bg-slate-100 px-2.5 py-0.5 text-xs font-bold text-slate-700">
                        Kelas: {{ $attempt->student?->class?->name ?? ($quiz->classSubject?->class?->name ?? '-') }}
                    </span>
                    @php
                        $isPassed = $attempt->score >= $quiz->pass_score;
                    @endphp
                    @if($attempt->status === 'completed')
                        <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-[11px] font-bold {{ $isPassed ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                            <i class="bi {{ $isPassed ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }}"></i>
                            <span>{{ $isPassed ? 'TUNTAS KKM' : 'REMEDIAL' }}</span>
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2.5 py-0.5 text-[11px] font-bold text-amber-700 border border-amber-200">
                            {{ ucfirst($attempt->status) }}
                        </span>
                    @endif
                </div>

                <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900 mt-2">
                    Lembar Jawaban: {{ $attempt->student?->user?->name ?? 'Siswa' }}
                </h1>
                <p class="text-xs text-slate-500">
                    NISN: <strong>{{ $attempt->student?->nisn ?? '-' }}</strong> &bull; Paket: <strong>{{ $quiz->title }}</strong> &bull; KKM: <strong>{{ $quiz->pass_score }}</strong>
                </p>
            </div>

            <div class="no-print flex items-center gap-2 shrink-0">
                <button type="button" onclick="window.print()" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition">
                    <i class="bi bi-printer"></i>
                    <span>Cetak Lembar</span>
                </button>
                <a href="{{ route('cbt.results', $quiz->id) }}" class="inline-flex items-center gap-1.5 rounded-xl bg-slate-900 px-3.5 py-2 text-xs font-semibold text-white shadow-sm hover:bg-slate-800 transition">
                    <i class="bi bi-arrow-left"></i>
                    <span>Kembali ke Rekap</span>
                </a>
            </div>
        </div>

        {{-- RINGKASAN STATISTIK --}}
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mt-6 pt-6 border-t border-slate-100 text-center">
            <div class="rounded-2xl border border-slate-100 bg-slate-50/70 p-3">
                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Nilai Akhir</div>
                <div class="text-2xl font-black {{ $isPassed ? 'text-emerald-600' : 'text-rose-600' }} mt-0.5">
                    {{ $attempt->score ?? 0 }}
                </div>
            </div>
            <div class="rounded-2xl border border-slate-100 bg-emerald-50/50 p-3">
                <div class="text-[10px] font-bold uppercase tracking-wider text-emerald-600">Jawaban Benar</div>
                <div class="text-2xl font-black text-emerald-700 mt-0.5">
                    {{ $correctCount }}
                </div>
            </div>
            <div class="rounded-2xl border border-slate-100 bg-rose-50/50 p-3">
                <div class="text-[10px] font-bold uppercase tracking-wider text-rose-600">Jawaban Salah</div>
                <div class="text-2xl font-black text-rose-700 mt-0.5">
                    {{ $incorrectCount }}
                </div>
            </div>
            <div class="rounded-2xl border border-slate-100 bg-slate-50/70 p-3">
                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Soal</div>
                <div class="text-2xl font-black text-slate-800 mt-0.5">
                    {{ $questions->count() }}
                </div>
            </div>
            <div class="col-span-2 sm:col-span-1 rounded-2xl border border-slate-100 bg-slate-50/70 p-3">
                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Waktu Selesai</div>
                <div class="text-xs font-bold text-slate-700 mt-1.5">
                    {{ $attempt->end_time ? $attempt->end_time->format('d/m/Y H:i') : '-' }}
                </div>
            </div>
        </div>
    </div>

    {{-- DAFTAR SOAL & JAWABAN SISWA --}}
    <div class="space-y-5">
        <div class="flex items-center justify-between">
            <h2 class="text-base font-bold text-slate-900">Rincian Jawaban per Nomor Soal</h2>
            <div class="flex items-center gap-3 text-xs font-medium">
                <span class="inline-flex items-center gap-1.5 text-emerald-700">
                    <span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span> Benar (Kunci)
                </span>
                <span class="inline-flex items-center gap-1.5 text-rose-700">
                    <span class="h-2.5 w-2.5 rounded-full bg-rose-500"></span> Jawaban Salah
                </span>
            </div>
        </div>

        @foreach($questions as $index => $q)
        @php
            $ans = $answersByQuestion->get($q->id);
            $hasAnswered = $ans && ($ans->question_option_id || !empty($ans->essay_answer) || !empty($ans->matching_answer));
            $isCorrect = $ans && $ans->is_correct;
            $typeLabel = match($q->type) {
                'multiple_choice' => 'Pilihan Ganda',
                'complex_true_false' => 'Pernyataan Kompleks (Benar/Salah)',
                'essay' => 'Essay / Uraian',
                default => 'Soal'
            };
        @endphp
        <div class="rounded-2xl border {{ $hasAnswered ? ($isCorrect ? 'border-emerald-200/80 bg-white' : 'border-rose-200/80 bg-white') : 'border-slate-200 bg-slate-50/40' }} p-5 shadow-xs transition space-y-4">
            {{-- Header Soal --}}
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="flex h-7 w-7 items-center justify-center rounded-xl bg-slate-900 text-xs font-bold text-white">
                        {{ $q->order_number ?: ($index + 1) }}
                    </span>
                    <span class="rounded-md bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-600">
                        {{ $typeLabel }}
                    </span>
                    <span class="text-[11px] text-slate-400 font-medium">
                        Bobot: {{ $q->points }} Poin
                    </span>
                </div>

                {{-- Status Badge --}}
                <div>
                    @if(!$hasAnswered)
                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600 border border-slate-200">
                            <i class="bi bi-dash-circle"></i>
                            <span>Tidak Dijawab</span>
                        </span>
                    @elseif($isCorrect)
                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700 border border-emerald-200">
                            <i class="bi bi-check-circle-fill text-emerald-600"></i>
                            <span>BENAR (+{{ $ans->score ?? $q->points }} Poin)</span>
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 rounded-full bg-rose-50 px-3 py-1 text-xs font-bold text-rose-700 border border-rose-200">
                            <i class="bi bi-x-circle-fill text-rose-600"></i>
                            <span>SALAH (0 Poin)</span>
                        </span>
                    @endif
                </div>
            </div>

            {{-- Isi Pertanyaan --}}
            <div class="math-content text-sm text-slate-900 leading-relaxed font-medium">
                {!! nl2br(e($q->question_text)) !!}
            </div>

            {{-- Lampiran Gambar --}}
            @if($q->image_path)
            <div class="my-2">
                <img src="{{ str_starts_with($q->image_path, 'http') || str_starts_with($q->image_path, 'data:') ? $q->image_path : asset($q->image_path) }}" alt="Gambar Soal" class="max-h-64 rounded-xl border border-slate-200 object-contain bg-slate-50" onerror="this.onerror=null; this.closest('div').style.display='none';">
            </div>
            @endif

            {{-- PILIHAN GANDA --}}
            @if($q->type === 'multiple_choice')
            <div class="space-y-2.5 pt-1">
                @php
                    $letters = ['A', 'B', 'C', 'D', 'E'];
                    $studentOptionId = $ans?->question_option_id;
                    $correctOption = $q->options->firstWhere('is_correct', true);
                    $studentOption = $studentOptionId ? $q->options->firstWhere('id', $studentOptionId) : null;
                @endphp

                @if($isCorrect && $studentOption)
                    {{-- Siswa Benar: Tampilkan HANYA jawaban benarnya saja --}}
                    @php
                        $optIdx = $q->options->search(fn($o) => $o->id === $studentOption->id);
                        $letter = $letters[$optIdx !== false ? $optIdx : 0] ?? 'A';
                    @endphp
                    <div class="flex items-center justify-between gap-3 rounded-2xl border-2 border-emerald-500 bg-emerald-50/70 p-3.5 text-xs text-emerald-950 font-medium shadow-xs">
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="flex h-7 w-7 items-center justify-center rounded-xl bg-emerald-600 text-white font-bold text-xs shrink-0 shadow-xs">
                                {{ $letter }}
                            </span>
                            <div class="space-y-0.5 min-w-0">
                                <span class="math-content break-words font-semibold text-slate-900">{{ $studentOption->option_text }}</span>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3 py-1 text-[11px] font-bold text-white shrink-0 shadow-2xs">
                            <i class="bi bi-check-circle-fill"></i> Jawaban Benar (Dipilih Siswa)
                        </span>
                    </div>

                @elseif(!$isCorrect && $studentOption)
                    {{-- Siswa Salah: Tampilkan Jawaban Pilihan Siswa dan Kunci Jawaban Benar --}}
                    @php
                        $studentIdx = $q->options->search(fn($o) => $o->id === $studentOption->id);
                        $studentLetter = $letters[$studentIdx !== false ? $studentIdx : 0] ?? 'A';

                        $correctIdx = $correctOption ? $q->options->search(fn($o) => $o->id === $correctOption->id) : false;
                        $correctLetter = $letters[$correctIdx !== false ? $correctIdx : 0] ?? 'B';
                    @endphp

                    {{-- 1. Jawaban Pilihan Siswa (Salah) --}}
                    <div class="flex items-center justify-between gap-3 rounded-2xl border-2 border-rose-400 bg-rose-50/70 p-3.5 text-xs text-rose-950 font-medium shadow-xs">
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="flex h-7 w-7 items-center justify-center rounded-xl bg-rose-600 text-white font-bold text-xs shrink-0 shadow-xs">
                                {{ $studentLetter }}
                            </span>
                            <div class="space-y-0.5 min-w-0">
                                <span class="math-content break-words font-semibold text-slate-900">{{ $studentOption->option_text }}</span>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1.5 rounded-lg bg-rose-600 px-3 py-1 text-[11px] font-bold text-white shrink-0 shadow-2xs">
                            <i class="bi bi-x-circle-fill"></i> Jawaban Pilihan Siswa (Salah)
                        </span>
                    </div>

                    {{-- 2. Kunci Jawaban Benar --}}
                    @if($correctOption)
                    <div class="flex items-center justify-between gap-3 rounded-2xl border-2 border-emerald-400 bg-emerald-50/60 p-3.5 text-xs text-emerald-950 font-medium shadow-xs">
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="flex h-7 w-7 items-center justify-center rounded-xl bg-emerald-600 text-white font-bold text-xs shrink-0 shadow-xs">
                                {{ $correctLetter }}
                            </span>
                            <div class="space-y-0.5 min-w-0">
                                <span class="math-content break-words font-semibold text-slate-900">{{ $correctOption->option_text }}</span>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-100 border border-emerald-300 text-emerald-800 px-3 py-1 text-[11px] font-bold shrink-0">
                            <i class="bi bi-key-fill text-emerald-600"></i> Kunci Jawaban Benar
                        </span>
                    </div>
                    @endif

                @else
                    {{-- Tidak Dijawab: Tampilkan Kunci Jawaban Benar --}}
                    @if($correctOption)
                    @php
                        $correctIdx = $q->options->search(fn($o) => $o->id === $correctOption->id);
                        $correctLetter = $letters[$correctIdx !== false ? $correctIdx : 0] ?? 'A';
                    @endphp
                    <div class="flex items-center justify-between gap-3 rounded-2xl border-2 border-emerald-400 bg-emerald-50/60 p-3.5 text-xs text-emerald-950 font-medium shadow-xs">
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="flex h-7 w-7 items-center justify-center rounded-xl bg-emerald-600 text-white font-bold text-xs shrink-0 shadow-xs">
                                {{ $correctLetter }}
                            </span>
                            <div class="space-y-0.5 min-w-0">
                                <span class="math-content break-words font-semibold text-slate-900">{{ $correctOption->option_text }}</span>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-100 border border-emerald-300 text-emerald-800 px-3 py-1 text-[11px] font-bold shrink-0">
                            <i class="bi bi-key-fill text-emerald-600"></i> Kunci Jawaban Benar
                        </span>
                    </div>
                    @endif
                    <div class="text-[11px] text-slate-400 italic pl-1">Siswa tidak memilih jawaban pada nomor ini.</div>
                @endif
            </div>

            {{-- KOMPLEKS BENAR / SALAH --}}
            @elseif($q->type === 'complex_true_false')
            <div class="rounded-xl border border-slate-200 overflow-hidden text-xs">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 text-[11px] font-bold uppercase text-slate-600 border-b border-slate-200">
                        <tr>
                            <th class="py-2.5 px-3 w-10 text-center">No</th>
                            <th class="py-2.5 px-3">Pernyataan</th>
                            <th class="py-2.5 px-3 text-center w-32">Jawaban Siswa</th>
                            <th class="py-2.5 px-3 text-center w-32">Kunci Sebenarnya</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($q->options as $sIdx => $stmt)
                        @php
                            $studentAns = is_array($ans?->matching_answer) ? ($ans->matching_answer[$sIdx] ?? null) : null;
                            $actualCorrect = (bool) $stmt->is_correct;
                            $isMatch = ($studentAns !== null) && (((string)$studentAns === 'true' && $actualCorrect) || ((string)$studentAns === 'false' && !$actualCorrect));
                        @endphp
                        <tr class="{{ $isMatch ? 'bg-emerald-50/20' : 'bg-rose-50/20' }}">
                            <td class="py-2.5 px-3 text-center font-bold text-slate-400">{{ $sIdx + 1 }}</td>
                            <td class="py-2.5 px-3 font-medium text-slate-800">{{ $stmt->option_text }}</td>
                            <td class="py-2.5 px-3 text-center">
                                @if($studentAns === null)
                                    <span class="text-slate-400">-</span>
                                @elseif((string)$studentAns === 'true')
                                    <span class="rounded px-2 py-0.5 font-bold {{ $actualCorrect ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">BENAR</span>
                                @else
                                    <span class="rounded px-2 py-0.5 font-bold {{ !$actualCorrect ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">SALAH</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3 text-center">
                                <span class="rounded bg-emerald-100 text-emerald-800 px-2 py-0.5 font-bold border border-emerald-200">
                                    {{ $actualCorrect ? 'BENAR' : 'SALAH' }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- ESSAY --}}
            @elseif($q->type === 'essay')
            <div class="rounded-xl border border-slate-200 bg-slate-50/70 p-4 text-xs space-y-2">
                <div class="font-bold text-slate-700 uppercase tracking-wider text-[10px]">Teks Jawaban Siswa:</div>
                <div class="rounded-lg border border-slate-200 bg-white p-3 text-slate-900 whitespace-pre-wrap">
                    {{ $ans?->essay_answer ?: '(Siswa tidak menginput jawaban essay)' }}
                </div>
            </div>
            @endif
        </div>
        @endforeach
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/katex.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/contrib/auto-render.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        if (typeof renderMathInElement === 'function') {
            renderMathInElement(document.body, {
                delimiters: [
                    {left: "$$", right: "$$", display: true},
                    {left: "$", right: "$", display: false},
                    {left: "\\(", right: "\\)", display: false},
                    {left: "\\[", right: "\\]", display: true}
                ],
                throwOnError: false
            });
        }
    });
</script>
@endpush
