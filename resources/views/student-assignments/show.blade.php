@extends('layouts.app')
@section('title', 'Detail Tugas')
@section('page-title', 'Detail Tugas')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="mb-4 d-flex align-items-center gap-3">
            <a href="{{ route('student.assignments.index') }}" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 shadow-sm transition hover:border-brand-300 hover:bg-brand-50 hover:text-brand-600" title="Kembali ke Daftar Tugas">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h5 class="fw-bold mb-0 text-slate-700">Kembali ke Daftar Tugas</h5>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4 p-md-5">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="bg-warning text-white rounded p-3 fs-3 d-flex align-items-center justify-content-center" style="width:60px; height:60px; background: linear-gradient(135deg, #f59e0b, #fbbf24);">
                        <i class="bi bi-journal-check"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-1">{{ $assignment->title }}</h4>
                        <div class="text-muted small">
                            {{ $assignment->classSubject->subject->name }} &bull; Tipe: {{ ucfirst($assignment->type) }}
                        </div>
                    </div>
                </div>

                <div class="alert {{ $assignment->isPastDue() ? 'alert-danger' : 'alert-info' }} d-flex align-items-center mb-4">
                    <i class="bi bi-calendar-event fs-4 me-3"></i>
                    <div>
                        <div class="fw-bold">Batas Waktu Pengumpulan:</div>
                        <div>{{ $assignment->due_date ? $assignment->due_date->format('l, d F Y, H:i') : 'Tidak ada batas waktu' }}</div>
                    </div>
                </div>

                @if($assignment->description)
                <div class="mb-4 bg-light rounded p-4 text-dark" style="border-left: 4px solid var(--primary);">
                    {!! nl2br(e($assignment->description)) !!}
                </div>
                @endif

                @if($assignment->file_path)
                <div class="mt-4 pt-4 border-top">
                    <h6 class="fw-bold mb-3">File Lampiran Tugas:</h6>
                    <a href="{{ asset('storage/' . $assignment->file_path) }}" target="_blank" class="btn btn-outline-primary rounded-pill">
                        <i class="bi bi-download me-2"></i> Unduh File ({{ $assignment->file_name }})
                    </a>
                </div>
                @endif
            </div>
        </div>

        <!-- FORM PENGUMPULAN TUGAS -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-cloud-upload me-2"></i>Pengumpulan Tugas</h6>
            </div>
            <div class="card-body p-4">
                @if($submission && $submission->grade)
                    <div class="alert alert-success mb-4">
                        <h5 class="alert-heading fw-bold"><i class="bi bi-check-circle-fill me-2"></i>Tugas Sudah Dinilai</h5>
                        <hr>
                        <div class="row">
                            <div class="col-6 col-md-4">
                                <div class="text-muted small">Nilai:</div>
                                <div class="fs-4 fw-bold">{{ (float)$submission->grade->score }} <span class="fs-6 text-muted">/ {{ $assignment->max_score }}</span></div>
                            </div>
                            <div class="col-6 col-md-8">
                                <div class="text-muted small">Catatan Guru:</div>
                                <div>{{ $submission->grade->feedback ?: '-' }}</div>
                            </div>
                        </div>
                    </div>
                @endif

                @if($submission)
                    <div class="mb-4 p-3 border rounded bg-light">
                        <h6 class="fw-bold mb-2 text-primary">Tugas yang Anda Kumpulkan:</h6>
                        <div class="text-muted small mb-3">Dikumpulkan pada: {{ $submission->submitted_at->format('d M Y, H:i') }} {!! $submission->is_late ? '<span class="badge bg-danger">Terlambat</span>' : '<span class="badge bg-success">Tepat Waktu</span>' !!}</div>
                        
                        @if($submission->content)
                            <div class="p-3 bg-white border rounded mb-3">
                                @if(filter_var($submission->content, FILTER_VALIDATE_URL))
                                    <a href="{{ $submission->content }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-link-45deg me-1"></i> Buka Link Google Drive / Tautan Tugas
                                    </a>
                                    <div class="text-muted small mt-2 truncate">{{ $submission->content }}</div>
                                @else
                                    {!! nl2br(e($submission->content)) !!}
                                @endif
                            </div>
                        @endif
                        
                        @if($submission->file_path)
                            <a href="{{ asset('storage/' . $submission->file_path) }}" target="_blank" class="btn btn-sm btn-primary">
                                <i class="bi bi-file-earmark-check me-2"></i>Lihat File Pengumpulan ({{ $submission->file_name }})
                            </a>
                        @endif
                    </div>
                @endif

                @if(!$submission || !$submission->grade)
                    @if($assignment->isPastDue() && !$submission)
                        <div class="alert alert-danger">Batas waktu pengumpulan telah lewat. Hubungi guru jika ingin mengumpulkan keterlambatan.</div>
                    @endif
                    
                    <form action="{{ route('student.assignments.submit', $assignment) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">Link Google Drive / Jawaban Teks (Opsional)</label>
                            <textarea name="content" class="form-control @error('content') is-invalid @enderror" rows="3" placeholder="Masukkan Link Google Drive tugas Anda di sini (misal: https://drive.google.com/...) atau tulis jawaban teks Anda...">{{ old('content', $submission->content ?? '') }}</textarea>
                            <div class="form-text text-muted">Jika mengumpulkan link Google Drive, pastikan akses link diatur ke "Siapa saja yang memiliki link dapat melihat".</div>
                            @error('content') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Unggah File (Opsional)</label>
                            <input type="file" name="file" class="form-control @error('file') is-invalid @enderror">
                            <div class="form-text text-muted">Maksimal 50MB. Kosongkan jika hanya mengirim jawaban teks.</div>
                            @error('file') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                {{ $submission ? 'Perbarui Pengumpulan' : 'Kumpulkan Tugas' }}
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
