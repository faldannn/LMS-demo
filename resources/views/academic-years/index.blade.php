@extends('layouts.app')
@section('title', 'Tahun Ajaran')
@section('page-title', 'Tahun Ajaran')
@section('content')
<x-master-data-toolbar :create-route="route('academic-years.create')" :export-excel-route="route('academic-years.export.excel', request()->query())" :export-pdf-route="route('academic-years.export.pdf', request()->query())" :trashed-route="route('academic-years.trashed')" create-label="Tambah Tahun Ajaran">
    <p class="text-muted mb-0">Total: <strong>{{ $academicYears->total() }}</strong></p>
</x-master-data-toolbar>
<div class="card mb-3"><div class="card-body py-3"><form method="GET" class="row g-2"><div class="col-md-8"><input name="search" class="form-control" placeholder="Cari tahun..." value="{{ request('search') }}"></div><div class="col-md-4"><button class="btn btn-primary w-100">Cari</button></div></form></div></div>
<div class="card"><div class="table-responsive"><table class="table table-hover mb-0">
<thead class="bg-light"><tr><th>Tahun</th><th>Deskripsi</th><th>Semester</th><th>Kelas</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
<tbody>
@forelse($academicYears as $year)
<tr>
    <td class="fw-semibold">{{ $year->year }}</td>
    <td>{{ $year->description ?? '—' }}</td>
    <td>{{ $year->semesters_count ?? 0 }}</td>
    <td>{{ $year->classes_count ?? 0 }}</td>
    <td><span class="badge {{ $year->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $year->is_active ? 'Aktif' : 'Non-Aktif' }}</span></td>
    <td class="text-end">
        <a href="{{ route('academic-years.show', $year) }}" class="btn btn-sm btn-light"><i class="bi bi-eye"></i></a>
        <a href="{{ route('academic-years.edit', $year) }}" class="btn btn-sm btn-light text-primary"><i class="bi bi-pencil"></i></a>
        <form action="{{ route('academic-years.destroy', $year) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="btn btn-sm btn-light text-danger"><i class="bi bi-trash"></i></button></form>
    </td>
</tr>
@empty<tr><td colspan="6" class="text-center py-4">Belum ada data</td></tr>@endforelse
</tbody></table></div><div class="p-3">{{ $academicYears->links() }}</div></div>
@endsection
