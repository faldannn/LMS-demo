@extends('layouts.app')
@section('title', 'Edit Tugas')
@section('page-title', 'Edit Tugas')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit Tugas: {{ $classSubject->subject->name }}</h6>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('assignments.update', $assignment) }}" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label">Judul Tugas <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $assignment->title) }}" required>
                        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Tipe Tugas <span class="text-danger">*</span></label>
                            <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                <option value="individu" {{ old('type', $assignment->type) == 'individu' ? 'selected' : '' }}>Individu</option>
                                <option value="kelompok" {{ old('type', $assignment->type) == 'kelompok' ? 'selected' : '' }}>Kelompok</option>
                            </select>
                            @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Batas Waktu (Tenggat)</label>
                            <input type="datetime-local" name="due_date" class="form-control @error('due_date') is-invalid @enderror" value="{{ old('due_date', $assignment->due_date ? $assignment->due_date->format('Y-m-d\TH:i') : '') }}">
                            @error('due_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Nilai Maksimal <span class="text-danger">*</span></label>
                            <input type="number" name="max_score" class="form-control @error('max_score') is-invalid @enderror" value="{{ old('max_score', $assignment->max_score) }}" min="1" max="100" required>
                            @error('max_score') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ganti File Lampiran (Opsional)</label>
                        <input type="file" name="file" class="form-control @error('file') is-invalid @enderror">
                        @if($assignment->file_name)
                            <div class="form-text text-muted">File saat ini: {{ $assignment->file_name }} (Biarkan kosong jika tidak ingin mengubah)</div>
                        @endif
                        @error('file') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Deskripsi / Instruksi Tugas</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="5">{{ old('description', $assignment->description) }}</textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4 form-check form-switch">
                        <input type="hidden" name="is_published" value="0">
                        <input class="form-check-input" type="checkbox" name="is_published" value="1" id="publishSwitch" {{ old('is_published', $assignment->is_published) ? 'checked' : '' }}>
                        <label class="form-check-label" for="publishSwitch">Publikasikan ke Siswa</label>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('assignments.index', $classSubject) }}" class="btn btn-light">Batal</a>
                        <button type="submit" class="btn btn-primary px-4">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
