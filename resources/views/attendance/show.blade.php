@extends('layouts.app')
@section('title', 'Detail Absensi')
@section('page-title', 'Detail Absensi')

@section('content')
    <x-detail-header>
        <div>
            <p class="mb-1 text-xs font-bold uppercase tracking-wider text-slate-400">Detail Absensi</p>
            <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                <span class="badge bg-primary">{{ ucfirst($attendance->session) }}</span>
                <span class="fw-bold" style="font-size:16px">{{ $attendance->date->translatedFormat('l, d F Y') }}</span>
            </div>
            <span class="text-muted" style="font-size:13px">{{ $attendance->class?->name ?? '-' }} · {{ $attendance->teacher?->name ?? '-' }}</span>
        </div>

        <x-slot:actions>
            @role('admin,guru')
                <a href="{{ route('attendance.edit', $attendance) }}" class="btn btn-primary">
                    <i class="bi bi-pencil me-1"></i>Edit Absensi
                </a>
            @endrole
            <a href="{{ route('attendance.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Kembali
            </a>
        </x-slot:actions>
    </x-detail-header>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-card text-center">
                <div class="stat-icon mx-auto" style="background:var(--color-brand-50);color:var(--color-brand-500)"><i class="bi bi-check-circle-fill"></i></div>
                <div style="font-size:32px;font-weight:800;font-family:'JetBrains Mono',monospace;color:var(--color-brand-500)">{{ $attendance->details->where('status','hadir')->count() }}</div>
                <div class="text-muted small">Hadir</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card text-center">
                <div class="stat-icon mx-auto" style="background:#fef3c7;color:#f59e0b"><i class="bi bi-clock-fill"></i></div>
                <div style="font-size:32px;font-weight:800;font-family:'JetBrains Mono',monospace;color:#f59e0b">{{ $attendance->details->where('status','terlambat')->count() }}</div>
                <div class="text-muted small">Terlambat</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card text-center">
                <div class="stat-icon mx-auto" style="background:#fee2e2;color:#ef4444"><i class="bi bi-x-circle-fill"></i></div>
                <div style="font-size:32px;font-weight:800;font-family:'JetBrains Mono',monospace;color:#ef4444">{{ $attendance->details->where('status','alfa')->count() }}</div>
                <div class="text-muted small">Alfa</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card text-center">
                <div class="stat-icon mx-auto" style="background:var(--color-brand-50);color:var(--color-brand-600)"><i class="bi bi-file-earmark-text-fill"></i></div>
                <div style="font-size:32px;font-weight:800;font-family:'JetBrains Mono',monospace;color:var(--color-brand-600)">{{ $attendance->details->whereIn('status',['sakit','izin'])->count() }}</div>
                <div class="text-muted small">Sakit / Izin</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-3">
            {{-- Filter Absensi --}}
            <div class="card mb-4 shadow-sm border-slate-200/60 rounded-2xl overflow-hidden bg-white">
                <div class="card-header bg-slate-50/80 border-bottom border-slate-100 px-4 py-3 fw-bold text-slate-800 flex items-center gap-2">
                    <i class="bi bi-funnel-fill text-brand-600"></i> Cari Absensi Lain
                </div>
                <div class="card-body p-4">
                    <form method="GET" action="{{ route('attendance.show', $attendance) }}">
                        <div class="mb-3">
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-400">Tanggal</label>
                            <input type="date" name="date" class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-800 focus:border-brand-500 focus:bg-white focus:ring-2 focus:ring-brand-500/20 transition-all outline-none" value="{{ $attendance->date->toDateString() }}" required>
                        </div>
                        <div class="mb-4">
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-400">Sesi</label>
                            <select name="session" class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-800 focus:border-brand-500 focus:bg-white focus:ring-2 focus:ring-brand-500/20 transition-all outline-none" required>
                                <option value="pagi" {{ $attendance->session === 'pagi' ? 'selected' : '' }}>Pagi</option>
                                <option value="siang" {{ $attendance->session === 'siang' ? 'selected' : '' }}>Siang</option>
                            </select>
                        </div>
                        <button type="submit" class="inline-flex w-full items-center justify-center gap-1.5 rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-bold text-white shadow-md hover:bg-brand-700 hover:scale-[1.01] active:scale-[0.99] transition-all border-0">
                            <i class="bi bi-search"></i> Cari
                        </button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><i class="bi bi-info-circle me-2 text-primary"></i>Info Sesi</div>
                <div class="card-body">
                    <dl class="mb-0">
                        <dt class="text-muted small">Kelas</dt>
                        <dd class="fw-semibold mb-2">{{ $attendance->class?->name ?? '-' }}</dd>
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
                <div class="card-header flex-wrap gap-2">
                    <span><i class="bi bi-list-check me-2 text-primary"></i>Daftar Kehadiran Siswa</span>
                    <span class="badge bg-primary">{{ $attendance->details->count() }} siswa</span>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th style="width:64px">No</th>
                                <th>Nama Siswa</th>
                                <th>Status</th>
                                <th>Jam Masuk</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attendance->details->sortBy('student.name')->values() as $i => $detail)
                                @php $student = $detail->student; @endphp
                                <tr>
                                    <td class="fw-semibold text-muted">{{ $i + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3 min-w-[240px]">
                                            <img src="{{ $student?->photo_url ?? 'https://ui-avatars.com/api/?name=Siswa&background=e2e8f0&color=475569' }}" class="h-10 w-10 rounded-full object-cover border border-slate-200" alt="Foto siswa">
                                            <div class="min-w-0">
                                                <div class="fw-bold text-slate-900 truncate">{{ $student?->name ?? 'Data siswa tidak ditemukan' }}</div>
                                                <div class="text-muted small">NIS: {{ $student?->nis ?? '-' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-status-{{ $detail->status }}">
                                            {{ $detail->status_label }}
                                        </span>
                                    </td>
                                    <td><code>{{ $detail->check_in ? $detail->check_in->format('H:i') : '-' }}</code></td>
                                    <td class="small text-muted">{{ $detail->notes ?: '-' }}</td>
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
