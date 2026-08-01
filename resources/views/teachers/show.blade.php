{{-- resources/views/teachers/show.blade.php --}}
@extends('layouts.app')
@section('title', 'Detail Guru')
@section('page-title', 'Detail Guru')

@section('content')
<x-detail-header>
    <div>
        <p class="mb-1 text-xs font-bold uppercase tracking-wider text-slate-400">Detail Guru</p>
        <h5 class="fw-bold mb-1">{{ $teacher->name }}</h5>
        <span class="text-muted" style="font-size:13px">{{ $teacher->nip ?? 'Tanpa NIP' }}</span>
    </div>

    <x-slot:actions>
        <a href="{{ route('teachers.edit', $teacher) }}" class="btn btn-primary"><i class="bi bi-pencil me-1"></i>Edit</a>
        <a href="{{ route('teachers.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
    </x-slot:actions>
</x-detail-header>

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
                    <dd class="fw-semibold mb-2">
                        <div class="flex flex-wrap gap-1 mt-1">
                            @forelse($teacher->subjects as $subj)
                                <span class="badge" style="background:#ede9fe;color:#5b21b6">{{ $subj->name }}</span>
                            @empty
                                <span class="badge" style="background:#ede9fe;color:#5b21b6">{{ $teacher->subject ?? '-' }}</span>
                            @endforelse
                        </div>
                    </dd>
                    <dt class="text-muted small">No. HP</dt>
                    <dd class="fw-semibold mb-2">{{ $teacher->phone ?? '-' }}</dd>
                    <dt class="text-muted small">Email</dt>
                    <dd class="fw-semibold mb-2">{{ $teacher->email ?? '-' }}</dd>
                    <dt class="text-muted small">Username</dt>
                    <dd class="fw-semibold mb-0"><code>{{ $teacher->user?->username ?? '-' }}</code></dd>
                </dl>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-building me-2 text-primary"></i>Wali Kelas</span>
                @role('kepala_sekolah')
                    <button type="button" class="btn btn-xs btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editHomeroomModal" style="padding: 2px 6px; font-size: 11px;">
                        <i class="bi bi-gear"></i> Atur
                    </button>
                @endrole
            </div>
            <div class="card-body">
                @if($teacher->homeroomClasses->count())
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($teacher->homeroomClasses as $cls)
                            <span class="badge" style="background:var(--color-brand-50);color:var(--color-brand-600);font-size:12px;padding:6px 10px">
                                {{ $cls->name }}
                            </span>
                        @endforeach
                    </div>
                @else
                    <span class="text-slate-400 small">Belum mengampu kelas manapun.</span>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-8">
        {{-- Tugas Mengajar (Class Subjects) --}}
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-book-half me-2 text-primary"></i>Mata Pelajaran & Kelas yang Diajar</span>
                @role('kepala_sekolah')
                    <button type="button" class="btn btn-xs btn-primary" data-bs-toggle="modal" data-bs-target="#addClassSubjectModal" style="padding: 4px 10px; font-size: 11px;">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Tugas Mengajar
                    </button>
                @endrole
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Kelas</th>
                            <th>Mata Pelajaran</th>
                            <th>Tahun Ajaran</th>
                            @role('kepala_sekolah')
                                <th class="text-end">Aksi</th>
                            @endrole
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($teacher->classSubjects as $cs)
                            <tr>
                                <td class="fw-bold text-slate-800">Kelas {{ $cs->class->name }}</td>
                                <td>
                                    <strong>{{ $cs->subject->name }}</strong>
                                    <div class="text-muted small">Code: {{ $cs->subject->code ?? '-' }}</div>
                                </td>
                                <td>{{ $cs->academicYear->year }}</td>
                                @role('kepala_sekolah')
                                    <td class="text-end">
                                        <form method="POST" action="{{ route('teachers.subjects.destroy', [$teacher->id, $cs->id]) }}" onsubmit="return confirm('Hapus tugas mengajar mata pelajaran ini dari guru?')" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" style="padding: 2px 6px">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                @endrole
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">Belum ada tugas mengajar yang dikonfigurasi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

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
@role('kepala_sekolah')
{{-- Modal Atur Wali Kelas --}}
<div class="modal fade" id="editHomeroomModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('teachers.homeroom.update', $teacher->id) }}" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Atur Wali Kelas — {{ $teacher->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-xs text-slate-500 mb-3">
                    Pilih <strong>satu kelas</strong> yang diampu oleh {{ $teacher->name }} sebagai Wali Kelas.
                    Seorang guru hanya dapat menjadi wali kelas untuk <strong>1 kelas</strong> saja.
                </p>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Pilih Kelas</label>
                    <select name="class_id" class="form-select">
                        <option value="">— Tidak ada / Lepas Wali Kelas —</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}"
                                {{ $c->homeroom_teacher_id === $teacher->id ? 'selected' : '' }}>
                                Kelas {{ $c->name }}
                                @if($c->homeroomTeacher && $c->homeroom_teacher_id !== $teacher->id)
                                    (Saat ini: {{ $c->homeroomTeacher->name }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text text-amber-600 mt-1">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        Jika kelas yang dipilih sudah memiliki wali kelas lain, wali kelas tersebut akan dilepas otomatis.
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Tambah Tugas Mengajar --}}
<div class="modal fade" id="addClassSubjectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('teachers.subjects.store', $teacher->id) }}" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Tambah Tugas Mengajar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Pilih Kelas</label>
                    <select name="class_id" class="form-select" required>
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}">Kelas {{ $c->name }} (Tingkat {{ $c->grade }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Pilih Mata Pelajaran</label>
                    <select name="subject_id" class="form-select" required>
                        <option value="">-- Pilih Mata Pelajaran --</option>
                        @foreach($teacher->subjects as $sub)
                            <option value="{{ $sub->id }}">{{ $sub->name }} ({{ $sub->code }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endrole

@endsection
