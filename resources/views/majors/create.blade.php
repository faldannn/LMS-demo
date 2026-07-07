@extends('layouts.app')
@section('title', 'Tambah Jurusan')
@section('page-title', 'Tambah Jurusan')
@section('content')
<div class="row justify-content-center"><div class="col-lg-6">
<div class="card"><div class="card-body">
<form method="POST" action="{{ route('majors.store') }}">@csrf
    <div class="mb-3"><label class="form-label">Kode *</label><input name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code') }}" required>@error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    <div class="mb-3"><label class="form-label">Nama Jurusan *</label><input name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    <div class="mb-3"><label class="form-label">Deskripsi</label><textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea></div>
    <div class="form-check mb-3"><input type="checkbox" name="status" value="1" class="form-check-input" id="status" @checked(old('status', true))><label class="form-check-label" for="status">Aktif</label></div>
    <div class="d-flex gap-2"><button class="btn btn-primary">Simpan</button><a href="{{ route('majors.index') }}" class="btn btn-light">Batal</a></div>
</form>
</div></div></div></div>
@endsection
