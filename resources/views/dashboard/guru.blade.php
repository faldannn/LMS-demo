@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard Guru')

@section('content')
<div id="dashboard-root" class="space-y-6">
    @include('dashboard.partials.hero', [
        'title' => 'Dashboard Guru',
        'subtitle' => 'Kelola pembelajaran, pantau jadwal, tugas, materi, dan kehadiran kelas Anda.',
    ])

    {{-- AKSI CEPAT PRESENSI GURU --}}
    <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200/70">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="space-y-1">
                <div class="inline-flex items-center gap-2 rounded-full bg-brand-50 border border-brand-200 px-3 py-1 text-xs font-extrabold text-brand-700">
                    <i class="bi bi-camera-fill"></i> PRESENSI MANDIRI GURU
                </div>
                <h3 class="text-lg font-bold text-slate-900 mb-0 flex items-center gap-2">
                    <i class="bi bi-person-bounding-box text-brand-600"></i> Absen Masuk / Pulang Mengajar (Absen Muka Guru)
                </h3>
                <p class="text-xs text-slate-500 max-w-xl mb-0">Lakukan presensi kehadiran Anda sebagai Guru hari ini menggunakan fitur Absen Muka / Swafoto.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                <a href="{{ route('attendance.teacher-checkin') }}" class="inline-flex items-center gap-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white px-5 py-3 text-sm font-extrabold shadow-sm transition transform active:scale-95">
                    <i class="bi bi-camera-fill text-lg"></i>
                    <span>ABSEN MUKA GURU SEKARANG</span>
                </a>
            </div>
        </div>

        {{-- RINGKASAN SUB-AKSI PRESENSI SISWA --}}
        <div class="mt-4 pt-3 border-t border-slate-100 flex flex-col gap-2.5 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
                <i class="bi bi-people text-brand-600"></i>
                <span>Presensi Kehadiran Siswa di Kelas:</span>
            </div>
            <div class="flex flex-wrap items-center gap-2 shrink-0">
                <a href="{{ route('attendance.create') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 hover:bg-brand-50 hover:text-brand-700 px-3 py-1.5 text-xs font-semibold text-slate-700 transition">
                    <i class="bi bi-pencil-square text-brand-600"></i> Input Absensi Siswa
                </a>
                <a href="{{ route('attendance.create', ['period' => '30_days']) }}" class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 hover:bg-brand-50 hover:text-brand-700 px-3 py-1.5 text-xs font-semibold text-slate-700 transition">
                    <i class="bi bi-calendar-range text-emerald-600"></i> Rekap Absensi Siswa
                </a>
                @if(auth()->user()->teacher && auth()->user()->teacher->homeroomClasses()->exists())
                <a href="{{ route('attendance.homeroom-create') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-amber-50 border border-amber-200 px-3 py-1.5 text-xs font-bold text-amber-800 hover:bg-amber-100 transition">
                    <i class="bi bi-star-fill text-amber-500"></i> Presensi Wali Kelas
                </a>
                @endif
            </div>
        </div>
    </div>

    {{-- Statistik --}}
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-3 xl:grid-cols-6">
        @include('dashboard.partials.stat-card', ['label' => 'Kelas', 'value' => $stats['classes'], 'icon' => 'bi-building', 'tone' => 'brand'])
        @include('dashboard.partials.stat-card', ['label' => 'Siswa', 'value' => number_format($stats['students']), 'icon' => 'bi-people', 'tone' => 'blue'])
        @include('dashboard.partials.stat-card', ['label' => 'Tugas Aktif', 'value' => $stats['assignments'], 'icon' => 'bi-journal-check', 'tone' => 'amber'])
        @include('dashboard.partials.stat-card', ['label' => 'Materi', 'value' => $stats['materials'], 'icon' => 'bi-file-earmark-text', 'tone' => 'slate'])
        @include('dashboard.partials.stat-card', ['label' => 'Perlu Dinilai', 'value' => $stats['pending_grading'], 'icon' => 'bi-pencil-square', 'tone' => 'rose', 'hint' => 'Tugas siswa'])
    </div>

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

    </div>

    {{-- Materi terbaru & kehadiran kelas --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
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

        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200/70">
            <h2 class="mb-4 text-base font-bold text-slate-900"><i class="bi bi-clipboard-check mr-2 text-blue-600"></i>Kehadiran Kelas Hari Ini</h2>
            <div class="space-y-3">
                @forelse($class_attendance as $row)
                <div class="rounded-xl bg-slate-50 px-4 py-3">
                    <div class="flex items-center justify-between">
                        <p class="font-semibold text-slate-900">{{ $row['class']?->name }}</p>
                        <span class="text-sm font-bold {{ ($row['percent'] ?? 0) >= 75 ? 'text-brand-600' : 'text-rose-600' }}">{{ $row['percent'] ?? 0 }}%</span>
                    </div>
                    <div class="mt-2 h-2 overflow-hidden rounded-full bg-slate-200">
                        <div class="h-full rounded-full bg-brand-500" style="width: {{ min($row['percent'] ?? 0, 100) }}%"></div>
                    </div>
                </div>
                @empty
                <p class="py-6 text-center text-sm text-slate-500">Belum ada data absensi hari ini.</p>
                @endforelse
            </div>
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
