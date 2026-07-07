{{-- resources/views/attendance/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Daftar Absensi')
@section('page-title', 'Daftar Absensi')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <p class="text-muted mb-0">Semua sesi absensi yang tercatat</p>
        @role('admin,guru')
        <div class="d-flex gap-2">
            <a href="{{ route('attendance.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Input Absensi
            </a>
        </div>
        @endrole
    </div>

    {{-- Filter --}}
    <div class="card mb-4 border-0 shadow-sm" style="border-radius:1rem">
        <div class="card-body p-3">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted ms-1">Filter Kelas</label>
                    <select name="class_id" class="form-select">
                        <option value="">Semua Kelas</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted ms-1">Tanggal Spesifik</label>
                    <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted ms-1">Bulan</label>
                    <input type="month" name="month" class="form-control" value="{{ request('month') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 py-2 mt-3 mt-md-0"><i class="bi bi-funnel"></i> Filter</button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('attendance.index') }}" class="btn btn-light w-100 py-2 mt-3 mt-md-0">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius:1rem; overflow:hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-muted fw-bold small">TANGGAL</th>
                        <th class="py-3 text-muted fw-bold small">KELAS</th>
                        <th class="py-3 text-muted fw-bold small">SESI</th>
                        <th class="py-3 text-muted fw-bold small">GURU / PENGAJAR</th>
                        <th class="py-3 text-muted fw-bold small text-center">STATISTIK KEHADIRAN</th>
                        <th class="pe-4 py-3 text-muted fw-bold small text-end">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $att)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark">{{ $att->date->translatedFormat('d F Y') }}</div>
                                <div class="text-muted small">{{ $att->date->translatedFormat('l') }}</div>
                            </td>
                            <td>
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle px-3 py-2 rounded-pill">
                                    {{ $att->class?->name }}
                                </span>
                            </td>
                            <td>
                                @if($att->session === 'pagi')
                                    <span class="badge bg-warning bg-opacity-10 text-warning-emphasis border border-warning-subtle px-3 py-2 rounded-pill">PAGI</span>
                                @else
                                    <span class="badge bg-info bg-opacity-10 text-info-emphasis border border-info-subtle px-3 py-2 rounded-pill">SIANG</span>
                                @endif
                            </td>
                            <td>
                                <div class="small fw-bold text-dark">{{ $att->teacher?->name ?? 'System' }}</div>
                                <div class="text-muted small" style="font-size:11px">NIP: {{ $att->teacher?->nip ?? '-' }}</div>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <div class="text-center">
                                        <div class="small text-muted fw-bold" style="font-size:9px text-transform:uppercase">Hadir</div>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-2">{{ $att->details->whereIn('status', ['hadir', 'terlambat'])->count() }}</span>
                                    </div>
                                    <div class="text-center">
                                        <div class="small text-muted fw-bold" style="font-size:9px text-transform:uppercase">Izin/S</div>
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info-subtle px-2">{{ $att->details->whereIn('status', ['izin', 'sakit'])->count() }}</span>
                                    </div>
                                    <div class="text-center">
                                        <div class="small text-muted fw-bold" style="font-size:9px text-transform:uppercase">Alfa</div>
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle px-2">{{ $att->details->where('status', 'alfa')->count() }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="pe-4 text-end">
                                <div class="btn-group shadow-sm rounded-3 overflow-hidden">
                                    <a href="{{ route('attendance.show', $att) }}" class="btn btn-sm btn-white text-info" title="Detail"><i class="bi bi-eye-fill"></i></a>
                                    @role('admin,guru')
                                    <a href="{{ route('attendance.edit', $att) }}" class="btn btn-sm btn-white text-primary" title="Edit"><i class="bi bi-pencil-fill"></i></a>
                                    @endrole
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="py-4 text-muted">
                                    <i class="bi bi-clipboard-x opacity-25" style="font-size: 5rem"></i>
                                    <h5 class="mt-3">Data absensi belum tersedia</h5>
                                    <p class="small">Belum ada rekaman absensi untuk filter yang dipilih.</p>
                                    @role('admin,guru')
                                    <a href="{{ route('attendance.create') }}" class="btn btn-primary mt-2">
                                        <i class="bi bi-plus-lg me-1"></i>Input Absensi Baru
                                    </a>
                                    @endrole
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-top-0 d-flex justify-content-between align-items-center py-3">
            <div class="text-muted small">Menampilkan <strong>{{ $attendances->firstItem() ?? 0 }} - {{ $attendances->lastItem() ?? 0 }}</strong> dari <strong>{{ $attendances->total() }}</strong> sesi</div>
            {{ $attendances->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection
