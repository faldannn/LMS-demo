@extends('layouts.app')
@section('title', 'Edit Jurusan')
@section('page-title', 'Edit Jurusan')
@section('content')
<div class="row justify-content-center"><div class="col-lg-6">
<div class="card"><div class="card-body">
<form method="POST" action="{{ route('majors.update', $major) }}">@csrf @method('PUT')
    <div class="mb-3"><label class="form-label">Kode *</label><input name="code" class="form-control" value="{{ old('code', $major->code) }}" required></div>
    <div class="mb-3"><label class="form-label">Nama Jurusan *</label><input name="name" class="form-control" value="{{ old('name', $major->name) }}" required></div>
    <div class="mb-3"><label class="form-label">Deskripsi</label><textarea name="description" class="form-control" rows="3">{{ old('description', $major->description) }}</textarea></div>
    <div class="form-check mb-3"><input type="checkbox" name="status" value="1" class="form-check-input" id="status" @checked(old('status', $major->status))><label class="form-check-label" for="status">Aktif</label></div>
    <div class="d-flex gap-2"><button class="btn btn-primary">Update</button><a href="{{ route('majors.index') }}" class="btn btn-light">Batal</a></div>
</form>
</div></div></div></div>
@endsection
