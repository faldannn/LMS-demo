@extends('layouts.app')
@section('title', 'Materi Pelajaran')
@section('page-title', 'Materi Pelajaran — ' . $classSubject->subject->name)

@php
    $flatMaterials = $materials->flatten();
    $groupedMaterials = $flatMaterials->groupBy(function($item) {
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
                <button type="button" onclick="toggleView('materials', 'grid')" id="btn-materials-grid" class="px-2.5 py-1.5 text-xs transition" title="Grid View">
                    <i class="bi bi-grid-3x3-gap-fill"></i>
                </button>
                <button type="button" onclick="toggleView('materials', 'list')" id="btn-materials-list" class="px-2.5 py-1.5 text-xs transition" title="List View">
                    <i class="bi bi-list-ul"></i>
                </button>
            </div>
            <a href="{{ route('materials.create', $classSubject) }}" class="inline-flex items-center gap-2 rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-brand-700">
                <i class="bi bi-plus-lg"></i>Tambah Materi
            </a>
        </div>
    </div>

    {{-- Materials by Date --}}
    @forelse($groupedMaterials as $date => $dateMaterials)
    <div>
        <div class="flex items-center gap-2 mb-3">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">
                <i class="bi bi-calendar-check me-1"></i>{{ formatDateGroup($date) }}
            </span>
            <div class="h-px bg-slate-200 flex-1"></div>
        </div>

        {{-- Grid view --}}
        <div id="materials-grid-{{ $date }}" class="materials-grid-container grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach($dateMaterials as $material)
            <div class="group relative overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-slate-200/70 transition hover:shadow-md hover:ring-brand-200 {{ !$material->is_published ? 'opacity-70' : '' }}">
                <div class="p-4">
                    <div class="flex items-start gap-3">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-xl text-brand-600">
                            <i class="bi {{ $material->type_icon }}"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h4 class="truncate font-bold text-slate-900 group-hover:text-brand-700 transition-colors">
                                {{ $material->title }}
                            </h4>
                            <div class="mt-1 flex items-center justify-between gap-2">
                                <span class="text-[10px] text-slate-400">Dibuat: {{ $material->created_at->format('d M Y') }}</span>
                                @if($material->is_published)
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700 ring-1 ring-emerald-100">
                                        Dipublikasi
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-slate-500 ring-1 ring-slate-200">
                                        Draft
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Action buttons --}}
                    <div class="mt-3 flex gap-1 border-t border-slate-100 pt-3">
                        <a href="{{ route('materials.show', $material) }}" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-brand-50 hover:text-brand-600" title="Lihat">
                            <i class="bi bi-eye-fill"></i>
                        </a>
                        <a href="{{ route('materials.edit', $material) }}" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-amber-50 hover:text-amber-600" title="Edit">
                            <i class="bi bi-pencil-fill"></i>
                        </a>
                        <form action="{{ route('materials.toggle-publish', $material) }}" method="POST">
                            @csrf @method('PUT')
                            <button type="submit" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-violet-50 hover:text-violet-600" title="{{ $material->is_published ? 'Batalkan Publikasi' : 'Publikasi' }}">
                                <i class="bi {{ $material->is_published ? 'bi-eye-slash-fill' : 'bi-eye-fill' }}"></i>
                            </button>
                        </form>
                        <form id="del-mat-{{ $material->id }}" action="{{ route('materials.destroy', $material) }}" method="POST" class="hidden">
                            @csrf @method('DELETE')
                        </form>
                        <button type="button" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-rose-50 hover:text-rose-600" data-confirm="Hapus materi '{{ $material->title }}'?" data-form="del-mat-{{ $material->id }}" title="Hapus">
                            <i class="bi bi-trash-fill"></i>
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- List view --}}
        <div id="materials-list-{{ $date }}" class="materials-list-container hidden divide-y divide-slate-100 rounded-xl border border-slate-200 bg-white">
            @foreach($dateMaterials as $material)
            <div class="flex items-center justify-between p-3 hover:bg-slate-50/50 transition">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
                        <i class="bi {{ $material->type_icon }}"></i>
                    </div>
                    <div class="min-w-0">
                        <h4 class="font-bold text-slate-900 text-sm truncate">{{ $material->title }}</h4>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="text-[10px] text-slate-400">Dibuat: {{ $material->created_at->format('d M Y') }}</span>
                            @if($material->is_published)
                                <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[9px] font-bold text-emerald-700">
                                    Dipublikasi
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[9px] font-bold text-slate-500">
                                    Draft
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-1">
                    <a href="{{ route('materials.show', $material) }}" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-brand-50 hover:text-brand-600" title="Lihat">
                        <i class="bi bi-eye-fill"></i>
                    </a>
                    <a href="{{ route('materials.edit', $material) }}" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-amber-50 hover:text-amber-600" title="Edit">
                        <i class="bi bi-pencil-fill"></i>
                    </a>
                    <form action="{{ route('materials.toggle-publish', $material) }}" method="POST">
                        @csrf @method('PUT')
                        <button type="submit" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-violet-50 hover:text-violet-600" title="{{ $material->is_published ? 'Batalkan Publikasi' : 'Publikasi' }}">
                            <i class="bi {{ $material->is_published ? 'bi-eye-slash-fill' : 'bi-eye-fill' }}"></i>
                        </button>
                    </form>
                    <button type="button" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-rose-50 hover:text-rose-600" data-confirm="Hapus materi '{{ $material->title }}'?" data-form="del-mat-{{ $material->id }}" title="Hapus">
                        <i class="bi bi-trash-fill"></i>
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @empty
    {{-- Empty state --}}
    <div class="rounded-2xl bg-white p-12 text-center shadow-sm ring-1 ring-slate-200/70">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-brand-50">
            <i class="bi bi-journal-text text-3xl text-brand-500"></i>
        </div>
        <h3 class="mt-4 text-base font-bold text-slate-900">Belum ada materi</h3>
        <p class="mt-2 text-sm text-slate-500">Klik tombol Tambah Materi untuk mengunggah materi baru.</p>
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
        applyViewPreference('materials');
    });
</script>
@endpush
@endsection
