@extends('layouts.app')
@section('title', 'Semester')
@section('page-title', 'Data Semester')
@section('content')
<x-master-data-toolbar :create-route="route('semesters.create')" :export-excel-route="route('semesters.export.excel', request()->query())" :export-pdf-route="route('semesters.export.pdf', request()->query())" :trashed-route="route('semesters.trashed')" create-label="Tambah Semester">
    <p class="text-muted mb-0">Total: <strong>{{ $semesters->total() }}</strong></p>
</x-master-data-toolbar>
<div class="card mb-3"><div class="card-body py-3"><form method="GET" class="row g-2">
<div class="col-md-4"><select name="academic_year_id" class="form-select"><option value="">Semua Tahun</option>@foreach($academicYears as $y)<option value="{{ $y->id }}" @selected(request('academic_year_id')==$y->id)>{{ $y->year }}</option>@endforeach</select></div>
<div class="col-md-3"><select name="number" class="form-select"><option value="">Semua</option><option value="1" @selected(request('number')=='1')>Ganjil</option><option value="2" @selected(request('number')=='2')>Genap</option></select></div>
<div class="col-md-2"><button class="btn btn-primary w-100">Filter</button></div>
</form></div></div>
<div class="card"><div class="table-responsive"><table class="table table-hover mb-0">
<thead class="bg-light"><tr><th>Tahun Ajaran</th><th>Semester</th><th>Periode</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
<tbody>@forelse($semesters as $s)<tr>
<td>{{ $s->academicYear?->year }}</td><td>{{ $s->label }}</td><td>{{ $s->start_date->format('d/m/Y') }} — {{ $s->end_date->format('d/m/Y') }}</td>
<td><span class="badge {{ $s->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $s->is_active ? 'Aktif' : '—' }}</span></td>
<td class="text-end">
<a href="{{ route('semesters.edit', $s) }}" class="btn btn-sm btn-light text-primary"><i class="bi bi-pencil"></i></a>
<form action="{{ route('semesters.destroy', $s) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="btn btn-sm btn-light text-danger"><i class="bi bi-trash"></i></button></form>
</td></tr>@empty<tr><td colspan="5" class="text-center py-4">Belum ada data</td></tr>@endforelse</tbody>
</table></div><div class="p-3">{{ $semesters->links() }}</div></div>
@endsection
