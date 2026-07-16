{{-- resources/views/classes/edit.blade.php --}}
@extends('layouts.app')
@section('title', 'Edit Kelas')
@section('page-title', 'Edit Kelas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <p class="text-muted mb-0">Edit kelas: <strong>{{ $class->name }}</strong></p>
    <div class="d-flex gap-2">
        <a href="{{ route('classes.show', $class) }}" class="btn btn-outline-info"><i class="bi bi-eye me-1"></i>Detail</a>
        <a href="{{ route('classes.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
    </div>
</div>
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Data Kelas</div>
            <div class="card-body">
                <form method="POST" action="{{ route('classes.update', $class) }}">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Nama Kelas <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $class->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tingkat <span class="text-danger">*</span></label>
                            <select name="grade" class="form-select" required>
                                <option value="10" {{ old('grade', $class->grade) == 10 ? 'selected' : '' }}>X (10)</option>
                                <option value="11" {{ old('grade', $class->grade) == 11 ? 'selected' : '' }}>XI (11)</option>
                                <option value="12" {{ old('grade', $class->grade) == 12 ? 'selected' : '' }}>XII (12)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Jurusan <span class="text-muted small">(Opsional)</span></label>
                            <select name="major_id" class="form-select">
                                <option value="">-- Pilih Jurusan --</option>
                                @foreach($majors as $m)
                                    <option value="{{ $m->id }}" {{ old('major_id', $class->major_id) == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Ruang <span class="text-muted small">(Opsional)</span></label>
                            <input type="text" name="room" class="form-control" value="{{ old('room', $class->room) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Sesi Kelas <span class="text-danger">*</span></label>
                            <select name="session" class="form-select @error('session') is-invalid @enderror" required>
                                <option value="pagi" {{ old('session', $class->session ?? 'pagi') == 'pagi' ? 'selected' : '' }}>Pagi (Sesi Pagi)</option>
                                <option value="siang" {{ old('session', $class->session ?? 'pagi') == 'siang' ? 'selected' : '' }}>Siang (Sesi Siang)</option>
                            </select>
                            @error('session')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Wali Kelas <span class="text-muted small">(Opsional)</span></label>
                            <select name="homeroom_teacher_id" class="form-select">
                                <option value="">-- Pilih Wali Kelas --</option>
                                @foreach($teachers as $t)
                                    <option value="{{ $t->id }}" {{ old('homeroom_teacher_id', $class->homeroom_teacher_id) == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Tahun Ajaran</label>
                            <select name="academic_year_id" class="form-select">
                                @foreach($academicYears as $ay)
                                    <option value="{{ $ay->id }}" {{ old('academic_year_id', $class->academic_year_id) == $ay->id ? 'selected' : '' }}>
                                        {{ $ay->year }} {{ $ay->is_active ? '(Aktif)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Kapasitas</label>
                            <input type="number" name="max_students" class="form-control"
                                value="{{ old('max_students', $class->max_students) }}" min="1" max="50">
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-save me-2"></i>Simpan</button>
                        <button type="button" class="btn btn-outline-danger"
                            data-confirm="Hapus kelas {{ $class->name }}?" data-form="del-c{{ $class->id }}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </form>
                <form id="del-c{{ $class->id }}" method="POST" action="{{ route('classes.destroy', $class) }}">@csrf @method('DELETE')</form>
            </div>
        </div>
    </div>
</div>
@endsection
