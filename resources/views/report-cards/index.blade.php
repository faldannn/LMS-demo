@extends('layouts.app')
@section('title', 'Penilaian & Raport')
@section('page-title', 'Penilaian & Raport Digital')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form action="{{ route('report-cards.index') }}" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label text-muted small">Pilih Kelas</label>
                        <select name="class_id" class="form-select" onchange="this.form.submit()">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}" {{ $selectedClassId == $c->id ? 'selected' : '' }}>
                                    {{ $c->name }} ({{ $c->students_count }} Siswa)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small">Semester</label>
                        <select name="semester" class="form-select" onchange="this.form.submit()">
                            <option value="ganjil" {{ $semester == 'ganjil' ? 'selected' : '' }}>Ganjil</option>
                            <option value="genap" {{ $semester == 'genap' ? 'selected' : '' }}>Genap</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>
        
        @if($selectedClassId)
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">Data Raport Siswa</h6>
                    @if(!$academicYear)
                        <span class="text-danger small">Tidak ada tahun ajaran aktif!</span>
                    @else
                        <form action="{{ route('report-cards.generate') }}" method="POST">
                            @csrf
                            <input type="hidden" name="class_id" value="{{ $selectedClassId }}">
                            <input type="hidden" name="semester" value="{{ $semester }}">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="bi bi-arrow-repeat me-1"></i> Generate / Update Raport
                            </button>
                        </form>
                    @endif
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">Siswa</th>
                                    <th>NIS</th>
                                    <th>Status Publikasi</th>
                                    <th>Terakhir Diupdate</th>
                                    <th class="text-end pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reportCards as $rc)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold">{{ $rc->student->name }}</div>
                                    </td>
                                    <td>{{ $rc->student->nis }}</td>
                                    <td>
                                        @if($rc->is_published)
                                            <span class="badge bg-success bg-opacity-10 text-success">Dipublikasi</span>
                                        @else
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary">Draft</span>
                                        @endif
                                    </td>
                                    <td class="text-muted small">{{ $rc->updated_at->diffForHumans() }}</td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group">
                                            <form action="{{ route('report-cards.toggle-publish', $rc) }}" method="POST">
                                                @csrf @method('PUT')
                                                <button type="submit" class="btn btn-sm {{ $rc->is_published ? 'btn-outline-warning' : 'btn-outline-success' }}" title="{{ $rc->is_published ? 'Tarik Publikasi' : 'Publikasikan' }}">
                                                    <i class="bi {{ $rc->is_published ? 'bi-eye-slash' : 'bi-eye' }}"></i>
                                                </button>
                                            </form>
                                            <a href="{{ route('report-cards.show', $rc) }}" class="btn btn-sm btn-light border" title="Lihat Detail">
                                                <i class="bi bi-list-columns"></i>
                                            </a>
                                            <a href="{{ route('report-cards.print', $rc) }}" target="_blank" class="btn btn-sm btn-primary" title="Cetak PDF">
                                                <i class="bi bi-printer"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        Belum ada raport yang di-generate untuk kelas ini pada semester ini.<br>
                                        Silakan klik tombol <strong>Generate Raport</strong> di atas.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-5 text-muted">
                <i class="bi bi-file-earmark-bar-graph display-1 mb-3 text-light"></i>
                <h5>Pilih Kelas</h5>
                <p>Silakan pilih kelas terlebih dahulu untuk melihat dan mengelola raport.</p>
            </div>
        @endif
    </div>
</div>
@endsection
