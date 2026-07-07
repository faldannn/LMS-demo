@extends('layouts.app')
@section('title', 'Edit Ujian CBT')
@section('page-title', 'Edit Pengaturan Ujian')

@section('content')
<div class="row justify-content-center"><div class="col-lg-9">
<div class="card border-0 shadow-sm"><div class="card-body p-4">
<form action="{{ route('quizzes.update', $quiz) }}" method="POST">@csrf @method('PUT')
    <div class="mb-3"><label class="form-label">Judul *</label><input name="title" class="form-control" value="{{ old('title', $quiz->title) }}" required></div>
    <div class="mb-3"><label class="form-label">Deskripsi</label><textarea name="description" class="form-control" rows="2">{{ old('description', $quiz->description) }}</textarea></div>
    <div class="mb-3"><label class="form-label">Bank Soal</label>
        <select name="question_bank_id" class="form-select">
            <option value="">—</option>
            @foreach($banks as $bank)<option value="{{ $bank->id }}" @selected(old('question_bank_id', $quiz->question_bank_id)==$bank->id)>{{ $bank->title }}</option>@endforeach
        </select>
    </div>
    <div class="row mb-3">
        <div class="col-md-4"><label class="form-label">Durasi (menit) *</label><input type="number" name="duration" class="form-control" value="{{ $quiz->duration }}" required></div>
        <div class="col-md-4"><label class="form-label">Mulai</label><input type="datetime-local" name="start_time" class="form-control" value="{{ $quiz->start_time?->format('Y-m-d\TH:i') }}"></div>
        <div class="col-md-4"><label class="form-label">Tutup</label><input type="datetime-local" name="end_time" class="form-control" value="{{ $quiz->end_time?->format('Y-m-d\TH:i') }}"></div>
    </div>
    <div class="row mb-3">
        <div class="col-md-6"><label class="form-label">Mode Soal</label>
            <select name="selection_mode" class="form-select">
                @foreach(['fixed'=>'Tetap','random'=>'Acak N','all_bank'=>'Semua Bank'] as $v=>$l)
                <option value="{{ $v }}" @selected($quiz->selection_mode===$v)>{{ $l }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3"><label class="form-label">Jumlah Acak</label><input type="number" name="random_question_count" class="form-control" value="{{ $quiz->random_question_count }}"></div>
        <div class="col-md-3"><label class="form-label">Soal/halaman</label><input type="number" name="questions_per_page" class="form-control" value="{{ $quiz->questions_per_page }}" min="1" max="10"></div>
    </div>
    <div class="mb-3"><label class="form-label">Nilai Lulus</label><input type="number" name="pass_score" class="form-control" value="{{ $quiz->pass_score }}" step="0.01"></div>
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="form-check form-switch"><input type="hidden" name="shuffle_questions" value="0"><input class="form-check-input" type="checkbox" name="shuffle_questions" value="1" @checked($quiz->shuffle_questions)><label>Acak soal</label></div>
            <div class="form-check form-switch"><input type="hidden" name="shuffle_answers" value="0"><input class="form-check-input" type="checkbox" name="shuffle_answers" value="1" @checked($quiz->shuffle_answers)><label>Acak jawaban</label></div>
        </div>
        <div class="col-md-6">
            <div class="form-check form-switch"><input type="hidden" name="auto_submit_on_timeout" value="0"><input class="form-check-input" type="checkbox" name="auto_submit_on_timeout" value="1" @checked($quiz->auto_submit_on_timeout)><label>Auto submit</label></div>
            <div class="form-check form-switch"><input type="hidden" name="show_results" value="0"><input class="form-check-input" type="checkbox" name="show_results" value="1" @checked($quiz->show_results)><label>Tampilkan hasil</label></div>
            <div class="form-check form-switch"><input type="hidden" name="is_published" value="0"><input class="form-check-input" type="checkbox" name="is_published" value="1" @checked($quiz->is_published)><label>Publikasikan</label></div>
        </div>
    </div>
    <button class="btn btn-primary">Simpan</button>
    <a href="{{ route('quizzes.show', $quiz) }}" class="btn btn-light">Batal</a>
</form>
</div></div></div></div>
@endsection
