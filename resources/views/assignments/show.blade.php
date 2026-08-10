@extends('layouts.app')
@section('title', 'Detail Tugas')
@section('page-title', 'Detail Tugas')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="mb-4 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('assignments.index', $assignment->class_subject_id) }}" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 shadow-sm transition hover:border-brand-300 hover:bg-brand-50 hover:text-brand-600" title="Kembali">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <span class="text-muted small">Kembali ke Daftar Tugas</span>
            </div>
            
            <a href="{{ route('assignments.edit', $assignment) }}" class="btn btn-sm btn-warning"><i class="bi bi-pencil me-1"></i> Edit</a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4 p-md-5">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary text-white rounded p-3 fs-3 d-flex align-items-center justify-content-center" style="width:60px; height:60px; background: linear-gradient(135deg, var(--primary), #8b5cf6);">
                            <i class="bi bi-journal-text"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-1">{{ $assignment->title }}</h4>
                            <div class="text-muted small">
                                {{ $assignment->classSubject->subject->name }} &bull; Dibuat {{ $assignment->created_at->format('d M Y') }}
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="badge bg-{{ $assignment->type === 'individu' ? 'primary' : 'success' }} mb-2">Tugas {{ ucfirst($assignment->type) }}</div>
                        <div class="text-muted small">Nilai Maks: {{ $assignment->max_score }}</div>
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
                    @include('partials.attachment-viewer', [
                        'filePath' => $assignment->file_path,
                        'fileName' => $assignment->file_name,
                        'fileSize' => $assignment->file_size ? round($assignment->file_size / 1024, 2) . ' KB' : null,
                        'mimeType' => $assignment->mime_type,
                        'type' => $assignment->type,
                        'title' => $assignment->title,
                    ])
                </div>
                @endif
                
                <div class="mt-5 pt-4 border-top text-center">
                    <a href="{{ route('assignments.grade', $assignment) }}" class="btn btn-primary px-5 py-2 rounded-pill shadow-sm">
                        <i class="bi bi-check2-square me-2"></i> Lihat & Nilai Pengumpulan ({{ $assignment->submissions->count() }})
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
