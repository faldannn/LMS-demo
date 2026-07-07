@extends('layouts.app')
@section('title', 'Edit Tahun Ajaran')
@section('page-title', 'Edit Tahun Ajaran')
@section('content')
<div class="row justify-content-center"><div class="col-lg-6"><div class="card"><div class="card-body">
<form method="POST" action="{{ route('academic-years.update', $academicYear) }}">@csrf @method('PUT')
    <div class="mb-3"><label class="form-label">Tahun Ajaran *</label><input name="year" class="form-control" value="{{ old('year', $academicYear->year) }}" required></div>
    <div class="mb-3"><label class="form-label">Deskripsi</label><textarea name="description" class="form-control" rows="2">{{ old('description', $academicYear->description) }}</textarea></div>
    <div class="form-check mb-3"><input type="checkbox" name="is_active" value="1" class="form-check-input" id="active" @checked(old('is_active', $academicYear->is_active))><label for="active" class="form-check-label">Aktif</label></div>
    <button class="btn btn-primary">Update</button><a href="{{ route('academic-years.index') }}" class="btn btn-light">Batal</a>
</form></div></div></div></div>
@endsection
