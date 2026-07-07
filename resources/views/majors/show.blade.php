@extends('layouts.app')
@section('title', $major->name)
@section('page-title', 'Detail Jurusan')
@section('content')
<div class="card"><div class="card-body">
    <h5>{{ $major->name }} <code>{{ $major->code }}</code></h5>
    <p class="text-muted">{{ $major->description ?? '—' }}</p>
    <p>Kelas terdaftar: <strong>{{ $major->classes_count }}</strong></p>
    <a href="{{ route('majors.edit', $major) }}" class="btn btn-primary btn-sm">Edit</a>
    <a href="{{ route('majors.index') }}" class="btn btn-light btn-sm">Kembali</a>
</div></div>
@endsection
