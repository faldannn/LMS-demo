@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard Guru')

@section('content')
<div id="dashboard-root" class="space-y-6">
    @include('dashboard.partials.hero', [
        'title' => 'Dashboard Guru',
        'subtitle' => 'Kelola pembelajaran, pantau jadwal, tugas, materi, dan kehadiran kelas Anda.',
    ])

    {{-- AKSI CEPAT GURU --}}
    <div class="glass-panel p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="space-y-1">
                <div class="inline-flex items-center gap-2 rounded-full bg-indigo-500/15 border border-indigo-500/25 px-3 py-1 text-xs font-extrabold text-indigo-700">
                    <i class="bi bi-people-fill"></i> ABSENSI SISWA
                </div>
                <h3 class="text-lg font-black text-slate-900 mb-0 flex items-center gap-2 tracking-tight">
                    <i class="bi bi-clipboard-check text-indigo-600"></i> Presensi Kehadiran Siswa
                </h3>
                <p class="text-xs text-slate-500 max-w-xl mb-0 font-medium">Input dan pantau kehadiran siswa di kelas Anda secara langsung.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                <a href="{{ route('attendance.create') }}" class="inline-flex items-center gap-2.5 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-5 py-3 text-sm font-extrabold shadow-md shadow-blue-500/25 transition transform active:scale-95">
                    <i class="bi bi-pencil-square text-lg"></i>
                    <span>Input Absensi Siswa</span>
                </a>
                @if(auth()->user()->teacher && auth()->user()->teacher->homeroomClasses()->exists())
                <a href="{{ route('attendance.homeroom-create') }}" class="inline-flex items-center gap-2.5 rounded-2xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white px-5 py-3 text-sm font-extrabold shadow-md shadow-amber-500/25 transition transform active:scale-95">
                    <i class="bi bi-star-fill text-lg"></i>
                    <span>Presensi Wali Kelas</span>
                </a>
                @endif
            </div>
        </div>
    </div>

    {{-- Statistik --}}
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-3 xl:grid-cols-6">
        @include('dashboard.partials.stat-card', ['label' => 'Kelas', 'value' => $stats['classes'], 'icon' => 'bi-building', 'tone' => 'brand'])
        @include('dashboard.partials.stat-card', ['label' => 'Siswa', 'value' => number_format($stats['students']), 'icon' => 'bi-people', 'tone' => 'blue'])
        @include('dashboard.partials.stat-card', ['label' => 'Kehadiran', 'value' => ($stats['attendance_percentage'] ?? 0) . '%', 'icon' => 'bi-pie-chart', 'tone' => ($stats['attendance_percentage'] ?? 0) >= 75 ? 'brand' : 'amber', 'hint' => 'Hari ini'])
        @include('dashboard.partials.stat-card', ['label' => 'Tugas Aktif', 'value' => $stats['assignments'], 'icon' => 'bi-journal-check', 'tone' => 'amber'])
        @include('dashboard.partials.stat-card', ['label' => 'Materi', 'value' => $stats['materials'], 'icon' => 'bi-file-earmark-text', 'tone' => 'slate'])
        @include('dashboard.partials.stat-card', ['label' => 'Perlu Dinilai', 'value' => $stats['pending_grading'], 'icon' => 'bi-pencil-square', 'tone' => 'rose', 'hint' => 'Tugas siswa'])
    </div>

    @if(!empty($homeroom_attendance_today))
    {{-- WIDGET KHUSUS WALI KELAS --}}
    <div class="rounded-2xl bg-gradient-to-r from-amber-500/10 via-amber-500/5 to-white p-5 shadow-sm ring-1 ring-amber-200 border-l-4 border-amber-500">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="space-y-1">
                <div class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 text-amber-900 px-2.5 py-0.5 text-[11px] font-extrabold">
                    <i class="bi bi-star-fill text-amber-500"></i> KELAS BINAAN WALI KELAS
                </div>
                <h3 class="text-base font-bold text-slate-900 mb-0 flex items-center gap-2">
                    Kehadiran Kelas {{ $homeroom_attendance_today['class']?->name }} Hari Ini
                </h3>
                <p class="text-xs text-slate-600 mb-0">
                    Total Siswa: <strong class="text-slate-900">{{ $homeroom_attendance_today['total'] }}</strong> · 
                    Hadir: <strong class="text-emerald-700">{{ $homeroom_attendance_today['present_total'] }}</strong> · 
                    Sakit: <strong class="text-amber-700">{{ $homeroom_attendance_today['sakit'] }}</strong> · 
                    Izin: <strong class="text-sky-700">{{ $homeroom_attendance_today['izin'] }}</strong> · 
                    Alfa: <strong class="text-rose-700">{{ $homeroom_attendance_today['alfa'] }}</strong>
                </p>
            </div>
            <div class="flex items-center gap-4 shrink-0">
                <div class="text-right">
                    <span class="text-2xl font-black {{ $homeroom_attendance_today['percentage'] >= 75 ? 'text-emerald-600' : ($homeroom_attendance_today['percentage'] > 0 ? 'text-amber-600' : 'text-slate-400') }}">
                        {{ $homeroom_attendance_today['percentage'] }}%
                    </span>
                    <p class="text-[10px] text-slate-500 mb-0 font-medium">Tingkat Kehadiran</p>
                </div>
                <a href="{{ route('attendance.homeroom-create', ['class_id' => $homeroom_attendance_today['class']?->id]) }}" class="inline-flex items-center gap-1.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white px-4 py-2.5 text-xs font-bold shadow-sm transition">
                    <i class="bi bi-pencil-square"></i>
                    <span>{{ $homeroom_attendance_today['has_recorded'] ? 'Update Absensi' : 'Input Absensi' }}</span>
                </a>
            </div>
        </div>
    </div>
    @endif

  <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        {{-- Jadwal hari ini --}}
        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200/70 xl:col-span-2">
            <h2 class="mb-4 text-base font-bold text-slate-900"><i class="bi bi-calendar-event mr-2 text-brand-600"></i>Jadwal Hari Ini</h2>
            @if($today_schedules->isNotEmpty())
            <div class="grid gap-3 sm:grid-cols-2">
                @foreach($today_schedules as $jadwal)
                <div class="rounded-xl border border-slate-100 bg-gradient-to-br from-white to-slate-50 p-4">
                    <div class="flex items-center justify-between gap-2">
                        <span class="rounded-full bg-brand-100 px-3 py-1 text-xs font-bold text-brand-700">
                            {{ \Carbon\Carbon::parse($jadwal->start_time)->format('H:i') }}–{{ \Carbon\Carbon::parse($jadwal->end_time)->format('H:i') }}
                        </span>
                        @if($jadwal->room)<span class="text-xs text-slate-500"><i class="bi bi-geo-alt"></i> {{ $jadwal->room }}</span>@endif
                    </div>
                    <div class="mt-3 flex items-center justify-between pt-2 border-t border-slate-100">
                        <div>
                            <p class="font-bold text-slate-900 text-sm mb-0">{{ $jadwal->subject }}</p>
                            <p class="text-xs text-slate-500 mb-0">Kelas {{ $jadwal->class?->name }}</p>
                        </div>
                        <a href="{{ route('attendance.create', ['class_id' => $jadwal->class_id]) }}" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-50 border border-brand-200 px-3 py-1.5 text-xs font-bold text-brand-700 hover:bg-brand-100 transition shadow-2xs">
                            <i class="bi bi-pencil-square"></i> Input Absen
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <p class="py-10 text-center text-sm text-slate-500">Tidak ada jadwal hari ini.</p>
            @endif
        </div>

        {{-- Grafik kehadiran --}}
        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200/70">
            <h2 class="mb-4 text-base font-bold text-slate-900"><i class="bi bi-graph-up mr-2 text-brand-600"></i>Tren Kehadiran</h2>
            <div class="h-56"><canvas id="guruAttendanceChart"></canvas></div>
        </div>
    </div>

    {{-- Kelas & mapel --}}
    <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200/70">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-base font-bold text-slate-900"><i class="bi bi-collection mr-2 text-brand-600"></i>Kelas & Mata Pelajaran</h2>
            <a href="{{ route('elearning.index') }}" class="text-xs font-semibold text-brand-600">Portal E-Learning →</a>
        </div>
        @if($class_subjects->isNotEmpty())
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach($class_subjects as $cs)
            <div class="rounded-xl border border-slate-100 p-4 transition hover:border-brand-200 hover:shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="rounded-full bg-brand-50 px-2.5 py-1 text-xs font-bold text-brand-700">{{ $cs->class?->name }}</span>
                    <span class="text-xs text-slate-400">Tingkat {{ $cs->class?->grade }}</span>
                </div>
                <p class="mt-3 font-bold text-slate-900">{{ $cs->subject?->name }}</p>
                <div class="mt-3 flex gap-2 text-xs text-slate-500">
                    <span>{{ $cs->materials_count }} materi</span>·<span>{{ $cs->assignments_count }} tugas</span>
                </div>
                <div class="mt-4 flex gap-2">
                    <a href="{{ route('materials.index', $cs->id) }}" class="flex-1 rounded-lg bg-slate-100 py-2 text-center text-xs font-semibold text-slate-700 hover:bg-brand-50 hover:text-brand-700">Materi</a>
                    <a href="{{ route('assignments.index', $cs->id) }}" class="flex-1 rounded-lg bg-slate-100 py-2 text-center text-xs font-semibold text-slate-700 hover:bg-brand-50 hover:text-brand-700">Tugas</a>
                    <a href="{{ route('attendance.create', ['class_id' => $cs->class_id]) }}" class="flex-1 rounded-lg bg-emerald-50 border border-emerald-200 py-2 text-center text-xs font-bold text-emerald-800 hover:bg-emerald-100 transition">
                        <i class="bi bi-pencil-square me-1"></i>Absensi
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <p class="py-8 text-center text-sm text-slate-500">Belum ada kelas/mapel yang diampu.</p>
        @endif
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        {{-- Tugas aktif --}}
        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200/70">
            <h2 class="mb-4 text-base font-bold text-slate-900"><i class="bi bi-journal-check mr-2 text-amber-500"></i>Tugas Aktif</h2>
            <div class="space-y-3">
                @forelse($active_assignments as $task)
                <a href="{{ route('assignments.show', $task) }}" class="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3 hover:bg-brand-50">
                    <div>
                        <p class="font-semibold text-slate-900">{{ $task->title }}</p>
                        <p class="text-xs text-slate-500">{{ $task->classSubject?->class?->name }} · {{ $task->classSubject?->subject?->name }}</p>
                    </div>
                    <span class="text-xs font-medium text-amber-600">{{ $task->due_date?->format('d M') ?? '—' }}</span>
                </a>
                @empty
                <p class="py-6 text-center text-sm text-slate-500">Tidak ada tugas aktif.</p>
                @endforelse
            </div>
        </div>

        {{-- Kehadiran kelas --}}
        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200/70">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-base font-bold text-slate-900 mb-0"><i class="bi bi-clipboard-check mr-2 text-blue-600"></i>Kehadiran Kelas Hari Ini</h2>
                <span class="text-xs text-slate-400 font-medium">{{ today()->translatedFormat('d M Y') }}</span>
            </div>
            <div class="space-y-3">
                @forelse($class_attendance as $row)
                @php
                    $pct = (int) ($row['percent'] ?? $row['percentage'] ?? 0);
                    $hadir = (int) ($row['hadir'] ?? 0);
                    $total = (int) ($row['total'] ?? 0);
                @endphp
                <div class="rounded-xl bg-slate-50 px-4 py-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-semibold text-slate-900 mb-0">{{ $row['class']?->name }}</p>
                            <p class="text-[11px] text-slate-500 mb-0">{{ $hadir }} dari {{ $total }} siswa hadir</p>
                        </div>
                        <span class="text-sm font-extrabold {{ $pct >= 75 ? 'text-emerald-600' : ($pct > 0 ? 'text-amber-600' : 'text-slate-400') }}">{{ $pct }}%</span>
                    </div>
                    <div class="mt-2 h-2 overflow-hidden rounded-full bg-slate-200">
                        <div class="h-full rounded-full {{ $pct >= 75 ? 'bg-emerald-500' : ($pct > 0 ? 'bg-amber-500' : 'bg-slate-300') }}" style="width: {{ min($pct, 100) }}%"></div>
                    </div>
                </div>
                @empty
                <p class="py-6 text-center text-sm text-slate-500">Belum ada data absensi hari ini.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Materi terbaru --}}
    <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200/70">
        <h2 class="mb-4 text-base font-bold text-slate-900"><i class="bi bi-file-earmark-richtext mr-2 text-brand-600"></i>Materi Terbaru</h2>
        <div class="space-y-3">
            @forelse($recent_materials as $mat)
            <div class="flex items-center gap-3 rounded-xl bg-slate-50 px-4 py-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-brand-100 text-brand-700"><i class="bi {{ $mat->type_icon }}"></i></div>
                <div class="min-w-0 flex-1">
                    <p class="truncate font-semibold text-slate-900">{{ $mat->title }}</p>
                    <p class="text-xs text-slate-500">{{ $mat->classSubject?->subject?->name }} · {{ $mat->created_at->diffForHumans() }}</p>
                </div>
            </div>
            @empty
            <p class="py-6 text-center text-sm text-slate-500">Belum ada materi.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('guruAttendanceChart'), {
    type: 'line',
    data: {
        labels: @json(collect($monthly_trend)->pluck('label')),
        datasets: [{
            label: 'Kehadiran %',
            data: @json(collect($monthly_trend)->pluck('percent')),
            borderColor: '#0058ff',
            backgroundColor: 'rgba(21,128,61,.12)',
            fill: true,
            tension: 0.35,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { y: { min: 0, max: 100 } }
    }
});
</script>
@endpush
