@extends('layouts.app')
@section('title', 'Sampah Mata Pelajaran')
@section('page-title', 'Mata Pelajaran Terhapus')
@section('content')
<div class="mb-3"><a href="{{ route('subjects.index') }}" class="btn btn-outline-secondary btn-sm">Kembali</a></div>
<div class="card"><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Kode</th><th>Nama</th><th class="text-end">Aksi</th></tr></thead>
<tbody>@forelse($subjects as $s)<tr><td>{{ $s->code }}</td><td>{{ $s->name }}</td><td class="text-end">
<form action="{{ route('subjects.restore', $s->id) }}" method="POST" class="d-inline">@csrf<button class="btn btn-sm btn-success">Pulihkan</button></form>
<form action="{{ route('subjects.force-destroy', $s->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus permanen?')">@csrf @method('DELETE')<button class="btn btn-sm btn-danger">Hapus</button></form>
</td></tr>@empty<tr><td colspan="3" class="text-center py-4">Kosong</td></tr>@endforelse</tbody></table></div><div class="p-3">{{ $subjects->links() }}</div></div>
@endsection
