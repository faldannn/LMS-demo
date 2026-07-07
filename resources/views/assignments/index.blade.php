@extends('layouts.app')
@section('title', 'Manajemen Tugas')
@section('page-title', 'Manajemen Tugas — ' . $classSubject->subject->name)

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
                    <span class="mx-1 text-slate-300">|</span>
                    Guru: <span class="font-semibold text-slate-700">{{ $classSubject->teacher->name }}</span>
                </p>
            </div>
        </div>
        <a href="{{ route('assignments.create', $classSubject) }}" class="inline-flex items-center gap-2 rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-brand-700">
            <i class="bi bi-plus-lg"></i>Buat Tugas Baru
        </a>
    </div>

    {{-- Assignments Grid --}}
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3">
        @forelse($assignments as $assignment)
        <div class="flex flex-col overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/70 transition hover:shadow-md hover:ring-brand-200 {{ !$assignment->is_published ? 'opacity-70' : '' }}">
            <div class="flex-1 p-5">
                <div class="flex items-start justify-between gap-2">
                    @if($assignment->type === 'individu')
                        <span class="inline-flex items-center rounded-full bg-blue-50 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-blue-700 ring-1 ring-blue-200">
                            Individu
                        </span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-emerald-700 ring-1 ring-emerald-200">
                            Kelompok
                        </span>
                    @endif

                    @if($assignment->is_published)
                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-800">
                            <i class="bi bi-check-circle"></i>Dipublikasi
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-slate-600">
                            <i class="bi bi-clock"></i>Draft
                        </span>
                    @endif
                </div>

                <h3 class="mt-3 text-base font-bold text-slate-900">{{ $assignment->title }}</h3>
                
                <div class="mt-3 flex items-center gap-2 text-xs font-medium {{ $assignment->isPastDue() ? 'text-rose-600' : 'text-slate-500' }}">
                    <i class="bi bi-calendar-event"></i>
                    Tenggat: {{ $assignment->due_date ? $assignment->due_date->format('d M Y, H:i') : 'Tanpa batas waktu' }}
                </div>

                <div class="mt-4 flex items-center justify-between rounded-xl bg-slate-50 p-3 ring-1 ring-slate-100">
                    <span class="text-xs text-slate-500">Terkumpul:</span>
                    <div class="text-lg font-extrabold text-brand-700">
                        {{ $assignment->submissions_count }} <span class="text-xs font-semibold text-slate-500">Siswa</span>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="{{ route('assignments.grade', $assignment) }}" class="flex w-full items-center justify-center gap-2 rounded-xl bg-brand-50 px-4 py-2.5 text-sm font-bold text-brand-700 ring-1 ring-brand-200 transition hover:bg-brand-100">
                        <i class="bi bi-check2-square"></i>Nilai Tugas
                    </a>
                </div>
            </div>

            <div class="flex items-center justify-center gap-2 border-t border-slate-100 bg-slate-50/50 py-3">
                <a href="{{ route('assignments.show', $assignment) }}" class="flex h-8 w-8 items-center justify-center rounded-lg bg-white text-slate-400 shadow-sm ring-1 ring-slate-200 transition hover:text-brand-600 hover:ring-brand-200" title="Lihat">
                    <i class="bi bi-eye-fill"></i>
                </a>
                <a href="{{ route('assignments.edit', $assignment) }}" class="flex h-8 w-8 items-center justify-center rounded-lg bg-white text-slate-400 shadow-sm ring-1 ring-slate-200 transition hover:text-amber-600 hover:ring-amber-200" title="Edit">
                    <i class="bi bi-pencil-fill"></i>
                </a>
                <form action="{{ route('assignments.toggle-publish', $assignment) }}" method="POST">
                    @csrf @method('PUT')
                    <button type="submit" class="flex h-8 w-8 items-center justify-center rounded-lg bg-white text-slate-400 shadow-sm ring-1 ring-slate-200 transition hover:text-violet-600 hover:ring-violet-200" title="{{ $assignment->is_published ? 'Batalkan Publikasi' : 'Publikasi' }}">
                        <i class="bi {{ $assignment->is_published ? 'bi-eye-slash-fill' : 'bi-eye-fill' }}"></i>
                    </button>
                </form>
                <form id="del-assign-{{ $assignment->id }}" action="{{ route('assignments.destroy', $assignment) }}" method="POST" class="hidden">
                    @csrf @method('DELETE')
                </form>
                <button type="button" class="flex h-8 w-8 items-center justify-center rounded-lg bg-white text-slate-400 shadow-sm ring-1 ring-slate-200 transition hover:text-rose-600 hover:ring-rose-200" data-confirm="Hapus tugas '{{ $assignment->title }}'?" data-form="del-assign-{{ $assignment->id }}" title="Hapus">
                    <i class="bi bi-trash-fill"></i>
                </button>
            </div>
        </div>
        @empty
        <div class="col-span-full rounded-2xl bg-white p-12 text-center shadow-sm ring-1 ring-slate-200/70">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-brand-50">
                <i class="bi bi-journal-x text-3xl text-brand-500"></i>
            </div>
            <h3 class="mt-4 text-base font-bold text-slate-900">Belum ada tugas</h3>
            <p class="mt-2 text-sm text-slate-500">Klik tombol Buat Tugas Baru untuk memberikan tugas ke siswa.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
