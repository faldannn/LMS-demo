@extends('layouts.app')
@section('title', $classSubject->subject->name)
@section('page-title', 'Detail Mata Pelajaran')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('schedules.index') }}" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 shadow-sm transition hover:border-brand-300 hover:bg-brand-50 hover:text-brand-600" title="Kembali ke Jadwal Pelajaran">
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
    </div>

    {{-- Info Card --}}
    <div class="card bg-gradient-to-br from-brand-600 to-brand-800 text-white border-none shadow-md overflow-hidden">
        <div class="card-body p-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <span class="inline-flex items-center gap-1 rounded-full bg-white/20 px-3 py-1 text-[11px] font-bold text-white mb-2">
                    <i class="bi bi-book"></i> Kode: {{ $classSubject->subject->code ?? '-' }}
                </span>
                <h3 class="text-2xl font-extrabold">{{ $classSubject->subject->name }}</h3>
                <p class="text-brand-100 text-sm mt-1">Dosen/Guru Pengampu: <span class="font-semibold text-white">{{ $classSubject->teacher->name }}</span></p>
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
                <span class="badge bg-primary">{{ $classSubject->materials->count() }} File</span>
            </div>
            <div class="card-body p-0 divide-y divide-slate-100">
                @forelse($classSubject->materials as $material)
                    <div class="p-6 transition hover:bg-slate-50/50 flex gap-4">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-xl text-brand-600">
                            <i class="bi {{ $material->type_icon }}"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h4 class="font-bold text-slate-900 text-sm truncate">{{ $material->title }}</h4>
                            <p class="text-xs text-slate-500 mt-1">Topik: <span class="font-medium text-slate-700">{{ $material->topic ?: 'Umum' }}</span></p>
                            <div class="mt-3 flex items-center justify-between">
                                <span class="text-[11px] text-slate-400 font-medium">
                                    <i class="bi bi-clock me-1"></i>{{ $material->created_at->diffForHumans() }}
                                </span>
                                <a href="{{ route('materials.show', $material) }}" class="inline-flex items-center gap-1 text-xs font-bold text-brand-600 hover:text-brand-800 transition">
                                    Lihat Materi <i class="bi bi-chevron-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-500">
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
                <span class="badge bg-primary">{{ $classSubject->assignments->count() }} Tugas</span>
            </div>
            <div class="card-body p-0 divide-y divide-slate-100">
                @forelse($classSubject->assignments as $assignment)
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
                    <div class="p-6 transition hover:bg-slate-50/50 flex gap-4">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-xl text-amber-600">
                            <i class="bi bi-file-earmark-check"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h4 class="font-bold text-slate-900 text-sm truncate">{{ $assignment->title }}</h4>
                            <div class="mt-2 flex flex-wrap items-center gap-2">
                                <span class="inline-flex items-center gap-1 rounded-lg border {{ $statusBorder }} {{ $statusBg }} px-2 py-0.5 text-[10px] font-semibold {{ $statusColor }}">
                                    <i class="bi {{ $iconClass }}"></i>
                                    {{ $statusText }}
                                </span>
                            </div>
                            <div class="mt-3 flex items-center justify-between">
                                <span class="text-[11px] text-slate-400 font-medium">
                                    <i class="bi bi-calendar-x me-1"></i>Tenggat: {{ $assignment->due_date ? $assignment->due_date->format('d M Y, H:i') : 'Tanpa batas waktu' }}
                                </span>
                                <a href="{{ route('student.assignments.show', $assignment) }}" class="inline-flex items-center gap-1 text-xs font-bold text-brand-600 hover:text-brand-800 transition">
                                    Lihat Tugas <i class="bi bi-chevron-right"></i>
                                </a>
                            </div>
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
@endsection
