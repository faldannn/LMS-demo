@extends('layouts.app')
@section('title', 'Manajemen Tugas')
@section('page-title', 'Manajemen Tugas — ' . $classSubject->subject->name)

@php
    $groupedAssignments = $assignments->groupBy(function($item) {
        return $item->created_at->toDateString();
    })->sortKeysDesc();

    if (!function_exists('formatDateGroup')) {
        function formatDateGroup($dateStr) {
            $date = \Carbon\Carbon::parse($dateStr);
            if ($date->isToday()) {
                return 'Hari Ini';
            } elseif ($date->isYesterday()) {
                return 'Kemarin';
            } elseif ($date->isTomorrow()) {
                return 'Besok';
            } elseif ($date->isFuture()) {
                return 'Mendatang';
            }
            return $date->translatedFormat('d F Y');
        }
    }
@endphp

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
        <div class="flex items-center gap-2">
            <div class="inline-flex overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                <button type="button" onclick="toggleView('assignments', 'grid')" id="btn-assignments-grid" class="px-2.5 py-1.5 text-xs transition" title="Grid View">
                    <i class="bi bi-grid-3x3-gap-fill"></i>
                </button>
                <button type="button" onclick="toggleView('assignments', 'list')" id="btn-assignments-list" class="px-2.5 py-1.5 text-xs transition" title="List View">
                    <i class="bi bi-list-ul"></i>
                </button>
            </div>
            <a href="{{ route('assignments.create', $classSubject) }}" class="inline-flex items-center gap-2 rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-brand-700">
                <i class="bi bi-plus-lg"></i>Buat Tugas Baru
            </a>
        </div>
    </div>

    {{-- Assignments by Date --}}
    @forelse($groupedAssignments as $date => $dateAssignments)
    <div>
        <div class="flex items-center gap-2 mb-3">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">
                <i class="bi bi-calendar-check me-1"></i>{{ formatDateGroup($date) }}
            </span>
            <div class="h-px bg-slate-200 flex-1"></div>
        </div>

        {{-- Grid view --}}
        <div id="assignments-grid-{{ $date }}" class="assignments-grid-container grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3">
            @foreach($dateAssignments as $assignment)
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
                    
                    <div class="mt-2 text-[11px] text-slate-400 font-semibold">
                        Dibuat: {{ $assignment->created_at->format('d M Y') }}
                    </div>

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
            @endforeach
        </div>

        {{-- List view --}}
        <div id="assignments-list-{{ $date }}" class="assignments-list-container hidden divide-y divide-slate-100 rounded-xl border border-slate-200 bg-white">
            @foreach($dateAssignments as $assignment)
            <div class="flex items-center justify-between p-4 hover:bg-slate-50/50 transition">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-xl text-amber-600">
                        <i class="bi bi-file-earmark-check"></i>
                    </div>
                    <div class="min-w-0">
                        <h4 class="font-bold text-slate-900 text-sm truncate">{{ $assignment->title }}</h4>
                        <div class="flex flex-wrap items-center gap-2 mt-1">
                            <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[9px] font-bold text-slate-600">
                                {{ ucfirst($assignment->type) }}
                            </span>
                            <span class="text-[10px] text-slate-400">Dibuat: {{ $assignment->created_at->format('d M Y') }}</span>
                            <span class="text-[10px] text-slate-500">Tenggat: {{ $assignment->due_date ? $assignment->due_date->format('d M Y, H:i') : '-' }}</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-1">
                    <a href="{{ route('assignments.grade', $assignment) }}" class="flex h-8 px-2.5 items-center justify-center gap-1 rounded-lg bg-brand-50 text-xs font-bold text-brand-700 hover:bg-brand-100 transition" title="Nilai">
                        <i class="bi bi-check2-square"></i> Nilai
                    </a>
                    <a href="{{ route('assignments.show', $assignment) }}" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100" title="Lihat">
                        <i class="bi bi-eye-fill"></i>
                    </a>
                    <a href="{{ route('assignments.edit', $assignment) }}" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100" title="Edit">
                        <i class="bi bi-pencil-fill"></i>
                    </a>
                    <form action="{{ route('assignments.toggle-publish', $assignment) }}" method="POST">
                        @csrf @method('PUT')
                        <button type="submit" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100" title="{{ $assignment->is_published ? 'Batalkan Publikasi' : 'Publikasi' }}">
                            <i class="bi {{ $assignment->is_published ? 'bi-eye-slash-fill' : 'bi-eye-fill' }}"></i>
                        </button>
                    </form>
                    <button type="button" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-rose-50 hover:text-rose-600" data-confirm="Hapus tugas '{{ $assignment->title }}'?" data-form="del-assign-{{ $assignment->id }}" title="Hapus">
                        <i class="bi bi-trash-fill"></i>
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @empty
    <div class="rounded-2xl bg-white p-12 text-center shadow-sm ring-1 ring-slate-200/70">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-brand-50">
            <i class="bi bi-journal-x text-3xl text-brand-500"></i>
        </div>
        <h3 class="mt-4 text-base font-bold text-slate-900">Belum ada tugas</h3>
        <p class="mt-2 text-sm text-slate-500">Klik tombol Buat Tugas Baru untuk memberikan tugas ke siswa.</p>
    </div>
    @endforelse
</div>

@push('scripts')
<script>
    function toggleView(section, view) {
        localStorage.setItem('teacher_' + section + '_view', view);
        applyViewPreference(section);
    }
    
    function applyViewPreference(section) {
        const view = localStorage.getItem('teacher_' + section + '_view') || 'grid';
        
        const gridContainers = document.querySelectorAll(`.${section}-grid-container`);
        const listContainers = document.querySelectorAll(`.${section}-list-container`);
        
        const btnGrid = document.getElementById(`btn-${section}-grid`);
        const btnList = document.getElementById(`btn-${section}-list`);
        
        if (view === 'grid') {
            gridContainers.forEach(el => el.classList.remove('hidden'));
            listContainers.forEach(el => el.classList.add('hidden'));
            
            if (btnGrid) {
                btnGrid.classList.remove('text-slate-500', 'hover:bg-slate-50');
                btnGrid.classList.add('bg-brand-600', 'text-white');
            }
            if (btnList) {
                btnList.classList.remove('bg-brand-600', 'text-white');
                btnList.classList.add('text-slate-500', 'hover:bg-slate-50');
            }
        } else {
            gridContainers.forEach(el => el.classList.add('hidden'));
            listContainers.forEach(el => el.classList.remove('hidden'));
            
            if (btnList) {
                btnList.classList.remove('text-slate-500', 'hover:bg-slate-50');
                btnList.classList.add('bg-brand-600', 'text-white');
            }
            if (btnGrid) {
                btnGrid.classList.remove('bg-brand-600', 'text-white');
                btnGrid.classList.add('text-slate-500', 'hover:bg-slate-50');
            }
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        applyViewPreference('assignments');
    });
</script>
@endpush
@endsection
