@extends('layouts.app')
@section('title', 'Bank Soal')
@section('page-title', 'Bank Soal — ' . $classSubject->subject->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <a href="{{ route('elearning.index') }}" class="btn btn-sm btn-light border">← E-Learning</a>
        <p class="text-muted small mb-0 mt-2">Kelas {{ $classSubject->class->name }}</p>
    </div>
    <a href="{{ route('question-banks.create', $classSubject) }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i> Bank Soal Baru
    </a>
</div>

<form method="GET" class="mb-3">
    <div class="input-group" style="max-width:320px">
        <input type="search" name="search" class="form-control" placeholder="Cari bank soal..." value="{{ request('search') }}">
        <button class="btn btn-outline-secondary">Cari</button>
    </div>
</form>

<div class="row g-3">
    @forelse($banks as $bank)
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="fw-bold">{{ $bank->title }}</h6>
                <p class="text-muted small mb-3">{{ Str::limit($bank->description, 80) ?: 'Tanpa deskripsi' }}</p>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="badge bg-primary bg-opacity-10 text-primary">{{ $bank->questions_count }} soal</span>
                    <a href="{{ route('question-banks.show', $bank) }}" class="btn btn-sm btn-outline-primary">Kelola</a>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12"><div class="card"><div class="card-body text-center text-muted py-5">Belum ada bank soal.</div></div></div>
    @endforelse
</div>
{{ $banks->links() }}
@endsection
