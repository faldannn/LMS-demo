@extends('layouts.app')
@section('title', 'Tahun Ajaran ' . $academicYear->year)
@section('page-title', 'Detail Tahun Ajaran')

@section('content')
<div class="space-y-6">
    <x-detail-header>
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Tahun Ajaran</span>
                @if($academicYear->is_active)
                    <span class="badge bg-emerald-100 text-emerald-800 border border-emerald-200 rounded-pill px-2.5 py-0.5 text-xs">
                        <i class="bi bi-check-circle-fill me-1"></i> Aktif Berjalan
                    </span>
                @else
                    <span class="badge bg-slate-100 text-slate-600 rounded-pill px-2.5 py-0.5 text-xs">
                        Non-Aktif
                    </span>
                @endif
            </div>
            <h4 class="fw-bold mb-1 text-slate-900">{{ $academicYear->year }}</h4>
            <span class="text-muted" style="font-size:13px">{{ $academicYear->description ?: 'Tidak ada deskripsi tambahan' }}</span>
        </div>

        <x-slot:actions>
            <button type="button" class="btn btn-outline-primary btn-sm d-inline-flex align-items-center gap-1.5" data-bs-toggle="modal" data-bs-target="#modalCopyClasses">
                <i class="bi bi-copy"></i>
                <span>Salin Kelas dari TA Lain</span>
            </button>
            <a href="{{ route('academic-years.edit', $academicYear) }}" class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1.5">
                <i class="bi bi-pencil"></i>
                <span>Edit</span>
            </a>
            <a href="{{ route('academic-years.index') }}" class="btn btn-light btn-sm border d-inline-flex align-items-center gap-1.5">
                <i class="bi bi-arrow-left"></i>
                <span>Kembali</span>
            </a>
        </x-slot:actions>
    </x-detail-header>

    {{-- Semester Card --}}
    <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/70 overflow-hidden">
        <div class="border-b border-slate-100 bg-slate-50/80 px-5 py-3.5 font-bold text-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="bi bi-calendar-event text-brand-600"></i>
                <span>Periode Semester</span>
            </div>
            <span class="badge bg-brand-50 text-brand-700 border border-brand-200">{{ $academicYear->semesters->count() }} Semester</span>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="bg-slate-50/50 text-xs font-semibold uppercase text-slate-500">
                    <tr>
                        <th class="ps-5">Semester</th>
                        <th>Tanggal Mulai</th>
                        <th>Tanggal Selesai</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($academicYear->semesters as $s)
                        <tr>
                            <td class="ps-5 fw-bold text-slate-800">{{ $s->label }}</td>
                            <td>{{ $s->start_date ? $s->start_date->format('d/m/Y') : '-' }}</td>
                            <td>{{ $s->end_date ? $s->end_date->format('d/m/Y') : '-' }}</td>
                            <td class="text-center">
                                @if($s->is_active)
                                    <span class="badge bg-emerald-100 text-emerald-800 border border-emerald-200">Aktif</span>
                                @else
                                    <span class="badge bg-slate-100 text-slate-600">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-slate-400">Belum ada data semester untuk tahun ajaran ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Classes in this Academic Year --}}
    <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/70 overflow-hidden">
        <div class="border-b border-slate-100 bg-slate-50/80 px-5 py-3.5 font-bold text-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="bi bi-door-open text-brand-600"></i>
                <span>Daftar Kelas pada TA {{ $academicYear->year }}</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="badge bg-brand-50 text-brand-700 border border-brand-200 font-mono">{{ $academicYear->classes->count() }} Kelas</span>
                <a href="{{ route('classes.create') }}" class="btn btn-sm btn-primary py-1 px-2.5 text-xs">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Kelas
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-slate-50/50 text-xs font-semibold uppercase text-slate-500">
                    <tr>
                        <th class="ps-5">Nama Kelas</th>
                        <th>Tingkat</th>
                        <th>Jurusan</th>
                        <th>Wali Kelas</th>
                        <th>Ruangan</th>
                        <th class="text-center">Jumlah Siswa</th>
                        <th class="text-end pe-5">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($academicYear->classes as $class)
                        <tr>
                            <td class="ps-5">
                                <span class="fw-bold text-slate-900">{{ $class->name }}</span>
                                <span class="badge bg-slate-100 text-slate-600 text-xs ms-1">{{ strtoupper($class->session ?? 'pagi') }}</span>
                            </td>
                            <td><span class="badge bg-sky-100 text-sky-800">Kelas {{ $class->grade }}</span></td>
                            <td>{{ $class->major?->name ?? 'Umum / Non-Jurusan' }}</td>
                            <td>{{ $class->homeroomTeacher?->name ?? 'Belum Ditugaskan' }}</td>
                            <td><code>{{ $class->room ?: '-' }}</code></td>
                            <td class="text-center font-mono">
                                <span class="badge {{ $class->students_count > 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $class->students_count }} Siswa
                                </span>
                            </td>
                            <td class="text-end pe-5">
                                <a href="{{ route('classes.show', $class) }}" class="btn btn-sm btn-outline-primary" title="Detail Kelas">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 text-slate-400">
                                <i class="bi bi-folder-x fs-1 d-block mb-2 text-slate-300"></i>
                                Belum ada kelas yang terdaftar pada tahun ajaran ini.
                                <div class="mt-3">
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalCopyClasses">
                                        <i class="bi bi-copy me-1"></i> Salin Struktur Kelas dari TA Sebelumnya
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Copy Classes --}}
<div class="modal fade" id="modalCopyClasses" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-2xl border-0 shadow-lg">
            <form action="{{ route('academic-years.duplicate-classes', $academicYear) }}" method="POST">
                @csrf
                <div class="modal-header border-b border-slate-100 bg-slate-50 px-4 py-3">
                    <h6 class="modal-title fw-bold text-slate-800">
                        <i class="bi bi-copy me-2 text-primary"></i> Salin Kelas ke TA {{ $academicYear->year }}
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 space-y-3">
                    <p class="text-sm text-slate-600 mb-3">
                        Menyalin seluruh susunan kelas (tingkat, jurusan, ruang, kapasitas) dari tahun ajaran sumber ke tahun ajaran <strong>{{ $academicYear->year }}</strong> tanpa membawa siswanya.
                    </p>
                    <div>
                        <label class="form-label fw-semibold text-xs uppercase text-slate-600">Pilih Tahun Ajaran Sumber</label>
                        <select name="source_academic_year_id" class="form-select text-sm" required>
                            <option value="">-- Pilih TA Sumber --</option>
                            @foreach($allYears as $sy)
                                <option value="{{ $sy->id }}">
                                    {{ $sy->year }} ({{ $sy->classes()->count() }} Kelas) {{ $sy->is_active ? '— Aktif' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" name="copy_homeroom" value="1" id="copyHomeroomDetail" checked>
                        <label class="form-check-label text-sm text-slate-700" for="copyHomeroomDetail">
                            Salin juga penugasan Wali Kelas
                        </label>
                    </div>
                </div>
                <div class="modal-footer border-t border-slate-100 bg-slate-50 px-4 py-3">
                    <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="bi bi-check-lg me-1"></i> Salin Kelas Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
