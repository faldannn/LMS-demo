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
                            <a href="{{ route('cbt.attempts.show', $attempt->id) }}" class="inline-flex items-center gap-1.5 rounded-xl bg-purple-50 px-3.5 py-2 text-xs font-bold text-purple-700 border border-purple-200 hover:bg-purple-100 hover:border-purple-300 transition shadow-2xs" title="Buka Lembar Jawaban Siswa &amp; Kunci (Halaman Penuh)">
                                <i class="bi bi-file-earmark-check-fill text-purple-600"></i>
                                <span>Lihat Jawaban</span>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

@endsection
