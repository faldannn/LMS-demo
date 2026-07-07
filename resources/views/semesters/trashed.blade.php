@extends('layouts.app')
@section('title', 'Sampah Semester')
@section('page-title', 'Semester Terhapus')
@section('content')
<div class="mb-3"><a href="{{ route('semesters.index') }}" class="btn btn-outline-secondary btn-sm">Kembali</a></div>
<div class="card"><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Label</th><th class="text-end">Aksi</th></tr></thead>
<tbody>@forelse($semesters as $s)<tr><td>{{ $s->full_label }}</td><td class="text-end">
<form action="{{ route('semesters.restore', $s->id) }}" method="POST" class="d-inline">@csrf<button class="btn btn-sm btn-success">Pulihkan</button></form>
<form action="{{ route('semesters.force-destroy', $s->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus permanen?')">@csrf @method('DELETE')<button class="btn btn-sm btn-danger">Hapus</button></form>
</td></tr>@empty<tr><td colspan="2" class="text-center py-4">Kosong</td></tr>@endforelse</tbody></table></div><div class="p-3">{{ $semesters->links() }}</div></div>
@endsection
