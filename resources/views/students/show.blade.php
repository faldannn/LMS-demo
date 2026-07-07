{{-- resources/views/students/show.blade.php --}}
@extends('layouts.app')
@section('title', 'Detail Siswa')
@section('page-title', 'Detail Siswa')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div></div>
    <div class="d-flex gap-2">
        <a href="{{ route('students.print-card', $student) }}" target="_blank" class="btn btn-outline-primary">
            <i class="bi bi-printer me-1"></i>Cetak Kartu
        </a>
        <a href="{{ route('students.attendance-history', $student) }}" class="btn btn-outline-info">
            <i class="bi bi-calendar3 me-1"></i>Riwayat Absensi
        </a>
        <a href="{{ route('students.edit', $student) }}" class="btn btn-primary">
            <i class="bi bi-pencil me-1"></i>Edit
        </a>
        <a href="{{ route('students.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>
</div>

<div class="row g-4">
    {{-- Info Siswa --}}
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body p-4">
                <img src="{{ $student->photo_url }}" alt="{{ $student->name }}"
                    style="width:100px;height:100px;border-radius:50%;object-fit:cover;border:4px solid var(--color-brand-50);margin-bottom:12px">
                <h5 class="fw-bold mb-1">{{ $student->name }}</h5>
                <div class="text-muted mb-3" style="font-size:13px">{{ $student->nis }}</div>
                <span class="badge {{ $student->status ? 'bg-success' : 'bg-secondary' }} mb-3">
                    {{ $student->status ? 'Aktif' : 'Non-Aktif' }}
                </span>
                <div class="d-flex justify-content-center gap-2 flex-wrap">
                    <span class="badge" style="background:var(--color-brand-50);color:var(--color-brand-500)">{{ $student->class?->name }}</span>
                    <span class="badge" style="background:{{ $student->gender==='L' ? 'var(--color-brand-50)' : '#fce7f3' }};color:{{ $student->gender==='L' ? 'var(--color-brand-600)' : '#9d174d' }}">
                        {{ $student->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Info Pribadi --}}
        <div class="card mt-4">
            <div class="card-header"><i class="bi bi-person-lines-fill me-2 text-primary"></i>Info Pribadi</div>
            <div class="card-body">
                <dl class="mb-0">
                    <dt class="text-muted small">NISN</dt>
                    <dd class="fw-semibold mb-2">{{ $student->nisn ?? '-' }}</dd>
                    <dt class="text-muted small">Tanggal Lahir</dt>
                    <dd class="fw-semibold mb-2">{{ $student->dob?->translatedFormat('d F Y') ?? '-' }}</dd>
                    <dt class="text-muted small">Wali Kelas</dt>
                    <dd class="fw-semibold mb-2">{{ $student->class?->homeroomTeacher?->name ?? '-' }}</dd>
                    <dt class="text-muted small">Orang Tua</dt>
                    <dd class="fw-semibold mb-2">{{ $student->parent_name ?? '-' }}</dd>
                    <dt class="text-muted small">No. HP Ortu</dt>
                    <dd class="fw-semibold mb-2">{{ $student->parent_phone ?? '-' }}</dd>
                    <dt class="text-muted small">Alamat</dt>
                    <dd class="fw-semibold mb-0">{{ $student->address ?? '-' }}</dd>
                </dl>
            </div>
        </div>
    </div>

    {{-- Statistik Absensi --}}
    <div class="col-md-8">
        @php
            $total = $monthlyStats->sum();
            $hadir = $monthlyStats->get('hadir', 0) + $monthlyStats->get('terlambat', 0);
            $pct = $total > 0 ? round($hadir / $total * 100) : 0;
        @endphp
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-bar-chart me-2 text-primary"></i>Statistik Absensi Bulan Ini</span>
                <span class="badge {{ $pct >= 75 ? 'bg-success' : 'bg-danger' }}">{{ $pct }}% Hadir</span>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-3">
                    @foreach(['hadir'=>['Hadir','var(--color-brand-50)','#0058ff','bi-check-circle-fill'], 'terlambat'=>['Terlambat','#fef3c7','#f59e0b','bi-clock-fill'], 'sakit'=>['Sakit','var(--color-brand-50)','#3b82f6','bi-heart-pulse-fill'], 'izin'=>['Izin','#e0e7ff','#6366f1','bi-file-earmark-text-fill'], 'alfa'=>['Alfa','#fee2e2','#ef4444','bi-x-circle-fill']] as $status => [$label, $bg, $color, $icon])
                        <div class="col-6 col-md-4">
                            <div style="background:{{ $bg }};border-radius:10px;padding:14px;text-align:center">
                                <i class="bi {{ $icon }}" style="font-size:22px;color:{{ $color }}"></i>
                                <div style="font-size:26px;font-weight:800;color:{{ $color }};font-family:'JetBrains Mono',monospace">
                                    {{ $monthlyStats->get($status, 0) }}
                                </div>
                                <div style="font-size:11px;color:#64748b">{{ $label }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if($pct < 75)
                    <div class="alert alert-danger mb-0">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Kehadiran di bawah 75%. Perlu perhatian khusus!
                    </div>
                @endif
            </div>
        </div>

        {{-- Riwayat Absensi Terbaru --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-clock-history me-2 text-primary"></i>Riwayat Absensi Terbaru</span>
                <a href="{{ route('students.attendance-history', $student) }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Sesi</th>
                            <th>Status</th>
                            <th>Jam Masuk</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($student->attendanceDetails->take(10) as $detail)
                            <tr>
                                <td style="font-size:13px">{{ $detail->attendance->date->translatedFormat('d M Y') }}</td>
                                <td><span class="badge bg-secondary">{{ ucfirst($detail->attendance->session) }}</span></td>
                                <td><span class="badge badge-status-{{ $detail->status }}">{{ ucfirst($detail->status) }}</span></td>
                                <td style="font-size:13px;font-family:'JetBrains Mono',monospace">{{ $detail->check_in ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">Belum ada riwayat absensi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
