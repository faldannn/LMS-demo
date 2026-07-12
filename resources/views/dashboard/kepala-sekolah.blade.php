@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard Kepala Sekolah')

@section('content')
<div id="dashboard-root" class="space-y-6">
    @include('dashboard.partials.hero', [
        'title' => 'Dashboard Kepala Sekolah',
        'subtitle' => 'Pantau data akademik, kehadiran, aktivitas LMS, dan pengumuman sekolah secara real-time.',
    ])

    {{-- Statistik utama --}}
    <div class="grid grid-cols-2 gap-4 xl:grid-cols-4">
        @include('dashboard.partials.stat-card', ['label' => 'Total Guru', 'value' => number_format($totals['teachers']), 'icon' => 'bi-person-badge', 'tone' => 'violet', 'hint' => 'Guru aktif'])
        @include('dashboard.partials.stat-card', ['label' => 'Total Siswa', 'value' => number_format($totals['students']), 'icon' => 'bi-people', 'tone' => 'blue', 'hint' => 'Siswa aktif'])
        @include('dashboard.partials.stat-card', ['label' => 'Total Kelas', 'value' => number_format($totals['classes']), 'icon' => 'bi-building', 'tone' => 'brand', 'hint' => 'Kelas terdaftar'])
        @include('dashboard.partials.stat-card', ['label' => 'Mata Pelajaran', 'value' => number_format($totals['subjects']), 'icon' => 'bi-book', 'tone' => 'amber', 'hint' => 'Mapel aktif'])
    </div>

    {{-- Kehadiran hari ini --}}
    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        @include('dashboard.partials.stat-card', ['label' => 'Guru Hadir Hari Ini', 'value' => number_format($attendance_today['teachers_present']), 'icon' => 'bi-person-check', 'tone' => 'brand', 'hint' => 'Menginput absensi'])
        @include('dashboard.partials.stat-card', ['label' => 'Siswa Hadir Hari Ini', 'value' => number_format($attendance_today['students_present']), 'icon' => 'bi-check-circle', 'tone' => 'blue', 'hint' => $attendance_today['students_percentage'] . '% kehadiran'])
        @include('dashboard.partials.stat-card', ['label' => 'Siswa Alfa', 'value' => number_format($attendance_today['alfa']), 'icon' => 'bi-x-circle', 'tone' => 'rose', 'hint' => 'Hari ini'])
    </div>

    {{-- Grafik --}}
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200/70 xl:col-span-2">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-base font-bold text-slate-900"><i class="bi bi-graph-up-arrow mr-2 text-brand-600"></i>Grafik Kehadiran Bulanan</h2>
                    <p class="text-xs text-slate-500">Persentase kehadiran siswa per bulan</p>
                </div>
                <select id="attendance-year" class="rounded-lg border-0 bg-slate-100 px-3 py-2 text-sm font-medium text-slate-700">
                    <option value="{{ date('Y') }}">{{ date('Y') }}</option>
                    <option value="{{ date('Y') - 1 }}">{{ date('Y') - 1 }}</option>
                </select>
            </div>
            <div class="h-72"><canvas id="attendanceChart"></canvas></div>
        </div>

        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200/70">
            <div class="mb-4">
                <h2 class="text-base font-bold text-slate-900"><i class="bi bi-activity mr-2 text-brand-600"></i>Aktivitas LMS (7 Hari)</h2>
                <p class="text-xs text-slate-500">Materi, dan Tugas yang dibuat</p>
            </div>
            <div class="h-64"><canvas id="lmsActivityChart"></canvas></div>
        </div>

        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200/70">
            <div class="mb-4 flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-base font-bold text-slate-900"><i class="bi bi-bar-chart mr-2 text-brand-600"></i>Grafik Nilai per Kelas</h2>
                    <p class="text-xs text-slate-500">Rata-rata nilai raport</p>
                </div>
                <select id="grade-semester" class="rounded-lg border-0 bg-slate-100 px-3 py-2 text-sm">
                    <option value="ganjil">Ganjil</option>
                    <option value="genap">Genap</option>
                </select>
            </div>
            <div class="h-64"><canvas id="gradeChart"></canvas></div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        {{-- Pengumuman --}}
        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200/70 xl:col-span-2">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-base font-bold text-slate-900"><i class="bi bi-megaphone mr-2 text-brand-600"></i>Pengumuman Terbaru</h2>
                <a href="{{ route('announcements.index') }}" class="text-xs font-semibold text-brand-600 hover:underline">Lihat semua</a>
            </div>
            <div class="space-y-3">
                @forelse($announcements as $item)
                <div class="rounded-xl border border-slate-100 bg-slate-50/80 p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-semibold text-slate-900">{{ $item->title }}</p>
                            <p class="mt-1 line-clamp-2 text-sm text-slate-600">{{ Str::limit(strip_tags($item->content), 120) }}</p>
                            <p class="mt-2 text-xs text-slate-400">{{ $item->creator?->name }} · {{ $item->created_at->diffForHumans() }}</p>
                        </div>
                        <span class="shrink-0 rounded-full bg-brand-100 px-2.5 py-1 text-[10px] font-bold uppercase text-brand-700">{{ $item->type ?? 'info' }}</span>
                    </div>
                </div>
                @empty
                <p class="py-8 text-center text-sm text-slate-500">Belum ada pengumuman.</p>
                @endforelse
            </div>
        </div>

        {{-- Kehadiran rendah --}}
        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200/70">
            <h2 class="mb-4 text-base font-bold text-slate-900"><i class="bi bi-exclamation-triangle mr-2 text-amber-500"></i>Kehadiran Rendah</h2>
            <div class="space-y-3">
                @forelse($low_attendance as $row)
                <div class="flex items-center justify-between rounded-xl bg-slate-50 px-3 py-2.5">
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold text-slate-800">{{ $row['student']->name }}</p>
                        <p class="text-xs text-slate-500">{{ $row['student']->class?->name ?? '-' }}</p>
                    </div>
                    <span class="rounded-full bg-rose-100 px-2.5 py-1 text-xs font-bold text-rose-700">{{ $row['percentage'] }}%</span>
                </div>
                @empty
                <p class="py-6 text-center text-sm text-slate-500">Semua siswa memiliki kehadiran baik.</p>
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
    const css = getComputedStyle(document.documentElement);
    const brand = css.getPropertyValue('--color-brand-500').trim() || '#0058ff';
    function hexToRgba(hex, alpha) {
        const h = hex.replace('#', '');
        const full = h.length === 3 ? h.split('').map(c => c + c).join('') : h;
        const bigint = parseInt(full, 16);
        const r = (bigint >> 16) & 255;
        const g = (bigint >> 8) & 255;
        const b = bigint & 255;
        return `rgba(${r}, ${g}, ${b}, ${alpha})`;
    }
    const brandLight = hexToRgba(brand, 0.15);
    const monthly = @json($monthly_trend);
    const lms = @json($lms_activity);

    const attendanceCtx = document.getElementById('attendanceChart');
    let attendanceChart = new Chart(attendanceCtx, {
        type: 'line',
        data: {
            labels: monthly.map(r => r.label),
            datasets: [{
                label: 'Kehadiran %',
                data: monthly.map(r => r.percent),
                borderColor: brand,
                backgroundColor: brandLight,
                fill: true,
                tension: 0.35,
                pointRadius: 4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { min: 0, max: 100, ticks: { callback: v => v + '%' } } }
        }
    });

    document.getElementById('attendance-year')?.addEventListener('change', async (e) => {
        const res = await fetch(`{{ route('dashboard.chart-data') }}?year=${e.target.value}`);
        const data = await res.json();
        attendanceChart.data.labels = data.map(r => r.label);
        attendanceChart.data.datasets[0].data = data.map(r => r.percent);
        attendanceChart.update();
    });

    new Chart(document.getElementById('lmsActivityChart'), {
        type: 'bar',
        data: {
            labels: lms.labels,
            datasets: [
                { label: 'Materi', data: lms.materials, backgroundColor: brand, borderRadius: 6 },
                { label: 'Tugas', data: lms.assignments, backgroundColor: css.getPropertyValue('--color-brand-600').trim() || '#0047d1', borderRadius: 6 },
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } },
            scales: { x: { stacked: false }, y: { beginAtZero: true, ticks: { precision: 0 } } }
        }
    });

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
                datasets: [{ label: 'Rata-rata', data: data.data, backgroundColor: css.getPropertyValue('--color-brand-600').trim() || '#0047d1', borderRadius: 8 }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, max: 100 } }
            }
        });
    }
    loadGrades('ganjil');
    document.getElementById('grade-semester')?.addEventListener('change', e => loadGrades(e.target.value));
})();
</script>
@endpush
