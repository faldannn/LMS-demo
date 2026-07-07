{{-- resources/views/reports/monthly.blade.php --}}
@extends('layouts.app')
@section('title', 'Laporan Bulanan')
@section('page-title', 'Laporan Absensi Bulanan')

@section('content')

    {{-- Filter --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label fw-600">Bulan</label>
                    <select name="month" class="form-select">
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create(null, $m)->translatedFormat('F') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-600">Tahun</label>
                    <select name="year" class="form-select">
                        @foreach([date('Y'), date('Y') - 1, date('Y') - 2] as $y)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-600">Kelas</label>
                    <select name="class_id" class="form-select">
                        <option value="">Semua Kelas</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ $classId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-1">
                        <i class="bi bi-search me-1"></i>Tampilkan
                    </button>
                    <a href="{{ route('reports.pdf', request()->query()) }}" class="btn btn-outline-danger">
                        <i class="bi bi-file-pdf me-1"></i>PDF
                    </a>
                    <a href="{{ route('reports.excel', request()->query()) }}" class="btn btn-outline-success">
                        <i class="bi bi-file-excel me-1"></i>Excel
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Chart --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header"><i class="bi bi-bar-chart me-2 text-primary"></i>Kehadiran Per Kelas</div>
                <div class="card-body"><canvas id="reportChart" style="height:240px"></canvas></div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header"><i class="bi bi-pie-chart me-2 text-primary"></i>Ringkasan</div>
                <div class="card-body">
                    @php
                        $totals = ['hadir' => 0, 'sakit' => 0, 'izin' => 0, 'alfa' => 0, 'terlambat' => 0, 'total' => 0];
                        foreach ($report['rows'] as $r) {
                            foreach ($totals as $k => $v) {
                                if (isset($r->$k))
                                    $totals[$k] += $r->$k;
                            }
                        }
                    @endphp
                    @foreach(['hadir' => ['#0058ff', 'Hadir'], 'sakit' => ['#f59e0b', 'Sakit'], 'izin' => ['#0047d1', 'Izin'], 'alfa' => ['#ef4444', 'Alfa'], 'terlambat' => ['#8b5cf6', 'Terlambat']] as $k => [$col, $lbl])
                        <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:12px;height:12px;border-radius:3px;background:{{ $col }}"></div>
                                <span style="font-size:13.5px">{{ $lbl }}</span>
                            </div>
                            <div class="fw-700">{{ number_format($totals[$k]) }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <span><i class="bi bi-table me-2 text-primary"></i>Rekap Per Kelas —
                {{ \Carbon\Carbon::create(null, $month)->translatedFormat('F') }} {{ $year }}</span>
            <span class="badge bg-primary">{{ $report['rows']->count() }} kelas</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Kelas</th>
                        <th>Wali Kelas</th>
                        <th>Hadir</th>
                        <th>Sakit</th>
                        <th>Izin</th>
                        <th>Alfa</th>
                        <th>Terlambat</th>
                        <th>% Kehadiran</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($report['rows'] as $row)
                        <tr>
                            <td><strong>{{ $row->class_name }}</strong></td>
                            <td style="font-size:12.5px">
                                {{ \App\Models\Classes::find($row->class_id)?->homeroomTeacher?->name ?? '-' }}</td>
                            <td><span class="badge badge-status-hadir">{{ $row->hadir }}</span></td>
                            <td><span class="badge badge-status-sakit">{{ $row->sakit }}</span></td>
                            <td><span class="badge badge-status-izin">{{ $row->izin }}</span></td>
                            <td><span class="badge badge-status-alfa">{{ $row->alfa }}</span></td>
                            <td><span class="badge badge-status-terlambat">{{ $row->terlambat }}</span></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress" style="width:80px;height:7px">
                                        <div class="progress-bar {{ $row->percentage >= 90 ? 'bg-success' : ($row->percentage >= 75 ? 'bg-warning' : 'bg-danger') }}"
                                            style="width:{{ $row->percentage }}%"></div>
                                    </div>
                                    <strong>{{ $row->percentage }}%</strong>
                                </div>
                            </td>
                            <td>
                                @if($row->percentage >= 90)
                                    <span class="badge bg-success">✅ Baik</span>
                                @elseif($row->percentage >= 75)
                                    <span class="badge bg-warning text-dark">⚠️ Cukup</span>
                                @else
                                    <span class="badge bg-danger">❌ Kurang</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">Tidak ada data untuk periode ini</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        const rows = @json($report['rows']);
        new Chart(document.getElementById('reportChart'), {
            type: 'bar',
            data: {
                labels: rows.map(r => r.class_name),
                datasets: [
                    { label: 'Hadir', data: rows.map(r => r.hadir), backgroundColor: 'rgba(16,185,129,.8)' },
                    { label: 'Sakit', data: rows.map(r => r.sakit), backgroundColor: 'rgba(245,158,11,.8)' },
                    { label: 'Izin', data: rows.map(r => r.izin), backgroundColor: 'rgba(26,86,219,.8)' },
                    { label: 'Alfa', data: rows.map(r => r.alfa), backgroundColor: 'rgba(239,68,68,.8)' },
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                scales: { x: { stacked: true }, y: { stacked: true } },
                plugins: { legend: { position: 'top', labels: { font: { size: 11 } } } }
            }
        });
    </script>
@endpush