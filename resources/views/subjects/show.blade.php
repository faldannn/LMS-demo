@extends('layouts.app')
@section('title', $subject->name)
@section('page-title', 'Detail Mata Pelajaran')
@section('content')
<div class="card"><div class="card-body">
    <h5>{{ $subject->name }} <code>{{ $subject->code }}</code></h5>
    <p>Kelompok: {{ $subject->group ?? '—' }} · Digunakan di {{ $subject->class_subjects_count ?? 0 }} kelas</p>
    <a href="{{ route('subjects.edit', $subject) }}" class="btn btn-primary btn-sm">Edit</a>
    <a href="{{ route('subjects.index') }}" class="btn btn-light btn-sm">Kembali</a>
</div></div>
@endsection
