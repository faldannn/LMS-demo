@extends('layouts.app')
@section('title', 'Buat Bank Soal')
@section('page-title', 'Buat Bank Soal')

@section('content')
<div class="row justify-content-center"><div class="col-lg-7">
<div class="card border-0 shadow-sm"><div class="card-body p-4">
<p class="text-muted small">{{ $classSubject->subject->name }} — {{ $classSubject->class->name }}</p>
<form method="POST" action="{{ route('question-banks.store', $classSubject) }}">@csrf
    <div class="mb-3"><label class="form-label">Nama Bank *</label><input name="title" class="form-control" required placeholder="Bank UTS Semester 1"></div>
    <div class="mb-3"><label class="form-label">Deskripsi</label><textarea name="description" class="form-control" rows="3"></textarea></div>
    <button class="btn btn-primary">Simpan</button>
    <a href="{{ route('question-banks.index', $classSubject) }}" class="btn btn-light">Batal</a>
</form>
</div></div></div></div>
@endsection
