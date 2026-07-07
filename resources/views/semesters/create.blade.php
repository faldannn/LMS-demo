@extends('layouts.app')
@section('title', 'Tambah Semester')
@section('page-title', 'Tambah Semester')
@section('content')
<div class="row justify-content-center"><div class="col-lg-6"><div class="card"><div class="card-body">
<form method="POST" action="{{ route('semesters.store') }}">@csrf
    <div class="mb-3"><label class="form-label">Tahun Ajaran *</label><select name="academic_year_id" class="form-select" required>@foreach($academicYears as $y)<option value="{{ $y->id }}">{{ $y->year }}</option>@endforeach</select></div>
    <div class="mb-3"><label class="form-label">Semester *</label><select name="number" class="form-select" required><option value="1">Ganjil</option><option value="2">Genap</option></select></div>
    <div class="mb-3"><label class="form-label">Tanggal Mulai *</label><input type="date" name="start_date" class="form-control" required></div>
    <div class="mb-3"><label class="form-label">Tanggal Selesai *</label><input type="date" name="end_date" class="form-control" required></div>
    <div class="form-check mb-3"><input type="checkbox" name="is_active" value="1" class="form-check-input" id="active"><label for="active" class="form-check-label">Semester aktif</label></div>
    <button class="btn btn-primary">Simpan</button><a href="{{ route('semesters.index') }}" class="btn btn-light">Batal</a>
</form></div></div></div></div>
@endsection
