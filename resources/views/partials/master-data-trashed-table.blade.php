@props(['routePrefix', 'items', 'headers'])

<div class="mb-3"><a href="{{ route($routePrefix . '.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Kembali</a></div>
<div class="card border-0 shadow-sm"><div class="table-responsive">
<table class="table table-hover mb-0">
<thead class="bg-light"><tr>@foreach($headers as $h)<th>{{ $h }}</th>@endforeach<th class="text-end">Aksi</th></tr></thead>
<tbody>
@forelse($items as $item)
<tr>
    @if(is_callable($row ?? null)){!! ($row)($item) !!}@endif
    <td class="text-end">
        <form action="{{ route($routePrefix.'.restore', $item->id) }}" method="POST" class="d-inline">@csrf<button class="btn btn-sm btn-success">Pulihkan</button></form>
        <form action="{{ route($routePrefix.'.force-destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus permanen?')">@csrf @method('DELETE')<button class="btn btn-sm btn-danger">Hapus</button></form>
    </td>
</tr>
@empty
<tr><td colspan="{{ count($headers)+1 }}" class="text-center py-4 text-muted">Kosong</td></tr>
@endforelse
</tbody></table></div>
@if(method_exists($items,'links'))<div class="p-3">{{ $items->links() }}</div>@endif
</div>
