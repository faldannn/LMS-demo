@extends('layouts.app')
@section('title', 'Sampah Kelas')
@section('page-title', 'Kelas Terhapus')
@section('content')
<div class="mb-3"><a href="{{ route('classes.index') }}" class="btn btn-outline-secondary btn-sm">Kembali</a></div>
<div class="card"><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Nama</th><th>Tingkat</th><th class="text-end">Aksi</th></tr></thead>
<tbody>@forelse($classes as $c)<tr><td>{{ $c->name }}</td><td>{{ $c->grade }}</td><td class="text-end">
<form action="{{ route('classes.restore', $c->id) }}" method="POST" class="d-inline">@csrf<button class="btn btn-sm btn-success">Pulihkan</button></form>
<form action="{{ route('classes.force-destroy', $c->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus permanen?')">@csrf @method('DELETE')<button class="btn btn-sm btn-danger">Hapus</button></form>
</td></tr>@empty<tr><td colspan="3" class="text-center py-4">Kosong</td></tr>@endforelse</tbody></table></div><div class="p-3">{{ $classes->links() }}</div></div>
@endsection
