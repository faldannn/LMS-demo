@extends('layouts.app')
@section('title', 'Detail Ujian CBT')
@section('page-title', 'Detail Ujian')

@section('content')
<div class="mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <a href="{{ route('quizzes.index', $quiz->class_subject_id) }}" class="btn btn-sm btn-light border">← Kembali</a>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('quizzes.results', $quiz) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-bar-chart me-1"></i> Rekap Nilai</a>
        <a href="{{ route('quizzes.edit', $quiz) }}" class="btn btn-sm btn-warning">Edit</a>
        <form action="{{ route('quizzes.toggle-publish', $quiz) }}" method="POST" class="d-inline">@csrf @method('PUT')
            <button class="btn btn-sm {{ $quiz->is_published ? 'btn-secondary' : 'btn-success' }}">{{ $quiz->is_published ? 'Draft' : 'Publikasi' }}</button>
        </form>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5 class="fw-bold">{{ $quiz->title }}</h5>
                <ul class="list-group list-group-flush small mt-3">
                    <li class="list-group-item px-0 d-flex justify-content-between"><span class="text-muted">Durasi</span><span>{{ $quiz->duration }} menit</span></li>
                    <li class="list-group-item px-0 d-flex justify-content-between"><span class="text-muted">Mode</span><span>{{ strtoupper($quiz->selection_mode) }}</span></li>
                    <li class="list-group-item px-0 d-flex justify-content-between"><span class="text-muted">Acak Soal</span><span>{{ $quiz->shuffle_questions ? 'Ya' : 'Tidak' }}</span></li>
                    <li class="list-group-item px-0 d-flex justify-content-between"><span class="text-muted">Acak Jawaban</span><span>{{ $quiz->shuffle_answers ? 'Ya' : 'Tidak' }}</span></li>
                    <li class="list-group-item px-0 d-flex justify-content-between"><span class="text-muted">Nilai Lulus</span><span>{{ $quiz->pass_score }}</span></li>
                    <li class="list-group-item px-0 d-flex justify-content-between"><span class="text-muted">Soal Terpasang</span><span class="fw-bold">{{ $quiz->bankQuestions->count() }}</span></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        @if($quiz->selection_mode === 'fixed' && $quiz->question_bank_id)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-bold">Tambah Soal dari Bank</div>
            <div class="card-body">
                @if($availableQuestions->isEmpty())
                <p class="text-muted small mb-0">Semua soal bank sudah terpasang atau bank kosong.</p>
                @else
                <form method="POST" action="{{ route('quizzes.attach-questions', $quiz) }}">@csrf
                    <div class="row g-2 mb-3" style="max-height:200px;overflow-y:auto">
                        @foreach($availableQuestions as $q)
                        <div class="col-md-6"><label class="border rounded p-2 d-flex gap-2 small w-100">
                            <input type="checkbox" name="question_ids[]" value="{{ $q->id }}">
                            <span>{{ $q->type_label }} — {{ Str::limit($q->question_text, 60) }}</span>
                        </label></div>
                        @endforeach
                    </div>
                    <button class="btn btn-sm btn-primary">Tambahkan ke Ujian</button>
                </form>
                @endif
            </div>
        </div>
        @endif

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-bold">Soal Ujian ({{ $quiz->bankQuestions->count() }})</div>
            <div class="list-group list-group-flush">
                @forelse($quiz->bankQuestions as $i => $question)
                <div class="list-group-item p-4">
                    <div class="d-flex justify-content-between">
                        <div>
                            <span class="badge bg-light text-dark border me-1">{{ $i+1 }}</span>
                            <span class="badge bg-primary bg-opacity-10 text-primary">{{ $question->type_label }}</span>
                            <span class="badge bg-secondary">{{ $question->points }} poin</span>
                            <p class="mt-2 mb-0 small">{!! nl2br(e(Str::limit($question->question_text, 300))) !!}</p>
                        </div>
                        @if($quiz->selection_mode === 'fixed')
                        <form action="{{ route('quizzes.detach-question', [$quiz, $question]) }}" method="POST" onsubmit="return confirm('Lepas soal?')">@csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Lepas</button>
                        </form>
                        @endif
                    </div>
                </div>
                @empty
                <div class="p-5 text-center text-muted">
                    @if($quiz->selection_mode === 'random')
                    Soal diambil acak {{ $quiz->random_question_count }} dari bank saat siswa mulai.
                    @elseif($quiz->selection_mode === 'all_bank')
                    Semua soal aktif di bank digunakan saat ujian.
                    @else
                    Pilih soal dari bank di atas.
                    @endif
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
