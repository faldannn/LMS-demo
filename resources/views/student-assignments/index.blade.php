@extends('layouts.app')
@section('title', 'Tugas Saya')
@section('page-title', 'Tugas Saya')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-extrabold text-slate-900">Daftar Tugas</h2>
            <p class="mt-1 text-sm text-slate-500">Kumpulkan tugas Anda sebelum batas waktu berakhir.</p>
        </div>
    </div>

    @php $hasAnyAssignments = false; @endphp

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        @foreach($classSubjects as $classSubject)
            @if($classSubject->assignments->count() > 0)
            @php $hasAnyAssignments = true; @endphp
            <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/70">
                <div class="border-b border-amber-500/10 bg-gradient-to-br from-amber-500 to-amber-600 px-6 py-4 text-white">
                    <h3 class="text-lg font-bold">{{ $classSubject->subject->name }}</h3>
                    <p class="mt-1 text-xs font-medium text-amber-100">{{ $classSubject->teacher->name }}
                        @if($classSubject->teacher->subjects->isNotEmpty())
                            ({{ $classSubject->teacher->subjects->pluck('name')->join(', ') }})
                        @elseif($classSubject->teacher->subject)
                            ({{ $classSubject->teacher->subject }})
                        @endif
                    </p>
                </div>
                
                <div class="divide-y divide-slate-100">
                    @foreach($classSubject->assignments as $assignment)
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
                        
                        <a href="{{ route('student.assignments.show', $assignment) }}" class="block px-6 py-4 transition hover:bg-slate-50">
                            <h4 class="font-bold text-slate-900 group-hover:text-brand-700">{{ $assignment->title }}</h4>
                            
                            <div class="mt-3 flex flex-wrap items-center gap-3">
                                <span class="inline-flex items-center gap-1.5 rounded-lg border {{ $statusBorder }} {{ $statusBg }} px-2.5 py-1 text-xs font-semibold {{ $statusColor }}">
                                    <i class="bi {{ $iconClass }}"></i>
                                    {{ $statusText }}
                                </span>
                                
                                <span class="flex items-center gap-1.5 text-xs font-medium text-slate-500">
                                    <i class="bi bi-calendar-x"></i>
                                    Tenggat: {{ $assignment->due_date ? $assignment->due_date->format('d M Y, H:i') : 'Tanpa batas waktu' }}
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
            @endif
        @endforeach
    </div>

    @if(!$hasAnyAssignments && $classSubjects->count() > 0)
    <div class="rounded-2xl bg-white p-12 text-center shadow-sm ring-1 ring-slate-200/70">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-emerald-50">
            <i class="bi bi-card-checklist text-3xl text-emerald-500"></i>
        </div>
        <h3 class="mt-4 text-base font-bold text-slate-900">Belum ada tugas saat ini. Hore!</h3>
        <p class="mt-2 text-sm text-slate-500">Gunakan waktu luang Anda untuk membaca materi atau beristirahat.</p>
    </div>
    @elseif($classSubjects->count() == 0)
    <div class="rounded-2xl bg-white p-12 text-center shadow-sm ring-1 ring-slate-200/70">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-50">
            <i class="bi bi-journal-x text-3xl text-slate-400"></i>
        </div>
        <h3 class="mt-4 text-base font-bold text-slate-900">Belum Ada Pelajaran</h3>
        <p class="mt-2 text-sm text-slate-500">Anda belum terdaftar di kelas atau mata pelajaran apapun.</p>
    </div>
    @endif
</div>
@endsection
