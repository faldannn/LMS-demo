{{-- resources/views/teachers/show.blade.php --}}
@extends('layouts.app')
@section('title', 'Detail Guru')
@section('page-title', 'Detail Guru')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div></div>
    <div class="d-flex gap-2">
        <a href="{{ route('teachers.edit', $teacher) }}" class="btn btn-primary"><i class="bi bi-pencil me-1"></i>Edit</a>
        <a href="{{ route('teachers.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body p-4">
                @if($teacher->photo)
                    <img src="{{ Storage::url($teacher->photo) }}" alt="{{ $teacher->name }}"
                        style="width:100px;height:100px;border-radius:50%;object-fit:cover;border:4px solid var(--color-brand-50);margin-bottom:12px">
                @else
                    <div style="width:100px;height:100px;border-radius:50%;background:linear-gradient(135deg,var(--color-brand-600),var(--color-brand-500));display:grid;place-items:center;font-size:36px;font-weight:800;color:#fff;margin:0 auto 12px">
                        {{ strtoupper(substr($teacher->name, 0, 1)) }}
                    </div>
                @endif
                <h5 class="fw-bold mb-1">{{ $teacher->name }}</h5>
                <div class="text-muted mb-2" style="font-size:13px">{{ $teacher->nip ?? 'Tanpa NIP' }}</div>
                <span class="badge {{ $teacher->status ? 'bg-success' : 'bg-secondary' }}">
                    {{ $teacher->status ? 'Aktif' : 'Non-Aktif' }}
                </span>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header"><i class="bi bi-info-circle me-2 text-primary"></i>Informasi</div>
            <div class="card-body">
                <dl class="mb-0">
                    <dt class="text-muted small">Mata Pelajaran</dt>
                    <dd class="fw-semibold mb-2"><span class="badge" style="background:#ede9fe;color:#5b21b6">{{ $teacher->subject ?? '-' }}</span></dd>
                    <dt class="text-muted small">No. HP</dt>
                    <dd class="fw-semibold mb-2">{{ $teacher->phone ?? '-' }}</dd>
                    <dt class="text-muted small">Email</dt>
                    <dd class="fw-semibold mb-2">{{ $teacher->email ?? '-' }}</dd>
                    <dt class="text-muted small">Username</dt>
                    <dd class="fw-semibold mb-0"><code>{{ $teacher->user?->username ?? '-' }}</code></dd>
                </dl>
            </div>
        </div>

        @if($teacher->homeroomClasses->count())
            <div class="card mt-4">
                <div class="card-header"><i class="bi bi-building me-2 text-primary"></i>Wali Kelas</div>
                <div class="card-body">
                    @foreach($teacher->homeroomClasses as $cls)
                        <span class="badge me-1 mb-1" style="background:var(--color-brand-50);color:var(--color-brand-600);font-size:13px;padding:6px 10px">
                            {{ $cls->name }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <div class="col-md-8">
        {{-- Jadwal mengajar --}}
        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-calendar3 me-2 text-primary"></i>Jadwal Mengajar</div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr><th>Hari</th><th>Mata Pelajaran</th><th>Kelas</th><th>Jam</th><th>Ruang</th></tr>
                    </thead>
                    <tbody>
                        @php $days = ['','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu']; @endphp
                        @forelse($teacher->schedules as $sched)
                            <tr>
                                <td><span class="badge bg-secondary">{{ $days[$sched->day_of_week] }}</span></td>
                                <td class="fw-semibold">{{ $sched->subject }}</td>
                                <td>{{ $sched->class?->name }}</td>
                                <td style="font-family:'JetBrains Mono',monospace;font-size:12px">
                                    {{ substr($sched->start_time,0,5) }} – {{ substr($sched->end_time,0,5) }}
                                </td>
                                <td>{{ $sched->room ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">Belum ada jadwal mengajar.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Absensi terbaru --}}
        <div class="card">
            <div class="card-header"><i class="bi bi-clock-history me-2 text-primary"></i>10 Absensi Terakhir yang Diisi</div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr><th>Tanggal</th><th>Kelas</th><th>Sesi</th><th>Total Siswa</th></tr>
                    </thead>
                    <tbody>
                        @forelse($teacher->attendances as $att)
                            <tr>
                                <td style="font-size:13px">{{ $att->date->translatedFormat('d M Y') }}</td>
                                <td>{{ $att->class?->name }}</td>
                                <td><span class="badge bg-secondary">{{ ucfirst($att->session) }}</span></td>
                                <td>{{ $att->details_count ?? $att->details()->count() }} siswa</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">Belum ada riwayat pengisian absensi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
