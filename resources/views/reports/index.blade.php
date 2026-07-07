{{-- resources/views/reports/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Laporan')
@section('page-title', 'Laporan')

@section('content')
    @if($summary ?? null)
        <div class="row g-3 mb-4">
            @foreach([
                ['label'=>'Total Siswa','value'=>$summary['total_students'],'icon'=>'bi-people-fill','bg'=>'var(--color-brand-50)','color'=>'#0058ff'],
                ['label'=>'Avg. Kehadiran','value'=>($summary['avg_attendance'] ?? 0).'%','icon'=>'bi-graph-up','bg'=>'var(--color-brand-50)','color'=>'#0058ff'],
                ['label'=>'Hadir Bulan Ini','value'=>$summary['total_hadir'] ?? 0,'icon'=>'bi-check-circle-fill','bg'=>'var(--color-brand-50)','color'=>'#0058ff'],
                ['label'=>'Alfa Bulan Ini','value'=>$summary['total_alfa'] ?? 0,'icon'=>'bi-x-circle-fill','bg'=>'#fee2e2','color'=>'#ef4444'],
            ] as $s)
                <div class="col-6 col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon" style="background:{{ $s['bg'] }};color:{{ $s['color'] }}"><i class="bi {{ $s['icon'] }}"></i></div>
                        <div style="font-size:26px;font-weight:800;font-family:'JetBrains Mono',monospace">{{ $s['value'] }}</div>
                        <div style="color:#64748b;font-size:12px">{{ $s['label'] }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <h5 class="fw-bold mb-3"><i class="bi bi-bar-chart-line me-2 text-primary"></i>Laporan Analitik</h5>
    <div class="row g-4 mb-4">
        @foreach($reportTypes as $slug => $meta)
            <div class="col-md-6 col-lg-4">
                <a href="{{ route('reports.show', $slug) }}" class="card text-decoration-none h-100"
                    style="transition:transform .2s" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform=''">
                    <div class="card-body p-4">
                        <div style="width:52px;height:52px;border-radius:12px;background:{{ $meta['bg'] }};display:grid;place-items:center;font-size:22px;margin-bottom:12px;color:{{ $meta['color'] }}">
                            <i class="bi {{ $meta['icon'] }}"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-1">{{ $meta['title'] }}</h5>
                        <p class="text-muted mb-0" style="font-size:13px">{{ $meta['description'] }}</p>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    <details class="card mb-4">
        <summary class="card-header" style="cursor:pointer">
            <i class="bi bi-clock-history me-2 text-secondary"></i>Laporan Absensi Klasik (Harian / Bulanan / Rekap)
        </summary>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <a href="{{ route('reports.daily') }}" class="btn btn-outline-primary w-100">
                        <i class="bi bi-calendar-day me-1"></i>Laporan Harian
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="{{ route('reports.monthly') }}" class="btn btn-outline-success w-100">
                        <i class="bi bi-calendar-month me-1"></i>Laporan Bulanan
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="{{ route('reports.recap') }}" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-table me-1"></i>Rekap Bulanan
                    </a>
                </div>
            </div>
            <hr>
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-muted mb-1">Kelas</label>
                    <select name="class_id" class="form-select form-select-sm">
                        <option value="">Semua Kelas</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Bulan</label>
                    <select name="month" class="form-select form-select-sm">
                        @foreach(range(1,12) as $m)
                            <option value="{{ $m }}" {{ $m == now()->month ? 'selected':'' }}>
                                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Tahun</label>
                    <select name="year" class="form-select form-select-sm">
                        @foreach(range(date('Y'), date('Y')-2) as $y)
                            <option value="{{ $y }}" {{ $y == now()->year ? 'selected':'' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button formaction="{{ route('reports.pdf') }}" type="submit" class="btn btn-outline-danger btn-sm w-100">
                        <i class="bi bi-file-earmark-pdf me-1"></i>PDF
                    </button>
                </div>
                <div class="col-md-2">
                    <button formaction="{{ route('reports.excel') }}" type="submit" class="btn btn-outline-success btn-sm w-100">
                        <i class="bi bi-file-earmark-excel me-1"></i>Excel
                    </button>
                </div>
            </form>
        </div>
    </details>
@endsection
