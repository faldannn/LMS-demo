@extends('layouts.app')
@section('title', 'Sampah Jurusan')
@section('page-title', 'Jurusan Terhapus')
@section('content')
<div class="mb-3"><a href="{{ route('majors.index') }}" class="btn btn-outline-secondary btn-sm">Kembali</a></div>
<div class="card"><div class="table-responsive"><table class="table mb-0">
<thead><tr><th>Kode</th><th>Nama</th><th class="text-end">Aksi</th></tr></thead>
<tbody>
@forelse($majors as $major)
<tr>
    <td><code>{{ $major->code }}</code></td><td>{{ $major->name }}</td>
    <td class="text-end">
        <form action="{{ route('majors.restore', $major->id) }}" method="POST" class="d-inline">@csrf<button class="btn btn-sm btn-success">Pulihkan</button></form>
        <form action="{{ route('majors.force-destroy', $major->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus permanen?')">@csrf @method('DELETE')<button class="btn btn-sm btn-danger">Hapus</button></form>
    </td>
</tr>
@empty<tr><td colspan="3" class="text-center py-4 text-muted">Kosong</td></tr>@endforelse
</tbody></table></div><div class="p-3">{{ $majors->links() }}</div></div>
@endsection
