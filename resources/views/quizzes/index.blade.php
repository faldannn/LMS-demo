@extends('layouts.app')
@section('title', 'Manajemen Kuis & Ujian')
@section('page-title', 'Manajemen Ujian — ' . $classSubject->subject->name)

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('elearning.index') }}" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 shadow-sm transition hover:border-brand-300 hover:bg-brand-50 hover:text-brand-600" title="Kembali ke E-Learning">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h2 class="text-lg font-extrabold text-slate-900">{{ $classSubject->subject->name }}</h2>
                <p class="text-xs text-slate-500">
                    Kelas: <span class="font-semibold text-slate-700">{{ $classSubject->class->name }}</span>
                </p>
            </div>
        </div>
        <a href="{{ route('quizzes.create', $classSubject) }}" class="inline-flex items-center gap-2 rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-brand-700">
            <i class="bi bi-plus-lg"></i>Buat Ujian Baru
        </a>
    </div>

    {{-- Quizzes Grid --}}
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3">
        @forelse($quizzes as $quiz)
        <div class="flex flex-col overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/70 transition hover:shadow-md hover:ring-brand-200 {{ !$quiz->is_published ? 'opacity-70' : '' }}">
            <div class="flex-1 p-5">
                <div class="flex items-start justify-between gap-2">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-brand-50 px-2.5 py-0.5 text-[10px] font-bold tracking-wide text-brand-700 ring-1 ring-brand-200">
                        <i class="bi bi-stopwatch"></i>{{ $quiz->duration }} Menit
                    </span>

                    @if($quiz->is_published)
                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-800">
                            <i class="bi bi-check-circle"></i>Aktif
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-slate-600">
                            <i class="bi bi-eye-slash"></i>Draft
                        </span>
                    @endif
                </div>

                <h3 class="mt-3 text-base font-bold text-slate-900">{{ $quiz->title }}</h3>
                
                <div class="mt-3 space-y-1.5">
                    <div class="flex items-center gap-2 text-xs font-medium text-slate-500">
                        <i class="bi bi-calendar-event"></i>
                        Mulai: <span class="text-slate-700">{{ $quiz->start_time ? $quiz->start_time->format('d/m/Y H:i') : 'Kapan saja' }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs font-medium text-slate-500">
                        <i class="bi bi-calendar-x"></i>
                        Tutup: <span class="text-slate-700">{{ $quiz->end_time ? $quiz->end_time->format('d/m/Y H:i') : 'Tidak dibatasi' }}</span>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-2 divide-x divide-slate-200 rounded-xl bg-slate-50 py-3 ring-1 ring-slate-100">
                    <div class="flex flex-col items-center justify-center px-2">
                        <div class="text-lg font-extrabold text-brand-700">{{ $quiz->bank_questions_count ?? $quiz->questions_count ?? 0 }}</div>
                        <div class="text-[10px] font-semibold text-slate-500">Soal</div>
                    </div>
                    <div class="flex flex-col items-center justify-center px-2">
                        <div class="text-lg font-extrabold text-blue-700">{{ $quiz->attempts()->count() }}</div>
                        <div class="text-[10px] font-semibold text-slate-500">Peserta</div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2 border-t border-slate-100 bg-slate-50/50 p-3">
                <a href="{{ route('quizzes.show', $quiz) }}" class="flex items-center justify-center gap-2 rounded-xl bg-white px-3 py-2 text-xs font-bold text-slate-700 shadow-sm ring-1 ring-slate-200 transition hover:text-brand-700 hover:ring-brand-200">
                    <i class="bi bi-ui-checks"></i>Kelola Soal
                </a>
                <a href="{{ route('quizzes.results', $quiz) }}" class="flex items-center justify-center gap-2 rounded-xl bg-white px-3 py-2 text-xs font-bold text-slate-700 shadow-sm ring-1 ring-slate-200 transition hover:text-emerald-700 hover:ring-emerald-200">
                    <i class="bi bi-bar-chart"></i>Hasil Ujian
                </a>
            </div>
        </div>
        @empty
        <div class="col-span-full rounded-2xl bg-white p-12 text-center shadow-sm ring-1 ring-slate-200/70">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-brand-50">
                <i class="bi bi-ui-radios text-3xl text-brand-500"></i>
            </div>
            <h3 class="mt-4 text-base font-bold text-slate-900">Belum ada ujian/kuis</h3>
            <p class="mt-2 text-sm text-slate-500">Klik tombol Buat Ujian Baru untuk menambahkan evaluasi.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
