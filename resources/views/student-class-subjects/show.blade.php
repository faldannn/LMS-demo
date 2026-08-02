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

    {{-- Banner Info Card --}}
    <div class="rounded-2xl bg-gradient-to-br from-brand-600 to-brand-800 text-white p-6 shadow-md">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <span class="inline-flex items-center gap-1 rounded-full bg-white/20 px-3 py-1 text-[11px] font-bold text-white mb-2">
                    <i class="bi bi-book"></i> Kode: {{ $classSubject->subject->code ?? '-' }}
                </span>
                <h3 class="text-2xl font-extrabold">{{ $classSubject->subject->name }}</h3>
                <p class="text-brand-100 text-sm mt-1">Guru Pengampu: <span class="font-semibold text-white">{{ $classSubject->teacher->name }}</span></p>
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

    {{-- Absensi Pelajaran Hari Ini (Student Self Attendance) --}}
    @if(auth()->user()->isSiswa())
        <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200/70">
            @if($studentAttendanceDetail && $studentAttendanceDetail->status === 'hadir')
                <div class="rounded-xl border-2 border-emerald-500 bg-emerald-500 text-white p-4 flex flex-col sm:flex-row items-center justify-between gap-3 shadow-md shadow-emerald-500/20">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-white/20 text-white font-bold text-xl">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-extrabold text-white">BERHASIL ABSEN HADIR MAPEL HARI INI</h4>
                            <p class="text-xs text-emerald-100 font-medium">Tercatat Hadir pada Pukul {{ $studentAttendanceDetail->check_in ? \Carbon\Carbon::parse($studentAttendanceDetail->check_in)->format('H:i') : now()->format('H:i') }} WIB</p>
                        </div>
                    </div>
                    <span class="rounded-full bg-white px-4 py-1.5 text-xs font-black text-emerald-700 uppercase tracking-wider shadow-sm">
                        ✅ HADIR
                    </span>
                </div>
            @elseif($studentAttendanceDetail && in_array($studentAttendanceDetail->status, ['sakit', 'izin', 'alfa']))
                <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-rose-100 text-rose-600 font-bold">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-extrabold text-rose-900">STATUS KEHADIRAN: {{ strtoupper($studentAttendanceDetail->status) }}</h4>
                            <p class="text-[11px] text-rose-700">Status kehadiran Anda telah ditetapkan oleh Guru Pengampu.</p>
                        </div>
                    </div>
                </div>
            @elseif(!$attendanceWindow['can_check_in'])
                <div class="rounded-xl border border-amber-200 bg-amber-50/70 p-4 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-500 text-white font-bold text-lg shadow-md">
                            @if(($attendanceWindow['status'] ?? '') === 'not_started')
                                <i class="bi bi-lock-fill"></i>
                            @elseif(($attendanceWindow['status'] ?? '') === 'ended')
                                <i class="bi bi-clock-history"></i>
                            @else
                                <i class="bi bi-calendar-x-fill"></i>
                            @endif
                        </div>
                        <div>
                            <h4 class="text-sm font-extrabold text-slate-900">
                                @if(($attendanceWindow['status'] ?? '') === 'not_started')
                                    Absensi Pelajaran Belum Dibuka
                                @elseif(($attendanceWindow['status'] ?? '') === 'ended')
                                    Waktu Absensi Pelajaran Telah Berakhir
                                @else
                                    Tidak Ada Jadwal Pelajaran Hari Ini
                                @endif
                            </h4>
                            <p class="text-xs text-slate-600 mt-0.5">{{ $attendanceWindow['message'] }}</p>
                        </div>
                    </div>
                    <button type="button" disabled class="inline-flex items-center gap-2 rounded-xl bg-slate-200 px-5 py-2.5 text-xs font-extrabold text-slate-500 cursor-not-allowed shadow-none border border-slate-300">
                        <i class="bi bi-lock"></i> 
                        @if(($attendanceWindow['status'] ?? '') === 'not_started')
                            BELUM DIBUKA
                        @elseif(($attendanceWindow['status'] ?? '') === 'ended')
                            SUDAH BERAKHIR
                        @else
                            TIDAK ADA JADWAL
                        @endif
                    </button>
                </div>
            @else
                <div class="rounded-xl border-2 border-brand-200 bg-brand-50/40 p-4 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-brand-600 text-white font-bold text-lg shadow-md">
                            <i class="bi bi-hand-index-thumb-fill"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-extrabold text-slate-900">Absensi Pelajaran Hari Ini</h4>
                            <p class="text-xs text-slate-500">{{ $attendanceWindow['message'] }}</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('student.class-subjects.check-in', $classSubject->id) }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-xs font-extrabold text-white shadow-lg shadow-emerald-600/20 hover:bg-emerald-500 transition">
                            <i class="bi bi-hand-index-thumb"></i> KLIK HADIR SEKARANG
                        </button>
                    </form>
                </div>
            @endif

        </div>
    @endif

    {{-- Main Content Grid --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        {{-- Materials Section --}}
        <div class="card shadow-sm border border-slate-200 rounded-2xl bg-white">
            <div class="card-header bg-slate-50/80 px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-extrabold text-slate-800 text-base flex items-center gap-2">
                    <i class="bi bi-file-earmark-richtext text-brand-500 text-lg"></i>
                    Materi Pelajaran
                </h3>
                <span class="badge bg-primary text-[10px]">{{ $classSubject->materials->count() }} File</span>
            </div>
            <div class="card-body p-4 space-y-4">
                @forelse($groupedMaterials as $date => $dateMaterials)
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">
                                <i class="bi bi-calendar-check me-1"></i>{{ formatDateGroup($date) }}
                            </span>
                            <div class="h-px bg-slate-100 flex-1"></div>
                        </div>
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
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
                                                    {{ $material->created_at->format('d M Y') }}
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
                    </div>
                @empty
                    <div class="text-center py-8 text-slate-400">
                        <i class="bi bi-folder2-open text-3xl"></i>
                        <p class="text-xs mt-2 font-medium">Belum ada materi pelajaran.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Assignments Section --}}
        <div class="card shadow-sm border border-slate-200 rounded-2xl bg-white">
            <div class="card-header bg-slate-50/80 px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-extrabold text-slate-800 text-base flex items-center gap-2">
                    <i class="bi bi-journal-check text-blue-500 text-lg"></i>
                    Tugas Pelajaran
                </h3>
                <span class="badge bg-primary text-[10px]">{{ $classSubject->assignments->count() }} Tugas</span>
            </div>
            <div class="card-body p-4 space-y-4">
                @forelse($groupedAssignments as $date => $dateAssignments)
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">
                                <i class="bi bi-calendar-check me-1"></i>{{ formatDateGroup($date) }}
                            </span>
                            <div class="h-px bg-slate-100 flex-1"></div>
                        </div>
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            @foreach($dateAssignments as $assignment)
                                @php
                                    $submission = $assignment->submissions->first();
                                @endphp
                                <div class="group relative overflow-hidden rounded-xl bg-slate-50/50 p-4 border border-slate-100 transition hover:shadow-md hover:bg-white hover:border-blue-200">
                                    <div class="flex items-start gap-3">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                                            <i class="bi bi-file-earmark-text"></i>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <h4 class="font-bold text-slate-900 text-sm truncate group-hover:text-blue-600 transition">{{ $assignment->title }}</h4>
                                            @if($submission)
                                                <span class="inline-flex items-center gap-1 mt-1 text-[10px] font-extrabold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded">
                                                    <i class="bi bi-check-circle"></i> Sudah Dikirim
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 mt-1 text-[10px] font-extrabold text-amber-600 bg-amber-50 px-2 py-0.5 rounded">
                                                    <i class="bi bi-clock"></i> Belum Dikirim
                                                </span>
                                            @endif
                                            <div class="mt-3 flex items-center justify-between">
                                                <span class="text-[10px] text-slate-400 font-medium">
                                                    Tenggat: {{ $assignment->due_date ? \Carbon\Carbon::parse($assignment->due_date)->format('d M Y') : '-' }}
                                                </span>
                                                <a href="{{ route('student.assignments.show', $assignment) }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 transition">
                                                    Buka <i class="bi bi-chevron-right"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-slate-400">
                        <i class="bi bi-journal-x text-3xl"></i>
                        <p class="text-xs mt-2 font-medium">Belum ada tugas pelajaran.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
