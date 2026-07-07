{{-- resources/views/reports/daily.blade.php --}}
@extends('layouts.app')
@section('title', 'Laporan Harian')
@section('page-title', 'Laporan Harian')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
    <div class="d-flex gap-2">
        <a href="{{ route('reports.pdf', ['date'=>$date,'class_id'=>$classId,'month'=>\Carbon\Carbon::parse($date)->month,'year'=>\Carbon\Carbon::parse($date)->year]) }}" class="btn btn-outline-danger btn-sm">
            <i class="bi bi-file-earmark-pdf me-1"></i>PDF
        </a>
        <a href="{{ route('reports.excel', ['date'=>$date,'class_id'=>$classId,'month'=>\Carbon\Carbon::parse($date)->month,'year'=>\Carbon\Carbon::parse($date)->year]) }}" class="btn btn-outline-success btn-sm">
            <i class="bi bi-file-earmark-excel me-1"></i>Excel
        </a>
    </div>
</div>

{{-- Filter --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Tanggal</label>
                <input type="date" name="date" class="form-control" value="{{ $date }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Kelas</label>
                <select name="class_id" class="form-select">
                    <option value="">Semua Kelas</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ $classId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100 mt-3"><i class="bi bi-search me-1"></i>Lihat</button>
            </div>
        </form>
    </div>
</div>

@php
    $dateLabel = \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y');
    $attendances = $report['attendances'] ?? collect();
    $summary = $report['summary'] ?? [];
@endphp

{{-- Summary --}}
@if(!empty($summary) && ($summary['total'] ?? 0) > 0)
    <div class="row g-3 mb-4">
        @foreach([
            ['Hadir',    $summary['hadir'] ?? 0,     'var(--color-brand-50)', '#0058ff','bi-check-circle-fill'],
            ['Terlambat',$summary['terlambat'] ?? 0,  '#fef3c7','#f59e0b','bi-clock-fill'],
            ['Sakit',    $summary['sakit'] ?? 0,      'var(--color-brand-50)', '#3b82f6','bi-heart-pulse-fill'],
            ['Izin',     $summary['izin'] ?? 0,       '#e0e7ff','#6366f1','bi-file-earmark-text-fill'],
            ['Alfa',     $summary['alfa'] ?? 0,       '#fee2e2','#ef4444','bi-x-circle-fill'],
        ] as [$label, $val, $bg, $color, $icon])
            <div class="col-6 col-md">
                <div class="stat-card text-center" style="padding:14px">
                    <i class="bi {{ $icon }}" style="font-size:20px;color:{{ $color }}"></i>
                    <div style="font-size:24px;font-weight:800;color:{{ $color }};font-family:'JetBrains Mono',monospace">{{ $val }}</div>
                    <div class="text-muted" style="font-size:11px">{{ $label }}</div>
                </div>
            </div>
        @endforeach
        <div class="col-6 col-md">
            <div class="stat-card text-center" style="padding:14px;background:#f0fdf4">
                <i class="bi bi-percent" style="font-size:20px;color:var(--color-brand-500)"></i>
                <div style="font-size:24px;font-weight:800;color:var(--color-brand-500);font-family:'JetBrains Mono',monospace">{{ $summary['percentage'] ?? 0 }}%</div>
                <div class="text-muted" style="font-size:11px">Kehadiran</div>
            </div>
        </div>
    </div>
@endif

@if($attendances->isEmpty())
    <div class="card">
        <div class="card-body text-center py-5 text-muted">
            <i class="bi bi-calendar-x fs-1 d-block mb-2"></i>
            Tidak ada data absensi untuk <strong>{{ $dateLabel }}</strong>.
        </div>
    </div>
@else
    @foreach($attendances as $att)
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-bold">
                    <i class="bi bi-building me-2 text-primary"></i>{{ $att->class?->name }}
                    <span class="text-muted ms-2" style="font-size:12px">{{ $dateLabel }}</span>
                </span>
                <div class="d-flex gap-2">
                    @php $dets = $att->details @endphp
                    <span class="badge badge-status-hadir">Hadir: {{ $dets->where('status','hadir')->count() }}</span>
                    <span class="badge badge-status-terlambat">Terlambat: {{ $dets->where('status','terlambat')->count() }}</span>
                    <span class="badge badge-status-alfa">Alfa: {{ $dets->where('status','alfa')->count() }}</span>
                    <span class="badge badge-status-sakit">Sakit: {{ $dets->where('status','sakit')->count() }}</span>
                    <span class="badge badge-status-izin">Izin: {{ $dets->where('status','izin')->count() }}</span>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr><th>#</th><th>NIS</th><th>Nama Siswa</th><th>Status</th><th>Jam Masuk</th><th>Keterangan</th></tr>
                    </thead>
                    <tbody>
                        @foreach($att->details->sortBy('student.name') as $i => $d)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td><code>{{ $d->student?->nis }}</code></td>
                                <td class="fw-semibold">{{ $d->student?->name }}</td>
                                <td>
                                    <span class="badge badge-status-{{ $d->status }}">
                                        @switch($d->status)
                                            @case('hadir') ✅ Hadir @break
                                            @case('terlambat') ⏰ Terlambat @break
                                            @case('sakit') 🤒 Sakit @break
                                            @case('izin') 📄 Izin @break
                                            @case('alfa') ❌ Alfa @break
                                        @endswitch
                                    </span>
                                </td>
                                <td style="font-size:13px;font-family:'JetBrains Mono',monospace">{{ $d->check_in ?? '-' }}</td>
                                <td style="font-size:12px">{{ $d->notes ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
@endif
@endsection
