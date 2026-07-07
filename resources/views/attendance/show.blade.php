{{-- resources/views/attendance/show.blade.php --}}
@extends('layouts.app')
@section('title', 'Detail Absensi')
@section('page-title', 'Detail Absensi')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <span class="badge bg-primary me-2">{{ ucfirst($attendance->session) }}</span>
            <span class="fw-bold" style="font-size:16px">{{ $attendance->date->translatedFormat('l, d F Y') }}</span>
        </div>
        <div class="d-flex gap-2">
            @role('admin,guru')
            <a href="{{ route('attendance.edit', $attendance) }}" class="btn btn-primary">
                <i class="bi bi-pencil me-1"></i>Edit Absensi
            </a>
            @endrole
            <a href="{{ route('attendance.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Kembali
            </a>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card text-center">
                <div class="stat-icon mx-auto" style="background:var(--color-brand-50);color:var(--color-brand-500)"><i class="bi bi-check-circle-fill"></i></div>
                <div style="font-size:32px;font-weight:800;font-family:'JetBrains Mono',monospace;color:var(--color-brand-500)">{{ $attendance->details->where('status','hadir')->count() }}</div>
                <div class="text-muted" style="font-size:12px">Hadir</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card text-center">
                <div class="stat-icon mx-auto" style="background:#fef3c7;color:#f59e0b"><i class="bi bi-clock-fill"></i></div>
                <div style="font-size:32px;font-weight:800;font-family:'JetBrains Mono',monospace;color:#f59e0b">{{ $attendance->details->where('status','terlambat')->count() }}</div>
                <div class="text-muted" style="font-size:12px">Terlambat</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card text-center">
                <div class="stat-icon mx-auto" style="background:#fee2e2;color:#ef4444"><i class="bi bi-x-circle-fill"></i></div>
                <div style="font-size:32px;font-weight:800;font-family:'JetBrains Mono',monospace;color:#ef4444">{{ $attendance->details->where('status','alfa')->count() }}</div>
                <div class="text-muted" style="font-size:12px">Alfa</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card text-center">
                <div class="stat-icon mx-auto" style="background:var(--color-brand-50);color:var(--color-brand-600)"><i class="bi bi-file-earmark-text-fill"></i></div>
                <div style="font-size:32px;font-weight:800;font-family:'JetBrains Mono',monospace;color:var(--color-brand-600)">{{ $attendance->details->whereIn('status',['sakit','izin'])->count() }}</div>
                <div class="text-muted" style="font-size:12px">Sakit / Izin</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-header"><i class="bi bi-info-circle me-2 text-primary"></i>Info Sesi</div>
                <div class="card-body">
                    <dl class="mb-0">
                        <dt class="text-muted small">Kelas</dt>
                        <dd class="fw-semibold mb-2">{{ $attendance->class?->name }}</dd>
                        <dt class="text-muted small">Guru Pengisi</dt>
                        <dd class="fw-semibold mb-2">{{ $attendance->teacher?->name ?? '-' }}</dd>
                        <dt class="text-muted small">Sesi</dt>
                        <dd class="fw-semibold mb-2">{{ ucfirst($attendance->session) }}</dd>
                        <dt class="text-muted small">Tanggal</dt>
                        <dd class="fw-semibold mb-0">{{ $attendance->date->translatedFormat('d F Y') }}</dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <span><i class="bi bi-list-check me-2 text-primary"></i>Daftar Kehadiran Siswa</span>
                    <span class="badge bg-primary">{{ $attendance->details->count() }} siswa</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Siswa</th>
                                <th>Status</th>
                                <th>Jam Masuk</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attendance->details->sortBy('student.name') as $i => $detail)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="student-num">{{ $i + 1 }}</div>
                                            <div>
                                                <div class="fw-semibold">{{ $detail->student->name }}</div>
                                                <div class="text-muted" style="font-size:11px">{{ $detail->student->nis }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-status-{{ $detail->status }}">
                                            @switch($detail->status)
                                                @case('hadir') ✅ Hadir @break
                                                @case('terlambat') ⏰ Terlambat @break
                                                @case('sakit') 🤒 Sakit @break
                                                @case('izin') 📄 Izin @break
                                                @case('alfa') ❌ Alfa @break
                                                @default {{ ucfirst($detail->status) }}
                                            @endswitch
                                        </span>
                                    </td>
                                    <td style="font-family:'JetBrains Mono',monospace;font-size:13px">{{ $detail->check_in ?? '-' }}</td>
                                    <td style="font-size:12px;color:#64748b">{{ $detail->notes ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-4">Belum ada detail absensi.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
