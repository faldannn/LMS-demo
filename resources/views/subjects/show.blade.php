@extends('layouts.app')
@section('title', $subject->name)
@section('page-title', 'Detail Mata Pelajaran')
@section('content')
<div class="space-y-4">
    <x-detail-header>
        <div>
            <p class="mb-1 text-xs font-bold uppercase tracking-wider text-slate-400">Detail Mata Pelajaran</p>
            <h5 class="mb-1 fw-bold">{{ $subject->name }}</h5>
            <div class="d-flex flex-wrap align-items-center gap-2 text-muted" style="font-size:13px">
                <code>{{ $subject->code }}</code>
                <span>•</span>
                <span>{{ $subject->group ?? 'Kelompok belum ditentukan' }}</span>
            </div>
        </div>

        <x-slot:actions>
            <a href="{{ route('subjects.edit', $subject) }}" class="btn btn-primary btn-sm"><i class="bi bi-pencil me-1"></i>Edit</a>
            <a href="{{ route('subjects.index') }}" class="btn btn-light btn-sm border"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
        </x-slot:actions>
    </x-detail-header>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="d-flex h-12 w-12 align-items-center justify-content-center rounded-3 bg-primary bg-opacity-10 text-primary" style="width:48px;height:48px">
                            <i class="bi bi-book-half fs-4"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Mata Pelajaran</div>
                            <h4 class="mb-0 fw-bold text-slate-900">{{ $subject->name }}</h4>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-6">
                            <div class="rounded-3 bg-slate-50 p-3">
                                <div class="text-muted small text-uppercase fw-semibold">Kode</div>
                                <div class="fw-bold">{{ $subject->code }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="rounded-3 bg-slate-50 p-3">
                                <div class="text-muted small text-uppercase fw-semibold">Kelompok</div>
                                <div class="fw-bold">{{ $subject->group ?? '—' }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="rounded-3 bg-slate-50 p-3">
                                <div class="text-muted small text-uppercase fw-semibold">Target Kelas</div>
                                <div class="fw-bold">{{ $subject->grade ? 'Kelas ' . $subject->grade : 'Semua kelas' }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="rounded-3 bg-slate-50 p-3">
                                <div class="text-muted small text-uppercase fw-semibold">Status</div>
                                <div class="fw-bold">
                                    <span class="badge {{ $subject->status ? 'bg-success' : 'bg-secondary' }}">{{ $subject->status ? 'Aktif' : 'Non-Aktif' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($subject->description)
                        <div class="mt-4 rounded-3 border border-slate-200 bg-white p-3">
                            <div class="text-muted small text-uppercase fw-semibold mb-2">Deskripsi</div>
                            <div class="text-slate-700" style="font-size:13px; line-height:1.7">{{ $subject->description }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <span class="fw-bold"><i class="bi bi-diagram-3 me-2 text-primary"></i>Penggunaan di Kelas</span>
                    <span class="badge bg-primary">{{ $subject->classSubjects->count() }} kelas</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>Kelas</th>
                                <th>Guru</th>
                                <th>Tahun Ajaran</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($subject->classSubjects as $cs)
                                <tr>
                                    <td class="fw-semibold">Kelas {{ $cs->class?->name ?? '-' }}</td>
                                    <td>{{ $cs->teacher?->name ?? '-' }}</td>
                                    <td>{{ $cs->academicYear?->year ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">Belum digunakan pada kelas mana pun.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
