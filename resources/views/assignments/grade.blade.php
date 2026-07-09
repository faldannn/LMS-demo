@extends('layouts.app')
@section('title', 'Penilaian Tugas')
@section('page-title', 'Penilaian Tugas')

@push('styles')
<style>
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 1050;
        background-color: rgba(15, 23, 42, 0.6);
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }
    .modal.show {
        display: flex;
    }
    body.modal-open {
        overflow: hidden;
    }
</style>
@endpush

@section('content')
<div class="mb-4 d-flex align-items-center gap-3">
    <a href="{{ route('assignments.index', $assignment->class_subject_id) }}" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 shadow-sm transition hover:border-brand-300 hover:bg-brand-50 hover:text-brand-600" title="Kembali">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h5 class="fw-bold mb-1">{{ $assignment->title }}</h5>
        <div class="text-muted small">{{ $assignment->classSubject->subject->name }} | Kelas: {{ $assignment->classSubject->class->name }}</div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4 py-3" width="5%">No</th>
                        <th class="py-3" width="12%">NIS</th>
                        <th class="py-3" width="20%">Nama Siswa</th>
                        <th class="py-3" width="10%">Kelas</th>
                        <th class="py-3" width="12%">Status</th>
                        <th class="py-3" width="23%">Link / File Tugas</th>
                        <th class="py-3" width="10%">Nilai</th>
                        <th class="px-4 py-3 text-end" width="8%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $index => $student)
                        @php
                            $submission = $submissions->get($student->id);
                        @endphp
                        <tr>
                            <td class="px-4 py-3">{{ $index + 1 }}</td>
                            <td class="py-3 fw-semibold text-slate-700">{{ $student->nis }}</td>
                            <td class="py-3">
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $student->photo_url }}" alt="Profile" class="rounded-circle object-fit-cover" width="32" height="32">
                                    <div class="fw-bold">{{ $student->name }}</div>
                                </div>
                            </td>
                            <td class="py-3">
                                <span class="badge bg-secondary">{{ $assignment->classSubject->class->name }}</span>
                            </td>
                            <td class="py-3">
                                @if($submission)
                                    <span class="badge {{ $submission->is_late ? 'bg-warning text-dark' : 'bg-success' }}">
                                        {{ $submission->is_late ? 'Terlambat' : 'Tepat Waktu' }}
                                    </span>
                                    <div class="small text-muted mt-1" style="font-size: 11px;">{{ $submission->submitted_at->format('d/m/Y H:i') }}</div>
                                @else
                                    <span class="badge bg-secondary opacity-50">Belum Mengumpulkan</span>
                                @endif
                            </td>
                            <td class="py-3">
                                @if($submission)
                                    @if(filter_var($submission->content, FILTER_VALIDATE_URL))
                                        <a href="{{ $submission->content }}" target="_blank" class="btn btn-xs btn-outline-primary mb-1">
                                            <i class="bi bi-link-45deg"></i> Buka Link GDrive
                                        </a>
                                    @elseif($submission->content)
                                        <button type="button" class="btn btn-xs btn-light border mb-1" data-bs-toggle="modal" data-bs-target="#textModal{{ $submission->id }}">
                                            <i class="bi bi-card-text"></i> Teks Jawaban
                                        </button>
                                    @endif
                                    
                                    @if($submission->file_path)
                                        <a href="{{ asset('storage/' . $submission->file_path) }}" target="_blank" class="btn btn-xs btn-outline-secondary mb-1 ms-1">
                                            <i class="bi bi-download"></i> Unduh File
                                        </a>
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
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Render Modals Outside Table Container --}}
@foreach($students as $student)
    @php
        $submission = $submissions->get($student->id);
    @endphp
    @if($submission)
        @if($submission->content && !filter_var($submission->content, FILTER_VALIDATE_URL))
            <!-- Modal for text content -->
            <div class="modal fade" id="textModal{{ $submission->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content text-start">
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
                        <div class="modal-body text-start">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-slate-700">Nilai (Maks: {{ $assignment->max_score }})</label>
                                <input type="number" name="score" class="form-control form-control-lg" value="{{ $submission->grade ? (float)$submission->grade->score : '' }}" min="0" max="{{ $assignment->max_score }}" step="0.01" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-slate-700">Umpan Balik / Catatan (Opsional)</label>
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
@endforeach

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('[data-bs-toggle="modal"]').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('data-bs-target');
                const modal = document.querySelector(targetId);
                if (modal) {
                    modal.classList.add('show');
                    document.body.classList.add('modal-open');
                }
            });
        });
        
        document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const modal = this.closest('.modal');
                if (modal) {
                    modal.classList.remove('show');
                    document.body.classList.remove('modal-open');
                }
            });
        });
        
        // Close modal when clicking outside modal-content
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.remove('show');
                    document.body.classList.remove('modal-open');
                }
            });
        });
    });
</script>
@endpush
@endsection
