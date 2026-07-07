@extends('layouts.app')
@section('title', 'Edit Semester')
@section('page-title', 'Edit Semester')
@section('content')
<div class="row justify-content-center"><div class="col-lg-6"><div class="card"><div class="card-body">
<form method="POST" action="{{ route('semesters.update', $semester) }}">@csrf @method('PUT')
    <div class="mb-3"><label class="form-label">Tahun Ajaran *</label><select name="academic_year_id" class="form-select" required>@foreach($academicYears as $y)<option value="{{ $y->id }}" @selected($semester->academic_year_id==$y->id)>{{ $y->year }}</option>@endforeach</select></div>
    <div class="mb-3"><label class="form-label">Semester *</label><select name="number" class="form-select" required><option value="1" @selected($semester->number==1)>Ganjil</option><option value="2" @selected($semester->number==2)>Genap</option></select></div>
    <div class="mb-3"><label class="form-label">Tanggal Mulai *</label><input type="date" name="start_date" class="form-control" value="{{ old('start_date', $semester->start_date->format('Y-m-d')) }}" required></div>
    <div class="mb-3"><label class="form-label">Tanggal Selesai *</label><input type="date" name="end_date" class="form-control" value="{{ old('end_date', $semester->end_date->format('Y-m-d')) }}" required></div>
    <div class="form-check mb-3"><input type="checkbox" name="is_active" value="1" class="form-check-input" id="active" @checked($semester->is_active)><label for="active" class="form-check-label">Semester aktif</label></div>
    <button class="btn btn-primary">Update</button><a href="{{ route('semesters.index') }}" class="btn btn-light">Batal</a>
</form></div></div></div></div>
@endsection
