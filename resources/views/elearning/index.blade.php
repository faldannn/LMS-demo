@extends('layouts.app')
@section('title', 'Portal E-Learning')
@section('page-title', 'Portal E-Learning')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-lg font-extrabold text-slate-900">Portal E-Learning</h2>
            <p class="mt-1 text-sm text-slate-500">
                Kelola materi, tugas, forum diskusi, dan bank soal dari satu tempat.
            </p>
        </div>
    </div>

    {{-- Class Subject Cards Grid --}}
    @if($classSubjects->isNotEmpty())
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-3">
        @foreach($classSubjects as $cs)
        <div class="group relative overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/70 transition hover:shadow-md hover:ring-brand-200">
            {{-- Accent bar --}}
            <div class="h-1.5 bg-brand-500"></div>

            <div class="flex flex-col justify-between p-5">
                {{-- Header row --}}
                <div class="flex items-center justify-between gap-2">
                    <span class="inline-flex items-center gap-1 rounded-full bg-brand-50 px-2.5 py-1 text-[11px] font-bold text-brand-700 ring-1 ring-brand-100">
                        <i class="bi bi-building text-[10px]"></i>
                        {{ $cs->class?->name ?? '-' }}
                    </span>
                    <span class="text-[11px] font-medium text-slate-400">Tingkat {{ $cs->class?->grade ?? '-' }}</span>
                </div>

                {{-- Subject title & teacher --}}
                <div class="mt-3">
                    <h3 class="text-base font-bold text-slate-900 group-hover:text-brand-700 transition-colors">
                        {{ $cs->subject?->name ?? '-' }}
                    </h3>
                    <p class="mt-1 flex items-center gap-1.5 text-xs text-slate-500">
                        <i class="bi bi-person-circle"></i>
                        {{ $cs->teacher?->name ?? 'Belum ditentukan' }}
                    </p>
                </div>

                {{-- Stats row --}}
<div class="mt-4 grid grid-cols-2 gap-2">
                        <div class="rounded-xl bg-slate-50 p-2.5 text-center ring-1 ring-slate-100">
                            <div class="text-base font-extrabold text-brand-600">{{ $cs->materials_count }}</div>
                            <div class="text-[10px] font-semibold text-slate-500">Materi</div>
                        </div>
                        <div class="rounded-xl bg-slate-50 p-2.5 text-center ring-1 ring-slate-100">
                            <div class="text-base font-extrabold text-brand-600">{{ $cs->assignments_count }}</div>
                            <div class="text-[10px] font-semibold text-slate-500">Tugas</div>
                    </div>
                </div>

                {{-- Action buttons --}}
                <div class="mt-4 grid grid-cols-3 gap-2 border-t border-slate-100 pt-4">
                    @if(auth()->user()->isSiswa())
                    <a href="{{ route('materials.mine', ['subject' => $cs->subject_id]) }}" class="flex items-center justify-center gap-1 rounded-lg bg-slate-50 py-2 text-[11px] font-semibold text-slate-700 ring-1 ring-slate-200/60 transition hover:bg-brand-50 hover:text-brand-700 hover:ring-brand-200">
                        <i class="bi bi-file-earmark-richtext"></i>Materi
                    </a>
                    <a href="{{ route('student.assignments.index', ['subject' => $cs->subject_id]) }}" class="flex items-center justify-center gap-1 rounded-lg bg-slate-50 py-2 text-[11px] font-semibold text-slate-700 ring-1 ring-slate-200/60 transition hover:bg-brand-50 hover:text-brand-700 hover:ring-brand-200">
                        <i class="bi bi-journal-check"></i>Tugas
                    </a>
                    @else
                    <a href="{{ route('materials.index', $cs->id) }}" class="flex items-center justify-center gap-1 rounded-lg bg-slate-50 py-2 text-[11px] font-semibold text-slate-700 ring-1 ring-slate-200/60 transition hover:bg-brand-50 hover:text-brand-700 hover:ring-brand-200">
                        <i class="bi bi-file-earmark-richtext"></i>Materi
                    </a>
                    <a href="{{ route('assignments.index', $cs->id) }}" class="flex items-center justify-center gap-1 rounded-lg bg-slate-50 py-2 text-[11px] font-semibold text-slate-700 ring-1 ring-slate-200/60 transition hover:bg-brand-50 hover:text-brand-700 hover:ring-brand-200">
                        <i class="bi bi-journal-check"></i>Tugas
                    </a>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    {{-- Empty state --}}
    <div class="rounded-2xl bg-white p-12 text-center shadow-sm ring-1 ring-slate-200/70">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-brand-50">
            <i class="bi bi-book text-3xl text-brand-500"></i>
        </div>
        <h3 class="mt-4 text-base font-bold text-slate-900">Belum ada mata pelajaran kelas</h3>
        <p class="mt-2 text-sm text-slate-500">
            Belum ada mata pelajaran kelas yang ditugaskan kepada Anda.
        </p>
        @role('kepala_sekolah')
        <p class="mt-3 text-sm text-slate-500">
            Silakan pergi ke <a href="{{ route('classes.index') }}" class="font-semibold text-brand-600 hover:underline">Data Kelas</a> untuk menambahkan mata pelajaran ke kelas.
        </p>
        @endrole
    </div>
    @endif
</div>
@endsection
