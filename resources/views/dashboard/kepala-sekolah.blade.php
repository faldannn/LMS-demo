@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div id="dashboard-root" class="space-y-6">
    @include('dashboard.partials.hero', [
        'title' => 'Dashboard ' . $user->name,
        'subtitle' => 'Pantau data akademik, kehadiran, aktivitas LMS, dan pengumuman sekolah secara real-time.',
    ])

    {{-- Row 1: 4 Hero Stat Widgets (Matching Reference Mockup Top Row) --}}
    <div class="grid grid-cols-2 gap-4 lg:gap-5 xl:grid-cols-4">
        {{-- Card 1: Total Guru (Frosted Soft Violet Glass) --}}
        @include('dashboard.partials.stat-card', [
            'label' => 'Total Guru',
            'value' => number_format($totals['teachers']),
            'icon' => 'bi-person-badge',
            'tone' => 'violet',
            'hint' => 'Guru aktif mengajar',
            'badge' => 'Pengajar'
        ])

        {{-- Card 2: Total Siswa (Vivid Cyan-Blue Gradient Glass — "Clogo" style) --}}
        @include('dashboard.partials.stat-card', [
            'label' => 'Total Siswa',
            'value' => number_format($totals['students']),
            'icon' => 'bi-people',
            'tone' => 'cyan-gradient',
            'hint' => 'Siswa aktif terdaftar',
            'badge' => 'Terdaftar'
        ])

        {{-- Card 3: Total Kelas (Frosted Soft Blue Glass — "Nex" style) --}}
        @include('dashboard.partials.stat-card', [
            'label' => 'Total Kelas',
            'value' => number_format($totals['classes']),
            'icon' => 'bi-building',
            'tone' => 'blue',
            'hint' => 'Rombongan belajar',
            'badge' => 'Rombel'
        ])

        {{-- Card 4: Mata Pelajaran (Vivid Magenta-Pink Gradient Glass — "Mojin" style) --}}
        @include('dashboard.partials.stat-card', [
            'label' => 'Mata Pelajaran',
            'value' => number_format($totals['subjects']),
            'icon' => 'bi-book',
            'tone' => 'pink-gradient',
            'hint' => 'Kurikulum aktif',
            'badge' => 'Kurikulum'
        ])
    </div>

    {{-- Row 2: Kehadiran Hari Ini (Frosted Glass Trio) --}}
    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        @include('dashboard.partials.stat-card', [
            'label' => 'Guru Hadir Hari Ini',
            'value' => number_format($attendance_today['teachers_present']),
            'icon' => 'bi-person-check',
            'tone' => 'brand',
            'hint' => 'Menginput absensi hari ini',
            'badge' => 'Presensi'
        ])

        @include('dashboard.partials.stat-card', [
            'label' => 'Siswa Hadir Hari Ini',
            'value' => number_format($attendance_today['students_present']),
            'icon' => 'bi-check-circle',
            'tone' => 'emerald',
            'hint' => $attendance_today['students_percentage'] . '% tingkat kehadiran',
            'badge' => 'Kehadiran'
        ])

        @include('dashboard.partials.stat-card', [
            'label' => 'Siswa Alfa',
            'value' => number_format($attendance_today['alfa']),
            'icon' => 'bi-x-circle',
            'tone' => 'rose',
            'hint' => 'Tanpa keterangan hari ini',
            'badge' => 'Absen'
        ])
    </div>

    {{-- Center Section: Main Wave Chart + Side Summary Panel (Matching Reference Mockup Layout!) --}}
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        {{-- Left Area (~68% width): Wave Chart & Gauge Sliders --}}
        <div class="glass-panel p-6 sm:p-7 xl:col-span-2 flex flex-col justify-between">
            <div>
                <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-indigo-500/15 text-indigo-600 border border-indigo-500/25 shadow-sm">
                            <i class="bi bi-graph-up-arrow text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-black text-slate-900 tracking-tight">Grafik Kehadiran Bulanan</h2>
                            <p class="text-xs font-medium text-slate-500">Tren persentase kehadiran siswa per bulan</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="relative">
                            <select id="attendance-year" class="glass-pill appearance-none py-2 pl-4 pr-9 text-xs font-bold text-slate-700 outline-none cursor-pointer hover:bg-white transition-all">
                                <option value="{{ date('Y') }}">{{ date('Y') }}</option>
                                <option value="{{ date('Y') - 1 }}">{{ date('Y') - 1 }}</option>
                            </select>
                            <i class="bi bi-chevron-down absolute right-3 top-2.5 text-[10px] text-slate-500 pointer-events-none"></i>
                        </div>
                    </div>
                </div>

                {{-- Chart Canvas --}}
                <div class="h-64 sm:h-72 w-full relative">
                    <canvas id="attendanceChart"></canvas>
                </div>
            </div>

            {{-- Bottom Metric Sliders (Gemoesicons, Coodora, Comfiyore style from Reference Mockup) --}}
            <div class="mt-6 pt-5 border-t border-slate-200/60 grid grid-cols-1 sm:grid-cols-3 gap-4">
                {{-- Slider 1: Kehadiran Rata-rata --}}
                <div class="rounded-2xl bg-white/60 backdrop-blur-md p-3.5 border border-white/80 shadow-sm">
                    <div class="flex items-center justify-between text-xs font-bold mb-2">
                        <span class="text-slate-600 flex items-center gap-1.5">
                            <span class="h-2 w-2 rounded-full bg-sky-500"></span> Kehadiran
                        </span>
                        <span class="text-sky-600 font-extrabold">{{ $attendance_today['students_percentage'] }}%</span>
                    </div>
                    <div class="h-2 w-full bg-slate-100 rounded-full overflow-hidden p-0.5 border border-slate-200/60">
                        <div class="h-full bg-gradient-to-r from-sky-400 to-blue-600 rounded-full transition-all duration-500" style="width: {{ max(10, min(100, $attendance_today['students_percentage'])) }}%;"></div>
                    </div>
                </div>

                {{-- Slider 2: Guru Aktif --}}
                <div class="rounded-2xl bg-white/60 backdrop-blur-md p-3.5 border border-white/80 shadow-sm">
                    <div class="flex items-center justify-between text-xs font-bold mb-2">
                        <span class="text-slate-600 flex items-center gap-1.5">
                            <span class="h-2 w-2 rounded-full bg-indigo-500"></span> Guru Presensi
                        </span>
                        <span class="text-indigo-600 font-extrabold">{{ $totals['teachers'] > 0 ? round(($attendance_today['teachers_present'] / $totals['teachers']) * 100) : 0 }}%</span>
                    </div>
                    <div class="h-2 w-full bg-slate-100 rounded-full overflow-hidden p-0.5 border border-slate-200/60">
                        <div class="h-full bg-gradient-to-r from-indigo-500 to-purple-600 rounded-full transition-all duration-500" style="width: {{ $totals['teachers'] > 0 ? max(10, min(100, round(($attendance_today['teachers_present'] / $totals['teachers']) * 100))) : 0 }}%;"></div>
                    </div>
                </div>

                {{-- Slider 3: Ketuntasan Belajar --}}
                <div class="rounded-2xl bg-white/60 backdrop-blur-md p-3.5 border border-white/80 shadow-sm">
                    <div class="flex items-center justify-between text-xs font-bold mb-2">
                        <span class="text-slate-600 flex items-center gap-1.5">
                            <span class="h-2 w-2 rounded-full bg-pink-500"></span> Aktivitas Mapel
                        </span>
                        <span class="text-pink-600 font-extrabold">92%</span>
                    </div>
                    <div class="h-2 w-full bg-slate-100 rounded-full overflow-hidden p-0.5 border border-slate-200/60">
                        <div class="h-full bg-gradient-to-r from-pink-500 to-rose-500 rounded-full transition-all duration-500" style="width: 92%;"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Panel (~32% width - "Seenono" style in Reference Mockup) --}}
        <div class="glass-panel p-6 sm:p-7 flex flex-col justify-between">
            <div>
                <div class="mb-5 flex items-center justify-between">
                    <div>
                        <div class="text-[10px] font-extrabold uppercase tracking-widest text-indigo-600">Total &amp; Summary</div>
                        <h2 class="text-lg font-black text-slate-900 tracking-tight">Monitor Presensi</h2>
                    </div>
                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-white/80 border border-white text-indigo-600 shadow-sm">
                        <i class="bi bi-shield-check text-lg"></i>
                    </div>
                </div>

                {{-- Role Quick Switch Chips --}}
                <div class="mb-5 p-3 rounded-2xl bg-white/50 backdrop-blur-md border border-white/80">
                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-2">Akses Terhubung</div>
                    <div class="flex flex-wrap gap-1.5">
                        <span class="px-2.5 py-1 rounded-xl bg-indigo-500/15 text-indigo-700 border border-indigo-200 text-xs font-bold">Super Admin</span>
                        <span class="px-2.5 py-1 rounded-xl bg-blue-500/15 text-blue-700 border border-blue-200 text-xs font-bold">Kepsek</span>
                        <span class="px-2.5 py-1 rounded-xl bg-emerald-500/15 text-emerald-700 border border-emerald-200 text-xs font-bold">Guru</span>
                        <span class="px-2.5 py-1 rounded-xl bg-purple-500/15 text-purple-700 border border-purple-200 text-xs font-bold">Siswa</span>
                    </div>
                </div>

                {{-- Kehadiran Rendah Alert List --}}
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-extrabold uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                            <i class="bi bi-exclamation-triangle text-amber-500"></i> Perhatian Khusus
                        </span>
                        <span class="text-[10px] font-bold text-slate-400">Kehadiran Rendah</span>
                    </div>

                    @forelse($low_attendance as $row)
                    <div class="flex items-center justify-between rounded-2xl bg-white/70 backdrop-blur-md p-3 border border-white/80 transition-all hover:bg-white hover:shadow-sm">
                        <div class="min-w-0 flex items-center gap-2.5">
                            <div class="h-8 w-8 rounded-xl bg-rose-500/10 text-rose-600 flex items-center justify-center font-bold text-xs border border-rose-200">
                                {{ substr($row['student']->name, 0, 1) }}
                            </div>
                            <div class="min-w-0">
                                <p class="truncate text-xs font-bold text-slate-800">{{ $row['student']->name }}</p>
                                <p class="text-[10px] text-slate-500">{{ $row['student']->class?->name ?? '-' }}</p>
                            </div>
                        </div>
                        <span class="rounded-full bg-rose-100 text-rose-700 border border-rose-200 px-2.5 py-0.5 text-xs font-extrabold">{{ $row['percentage'] }}%</span>
                    </div>
                    @empty
                    <div class="py-6 text-center text-xs font-semibold text-slate-500 bg-white/40 rounded-2xl border border-white/60">
                        <i class="bi bi-check2-circle text-2xl text-emerald-500 block mb-1"></i>
                        Semua siswa memiliki kehadiran optimal.
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- Quick Action Buttons --}}
            <div class="mt-6 pt-4 border-t border-slate-200/60 space-y-2">
                <a href="{{ route('attendance.index') }}" class="w-full flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-extrabold text-xs shadow-md shadow-blue-500/25 transition-all">
                    <i class="bi bi-clipboard-data text-sm"></i> Buka Rekap Presensi Lengkap
                </a>
                <a href="{{ route('elearning.index') }}" class="w-full flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl bg-white/70 hover:bg-white text-slate-700 font-extrabold text-xs border border-white/90 shadow-sm transition-all">
                    <i class="bi bi-laptop text-sm text-indigo-600"></i> Buka Portal E-Learning
                </a>
            </div>
        </div>
    </div>

    {{-- Bottom Analytics Section: Aktivitas LMS, Nilai per Kelas, Pengumuman --}}
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        {{-- Aktivitas LMS (7 Hari) --}}
        <div class="glass-panel p-6">
            <div class="mb-4 flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-blue-500/15 text-blue-600 border border-blue-500/25 shadow-sm">
                    <i class="bi bi-activity text-lg"></i>
                </div>
                <div>
                    <h2 class="text-base font-black text-slate-900 tracking-tight">Aktivitas LMS (7 Hari)</h2>
                    <p class="text-xs font-medium text-slate-500">Materi dan Tugas yang dibuat</p>
                </div>
            </div>
            <div class="h-60"><canvas id="lmsActivityChart"></canvas></div>
        </div>

        {{-- Grafik Nilai per Kelas --}}
        <div class="glass-panel p-6">
            <div class="mb-4 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-purple-500/15 text-purple-600 border border-purple-500/25 shadow-sm">
                        <i class="bi bi-bar-chart text-lg"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-black text-slate-900 tracking-tight">Nilai per Kelas</h2>
                        <p class="text-xs font-medium text-slate-500">Rata-rata nilai raport</p>
                    </div>
                </div>
                <div class="relative">
                    <select id="grade-semester" class="glass-pill appearance-none py-1.5 pl-3 pr-7 text-xs font-bold text-slate-700 outline-none cursor-pointer">
                        <option value="ganjil">Ganjil</option>
                        <option value="genap">Genap</option>
                    </select>
                    <i class="bi bi-chevron-down absolute right-2.5 top-2 text-[9px] text-slate-500 pointer-events-none"></i>
                </div>
            </div>
            <div class="h-60"><canvas id="gradeChart"></canvas></div>
        </div>

        {{-- Pengumuman Terbaru --}}
        <div class="glass-panel p-6">
            <div class="mb-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-amber-500/15 text-amber-600 border border-amber-500/25 shadow-sm">
                        <i class="bi bi-megaphone text-lg"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-black text-slate-900 tracking-tight">Pengumuman</h2>
                        <p class="text-xs font-medium text-slate-500">Informasi sekolah terbaru</p>
                    </div>
                </div>
                <a href="{{ route('announcements.index') }}" class="text-xs font-extrabold text-indigo-600 hover:text-indigo-800 transition-colors">Lihat semua</a>
            </div>
            <div class="space-y-3">
                @forelse($announcements as $item)
                <div class="rounded-2xl border border-white/80 bg-white/70 backdrop-blur-md p-3.5 transition-all hover:bg-white hover:shadow-sm">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <p class="font-bold text-xs text-slate-900">{{ $item->title }}</p>
                            <p class="mt-1 line-clamp-2 text-[11px] text-slate-600">{{ Str::limit(strip_tags($item->content), 90) }}</p>
                            <p class="mt-1.5 text-[10px] font-semibold text-slate-400">{{ $item->creator?->name }} · {{ $item->created_at->diffForHumans() }}</p>
                        </div>
                        <span class="shrink-0 rounded-full bg-indigo-100 text-indigo-700 px-2 py-0.5 text-[9px] font-extrabold uppercase">{{ $item->type ?? 'info' }}</span>
                    </div>
                </div>
                @empty
                <div class="py-8 text-center text-xs font-semibold text-slate-500">Belum ada pengumuman baru.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(() => {
    const monthly = @json($monthly_trend);
    const lms = @json($lms_activity);

    const attendanceCanvas = document.getElementById('attendanceChart');
    const ctx = attendanceCanvas.getContext('2d');

    // Create vibrant linear gradient for smooth wave fill (Image 2 style)
    const gradientFill = ctx.createLinearGradient(0, 0, 0, 280);
    gradientFill.addColorStop(0, 'rgba(99, 102, 241, 0.42)');
    gradientFill.addColorStop(0.5, 'rgba(56, 189, 248, 0.22)');
    gradientFill.addColorStop(1, 'rgba(56, 189, 248, 0.01)');

    const gradientStroke = ctx.createLinearGradient(0, 0, 600, 0);
    gradientStroke.addColorStop(0, '#6366f1');
    gradientStroke.addColorStop(0.5, '#38bdf8');
    gradientStroke.addColorStop(1, '#8b5cf6');

    let attendanceChart = new Chart(attendanceCanvas, {
        type: 'line',
        data: {
            labels: monthly.map(r => r.label),
            datasets: [{
                label: 'Kehadiran %',
                data: monthly.map(r => r.percent),
                borderColor: gradientStroke,
                borderWidth: 3.5,
                backgroundColor: gradientFill,
                fill: true,
                tension: 0.45,
                pointRadius: 4,
                pointHoverRadius: 7,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#6366f1',
                pointBorderWidth: 2.5,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { 
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.85)',
                    titleFont: { family: "'Plus Jakarta Sans', sans-serif", weight: 'bold' },
                    bodyFont: { family: "'Plus Jakarta Sans', sans-serif" },
                    padding: 10,
                    cornerRadius: 12,
                    displayColors: false,
                    callbacks: {
                        label: (ctx) => ` Kehadiran: ${ctx.parsed.y}%`
                    }
                }
            },
            scales: { 
                x: {
                    grid: { display: false },
                    ticks: {
                        font: { family: "'Plus Jakarta Sans', sans-serif", size: 11, weight: '600' },
                        color: '#64748b'
                    }
                },
                y: { 
                    min: 0, 
                    max: 100, 
                    grid: { 
                        color: 'rgba(148, 163, 184, 0.15)',
                        borderDash: [5, 5]
                    },
                    ticks: { 
                        callback: v => v + '%',
                        font: { family: "'Plus Jakarta Sans', sans-serif", size: 10, weight: '600' },
                        color: '#94a3b8'
                    } 
                } 
            }
        }
    });

    document.getElementById('attendance-year')?.addEventListener('change', async (e) => {
        const res = await fetch(`{{ route('dashboard.chart-data') }}?year=${e.target.value}`);
        const data = await res.json();
        attendanceChart.data.labels = data.map(r => r.label);
        attendanceChart.data.datasets[0].data = data.map(r => r.percent);
        attendanceChart.update();
    });

    // LMS Activity Bar Chart
    new Chart(document.getElementById('lmsActivityChart'), {
        type: 'bar',
        data: {
            labels: lms.labels,
            datasets: [
                { 
                    label: 'Materi', 
                    data: lms.materials, 
                    backgroundColor: '#3b82f6', 
                    borderRadius: 8,
                    barPercentage: 0.65
                },
                { 
                    label: 'Tugas', 
                    data: lms.assignments, 
                    backgroundColor: '#6366f1', 
                    borderRadius: 8,
                    barPercentage: 0.65
                },
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { 
                legend: { 
                    position: 'bottom',
                    labels: {
                        font: { family: "'Plus Jakarta Sans', sans-serif", size: 11, weight: '600' },
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                } 
            },
            scales: { 
                x: { 
                    grid: { display: false },
                    ticks: { font: { family: "'Plus Jakarta Sans', sans-serif", size: 10 } }
                }, 
                y: { 
                    beginAtZero: true, 
                    grid: { color: 'rgba(148, 163, 184, 0.15)', borderDash: [4, 4] },
                    ticks: { precision: 0, font: { family: "'Plus Jakarta Sans', sans-serif", size: 10 } } 
                } 
            }
        }
    });

    // Grades per class chart
    const gradeCtx = document.getElementById('gradeChart');
    let gradeChart;
    async function loadGrades(semester) {
        const res = await fetch(`{{ route('dashboard.grade-chart-data') }}?semester=${semester}`);
        const data = await res.json();
        if (gradeChart) gradeChart.destroy();
        gradeChart = new Chart(gradeCtx, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [{ 
                    label: 'Rata-rata', 
                    data: data.data, 
                    backgroundColor: '#8b5cf6', 
                    borderRadius: 10,
                    barPercentage: 0.55
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { 
                    x: { grid: { display: false }, ticks: { font: { family: "'Plus Jakarta Sans', sans-serif", size: 10 } } },
                    y: { 
                        beginAtZero: true, 
                        max: 100,
                        grid: { color: 'rgba(148, 163, 184, 0.15)', borderDash: [4, 4] },
                        ticks: { font: { family: "'Plus Jakarta Sans', sans-serif", size: 10 } }
                    } 
                }
            }
        });
    }
    loadGrades('ganjil');
    document.getElementById('grade-semester')?.addEventListener('change', e => loadGrades(e.target.value));
})();
</script>
@endpush
