@extends('layouts.app')
@section('title', 'Tambah Tahun Ajaran')
@section('page-title', 'Tambah Tahun Ajaran')
@section('content')
<div class="row justify-content-center"><div class="col-lg-6"><div class="card"><div class="card-body">
<form method="POST" action="{{ route('academic-years.store') }}">@csrf
    <div class="mb-3"><label class="form-label">Tahun Ajaran *</label><input name="year" class="form-control" placeholder="2025/2026" value="{{ old('year') }}" required></div>
    <div class="mb-3"><label class="form-label">Deskripsi</label><textarea name="description" class="form-control" rows="2">{{ old('description') }}</textarea></div>
    <div class="form-check mb-3"><input type="checkbox" name="is_active" value="1" class="form-check-input" id="active" @checked(old('is_active'))><label for="active" class="form-check-label">Jadikan tahun ajaran aktif</label></div>
    <button class="btn btn-primary">Simpan</button><a href="{{ route('academic-years.index') }}" class="btn btn-light">Batal</a>
</form></div></div></div></div>
@endsection
