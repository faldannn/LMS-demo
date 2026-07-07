@extends('layouts.app')
@section('title', $semester->full_label)
@section('page-title', 'Detail Semester')
@section('content')
<div class="card"><div class="card-body">
    <h5>{{ $semester->full_label }}</h5>
    <p>Periode: {{ $semester->start_date->format('d M Y') }} — {{ $semester->end_date->format('d M Y') }}</p>
    <a href="{{ route('semesters.edit', $semester) }}" class="btn btn-primary btn-sm">Edit</a>
</div></div>
@endsection
