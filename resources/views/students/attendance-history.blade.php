{{-- resources/views/students/attendance-history.blade.php --}}
@extends('layouts.app')
@section('title', 'Riwayat Absensi Siswa')
@section('page-title', 'Riwayat Absensi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h6 class="fw-bold mb-0">{{ $student->name }}</h6>
        <span class="text-muted" style="font-size:13px">{{ $student->nis }} · {{ $student->class?->name }}</span>
    </div>
    <a href="{{ route('students.show', $student) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Kembali ke Detail
    </a>
</div>

{{-- Filter --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Bulan</label>
                <select name="month" class="form-select">
                    @foreach(range(1,12) as $m)
                        <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold text-muted mb-1">Tahun</label>
                <select name="year" class="form-select">
                    @foreach(range(date('Y'), date('Y')-2) as $y)
                        <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100 mt-3"><i class="bi bi-search me-1"></i>Lihat</button>
            </div>
        </form>
    </div>
</div>

{{-- Ringkasan --}}
@php
    $grouped = $history->groupBy('status');
    $totalDays = $history->count();
    $hadirCount = $grouped->get('hadir',collect())->count() + $grouped->get('terlambat',collect())->count();
    $pct = $totalDays > 0 ? round($hadirCount / $totalDays * 100) : 0;
@endphp

<div class="row g-3 mb-4">
    @foreach(['hadir'=>'var(--color-brand-50)', 'terlambat'=>'#fef3c7', 'sakit'=>'var(--color-brand-50)', 'izin'=>'#e0e7ff', 'alfa'=>'#fee2e2'] as $s => $bg)
        <div class="col-6 col-md-2">
            <div class="stat-card text-center" style="padding:14px">
                <div style="font-size:22px;font-weight:800;font-family:'JetBrains Mono',monospace">
                    {{ $grouped->get($s,collect())->count() }}
                </div>
                <div class="text-muted" style="font-size:11px">{{ ucfirst($s) }}</div>
            </div>
        </div>
    @endforeach
    <div class="col-6 col-md-2">
            <div class="stat-card text-center" style="padding:14px;background:{{ $pct >= 75 ? 'var(--color-brand-50)' : '#fee2e2' }}">
                <div style="font-size:22px;font-weight:800;font-family:'JetBrains Mono',monospace;color:{{ $pct >= 75 ? 'var(--color-brand-500)' : '#ef4444' }}">
                {{ $pct }}%
            </div>
            <div class="text-muted" style="font-size:11px">Kehadiran</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-calendar3 me-2 text-primary"></i>
        Riwayat Bulan {{ \Carbon\Carbon::create()->month($month)->translatedFormat('F') }} {{ $year }}
        <span class="badge bg-primary ms-2">{{ $totalDays }} hari</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tanggal</th>
                    <th>Hari</th>
                    <th>Sesi</th>
                    <th>Status</th>
                    <th>Jam Masuk</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($history as $i => $detail)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td style="font-family:'JetBrains Mono',monospace;font-size:13px">
                            {{ $detail->attendance->date->format('d/m/Y') }}
                        </td>
                        <td>{{ $detail->attendance->date->translatedFormat('l') }}</td>
                        <td><span class="badge bg-secondary">{{ ucfirst($detail->attendance->session) }}</span></td>
                        <td>
                            <span class="badge badge-status-{{ $detail->status }}">
                                @switch($detail->status)
                                    @case('hadir') ✅ Hadir @break
                                    @case('terlambat') ⏰ Terlambat @break
                                    @case('sakit') 🤒 Sakit @break
                                    @case('izin') 📄 Izin @break
                                    @case('alfa') ❌ Alfa @break
                                @endswitch
                            </span>
                        </td>
                        <td style="font-size:13px">{{ $detail->check_in ?? '-' }}</td>
                        <td style="font-size:12px;color:#64748b">{{ $detail->notes ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-calendar-x fs-1 d-block mb-2"></i>
                            Tidak ada riwayat absensi pada bulan ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
