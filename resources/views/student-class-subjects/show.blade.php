@extends('layouts.app')
@section('title', $classSubject->subject->name)
@section('page-title', 'Detail Mata Pelajaran')

@php
    $groupedMaterials = $classSubject->materials->groupBy(function($item) {
        return $item->created_at->toDateString();
    })->sortKeysDesc();

    $groupedAssignments = $classSubject->assignments->groupBy(function($item) {
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
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard') }}" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 shadow-sm transition hover:border-brand-300 hover:bg-brand-50 hover:text-brand-600" title="Kembali ke Dashboard">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h2 class="text-lg font-extrabold text-slate-900">{{ $classSubject->subject->name }}</h2>
                <p class="text-xs text-slate-500">
                    Kelas: <span class="font-semibold text-slate-700">{{ $classSubject->class->name }}</span>
                    <span class="mx-1 text-slate-300">|</span>
                    Guru: <span class="font-semibold text-slate-700">{{ $classSubject->teacher->name }}</span>
                    @if($classSubject->teacher->subjects->isNotEmpty())
                        <span class="text-xs text-slate-500">({{ $classSubject->teacher->subjects->pluck('name')->join(', ') }})</span>
                    @elseif($classSubject->teacher->subject)
                        <span class="text-xs text-slate-500">({{ $classSubject->teacher->subject }})</span>
                    @endif
                </p>
            </div>
        </div>
    </div>

    {{-- Info Card --}}
    <div class="card bg-gradient-to-br from-brand-600 to-brand-800 text-white border-none shadow-md overflow-hidden">
        <div class="card-body p-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <span class="inline-flex items-center gap-1 rounded-full bg-white/20 px-3 py-1 text-[11px] font-bold text-white mb-2">
                    <i class="bi bi-book"></i> Kode: {{ $classSubject->subject->code ?? '-' }}
                </span>
                <h3 class="text-2xl font-extrabold">{{ $classSubject->subject->name }}</h3>
                <p class="text-brand-100 text-sm mt-1">Dosen/Guru Pengampu: <span class="font-semibold text-white">{{ $classSubject->teacher->name }}</span>
                    @if($classSubject->teacher->subjects->isNotEmpty())
                        <span class="text-xs text-brand-100">({{ $classSubject->teacher->subjects->pluck('name')->join(', ') }})</span>
                    @elseif($classSubject->teacher->subject)
                        <span class="text-xs text-brand-100">({{ $classSubject->teacher->subject }})</span>
                    @endif
                </p>
            </div>
            <div class="flex gap-3">
                <div class="rounded-xl bg-white/10 px-4 py-2.5 text-center min-w-[80px]">
                    <div class="text-xl font-black">{{ $classSubject->materials->count() }}</div>
                    <div class="text-[10px] text-brand-100 font-semibold uppercase tracking-wider">Materi</div>
                </div>
                <div class="rounded-xl bg-white/10 px-4 py-2.5 text-center min-w-[80px]">
                    <div class="text-xl font-black">{{ $classSubject->assignments->count() }}</div>
                    <div class="text-[10px] text-brand-100 font-semibold uppercase tracking-wider">Tugas</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content Grid --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        
        {{-- Materials Section --}}
        <div class="card shadow-sm border border-slate-200 rounded-2xl bg-white">
            <div class="card-header bg-slate-50/80 px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-extrabold text-slate-800 text-base flex items-center gap-2">
                    <i class="bi bi-file-earmark-richtext text-brand-500 text-lg"></i>
                    Materi Pelajaran
                </h3>
                <div class="flex items-center gap-2">
                    <div class="inline-flex overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                        <button type="button" onclick="toggleView('materials', 'grid')" id="btn-materials-grid" class="px-2.5 py-1.5 text-xs transition" title="Grid View">
                            <i class="bi bi-grid-3x3-gap-fill"></i>
                        </button>
                        <button type="button" onclick="toggleView('materials', 'list')" id="btn-materials-list" class="px-2.5 py-1.5 text-xs transition" title="List View">
                            <i class="bi bi-list-ul"></i>
                        </button>
                    </div>
                    <span class="badge bg-primary text-[10px]">{{ $classSubject->materials->count() }} File</span>
                </div>
            </div>
            <div class="card-body p-4 space-y-6">
                @forelse($groupedMaterials as $date => $dateMaterials)
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">
                                <i class="bi bi-calendar-check me-1"></i>{{ formatDateGroup($date) }}
                            </span>
                            <div class="h-px bg-slate-100 flex-1"></div>
                        </div>
                        
                        {{-- Grid view --}}
                        <div id="materials-grid-{{ $date }}" class="materials-grid-container grid grid-cols-1 gap-4 sm:grid-cols-2">
                            @foreach($dateMaterials as $material)
                                <div class="group relative overflow-hidden rounded-xl bg-slate-50/50 p-4 border border-slate-100 transition hover:shadow-md hover:bg-white hover:border-brand-200">
                                    <div class="flex items-start gap-3">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
                                            <i class="bi {{ $material->type_icon }}"></i>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <h4 class="font-bold text-slate-900 text-sm truncate group-hover:text-brand-600 transition">{{ $material->title }}</h4>
                                            <p class="text-xs text-slate-500 mt-1 truncate">Topik: {{ $material->topic ?: 'Umum' }}</p>
                                            <div class="mt-3 flex items-center justify-between">
                                                <span class="text-[10px] text-slate-400 font-medium">
                                                    Dibuat: {{ $material->created_at->format('d M Y') }}
                                                </span>
                                                <a href="{{ route('materials.show', $material) }}" class="text-xs font-bold text-brand-600 hover:text-brand-800 transition">
                                                    Buka <i class="bi bi-chevron-right"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- List view --}}
                        <div id="materials-list-{{ $date }}" class="materials-list-container hidden divide-y divide-slate-100 rounded-xl border border-slate-100 bg-white">
                            @foreach($dateMaterials as $material)
                                <div class="flex items-center justify-between p-3 hover:bg-slate-50/50 transition">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
                                            <i class="bi {{ $material->type_icon }}"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <h4 class="font-bold text-slate-900 text-sm truncate">{{ $material->title }}</h4>
                                            <span class="text-[10px] text-slate-400 font-medium">Dibuat: {{ $material->created_at->format('d M Y') }}</span>
                                        </div>
                                    </div>
                                    <a href="{{ route('materials.show', $material) }}" class="btn btn-xs btn-outline-primary">Lihat</a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-slate-500">
                        <div class="flex justify-center mb-3">
                            <div class="h-12 w-12 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 text-2xl">
                                <i class="bi bi-journal-x"></i>
                            </div>
                        </div>
                        <p class="text-sm font-semibold text-slate-700">Belum ada materi pelajaran</p>
                        <p class="text-xs text-slate-400 mt-1">Guru belum mempublikasikan materi untuk pelajaran ini.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Assignments Section --}}
        <div class="card shadow-sm border border-slate-200 rounded-2xl bg-white">
            <div class="card-header bg-slate-50/80 px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-extrabold text-slate-800 text-base flex items-center gap-2">
                    <i class="bi bi-journal-check text-brand-500 text-lg"></i>
                    Tugas Pelajaran
                </h3>
                <div class="flex items-center gap-2">
                    <div class="inline-flex overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                        <button type="button" onclick="toggleView('assignments', 'grid')" id="btn-assignments-grid" class="px-2.5 py-1.5 text-xs transition" title="Grid View">
                            <i class="bi bi-grid-3x3-gap-fill"></i>
                        </button>
                        <button type="button" onclick="toggleView('assignments', 'list')" id="btn-assignments-list" class="px-2.5 py-1.5 text-xs transition" title="List View">
                            <i class="bi bi-list-ul"></i>
                        </button>
                    </div>
                    <span class="badge bg-primary text-[10px]">{{ $classSubject->assignments->count() }} Tugas</span>
                </div>
            </div>
            <div class="card-body p-4 space-y-6">
                @forelse($groupedAssignments as $date => $dateAssignments)
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">
                                <i class="bi bi-calendar-check me-1"></i>{{ formatDateGroup($date) }}
                            </span>
                            <div class="h-px bg-slate-100 flex-1"></div>
                        </div>

                        {{-- Grid view --}}
                        <div id="assignments-grid-{{ $date }}" class="assignments-grid-container grid grid-cols-1 gap-4 sm:grid-cols-2">
                            @foreach($dateAssignments as $assignment)
                                @php
                                    $submission = $assignment->submissions->first();
                                    $statusBg = 'bg-slate-50';
                                    $statusBorder = 'border-slate-200';
                                    $statusText = 'Belum Dikerjakan';
                                    $statusColor = 'text-slate-600';
                                    $iconClass = 'bi-exclamation-circle text-slate-400';
                                    
                                    if ($submission) {
                                        if ($submission->grade) {
                                            $statusBg = 'bg-emerald-50';
                                            $statusBorder = 'border-emerald-200';
                                            $statusText = 'Sudah Dinilai (' . (float)$submission->grade->score . ')';
                                            $statusColor = 'text-emerald-700';
                                            $iconClass = 'bi-check-circle-fill text-emerald-500';
                                        } else {
                                            $statusBg = 'bg-blue-50';
                                            $statusBorder = 'border-blue-200';
                                            $statusText = 'Menunggu Penilaian';
                                            $statusColor = 'text-blue-700';
                                            $iconClass = 'bi-clock-fill text-blue-500';
                                        }
                                    } elseif ($assignment->isPastDue()) {
                                        $statusBg = 'bg-rose-50';
                                        $statusBorder = 'border-rose-200';
                                        $statusText = 'Terlambat / Melewati Tenggat';
                                        $statusColor = 'text-rose-700';
                                        $iconClass = 'bi-x-circle-fill text-rose-500';
                                    }
                                @endphp
                                <div class="group relative overflow-hidden rounded-xl bg-slate-50/50 p-4 border border-slate-100 transition hover:shadow-md hover:bg-white hover:border-brand-200">
                                    <div class="flex items-start gap-3">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                                            <i class="bi bi-file-earmark-check"></i>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <h4 class="font-bold text-slate-900 text-sm truncate group-hover:text-brand-600 transition">{{ $assignment->title }}</h4>
                                            <div class="mt-2">
                                                <span class="inline-flex items-center gap-1 rounded bg-slate-100 px-1.5 py-0.5 text-[9px] font-bold text-slate-600 ring-1 ring-slate-200">
                                                    {{ ucfirst($assignment->type) }}
                                                </span>
                                                <span class="inline-flex items-center gap-1 rounded-lg border {{ $statusBorder }} {{ $statusBg }} px-2 py-0.5 text-[10px] font-semibold {{ $statusColor }} ml-1">
                                                    <i class="bi {{ $iconClass }}"></i>
                                                    {{ $statusText }}
                                                </span>
                                            </div>
                                            <div class="mt-3 flex items-center justify-between">
                                                <span class="text-[10px] text-slate-400 font-medium">
                                                    Dibuat: {{ $assignment->created_at->format('d M Y') }}
                                                </span>
                                                <a href="{{ route('student.assignments.show', $assignment) }}" class="text-xs font-bold text-brand-600 hover:text-brand-800 transition">
                                                    Buka <i class="bi bi-chevron-right"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- List view --}}
                        <div id="assignments-list-{{ $date }}" class="assignments-list-container hidden divide-y divide-slate-100 rounded-xl border border-slate-100 bg-white">
                            @foreach($dateAssignments as $assignment)
                                @php
                                    $submission = $assignment->submissions->first();
                                    $statusBg = 'bg-slate-50';
                                    $statusBorder = 'border-slate-200';
                                    $statusText = 'Belum Dikerjakan';
                                    $statusColor = 'text-slate-600';
                                    $iconClass = 'bi-exclamation-circle text-slate-400';
                                    
                                    if ($submission) {
                                        if ($submission->grade) {
                                            $statusBg = 'bg-emerald-50';
                                            $statusBorder = 'border-emerald-200';
                                            $statusText = 'Sudah Dinilai (' . (float)$submission->grade->score . ')';
                                            $statusColor = 'text-emerald-700';
                                            $iconClass = 'bi-check-circle-fill text-emerald-500';
                                        } else {
                                            $statusBg = 'bg-blue-50';
                                            $statusBorder = 'border-blue-200';
                                            $statusText = 'Menunggu Penilaian';
                                            $statusColor = 'text-blue-700';
                                            $iconClass = 'bi-clock-fill text-blue-500';
                                        }
                                    } elseif ($assignment->isPastDue()) {
                                        $statusBg = 'bg-rose-50';
                                        $statusBorder = 'border-rose-200';
                                        $statusText = 'Terlambat / Melewati Tenggat';
                                        $statusColor = 'text-rose-700';
                                        $iconClass = 'bi-x-circle-fill text-rose-500';
                                    }
                                @endphp
                                <div class="flex items-center justify-between p-3 hover:bg-slate-50/50 transition">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                                            <i class="bi bi-file-earmark-check"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <h4 class="font-bold text-slate-900 text-sm truncate">{{ $assignment->title }}</h4>
                                            <span class="inline-flex items-center gap-1 rounded bg-slate-100 px-1 py-0.5 text-[8px] font-bold text-slate-600">
                                                {{ ucfirst($assignment->type) }}
                                            </span>
                                            <span class="text-[10px] text-slate-400 font-medium ml-2">Dibuat: {{ $assignment->created_at->format('d M Y') }}</span>
                                        </div>
                                    </div>
                                    <a href="{{ route('student.assignments.show', $assignment) }}" class="btn btn-xs btn-outline-primary">Lihat</a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-500">
                        <div class="flex justify-center mb-3">
                            <div class="h-12 w-12 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 text-2xl">
                                <i class="bi bi-clipboard-x"></i>
                            </div>
                        </div>
                        <p class="text-sm font-semibold text-slate-700">Belum ada tugas pelajaran</p>
                        <p class="text-xs text-slate-400 mt-1">Guru belum mempublikasikan tugas untuk pelajaran ini.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
    function toggleView(section, view) {
        localStorage.setItem('student_class_subject_' + section + '_view', view);
        applyViewPreference(section);
    }
    
    function applyViewPreference(section) {
        const view = localStorage.getItem('student_class_subject_' + section + '_view') || 'grid';
        
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
        applyViewPreference('assignments');
    });
</script>
@endpush
@endsection
