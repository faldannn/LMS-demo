@extends('layouts.app')
@section('title', 'Data Jurusan')
@section('page-title', 'Data Jurusan')

@section('content')
<x-master-data-toolbar
    :create-route="route('majors.create')"
    :import-route="route('majors.import')"
    :export-excel-route="route('majors.export.excel', request()->query())"
    :export-pdf-route="route('majors.export.pdf', request()->query())"
    :trashed-route="route('majors.trashed')"
    create-label="Tambah Jurusan">
    <p class="text-muted mb-0">Total: <strong>{{ $majors->total() }}</strong> jurusan</p>
</x-master-data-toolbar>

<div class="card mb-4 border-0 shadow-sm">
    <div class="card-body py-3">
        <form method="GET" class="row g-2">
            <div class="col-md-6"><input type="text" name="search" class="form-control" placeholder="Cari kode/nama..." value="{{ request('search') }}"></div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="1" @selected(request('status')==='1')>Aktif</option>
                    <option value="0" @selected(request('status')==='0')>Non-Aktif</option>
                </select>
            </div>
            <div class="col-md-3"><button class="btn btn-primary w-100">Filter</button></div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="bg-light"><tr><th>Kode</th><th>Nama</th><th>Kelas</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
                @forelse($majors as $major)
                <tr>
                    <td><code>{{ $major->code }}</code></td>
                    <td class="fw-semibold">{{ $major->name }}</td>
                    <td>{{ $major->classes_count ?? 0 }}</td>
                    <td><span class="badge {{ $major->status ? 'bg-success' : 'bg-secondary' }}">{{ $major->status ? 'Aktif' : 'Non-Aktif' }}</span></td>
                    <td class="text-end">
                        <a href="{{ route('majors.show', $major) }}" class="btn btn-sm btn-light"><i class="bi bi-eye"></i></a>
                        <a href="{{ route('majors.edit', $major) }}" class="btn btn-sm btn-light text-primary"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('majors.destroy', $major) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus jurusan?')">@csrf @method('DELETE')<button class="btn btn-sm btn-light text-danger"><i class="bi bi-trash"></i></button></form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada data jurusan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3">{{ $majors->links() }}</div>
</div>
@endsection
