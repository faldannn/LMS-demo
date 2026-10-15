{{-- resources/views/attendance/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Daftar Absensi')
@section('page-title', 'Daftar Absensi')

@section('content')
    @php
        $isPrincipal = auth()->user()->isKepalaSekolah();
        $isAdmin = auth()->user()->isAdmin() || auth()->user()->isSuperAdmin();
        $isStaff = $isPrincipal || $isAdmin;
    @endphp

    {{-- Top Header & Main Action --}}
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">Manajemen Presensi</h1>
            <p class="text-sm text-slate-500">
                Kelola dan pantau presensi harian siswa dan guru secara real-time.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            @if($isStaff)
                {{-- Export Buttons --}}
                <div class="inline-flex overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                    <button type="button" onclick="exportExcelSiswa()" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold text-emerald-700 hover:bg-emerald-50 border-r border-slate-200" title="Export Rekap Siswa Sesuai Filter">
                        <i class="bi bi-file-earmark-excel text-base"></i> Export Excel Siswa
                    </button>
                    <button type="button" onclick="exportExcelGuru()" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold text-brand-700 hover:bg-brand-50" title="Export Rekap Guru Sesuai Filter">
                        <i class="bi bi-file-earmark-excel text-base"></i> Export Excel Guru
                    </button>
                </div>
                <a href="{{ route('attendance.scan-qr') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 px-3.5 py-2 text-xs font-bold text-white shadow-sm transition" title="Buka Scanner QR Siswa">
                    <i class="bi bi-camera-fill text-sm"></i> Scan QR Siswa
                </a>
                <a href="{{ route('attendance.self-logs') }}" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 shadow-sm" title="Lihat Log Presensi QR Code Siswa">
                    <i class="bi bi-qr-code-scan text-brand-600 text-sm"></i> Log Presensi QR
                </a>
            @endif

            @php
                $userTeacher = auth()->user()->teacher;
                $isHomeroomTeacher = $userTeacher && $userTeacher->homeroomClasses()->exists();
            @endphp
            @if($isAdmin || $isHomeroomTeacher)
            <a href="{{ route('attendance.homeroom-create') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700">
                <i class="bi bi-plus-lg"></i> Input Absensi Kelas (Wali Kelas)
            </a>
            @endif
        </div>
    </div>

    {{-- Navigation Tabs (Siswa vs Guru) --}}
    @if($isStaff)
    <div class="mb-6 border-b border-slate-200">
        <nav class="-mb-px flex space-x-6">
            <a href="{{ route('attendance.index', array_merge(request()->all(), ['tab' => 'students'])) }}"
               class="flex items-center gap-2 border-b-2 py-3 px-1 text-sm font-extrabold transition {{ $activeTab === 'students' ? 'border-brand-600 text-brand-600' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700' }}">
                <i class="bi bi-people text-base"></i> Presensi Siswa
            </a>
            <a href="{{ route('attendance.index', array_merge(request()->all(), ['tab' => 'teachers'])) }}"
               class="flex items-center gap-2 border-b-2 py-3 px-1 text-sm font-extrabold transition {{ $activeTab === 'teachers' ? 'border-brand-600 text-brand-600' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700' }}">
                <i class="bi bi-person-badge text-base"></i> Presensi Guru (Masuk & Pulang)
            </a>
        </nav>
    </div>
    @endif

    {{-- Filter Form --}}
    <div class="mb-6 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200/60">
        <form method="GET" id="attendanceFilterForm" class="grid grid-cols-1 gap-4 sm:grid-cols-12 items-end">
            <input type="hidden" name="tab" value="{{ $activeTab }}">

            @if($activeTab === 'students')
            <div class="sm:col-span-3">
                <label class="block mb-1.5 text-xs font-bold uppercase tracking-wider text-slate-500">Filter Kelas</label>
                <select name="class_id" class="form-select">
                    <option value="">Semua Kelas</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <div class="{{ $activeTab === 'students' ? 'sm:col-span-2' : 'sm:col-span-3' }}">
                <label class="block mb-1.5 text-xs font-bold uppercase tracking-wider text-slate-500">Periode</label>
                <select name="period" class="form-select">
                    <option value="today" {{ request('period') === 'today' ? 'selected' : '' }}>Hari Ini</option>
                    <option value="30_days" {{ request('period', '30_days') === '30_days' ? 'selected' : '' }}>30 Hari Terakhir</option>
                    <option value="week" {{ request('period') === 'week' ? 'selected' : '' }}>Minggu Ini</option>
                    <option value="month" {{ request('period') === 'month' ? 'selected' : '' }}>Bulan Ini</option>
                </select>
            </div>

            <div class="{{ $activeTab === 'students' ? 'sm:col-span-2' : 'sm:col-span-3' }}">
                <label class="block mb-1.5 text-xs font-bold uppercase tracking-wider text-slate-500">Tanggal Mulai</label>
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date', $sDate->toDateString()) }}">
            </div>

            <div class="{{ $activeTab === 'students' ? 'sm:col-span-2' : 'sm:col-span-3' }}">
                <label class="block mb-1.5 text-xs font-bold uppercase tracking-wider text-slate-500">Tanggal Selesai</label>
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date', $eDate->toDateString()) }}">
            </div>

            @if($isPrincipal && $activeTab === 'students')
                <input type="hidden" name="view" value="{{ $viewMode }}">
            @endif

            <div class="sm:col-span-3 flex gap-2">
                <button type="submit" class="inline-flex w-full items-center justify-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700">
                    <i class="bi bi-funnel"></i> Filter
                </button>
                <a href="{{ route('attendance.index', ['tab' => $activeTab]) }}" class="inline-flex w-full items-center justify-center gap-1.5 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- TAB 1: PRESENSI SISWA --}}
    @if($activeTab === 'students')
        @if($isPrincipal)
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                <div class="text-sm text-slate-500">
                    Menampilkan <strong>{{ $classSummaries->count() }}</strong> kelas
                </div>
                <div class="inline-flex overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                    <a href="{{ route('attendance.index', array_merge(request()->except('view'), ['view' => 'grid', 'tab' => 'students'])) }}" class="px-3 py-2 text-sm transition {{ $viewMode === 'grid' ? 'bg-brand-600 text-white' : 'text-slate-500 hover:bg-brand-50 hover:text-brand-600' }}" title="Tampilan Grid">
                        <i class="bi bi-grid-3x3-gap-fill"></i>
                    </a>
                    <a href="{{ route('attendance.index', array_merge(request()->except('view'), ['view' => 'list', 'tab' => 'students'])) }}" class="px-3 py-2 text-sm transition {{ $viewMode === 'list' ? 'bg-brand-600 text-white' : 'text-slate-500 hover:bg-brand-50 hover:text-brand-600' }}" title="Tampilan List">
                        <i class="bi bi-list-ul"></i>
                    </a>
                </div>
            </div>

            @if($viewMode === 'grid')
                <div class="grid grid-cols-1 gap-4 lg:grid-cols-2 xl:grid-cols-3">
                    @forelse($classSummaries as $class)
                        @php $summary = $class->attendance_summary; @endphp
                        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200/60">
                            <div class="mb-4 flex items-start justify-between gap-3">
                                <div>
                                    <div class="text-lg font-extrabold text-slate-900">{{ $class->name }}</div>
                                    <div class="text-xs text-slate-500">Wali kelas: {{ $class->homeroomTeacher?->name ?? '-' }}</div>
                                </div>
                                <span class="rounded-full bg-brand-50 px-3 py-1 text-xs font-bold text-brand-700">{{ $summary['students'] }} siswa</span>
                            </div>

                            <div class="mb-4 grid grid-cols-2 gap-3">
                                <div class="rounded-xl bg-slate-50 p-3">
                                    <div class="text-xs font-bold uppercase tracking-wider text-slate-400">Sesi</div>
                                    <div class="mt-1 font-mono text-2xl font-extrabold text-slate-900">{{ $summary['sessions'] }}</div>
                                </div>
                                <div class="rounded-xl bg-emerald-50 p-3">
                                    <div class="text-xs font-bold uppercase tracking-wider text-emerald-600">Kehadiran</div>
                                    <div class="mt-1 font-mono text-2xl font-extrabold text-emerald-700">{{ $summary['percentage'] }}%</div>
                                </div>
                            </div>

                            <div class="mb-5 grid grid-cols-3 gap-2 text-center">
                                <div class="rounded-lg bg-emerald-50 px-2 py-2">
                                    <div class="text-[10px] font-bold uppercase text-emerald-600">Hadir</div>
                                    <div class="font-mono font-extrabold text-emerald-700">{{ $summary['hadir'] + $summary['terlambat'] }}</div>
                                </div>
                                <div class="rounded-lg bg-sky-50 px-2 py-2">
                                    <div class="text-[10px] font-bold uppercase text-sky-600">I/S</div>
                                    <div class="font-mono font-extrabold text-sky-700">{{ $summary['izin_sakit'] }}</div>
                                </div>
                                <div class="rounded-lg bg-rose-50 px-2 py-2">
                                    <div class="text-[10px] font-bold uppercase text-rose-600">Alfa</div>
                                    <div class="font-mono font-extrabold text-rose-700">{{ $summary['alfa'] }}</div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between gap-3 border-t border-slate-100 pt-4">
                                <div class="text-xs text-slate-500">
                                    Terakhir:
                                    <span class="font-semibold text-slate-700">{{ $class->latest_attendance?->date?->translatedFormat('d M Y') ?? '-' }}</span>
                                </div>
                                @if($class->latest_attendance)
                                    <a href="{{ route('attendance.show', $class->latest_attendance) }}" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-brand-700">
                                        <i class="bi bi-eye-fill"></i> Detail
                                    </a>
                                @else
                                    <span class="inline-flex items-center rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-400">Belum ada detail</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="rounded-2xl bg-white px-5 py-10 text-center text-slate-400 shadow-sm ring-1 ring-slate-200/60 lg:col-span-2 xl:col-span-3">
                            <i class="bi bi-clipboard-x text-4xl text-slate-300"></i>
                            <h5 class="mt-2 font-bold text-slate-800">Data kelas belum tersedia</h5>
                        </div>
                    @endforelse
                </div>
            @else
                <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-600">
                            <thead>
                                <tr class="border-b border-slate-100 bg-slate-50/80 text-xs font-bold uppercase tracking-wider text-slate-500">
                                    <th class="px-5 py-3">Kelas</th>
                                    <th class="px-5 py-3">Wali Kelas</th>
                                    <th class="px-5 py-3 text-center">Siswa</th>
                                    <th class="px-5 py-3 text-center">Sesi</th>
                                    <th class="px-5 py-3 text-center">Kehadiran</th>
                                    <th class="px-5 py-3 text-center">Status</th>
                                    <th class="px-5 py-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($classSummaries as $class)
                                    @php $summary = $class->attendance_summary; @endphp
                                    <tr class="transition hover:bg-slate-50/30">
                                        <td class="px-5 py-4 font-bold text-slate-900">{{ $class->name }}</td>
                                        <td class="px-5 py-4">{{ $class->homeroomTeacher?->name ?? '-' }}</td>
                                        <td class="px-5 py-4 text-center font-mono font-bold">{{ $summary['students'] }}</td>
                                        <td class="px-5 py-4 text-center font-mono font-bold">{{ $summary['sessions'] }}</td>
                                        <td class="px-5 py-4 text-center">
                                            <span class="inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">{{ $summary['percentage'] }}%</span>
                                        </td>
                                        <td class="px-5 py-4">
                                            <div class="flex justify-center gap-2">
                                                <span class="rounded bg-emerald-50 px-2 py-0.5 text-xs font-bold text-emerald-700">H {{ $summary['hadir'] + $summary['terlambat'] }}</span>
                                                <span class="rounded bg-sky-50 px-2 py-0.5 text-xs font-bold text-sky-700">I/S {{ $summary['izin_sakit'] }}</span>
                                                <span class="rounded bg-rose-50 px-2 py-0.5 text-xs font-bold text-rose-700">A {{ $summary['alfa'] }}</span>
                                            </div>
                                        </td>
                                        <td class="px-5 py-4 text-right">
                                            @if($class->latest_attendance)
                                                <a href="{{ route('attendance.show', $class->latest_attendance) }}" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 shadow-sm transition hover:bg-brand-50 hover:text-brand-600">
                                                    <i class="bi bi-eye-fill"></i> Detail
                                                </a>
                                            @else
                                                <span class="text-xs text-slate-400">Belum ada detail</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="px-5 py-8 text-center text-slate-400">Data kelas belum tersedia.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        @else
            <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50/80 text-xs font-bold uppercase tracking-wider text-slate-500">
                                <th class="px-5 py-3">Tanggal</th>
                                <th class="px-5 py-3">Kelas</th>
                                <th class="px-5 py-3">Sesi</th>
                                <th class="px-5 py-3">Guru / Pengajar</th>
                                <th class="px-5 py-3 text-center">Statistik Kehadiran</th>
                                <th class="px-5 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($attendances as $att)
                                <tr class="transition hover:bg-slate-50/30">
                                    <td class="px-5 py-4">
                                        <div class="font-semibold text-slate-900">{{ $att->date->translatedFormat('d F Y') }}</div>
                                        <div class="text-xs text-slate-500">{{ $att->date->translatedFormat('l') }}</div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex items-center rounded-full bg-brand-50 px-2.5 py-1 text-[11px] font-bold text-brand-700">
                                            {{ $att->class?->name }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4">
                                        @if($att->session === 'pagi')
                                            <span class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-1 text-[11px] font-bold text-amber-700">PAGI</span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-sky-50 px-2.5 py-1 text-[11px] font-bold text-sky-700">SIANG</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="font-semibold text-slate-900">{{ $att->teacher?->name ?? 'System' }}</div>
                                        <div class="text-xs text-slate-500">NIP: {{ $att->teacher?->nip ?? '-' }}</div>
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        <div class="flex justify-center gap-3">
                                            <div class="text-center">
                                                <div class="text-[9px] font-bold uppercase tracking-wider text-slate-400 mb-0.5">Hadir</div>
                                                <span class="inline-flex items-center rounded bg-emerald-50 px-2 py-0.5 text-xs font-bold text-emerald-700">
                                                    {{ $att->details->whereIn('status', ['hadir', 'terlambat'])->count() }}
                                                </span>
                                            </div>
                                            <div class="text-center">
                                                <div class="text-[9px] font-bold uppercase tracking-wider text-slate-400 mb-0.5">Izin/Sakit</div>
                                                <span class="inline-flex items-center rounded bg-sky-50 px-2 py-0.5 text-xs font-bold text-sky-700">
                                                    {{ $att->details->whereIn('status', ['izin', 'sakit'])->count() }}
                                                </span>
                                            </div>
                                            <div class="text-center">
                                                <div class="text-[9px] font-bold uppercase tracking-wider text-slate-400 mb-0.5">Alfa</div>
                                                <span class="inline-flex items-center rounded bg-rose-50 px-2 py-0.5 text-xs font-bold text-rose-700">
                                                    {{ $att->details->where('status', 'alfa')->count() }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-right">
                                        <div class="inline-flex items-center divide-x divide-slate-200 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                                            <a href="{{ route('attendance.show', $att) }}" class="px-2.5 py-1.5 text-slate-500 transition hover:bg-brand-50 hover:text-brand-600" title="Detail">
                                                <i class="bi bi-eye-fill"></i>
                                            </a>
                                            @role('admin,guru')
                                            <a href="{{ route('attendance.edit', $att) }}" class="px-2.5 py-1.5 text-slate-500 transition hover:bg-brand-50 hover:text-brand-600" title="Edit">
                                                <i class="bi bi-pencil-fill"></i>
                                            </a>
                                            @endrole
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-8 text-center text-slate-400">
                                        <div class="py-4 text-center">
                                            <i class="bi bi-clipboard-x text-4xl text-slate-300 d-block mb-2"></i>
                                            <h5 class="font-bold text-slate-800">Data absensi belum tersedia</h5>
                                            <p class="text-xs text-slate-500">Belum ada rekaman absensi untuk filter yang dipilih.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    @endif

    {{-- TAB 2: PRESENSI GURU (MASUK & PULANG) --}}
    @if($activeTab === 'teachers')
        {{-- Ringkasan Statistik Presensi Guru --}}
        <div class="mb-6 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
            <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-200/60 text-center">
                <p class="text-xs font-bold uppercase text-slate-400">Total Guru</p>
                <p class="mt-1 font-mono text-2xl font-extrabold text-slate-900">{{ $teacherSummary['total'] }}</p>
            </div>
            <div class="rounded-xl bg-emerald-50 p-4 shadow-sm ring-1 ring-emerald-200/60 text-center">
                <p class="text-xs font-bold uppercase text-emerald-700">Hadir</p>
                <p class="mt-1 font-mono text-2xl font-extrabold text-emerald-800">{{ $teacherSummary['hadir'] }}</p>
            </div>
            <div class="rounded-xl bg-amber-50 p-4 shadow-sm ring-1 ring-amber-200/60 text-center">
                <p class="text-xs font-bold uppercase text-amber-700">Terlambat</p>
                <p class="mt-1 font-mono text-2xl font-extrabold text-amber-800">{{ $teacherSummary['terlambat'] }}</p>
            </div>
            <div class="rounded-xl bg-sky-50 p-4 shadow-sm ring-1 ring-sky-200/60 text-center">
                <p class="text-xs font-bold uppercase text-sky-700">Izin / Sakit</p>
                <p class="mt-1 font-mono text-2xl font-extrabold text-sky-800">{{ $teacherSummary['izin_sakit'] }}</p>
            </div>
            <div class="rounded-xl bg-rose-50 p-4 shadow-sm ring-1 ring-rose-200/60 text-center">
                <p class="text-xs font-bold uppercase text-rose-700">Belum Absen</p>
                <p class="mt-1 font-mono text-2xl font-extrabold text-rose-800">{{ $teacherSummary['total'] - ($teacherSummary['hadir'] + $teacherSummary['terlambat'] + $teacherSummary['izin_sakit']) }}</p>
            </div>
            <div class="rounded-xl bg-indigo-50 p-4 shadow-sm ring-1 ring-indigo-200/60 text-center">
                <p class="text-xs font-bold uppercase text-indigo-700">Absen Pulang</p>
                <p class="mt-1 font-mono text-2xl font-extrabold text-indigo-800">{{ $teacherSummary['checked_out'] }}</p>
            </div>
        </div>

        {{-- Tabel Absensi Seluruh Guru --}}
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60">
            <div class="border-b border-slate-100 px-6 py-4 flex items-center justify-between">
                <h5 class="text-base font-bold text-slate-900">
                    <i class="bi bi-person-badge-fill text-brand-600 me-2"></i>Daftar Presensi Guru ({{ $selectedDateLabel }})
                </h5>
                <span class="text-xs text-slate-500">Menampilkan {{ $teachers->count() }} Guru</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/80 text-xs font-bold uppercase tracking-wider text-slate-500">
                            <th class="px-5 py-3">Guru & NIP</th>
                            <th class="px-5 py-3">Mata Pelajaran</th>
                            @if($isSingleDay)
                                <th class="px-5 py-3 text-center">Status</th>
                                <th class="px-5 py-3">Absen Masuk (Waktu & Foto)</th>
                                <th class="px-5 py-3">Absen Pulang (Waktu & Foto)</th>
                            @else
                                <th class="px-5 py-3 text-center">Rekap Kehadiran (Periode Ini)</th>
                                <th class="px-5 py-3 text-center">Aksi & Log Foto</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($teachers as $teacher)
                            @php
                                $tLogs = $teacherLogsGrouped->get($teacher->id, collect());
                                $att = $tLogs->first();
                            @endphp
                            <tr class="transition hover:bg-slate-50/30">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $teacher->photo_url }}" class="h-10 w-10 rounded-full object-cover shadow-sm ring-1 ring-slate-200" alt="">
                                        <div>
                                            <div class="font-bold text-slate-900">{{ $teacher->name }}</div>
                                            <div class="text-xs text-slate-500">NIP: {{ $teacher->nip ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-xs font-medium text-slate-700">
                                    {{ $teacher->subject ?? '-' }}
                                </td>

                                @if($isSingleDay)
                                    <td class="px-5 py-4 text-center">
                                        @if($att)
                                            <span class="inline-flex items-center rounded-full bg-{{ $att->status_badge }}-100 px-3 py-1 text-xs font-bold text-{{ $att->status_badge }}-800 border border-{{ $att->status_badge }}-200">
                                                {{ $att->status_label }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-500">
                                                Belum Absen
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4">
                                        @if($att && $att->check_in_at)
                                            <div class="flex items-center gap-3">
                                                @if($att->check_in_photo)
                                                    <button type="button" onclick="showPhotoModal('Foto Absen Masuk - {{ addslashes($teacher->name) }}', '{{ asset('storage/' . $att->check_in_photo) }}', '{{ $att->check_in_at->format('d M Y, H:i:s') }} WIB', 'Lokasi: {{ $att->check_in_lat ?? '-' }}, {{ $att->check_in_lng ?? '-' }} (Jarak {{ round($att->check_in_distance_meters ?? 0) }}m)')" class="group relative overflow-hidden rounded-lg shadow-sm border border-slate-200">
                                                        <img src="{{ asset('storage/' . $att->check_in_photo) }}" class="h-11 w-11 object-cover transition group-hover:scale-105" alt="Foto Masuk">
                                                        <span class="absolute inset-0 flex items-center justify-center bg-slate-900/40 opacity-0 group-hover:opacity-100 transition text-white text-xs">
                                                            <i class="bi bi-eye"></i>
                                                        </span>
                                                    </button>
                                                @endif
                                                <div>
                                                    <p class="font-extrabold text-slate-900 text-xs">{{ $att->check_in_at->format('H:i:s') }} WIB</p>
                                                    <p class="text-[10px] text-slate-400">Jarak: {{ round($att->check_in_distance_meters ?? 0) }} m</p>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-xs text-slate-400 italic">Belum Masuk</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4">
                                        @if($att && $att->check_out_at)
                                            <div class="flex items-center gap-3">
                                                @if($att->check_out_photo)
                                                    <button type="button" onclick="showPhotoModal('Foto Absen Pulang - {{ addslashes($teacher->name) }}', '{{ asset('storage/' . $att->check_out_photo) }}', '{{ $att->check_out_at->format('d M Y, H:i:s') }} WIB', 'Lokasi: {{ $att->check_out_lat ?? '-' }}, {{ $att->check_out_lng ?? '-' }} (Jarak {{ round($att->check_out_distance_meters ?? 0) }}m)')" class="group relative overflow-hidden rounded-lg shadow-sm border border-slate-200">
                                                        <img src="{{ asset('storage/' . $att->check_out_photo) }}" class="h-11 w-11 object-cover transition group-hover:scale-105" alt="Foto Pulang">
                                                        <span class="absolute inset-0 flex items-center justify-center bg-slate-900/40 opacity-0 group-hover:opacity-100 transition text-white text-xs">
                                                            <i class="bi bi-eye"></i>
                                                        </span>
                                                    </button>
                                                @endif
                                                <div>
                                                    <p class="font-extrabold text-slate-900 text-xs">{{ $att->check_out_at->format('H:i:s') }} WIB</p>
                                                    <p class="text-[10px] text-slate-400">Jarak: {{ round($att->check_out_distance_meters ?? 0) }} m</p>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-xs text-slate-400 italic">Belum Pulang</span>
                                        @endif
                                    </td>
                                @else
                                    <td class="px-5 py-4 text-center">
                                        <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                            <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-bold text-emerald-800">Hadir: {{ $tLogs->where('status', 'hadir')->count() }}</span>
                                            <span class="rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-bold text-amber-800">Terlambat: {{ $tLogs->where('status', 'terlambat')->count() }}</span>
                                            <span class="rounded-full bg-sky-100 px-2.5 py-0.5 text-xs font-bold text-sky-800">Izin/Sakit: {{ $tLogs->whereIn('status', ['izin', 'sakit'])->count() }}</span>
                                            <span class="rounded-full bg-rose-100 px-2.5 py-0.5 text-xs font-bold text-rose-800">Alfa: {{ $tLogs->where('status', 'alfa')->count() }}</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        <button type="button" onclick="openTeacherLogsModal('{{ addslashes($teacher->name) }}', {{ json_encode($tLogs->map(function($l) { return [ 'date' => $l->date->translatedFormat('d M Y'), 'status_label' => $l->status_label, 'status_badge' => $l->status_badge, 'check_in_at' => $l->check_in_at ? $l->check_in_at->format('H:i:s') : null, 'check_in_photo' => $l->check_in_photo ? asset('storage/' . $l->check_in_photo) : null, 'check_out_at' => $l->check_out_at ? $l->check_out_at->format('H:i:s') : null, 'check_out_photo' => $l->check_out_photo ? asset('storage/' . $l->check_out_photo) : null, 'check_in_dist' => round($l->check_in_distance_meters ?? 0), 'check_out_dist' => round($l->check_out_distance_meters ?? 0) ]; })->values()) }})" class="inline-flex items-center gap-1.5 rounded-lg border border-brand-200 bg-brand-50 px-3 py-1.5 text-xs font-bold text-brand-700 hover:bg-brand-100 transition shadow-sm">
                                            <i class="bi bi-images text-sm"></i> Detail Log Foto ({{ $tLogs->count() }})
                                        </button>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-8 text-center text-slate-400">Data guru belum tersedia.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- MODAL LIST LOG FOTO ABSENSI GURU (UNTUK PERIODE DATE RANGE) --}}
    <div id="teacherLogsModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/80 backdrop-blur-sm p-4 flex items-center justify-center">
        <div class="w-full max-w-2xl overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-slate-200 max-h-[85vh] flex flex-col">
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                <h5 id="tlmTitle" class="text-base font-bold text-slate-900">Riwayat Presensi Guru</h5>
                <button type="button" onclick="closeTeacherLogsModal()" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div id="tlmBody" class="p-6 overflow-y-auto space-y-4">
                {{-- Dynamic contents --}}
            </div>
        </div>
    </div>

    {{-- MODAL VIEWER FOTO BUKTI ABSEN (ADMIN/KEPALA SEKOLAH) --}}
    <div id="photoViewerModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/80 backdrop-blur-sm p-4 flex items-center justify-center">
        <div class="w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-slate-200">
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                <h5 id="pvTitle" class="text-sm font-bold text-slate-900">Bukti Foto Absen</h5>
                <button type="button" onclick="closePhotoModal()" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="p-6 text-center">
                <img id="pvImage" src="" class="mx-auto mb-4 max-h-80 rounded-xl object-cover shadow-md border border-slate-200" alt="Foto Absen">
                <p id="pvTime" class="text-sm font-bold text-slate-900 mb-1"></p>
                <p id="pvLocation" class="text-xs text-slate-500"></p>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function openTeacherLogsModal(teacherName, logs) {
            document.getElementById('tlmTitle').textContent = 'Riwayat Presensi: ' + teacherName;
            const container = document.getElementById('tlmBody');
            container.innerHTML = '';

            if (!logs || logs.length === 0) {
                container.innerHTML = '<p class="text-center text-sm text-slate-400 py-6">Tidak ada log presensi pada rentang tanggal ini.</p>';
            } else {
                logs.forEach(log => {
                    const card = document.createElement('div');
                    card.className = 'rounded-xl border border-slate-200 p-4 bg-slate-50/50 space-y-3';
                    card.innerHTML = `
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-extrabold text-slate-900"><i class="bi bi-calendar3 me-1.5 text-brand-600"></i> ${log.date}</span>
                            <span class="rounded-full bg-${log.status_badge}-100 px-2.5 py-0.5 text-xs font-bold text-${log.status_badge}-800">${log.status_label}</span>
                        </div>
                        <div class="grid grid-cols-2 gap-3 pt-1">
                            <div class="rounded-lg bg-white p-2.5 border border-slate-200 flex items-center gap-3">
                                ${log.check_in_photo ? `<button type="button" onclick="showPhotoModal('Foto Absen Masuk - ${teacherName} (${log.date})', '${log.check_in_photo}', '${log.date} - ${log.check_in_at} WIB', 'Jarak: ${log.check_in_dist}m')" class="group relative overflow-hidden rounded-lg shadow-sm"><img src="${log.check_in_photo}" class="h-12 w-12 object-cover"></button>` : '<div class="h-12 w-12 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400 text-xs"><i class="bi bi-camera"></i></div>'}
                                <div>
                                    <p class="text-[10px] text-slate-400 uppercase font-bold">Absen Masuk</p>
                                    <p class="text-xs font-extrabold text-slate-800">${log.check_in_at ? log.check_in_at + ' WIB' : 'Belum Absen'}</p>
                                </div>
                            </div>
                            <div class="rounded-lg bg-white p-2.5 border border-slate-200 flex items-center gap-3">
                                ${log.check_out_photo ? `<button type="button" onclick="showPhotoModal('Foto Absen Pulang - ${teacherName} (${log.date})', '${log.check_out_photo}', '${log.date} - ${log.check_out_at} WIB', 'Jarak: ${log.check_out_dist}m')" class="group relative overflow-hidden rounded-lg shadow-sm"><img src="${log.check_out_photo}" class="h-12 w-12 object-cover"></button>` : '<div class="h-12 w-12 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400 text-xs"><i class="bi bi-camera"></i></div>'}
                                <div>
                                    <p class="text-[10px] text-slate-400 uppercase font-bold">Absen Pulang</p>
                                    <p class="text-xs font-extrabold text-slate-800">${log.check_out_at ? log.check_out_at + ' WIB' : 'Belum Absen'}</p>
                                </div>
                            </div>
                        </div>
                    `;
                    container.appendChild(card);
                });
            }

            document.getElementById('teacherLogsModal').classList.remove('hidden');
        }

        function closeTeacherLogsModal() {
            document.getElementById('teacherLogsModal').classList.add('hidden');
        }

        function showPhotoModal(title, photoUrl, timeStr, locationStr) {
            document.getElementById('pvTitle').textContent = title;
            document.getElementById('pvImage').src = photoUrl;
            document.getElementById('pvTime').textContent = timeStr;
            document.getElementById('pvLocation').textContent = locationStr;
            document.getElementById('photoViewerModal').classList.remove('hidden');
        }

        function exportExcelSiswa() {
            const form = document.getElementById('attendanceFilterForm');
            const formData = new FormData(form);
            const params = new URLSearchParams(formData).toString();
            window.open('{{ route("attendance.export.students.excel") }}' + '?' + params, '_blank');
        }

        function exportExcelGuru() {
            const form = document.getElementById('attendanceFilterForm');
            const formData = new FormData(form);
            const params = new URLSearchParams(formData).toString();
            window.open('{{ route("attendance.export.teachers.excel") }}' + '?' + params, '_blank');
        }
    </script>
    @endpush
@endsection
