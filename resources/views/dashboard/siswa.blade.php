@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div id="dashboard-root" class="space-y-6">
    @include('dashboard.partials.hero', [
        'title' => 'Dashboard Siswa',
        'subtitle' => 'Pantau jadwal, materi, tugas, kuis, nilai, dan kehadiran Anda di ' . config('school.name') . '.',
    ])

    {{-- Quick actions --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <a href="{{ route('attendance.self-checkin') }}" class="group flex flex-col rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200/70 transition hover:shadow-md hover:ring-brand-200">
            <div class="mb-3 w-fit rounded-xl bg-brand-50 p-2.5 text-brand-600 ring-1 ring-brand-100/50">
                <i class="bi bi-camera-video text-xl"></i>
            </div>
            <p class="font-bold text-slate-900 group-hover:text-brand-700 transition-colors">Presensi Siswa (Foto & GPS)</p>
            <p class="mt-1 text-xs text-slate-500">Ambil foto selfie cepat dan catat lokasi GPS sekolah</p>
        </a>
        <a href="{{ route('attendance.mine') }}" class="group flex flex-col rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200/70 transition hover:shadow-md hover:ring-brand-200">
            <div class="mb-3 w-fit rounded-xl bg-brand-50 p-2.5 text-brand-600 ring-1 ring-brand-100/50">
                <i class="bi bi-calendar-check text-xl"></i>
            </div>
            <p class="font-bold text-slate-900 group-hover:text-brand-700 transition-colors">Riwayat Absensi</p>
            <p class="mt-1 text-xs text-slate-500">Lihat rekap kehadiran</p>
        </a>
    </div>

    {{-- Kehadiran --}}
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        @include('dashboard.partials.stat-card', ['label' => 'Hadir', 'value' => $attendance_stats['hadir'], 'icon' => 'bi-check-circle', 'tone' => 'brand', 'hint' => 'Bulan ini'])
        @include('dashboard.partials.stat-card', ['label' => 'Terlambat', 'value' => $attendance_stats['terlambat'], 'icon' => 'bi-clock', 'tone' => 'amber'])
        @include('dashboard.partials.stat-card', ['label' => 'Alfa', 'value' => $attendance_stats['alfa'], 'icon' => 'bi-x-circle', 'tone' => 'rose'])
        @include('dashboard.partials.stat-card', ['label' => 'Kehadiran', 'value' => $attendance_stats['percentage'] . '%', 'icon' => 'bi-percent', 'tone' => $attendance_stats['percentage'] >= 75 ? 'brand' : 'rose', 'hint' => 'Bulan ini'])
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        {{-- Jadwal --}}
        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200/70 xl:col-span-2">
            <h2 class="mb-4 text-base font-bold text-slate-900"><i class="bi bi-calendar-event mr-2 text-brand-600"></i>Jadwal Hari Ini</h2>
            @if($today_schedules->isNotEmpty())
            <div class="grid gap-3 sm:grid-cols-2">
                @foreach($today_schedules as $jadwal)
                <div class="flex gap-3 rounded-xl border border-slate-100 bg-slate-50/80 p-4 transition hover:bg-white hover:shadow-sm">
                    <div class="flex h-12 w-12 shrink-0 flex-col items-center justify-center rounded-xl bg-brand-600 text-white">
                        <span class="text-[10px] font-bold uppercase">{{ \Carbon\Carbon::parse($jadwal->start_time)->format('H:i') }}</span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-start justify-between gap-2">
                            <p class="font-bold text-slate-900 truncate">
                                @if($jadwal->class_subject_id)
                                    <a href="{{ route('student.class-subjects.show', $jadwal->class_subject_id) }}" class="hover:text-brand-600">
                                        {{ $jadwal->subject }}
                                    </a>
                                @else
                                    {{ $jadwal->subject }}
                                @endif
                            </p>
                            @if(in_array($jadwal->today_attendance_status ?? '', ['hadir', 'terlambat'], true))
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-800 font-extrabold text-[10px] shrink-0">
                                    <i class="bi bi-check-circle-fill"></i> Hadir
                                </span>
                            @elseif(($jadwal->today_attendance_status ?? '') === 'alfa')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-rose-100 text-rose-800 font-extrabold text-[10px] shrink-0">
                                    <i class="bi bi-x-circle-fill"></i> Tidak Hadir
                                </span>
                            @elseif(($jadwal->today_attendance_status ?? '') === 'sakit')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-amber-100 text-amber-800 font-extrabold text-[10px] shrink-0">
                                    <i class="bi bi-bandaid-fill"></i> Sakit
                                </span>
                            @elseif(($jadwal->today_attendance_status ?? '') === 'izin')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-sky-100 text-sky-800 font-extrabold text-[10px] shrink-0">
                                    <i class="bi bi-file-earmark-text-fill"></i> Izin
                                </span>
                            @endif
                        </div>
                        <p class="text-sm text-slate-500">{{ $jadwal->teacher?->name ?? 'Guru' }}</p>
                        <p class="mt-1 text-xs text-slate-400">{{ \Carbon\Carbon::parse($jadwal->end_time)->format('H:i') }} @if($jadwal->room)· {{ $jadwal->room }}@endif</p>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <p class="py-10 text-center text-sm text-slate-500">Tidak ada jadwal hari ini. Selamat beristirahat!</p>
            @endif
        </div>

        {{-- Pengumuman --}}
        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200/70">
            <h2 class="mb-4 text-base font-bold text-slate-900"><i class="bi bi-megaphone mr-2 text-brand-600"></i>Pengumuman</h2>
            <div class="space-y-3">
                @forelse($announcements as $item)
                <div class="rounded-xl bg-slate-50 p-3">
                    <p class="text-sm font-semibold text-slate-900">{{ $item->title }}</p>
                    <p class="mt-1 line-clamp-2 text-xs text-slate-600">{{ Str::limit(strip_tags($item->content), 80) }}</p>
                    <p class="mt-2 text-[10px] text-slate-400">{{ $item->created_at->diffForHumans() }}</p>
                </div>
                @empty
                <p class="py-6 text-center text-sm text-slate-500">Tidak ada pengumuman.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        {{-- Materi terbaru --}}
        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200/70">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-base font-bold text-slate-900"><i class="bi bi-journal-text mr-2 text-brand-600"></i>Materi Terbaru</h2>
                <a href="{{ route('materials.mine') }}" class="text-xs font-semibold text-brand-600">Semua →</a>
            </div>
            <div class="space-y-3">
                @forelse($recent_materials as $mat)
                <div class="flex items-center gap-3 rounded-xl bg-slate-50 px-4 py-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-brand-100 text-brand-700"><i class="bi {{ $mat->type_icon }}"></i></div>
                    <div>
                        <p class="font-semibold text-slate-900">{{ $mat->title }}</p>
                        <p class="text-xs text-slate-500">{{ $mat->classSubject?->subject?->name }}</p>
                    </div>
                </div>
                @empty
                <p class="py-6 text-center text-sm text-slate-500">Belum ada materi baru.</p>
                @endforelse
            </div>
        </div>

        {{-- Tugas --}}
        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200/70">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-base font-bold text-slate-900"><i class="bi bi-journal-check mr-2 text-amber-500"></i>Tugas</h2>
                <a href="{{ route('student.assignments.index') }}" class="text-xs font-semibold text-brand-600">Semua →</a>
            </div>
            <div class="space-y-3">
                @forelse($active_assignments as $task)
                <a href="{{ route('student.assignments.show', $task) }}" class="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3 hover:bg-amber-50">
                    <div>
                        <p class="font-semibold text-slate-900">{{ $task->title }}</p>
                        <p class="text-xs text-slate-500">{{ $task->classSubject?->subject?->name }}</p>
                    </div>
                    <span class="text-xs font-medium text-amber-600">Deadline {{ $task->due_date?->format('d M') ?? '—' }}</span>
                </a>
                @empty
                <p class="py-6 text-center text-sm text-slate-500">Tidak ada tugas aktif.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
