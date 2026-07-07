@extends('layouts.app')
@section('title', 'Sampah Tahun Ajaran')
@section('page-title', 'Tahun Ajaran Terhapus')
@section('content')
<div class="mb-3"><a href="{{ route('academic-years.index') }}" class="btn btn-outline-secondary btn-sm">Kembali</a></div>
<div class="card"><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Tahun</th><th class="text-end">Aksi</th></tr></thead>
<tbody>@forelse($academicYears as $y)<tr><td>{{ $y->year }}</td><td class="text-end">
<form action="{{ route('academic-years.restore', $y->id) }}" method="POST" class="d-inline">@csrf<button class="btn btn-sm btn-success">Pulihkan</button></form>
<form action="{{ route('academic-years.force-destroy', $y->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus permanen?')">@csrf @method('DELETE')<button class="btn btn-sm btn-danger">Hapus</button></form>
</td></tr>@empty<tr><td colspan="2" class="text-center py-4">Kosong</td></tr>@endforelse</tbody></table></div><div class="p-3">{{ $academicYears->links() }}</div></div>
@endsection
