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
            <div class="flex flex-wrap gap-2 sm:gap-3">
                <div class="rounded-xl bg-white/10 px-3.5 py-2 text-center min-w-[75px] backdrop-blur-xs">
                    <div class="text-lg sm:text-xl font-black">{{ $classSubject->materials->count() }}</div>
                    <div class="text-[10px] text-brand-100 font-semibold uppercase tracking-wider">Materi</div>
                </div>
                <div class="rounded-xl bg-white/10 px-3.5 py-2 text-center min-w-[75px] backdrop-blur-xs">
                    <div class="text-lg sm:text-xl font-black">{{ $classSubject->assignments->count() }}</div>
                    <div class="text-[10px] text-brand-100 font-semibold uppercase tracking-wider">Tugas</div>
                </div>
                @if(!auth()->user()->isSiswa())
                <div class="rounded-xl bg-white/10 px-3.5 py-2 text-center min-w-[85px] backdrop-blur-xs">
                    <div class="text-lg sm:text-xl font-black">
                        {{ ($attendanceStats['hadir'] ?? 0) + ($attendanceStats['terlambat'] ?? 0) }}/{{ $attendanceStats['total'] ?? $classStudents->count() }}
                    </div>
                    <div class="text-[10px] text-brand-100 font-semibold uppercase tracking-wider">Siswa Hadir</div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ── 1. UNTUK SISWA: HANYA STATUS KEHADIRAN PRIBADI & TOMBOL KLIK HADIR (TANPA LAPORAN SEKELAS) ── --}}
    @if(auth()->user()->isSiswa())
        <div class="rounded-2xl bg-white p-5 shadow-sm border border-slate-200 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 font-extrabold text-lg border border-emerald-200">
                        <i class="bi bi-person-check-fill"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                            Presensi Pelajaran Hari Ini
                            <span class="inline-flex items-center rounded-md bg-emerald-50 px-2 py-0.5 text-[10px] font-black text-emerald-700 border border-emerald-200 uppercase tracking-wider">
                                Aktif
                            </span>
                        </h3>
                        <p class="text-xs text-slate-500 font-medium mt-0.5">
                            Status kehadiran mandiri Anda pada mata pelajaran {{ $classSubject->subject->name }}.
                        </p>
                    </div>
                </div>

                @if(!empty($activeSchedule))
                    <span class="hidden sm:inline-flex items-center gap-1.5 rounded-lg bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">
                        <i class="bi bi-clock"></i> {{ $activeSchedule->start_time->format('H:i') }} - {{ $activeSchedule->end_time->format('H:i') }} WIB
                    </span>
                @endif
            </div>

            @if($studentAttendanceDetail && in_array($studentAttendanceDetail->status, ['hadir', 'terlambat'], true))
                <div class="rounded-xl border-2 {{ $studentAttendanceDetail->status === 'terlambat' ? 'border-amber-400 bg-amber-500' : 'border-emerald-500 bg-emerald-600' }} text-white p-4 flex flex-col sm:flex-row items-center justify-between gap-3 shadow-md">
                    <div class="flex items-center gap-3.5">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-white/20 text-white font-black text-2xl">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h4 class="text-sm font-black text-white uppercase tracking-wide">
                                    {{ $studentAttendanceDetail->status === 'terlambat' ? 'STATUS: HADIR (TERLAMBAT)' : 'STATUS: HADIR DI KELAS' }}
                                </h4>
                                <span class="rounded-full bg-white/20 px-2 py-0.5 text-[10px] font-extrabold text-white">
                                    {{ $studentAttendanceDetail->check_in ? \Carbon\Carbon::parse($studentAttendanceDetail->check_in)->format('H:i') : now()->format('H:i') }} WIB
                                </span>
                            </div>
                            <p class="text-xs text-emerald-100 font-medium mt-0.5">
                                Kehadiran Anda pada mata pelajaran {{ $classSubject->subject->name }} telah sukses tercatat di sistem LMS.
                            </p>
                        </div>
                    </div>
                    <span class="rounded-full bg-white px-4 py-1.5 text-xs font-black {{ $studentAttendanceDetail->status === 'terlambat' ? 'text-amber-800' : 'text-emerald-700' }} uppercase tracking-wider shadow-sm shrink-0">
                        {{ $studentAttendanceDetail->status === 'terlambat' ? '⏰ TERLAMBAT' : '✅ SUDAH HADIR' }}
                    </span>
                </div>
            @elseif($studentAttendanceDetail && $studentAttendanceDetail->status === 'sakit')
                <div class="rounded-xl border-2 border-amber-500 bg-amber-500 text-white p-4 flex flex-col sm:flex-row items-center justify-between gap-3 shadow-md shadow-amber-500/20">
                    <div class="flex items-center gap-3.5">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-white/20 text-white font-bold text-2xl">
                            <i class="bi bi-bandaid-fill"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-extrabold text-white">STATUS: SAKIT</h4>
                            <p class="text-xs text-amber-100 font-medium mt-0.5">Anda tercatat Sakit pada presensi hari ini (Keterangan: {{ $studentAttendanceDetail->notes ?: 'Disinkronkan dari presensi harian' }}).</p>
                        </div>
                    </div>
                    <span class="rounded-full bg-white px-4 py-1.5 text-xs font-black text-amber-800 uppercase tracking-wider shadow-sm shrink-0">
                        🤒 SAKIT
                    </span>
                </div>
            @elseif($studentAttendanceDetail && $studentAttendanceDetail->status === 'izin')
                <div class="rounded-xl border-2 border-sky-500 bg-sky-600 text-white p-4 flex flex-col sm:flex-row items-center justify-between gap-3 shadow-md shadow-sky-600/20">
                    <div class="flex items-center gap-3.5">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-white/20 text-white font-bold text-2xl">
                            <i class="bi bi-file-earmark-text-fill"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-extrabold text-white">STATUS: IZIN</h4>
                            <p class="text-xs text-sky-100 font-medium mt-0.5">Anda tercatat Izin pada presensi hari ini (Keterangan: {{ $studentAttendanceDetail->notes ?: 'Disinkronkan dari presensi harian' }}).</p>
                        </div>
                    </div>
                    <span class="rounded-full bg-white px-4 py-1.5 text-xs font-black text-sky-800 uppercase tracking-wider shadow-sm shrink-0">
                        📄 IZIN
                    </span>
                </div>
            @elseif(!empty($attendanceWindow['can_check_in']))
                <div class="rounded-xl border-2 border-emerald-300 bg-emerald-50/60 p-4 sm:p-5 flex flex-col sm:flex-row items-center justify-between gap-4 shadow-sm">
                    <div class="flex items-center gap-3.5">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-emerald-600 text-white font-bold text-xl shadow-md shadow-emerald-600/20">
                            <i class="bi bi-hand-index-thumb-fill"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h4 class="text-sm font-black text-slate-900">
                                    {{ !empty($attendanceWindow['is_late']) ? 'Presensi Susulan Mata Pelajaran' : 'Sesi Presensi Sedang Dibuka' }}
                                </h4>
                                @if(!empty($attendanceWindow['is_late']))
                                    <span class="rounded-md bg-amber-100 px-2 py-0.5 text-[10px] font-extrabold text-amber-800">
                                        Terlambat
                                    </span>
                                @else
                                    <span class="rounded-md bg-emerald-100 px-2 py-0.5 text-[10px] font-extrabold text-emerald-800">
                                        Jam Pelajaran
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-600 font-medium mt-1">
                                {{ $attendanceWindow['message'] }} Klik tombol di samping untuk mengonfirmasi kehadiran Anda.
                            </p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('student.class-subjects.check-in', $classSubject->id) }}" class="shrink-0 w-full sm:w-auto">
                        @csrf
                        <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-6 py-3 text-xs font-black text-white shadow-lg shadow-emerald-600/25 hover:bg-emerald-500 hover:scale-[1.02] active:scale-[0.98] transition cursor-pointer">
                            <i class="bi bi-fingerprint text-base"></i>
                            <span>{{ !empty($attendanceWindow['is_late']) ? 'KLIK HADIR (TERLAMBAT)' : 'KLIK HADIR SEKARANG' }}</span>
                        </button>
                    </form>
                </div>
            @else
                <div class="rounded-xl border border-slate-200 bg-slate-50/80 p-4 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-200 text-slate-600 font-bold text-lg">
                            @if(($attendanceWindow['status'] ?? '') === 'not_started')
                                <i class="bi bi-clock"></i>
                            @else
                                <i class="bi bi-calendar-event"></i>
                            @endif
                        </div>
                        <div>
                            <h4 class="text-xs font-black text-slate-800">
                                @if(($attendanceWindow['status'] ?? '') === 'not_started')
                                    Presensi Belum Dibuka
                                @else
                                    Jadwal Pelajaran
                                @endif
                            </h4>
                            <p class="text-xs text-slate-500 font-medium mt-0.5">{{ $attendanceWindow['message'] }}</p>
                        </div>
                    </div>
                    <span class="rounded-xl bg-slate-200 px-3.5 py-1.5 text-[11px] font-bold text-slate-600 shrink-0">
                        @if(($attendanceWindow['status'] ?? '') === 'not_started')
                            Belum Jam Mulai
                        @else
                            Di Luar Jam Jadwal
                        @endif
                    </span>
                </div>
            @endif
        </div>

    {{-- ── 2. UNTUK GURU & STAFF: LAPORAN LENGKAP PRESENSI KELAS ("SIAPA SAJA YANG MENGIKUTI") ── --}}
    @else
        <div class="rounded-2xl bg-white p-5 shadow-sm border border-slate-200 space-y-4">
            {{-- Header Laporan Presensi Guru --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600 font-extrabold text-lg border border-brand-200">
                        <i class="bi bi-clipboard2-check-fill"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                            Laporan Presensi Siswa Mata Pelajaran
                            <span class="inline-flex items-center rounded-md bg-brand-50 px-2 py-0.5 text-[10px] font-black text-brand-700 border border-brand-200 uppercase tracking-wider">
                                Laporan Guru
                            </span>
                        </h3>
                        <p class="text-xs text-slate-500 font-medium mt-0.5">
                            Mengetahui siapa saja siswa kelas {{ $classSubject->class->name }} yang mengikuti pelajaran {{ $classSubject->subject->name }} hari ini.
                        </p>
                    </div>
                </div>

                <a href="{{ route('attendance.create', ['class_id' => $classSubject->class_id]) }}" class="inline-flex items-center gap-1.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white px-4 py-2 text-xs font-black shadow-sm transition">
                    <i class="bi bi-pencil-square"></i> Input / Edit Presensi Kelas
                </a>
            </div>

            {{-- Ringkasan Statistik Kehadiran --}}
            <div class="flex flex-wrap items-center gap-1.5">
                <span class="inline-flex items-center gap-1 rounded-lg bg-white px-2.5 py-1 text-[11px] font-black text-slate-700 border border-slate-200 shadow-2xs">
                    👥 Total: {{ $attendanceStats['total'] ?? $classStudents->count() }}
                </span>
                <span class="inline-flex items-center gap-1 rounded-lg bg-emerald-50 px-2.5 py-1 text-[11px] font-black text-emerald-700 border border-emerald-200 shadow-2xs">
                    🟢 Hadir: {{ $attendanceStats['hadir'] ?? 0 }}
                </span>
                @if(!empty($attendanceStats['terlambat']))
                <span class="inline-flex items-center gap-1 rounded-lg bg-amber-50 px-2.5 py-1 text-[11px] font-black text-amber-700 border border-amber-200 shadow-2xs">
                    ⏰ Telat: {{ $attendanceStats['terlambat'] }}
                </span>
                @endif
                <span class="inline-flex items-center gap-1 rounded-lg bg-yellow-50 px-2.5 py-1 text-[11px] font-black text-yellow-800 border border-yellow-200 shadow-2xs">
                    🟡 Sakit: {{ $attendanceStats['sakit'] ?? 0 }}
                </span>
                <span class="inline-flex items-center gap-1 rounded-lg bg-sky-50 px-2.5 py-1 text-[11px] font-black text-sky-700 border border-sky-200 shadow-2xs">
                    🔵 Izin: {{ $attendanceStats['izin'] ?? 0 }}
                </span>
                <span class="inline-flex items-center gap-1 rounded-lg bg-rose-50 px-2.5 py-1 text-[11px] font-black text-rose-700 border border-rose-200 shadow-2xs">
                    🔴 Belum Hadir: {{ $attendanceStats['alfa'] ?? 0 }}
                </span>
            </div>

            {{-- Filter Pencarian Cepat Siswa --}}
            <div class="relative">
                <i class="bi bi-search absolute left-3.5 top-2.5 text-xs text-slate-400"></i>
                <input type="text" 
                       id="searchStudentInput" 
                       placeholder="Ketik untuk mencari nama siswa sekelas..." 
                       onkeyup="filterStudentList()" 
                       class="w-full rounded-xl border border-slate-200 bg-white py-2 pl-9 pr-3 text-xs text-slate-700 focus:border-brand-500 focus:outline-hidden focus:ring-1 focus:ring-brand-500">
            </div>

            {{-- Tabel Laporan Presensi Siswa Khusus Guru --}}
            <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
                <table class="w-full text-left text-xs text-slate-600" id="studentRosterTable">
                    <thead class="bg-slate-50/80 text-[10px] font-black uppercase tracking-wider text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="py-2.5 px-3 w-12 text-center">No</th>
                            <th class="py-2.5 px-3">Nama Siswa</th>
                            <th class="py-2.5 px-3">NIS</th>
                            <th class="py-2.5 px-3 text-center">Status Kehadiran</th>
                            <th class="py-2.5 px-3 text-center">Waktu Presensi</th>
                            <th class="py-2.5 px-3">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @php
                            $attendanceMap = $todayAttendance ? $todayAttendance->details->keyBy('student_id') : collect();
                        @endphp

                        @forelse($classStudents as $idx => $st)
                            @php
                                $detail = $attendanceMap->get($st->id);
                                $stStatus = $detail?->status ?? 'alfa';
                            @endphp
                            <tr class="hover:bg-slate-50/60 transition" data-student-name="{{ strtolower($st->name) }}">
                                <td class="py-2.5 px-3 text-center text-slate-400 font-bold text-[11px]">
                                    {{ $idx + 1 }}
                                </td>
                                <td class="py-2.5 px-3">
                                    <div class="flex items-center gap-2">
                                        <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-600 text-[10px] font-black">
                                            {{ strtoupper(substr($st->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="text-xs font-bold text-slate-900">
                                                {{ $st->name }}
                                            </div>
                                            <div class="text-[10px] text-slate-400">
                                                NISN: {{ $st->nisn ?: '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-2.5 px-3 font-mono text-[11px] text-slate-500">
                                    {{ $st->nis }}
                                </td>
                                <td class="py-2.5 px-3 text-center">
                                    @if($stStatus === 'hadir')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-0.5 text-[10px] font-black text-emerald-800">
                                            <i class="bi bi-check-circle-fill"></i> Hadir
                                        </span>
                                    @elseif($stStatus === 'terlambat')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-0.5 text-[10px] font-black text-amber-800">
                                            <i class="bi bi-clock-history"></i> Terlambat
                                        </span>
                                    @elseif($stStatus === 'sakit')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-yellow-100 px-2.5 py-0.5 text-[10px] font-black text-yellow-800">
                                            <i class="bi bi-bandaid"></i> Sakit
                                        </span>
                                    @elseif($stStatus === 'izin')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-sky-100 px-2.5 py-0.5 text-[10px] font-black text-sky-800">
                                            <i class="bi bi-file-earmark-text"></i> Izin
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-0.5 text-[10px] font-bold text-slate-500">
                                            <i class="bi bi-dash-circle"></i> Belum Hadir
                                        </span>
                                    @endif
                                </td>
                                <td class="py-2.5 px-3 text-center font-mono text-[11px] text-slate-600">
                                    @if($detail && $detail->check_in)
                                        <span class="font-bold text-slate-800">{{ \Carbon\Carbon::parse($detail->check_in)->format('H:i') }}</span>
                                        <span class="text-[9px] text-slate-400">WIB</span>
                                    @else
                                        <span class="text-slate-300">-</span>
                                    @endif
                                </td>
                                <td class="py-2.5 px-3 text-[11px] text-slate-500 truncate max-w-[180px]">
                                    @if($detail && $detail->notes)
                                        <span title="{{ $detail->notes }}">{{ $detail->notes }}</span>
                                    @elseif($stStatus === 'hadir' || $stStatus === 'terlambat')
                                        <span class="text-slate-400">Presensi Mandiri</span>
                                    @else
                                        <span class="text-slate-300">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-6 text-center text-slate-400">
                                    Tidak ada data siswa di kelas ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @push('scripts')
        <script>
            function filterStudentList() {
                const input = document.getElementById('searchStudentInput');
                if (!input) return;
                const filter = input.value.toLowerCase();
                const rows = document.querySelectorAll('#studentRosterTable tbody tr');

                rows.forEach(row => {
                    const name = row.getAttribute('data-student-name') || '';
                    if (name.includes(filter)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }
        </script>
        @endpush
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
