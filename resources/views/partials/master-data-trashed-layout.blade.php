@props(['routePrefix', 'items', 'columns' => []])

@extends('layouts.app')
@section('title', 'Sampah Data')
@section('page-title', 'Data Terhapus')

@section('content')
<div class="mb-3">
    <a href="{{ route($routePrefix . '.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="bg-light">
                <tr>
                    @foreach($columns as $col)<th>{{ $col }}</th>@endforeach
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                <tr>
                    {{ $slot($item) }}
                    <td class="text-end">
                        <form action="{{ route($routePrefix . '.restore', $item->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-sm btn-success">Pulihkan</button>
                        </form>
                        <form action="{{ route($routePrefix . '.force-destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus permanen?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger">Hapus Permanen</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="{{ count($columns)+1 }}" class="text-center py-4 text-muted">Tidak ada data terhapus.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($items, 'links'))
        <div class="p-3">{{ $items->links() }}</div>
    @endif
</div>
@endsection
