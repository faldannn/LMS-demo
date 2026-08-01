@extends('layouts.app')
@section('title', $academicYear->year)
@section('page-title', 'Detail Tahun Ajaran')
@section('content')
<x-detail-header>
    <div>
        <p class="mb-1 text-xs font-bold uppercase tracking-wider text-slate-400">Detail Tahun Ajaran</p>
        <h5 class="fw-bold mb-1">{{ $academicYear->year }}</h5>
        <span class="text-muted" style="font-size:13px">{{ $academicYear->description ?? '—' }}</span>
    </div>

    <x-slot:actions>
        <a href="{{ route('academic-years.edit', $academicYear) }}" class="btn btn-primary btn-sm">Edit</a>
        <a href="{{ route('academic-years.index') }}" class="btn btn-light btn-sm border">Kembali</a>
    </x-slot:actions>
</x-detail-header>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white fw-bold">Semester</div>
    <div class="table-responsive"><table class="table mb-0">
        <thead><tr><th>Semester</th><th>Mulai</th><th>Selesai</th><th>Status</th></tr></thead>
        <tbody>@foreach($academicYear->semesters as $s)<tr><td>{{ $s->label }}</td><td>{{ $s->start_date->format('d/m/Y') }}</td><td>{{ $s->end_date->format('d/m/Y') }}</td><td>{{ $s->is_active ? 'Aktif' : '—' }}</td></tr>@endforeach</tbody>
    </table></div>
</div>
@endsection
