@extends('layouts.app')
@section('title', $questionBank->title)
@section('page-title', 'Bank Soal')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <a href="{{ route('question-banks.index', $questionBank->class_subject_id) }}" class="btn btn-sm btn-light border">← Kembali</a>
        <h5 class="fw-bold mt-2 mb-0">{{ $questionBank->title }}</h5>
        <p class="text-muted small mb-0">{{ $questionBank->classSubject->subject->name }} — {{ $questions->total() }} soal</p>
    </div>
    <a href="{{ route('questions.create', $questionBank) }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i> Tambah Soal</a>
</div>

<form method="GET" class="row g-2 mb-3">
    <div class="col-md-3">
        <select name="type" class="form-select form-select-sm">
            <option value="">Semua Tipe</option>
            <option value="multiple_choice" @selected(request('type')==='multiple_choice')>Pilihan Ganda</option>
            <option value="essay" @selected(request('type')==='essay')>Essay</option>
        </select>
    </div>
    <div class="col-md-3">
        <select name="difficulty" class="form-select form-select-sm">
            <option value="">Semua Tingkat</option>
            <option value="easy" @selected(request('difficulty')==='easy')>Mudah</option>
            <option value="medium" @selected(request('difficulty')==='medium')>Sedang</option>
            <option value="hard" @selected(request('difficulty')==='hard')>Sulit</option>
        </select>
    </div>
    <div class="col-md-4"><input type="search" name="search" class="form-control form-control-sm" placeholder="Cari teks soal..." value="{{ request('search') }}"></div>
    <div class="col-md-2"><button class="btn btn-sm btn-outline-primary w-100">Filter</button></div>
</form>

<div class="card border-0 shadow-sm">
    <div class="list-group list-group-flush">
        @forelse($questions as $question)
        <div class="list-group-item p-4">
            <div class="d-flex justify-content-between align-items-start gap-3">
                <div class="flex-grow-1">
                    <span class="badge bg-{{ $question->type === 'multiple_choice' ? 'primary' : 'info' }} bg-opacity-10 text-{{ $question->type === 'multiple_choice' ? 'primary' : 'info' }} me-1">{{ $question->type_label }}</span>
                    <span class="badge bg-secondary bg-opacity-10 text-secondary me-1">{{ $question->difficulty_label }}</span>
                    <span class="badge bg-light text-dark border">{{ $question->points }} poin</span>
                    <p class="mt-2 mb-0 small">{!! nl2br(e(Str::limit($question->question_text, 200))) !!}</p>
                    @if($question->type === 'multiple_choice')
                    <div class="text-muted small mt-1">{{ $question->options_count }} opsi jawaban</div>
                    @endif
                </div>
                <form action="{{ route('questions.destroy', $question) }}" method="POST" onsubmit="return confirm('Hapus soal?')">@csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger">Hapus</button>
                </form>
            </div>
        </div>
        @empty
        <div class="p-5 text-center text-muted">Belum ada soal di bank ini.</div>
        @endforelse
    </div>
    @if($questions->hasPages())<div class="p-3">{{ $questions->links() }}</div>@endif
</div>
@endsection
