@extends('layouts.app')
@section('title', 'Penilaian Tugas')
@section('page-title', 'Penilaian Tugas')

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center">
    <div>
        <h5 class="fw-bold mb-1">{{ $assignment->title }}</h5>
        <div class="text-muted small">{{ $assignment->classSubject->subject->name }} | Kelas: {{ $assignment->classSubject->class->name }}</div>
    </div>
    <a href="{{ route('assignments.index', $assignment->class_subject_id) }}" class="btn btn-light border rounded-pill px-4">
        <i class="bi bi-arrow-left me-2"></i> Kembali
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4 py-3" width="5%">No</th>
                        <th class="py-3" width="25%">Siswa</th>
                        <th class="py-3" width="15%">Status</th>
                        <th class="py-3" width="25%">File/Teks Jawaban</th>
                        <th class="py-3" width="15%">Nilai</th>
                        <th class="px-4 py-3 text-end" width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $index => $student)
                        @php
                            $submission = $submissions->get($student->id);
                        @endphp
                        <tr>
                            <td class="px-4 py-3">{{ $index + 1 }}</td>
                            <td class="py-3">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ $student->photo_url }}" alt="Profile" class="rounded-circle object-fit-cover" width="40" height="40">
                                    <div>
                                        <div class="fw-bold">{{ $student->name }}</div>
                                        <div class="text-muted small">{{ $student->nis }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3">
                                @if($submission)
                                    <span class="badge {{ $submission->is_late ? 'bg-warning text-dark' : 'bg-success' }}">
                                        {{ $submission->is_late ? 'Terlambat' : 'Tepat Waktu' }}
                                    </span>
                                    <div class="small text-muted mt-1">{{ $submission->submitted_at->format('d/m/Y H:i') }}</div>
                                @else
                                    <span class="badge bg-secondary">Belum Mengumpulkan</span>
                                @endif
                            </td>
                            <td class="py-3">
                                @if($submission)
                                    @if($submission->file_path)
                                        <a href="{{ asset('storage/' . $submission->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary mb-1">
                                            <i class="bi bi-download me-1"></i> Unduh File
                                        </a>
                                    @endif
                                    @if($submission->content)
                                        <button type="button" class="btn btn-sm btn-light border" data-bs-toggle="modal" data-bs-target="#textModal{{ $submission->id }}">
                                            <i class="bi bi-card-text me-1"></i> Lihat Teks
                                        </button>
                                        
                                        <!-- Modal for text content -->
                                        <div class="modal fade" id="textModal{{ $submission->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Jawaban Teks - {{ $student->name }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        {!! nl2br(e($submission->content)) !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="py-3">
                                @if($submission && $submission->grade)
                                    <div class="fw-bold fs-5 {{ $submission->grade->score < 75 ? 'text-danger' : 'text-success' }}">
                                        {{ (float)$submission->grade->score }} <span class="fs-6 text-muted fw-normal">/ {{ $assignment->max_score }}</span>
                                    </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-end">
                                @if($submission)
                                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#gradeModal{{ $submission->id }}">
                                        {{ $submission->grade ? 'Edit Nilai' : 'Beri Nilai' }}
                                    </button>
                                    
                                    <!-- Modal for Grading -->
                                    <div class="modal fade text-start" id="gradeModal{{ $submission->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Penilaian: {{ $student->name }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('assignments.store-grade', $submission) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Nilai (Maks: {{ $assignment->max_score }})</label>
                                                            <input type="number" name="score" class="form-control form-control-lg" value="{{ $submission->grade ? (float)$submission->grade->score : '' }}" min="0" max="{{ $assignment->max_score }}" step="0.01" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Umpan Balik / Catatan (Opsional)</label>
                                                            <textarea name="feedback" class="form-control" rows="3">{{ $submission->grade ? $submission->grade->feedback : '' }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer bg-light border-0">
                                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-primary px-4">Simpan Nilai</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
