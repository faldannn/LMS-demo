@extends('layouts.app')
@section('title', 'Topik Baru')
@section('page-title', 'Buat Topik Diskusi')

@section('content')
<div class="row justify-content-center"><div class="col-lg-8">
<div class="card border-0 shadow-sm"><div class="card-body p-4">
<p class="text-muted small mb-3">{{ $classSubject->class->name }} &bull; {{ $classSubject->subject->name }}</p>
<form method="POST" action="{{ route('discussions.store', $classSubject) }}">@csrf
    <div class="mb-3"><label class="form-label">Judul Topik *</label><input name="title" class="form-control" value="{{ old('title') }}" required></div>
    <div class="mb-3"><label class="form-label">Isi Diskusi *</label><textarea name="body" class="form-control" rows="6" required>{{ old('body') }}</textarea></div>
    <div class="d-flex gap-2"><button class="btn btn-primary">Publikasikan</button><a href="{{ route('discussions.index', $classSubject) }}" class="btn btn-light">Batal</a></div>
</form>
</div></div></div></div>
@endsection
