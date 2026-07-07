{{-- resources/views/reports/recap.blade.php --}}
@extends('layouts.app')
@section('title', 'Rekap Bulanan')
@section('page-title', 'Rekap Bulanan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
    @if($class)
        <div class="d-flex gap-2">
            <a href="{{ route('reports.pdf', ['class_id'=>$class->id,'month'=>$month,'year'=>$year]) }}" class="btn btn-outline-danger btn-sm">
                <i class="bi bi-file-earmark-pdf me-1"></i>PDF
            </a>
            <a href="{{ route('reports.excel', ['class_id'=>$class->id,'month'=>$month,'year'=>$year]) }}" class="btn btn-outline-success btn-sm">
                <i class="bi bi-file-earmark-excel me-1"></i>Excel
            </a>
        </div>
    @endif
</div>

{{-- Filter --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Kelas <span class="text-danger">*</span></label>
                <select name="class_id" class="form-select" required>
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold text-muted mb-1">Bulan</label>
                <select name="month" class="form-select">
                    @foreach(range(1,12) as $m)
                        <option value="{{ $m }}" {{ $m == $month ? 'selected':'' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold text-muted mb-1">Tahun</label>
                <select name="year" class="form-select">
                    @foreach(range(date('Y'), date('Y')-2) as $y)
                        <option value="{{ $y }}" {{ $y == $year ? 'selected':'' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100 mt-3"><i class="bi bi-search me-1"></i>Lihat Rekap</button>
            </div>
        </form>
    </div>
</div>

@if($class && $recap)
    <div class="card mb-3" style="background:linear-gradient(135deg,var(--color-brand-600),var(--color-brand-500));border:none">
        <div class="card-body p-3">
            <div class="text-white fw-bold" style="font-size:16px">{{ $class->name }}</div>
            <div style="color:rgba(255,255,255,.7);font-size:13px">
                Rekap Bulan {{ \Carbon\Carbon::create()->month($month)->translatedFormat('F') }} {{ $year }}
                · {{ count($recap['days'] ?? []) }} hari aktif
            </div>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover table-bordered mb-0" style="font-size:12px">
                <thead>
                    <tr style="background:#f8faff">
                        <th rowspan="2" style="vertical-align:middle;min-width:150px">Nama Siswa</th>
                        <th rowspan="2" style="vertical-align:middle">NIS</th>
                        @foreach($recap['days'] ?? [] as $day)
                            <th class="text-center" style="min-width:30px;padding:4px">{{ $day }}</th>
                        @endforeach
                        <th class="text-center" style="background:var(--color-brand-50)">H</th>
                        <th class="text-center" style="background:#fef3c7">T</th>
                        <th class="text-center" style="background:var(--color-brand-50)">S</th>
                        <th class="text-center" style="background:#e0e7ff">I</th>
                        <th class="text-center" style="background:#fee2e2">A</th>
                        <th class="text-center" style="background:#f1f5f9">%</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recap['students'] ?? [] as $row)
                        <tr>
                            <td class="fw-semibold">{{ $row['student']->name }}</td>
                            <td><code style="font-size:11px">{{ $row['student']->nis }}</code></td>
                            @foreach($recap['days'] ?? [] as $day)
                                @php $s = $row['attendance'][$day] ?? '-'; @endphp
                                    <td class="text-center" style="padding:4px;
                                    background:{{ match($s) {
                                        'hadir'=>'var(--color-brand-50)','terlambat'=>'#fef3c7','sakit'=>'var(--color-brand-50)','izin'=>'#e0e7ff','alfa'=>'#fee2e2', default=>'transparent'
                                    } }}">
                                    <span style="font-weight:700;font-size:11px;color:{{ match($s) {
                                        'hadir'=>'#0058ff','terlambat'=>'#f59e0b','sakit'=>'#3b82f6','izin'=>'#6366f1','alfa'=>'#ef4444', default=>'#94a3b8'
                                    } }}">
                                        {{ strtoupper(substr($s === '-' ? '-' : $s, 0, 1)) }}
                                    </span>
                                </td>
                            @endforeach
                            <td class="text-center fw-bold text-success">{{ $row['counts']['hadir'] ?? 0 }}</td>
                            <td class="text-center fw-bold text-warning">{{ $row['counts']['terlambat'] ?? 0 }}</td>
                            <td class="text-center fw-bold text-info">{{ $row['counts']['sakit'] ?? 0 }}</td>
                            <td class="text-center fw-bold" style="color:#6366f1">{{ $row['counts']['izin'] ?? 0 }}</td>
                            <td class="text-center fw-bold text-danger">{{ $row['counts']['alfa'] ?? 0 }}</td>
                            <td class="text-center fw-bold {{ ($row['percentage'] ?? 0) >= 75 ? 'text-success' : 'text-danger' }}">
                                {{ $row['percentage'] ?? 0 }}%
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            <div class="d-flex gap-3 flex-wrap" style="font-size:12px">
                <span><span style="color:var(--color-brand-500);font-weight:700">H</span> = Hadir</span>
                <span><span style="color:#f59e0b;font-weight:700">T</span> = Terlambat</span>
                <span><span style="color:#3b82f6;font-weight:700">S</span> = Sakit</span>
                <span><span style="color:#6366f1;font-weight:700">I</span> = Izin</span>
                <span><span style="color:#ef4444;font-weight:700">A</span> = Alfa</span>
                <span><span style="color:#94a3b8;font-weight:700">-</span> = Tidak ada kelas</span>
            </div>
        </div>
    </div>
@elseif(!$class)
    <div class="card">
        <div class="card-body text-center py-5 text-muted">
            <i class="bi bi-table fs-1 d-block mb-2"></i>
            Pilih kelas dan bulan untuk melihat rekap absensi.
        </div>
    </div>
@endif
@endsection
