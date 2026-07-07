{{-- resources/views/classes/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Tambah Kelas')
@section('page-title', 'Tambah Kelas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <p class="text-muted mb-0">Tambah kelas baru ke sistem</p>
    <a href="{{ route('classes.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
</div>
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><i class="bi bi-plus-circle me-2 text-primary"></i>Form Data Kelas</div>
            <div class="card-body">
                <form method="POST" action="{{ route('classes.store') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Nama Kelas <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name') }}" placeholder="Contoh: X IPA 1" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tingkat <span class="text-danger">*</span></label>
                            <select name="grade" class="form-select @error('grade') is-invalid @enderror" required>
                                <option value="">--</option>
                                <option value="10" {{ old('grade') == 10 ? 'selected' : '' }}>X (10)</option>
                                <option value="11" {{ old('grade') == 11 ? 'selected' : '' }}>XI (11)</option>
                                <option value="12" {{ old('grade') == 12 ? 'selected' : '' }}>XII (12)</option>
                            </select>
                            @error('grade')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Jurusan</label>
                            <select name="major_id" class="form-select">
                                <option value="">-- Pilih Jurusan --</option>
                                @foreach($majors as $m)
                                    <option value="{{ $m->id }}" {{ old('major_id') == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Ruang Kelas</label>
                            <input type="text" name="room" class="form-control"
                                value="{{ old('room') }}" placeholder="Contoh: R-101">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Wali Kelas</label>
                            <select name="homeroom_teacher_id" class="form-select">
                                <option value="">-- Pilih Wali Kelas --</option>
                                @foreach($teachers as $t)
                                    <option value="{{ $t->id }}" {{ old('homeroom_teacher_id') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Tahun Ajaran <span class="text-danger">*</span></label>
                            <select name="academic_year_id" class="form-select @error('academic_year_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Tahun Ajaran --</option>
                                @foreach($academicYears as $ay)
                                    <option value="{{ $ay->id }}" {{ (old('academic_year_id') == $ay->id || $ay->is_active) ? 'selected' : '' }}>
                                        {{ $ay->year }} {{ $ay->is_active ? '(Aktif)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('academic_year_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Kapasitas</label>
                            <input type="number" name="max_students" class="form-control"
                                value="{{ old('max_students', 36) }}" min="1" max="50">
                        </div>
                    </div>
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-save me-2"></i>Simpan Kelas</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
