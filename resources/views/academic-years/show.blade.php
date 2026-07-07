@extends('layouts.app')
@section('title', $academicYear->year)
@section('page-title', 'Detail Tahun Ajaran')
@section('content')
<div class="card mb-3"><div class="card-body">
    <h5>{{ $academicYear->year }}</h5>
    <p>{{ $academicYear->description ?? '—' }}</p>
    <a href="{{ route('academic-years.edit', $academicYear) }}" class="btn btn-primary btn-sm">Edit</a>
</div></div>
<div class="card"><div class="card-header">Semester</div><div class="table-responsive"><table class="table mb-0">
<thead><tr><th>Semester</th><th>Mulai</th><th>Selesai</th><th>Status</th></tr></thead>
<tbody>@foreach($academicYear->semesters as $s)<tr><td>{{ $s->label }}</td><td>{{ $s->start_date->format('d/m/Y') }}</td><td>{{ $s->end_date->format('d/m/Y') }}</td><td>{{ $s->is_active ? 'Aktif' : '—' }}</td></tr>@endforeach</tbody>
</table></div></div>
@endsection
