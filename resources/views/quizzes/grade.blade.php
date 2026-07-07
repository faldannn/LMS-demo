@extends('layouts.app')
@section('title', 'Koreksi Essay')
@section('page-title', 'Koreksi Essay — ' . $attempt->student->name)

@section('content')
<a href="{{ route('quizzes.results', $quiz) }}" class="btn btn-sm btn-light border mb-3">← Rekap Nilai</a>

<div class="alert alert-info small">Nilai PG sudah dinilai otomatis. Koreksi manual hanya untuk soal essay.</div>

@foreach($essayAnswers as $answer)
<div class="card border-0 shadow-sm mb-4">
  <div class="card-body">
    <h6 class="fw-bold">Soal Essay (maks {{ $answer->question->effectivePoints($quiz) }} poin)</h6>
    <p class="small text-muted mb-2">{!! nl2br(e($answer->question->question_text)) !!}</p>
    <div class="bg-light rounded p-3 mb-3"><strong>Jawaban siswa:</strong><br>{!! nl2br(e($answer->essay_answer ?: '(kosong)')) !!}</div>
    <form method="POST" action="{{ route('quizzes.grade-essay', $answer) }}" class="row g-2">@csrf
      <div class="col-md-3">
        <label class="form-label small">Nilai</label>
        <input type="number" name="score" class="form-control" min="0" max="{{ $answer->question->effectivePoints($quiz) }}" step="0.01" value="{{ $answer->score ?? 0 }}" required>
      </div>
      <div class="col-md-7">
        <label class="form-label small">Feedback</label>
        <input type="text" name="feedback" class="form-control" value="{{ $answer->feedback }}">
      </div>
      <div class="col-md-2 d-flex align-items-end"><button class="btn btn-primary w-100">Simpan</button></div>
    </form>
  </div>
</div>
@endforeach

@if($essayAnswers->isEmpty())
<div class="card"><div class="card-body text-muted text-center py-4">Tidak ada jawaban essay.</div></div>
@endif
@endsection
