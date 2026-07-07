@extends('layouts.app')
@section('title', 'Kalender Akademik')
@section('page-title', 'Kalender Akademik')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <form method="GET" class="d-flex gap-2">
        <select name="month" class="form-select form-select-sm">@for($m=1;$m<=12;$m++)<option value="{{ $m }}" @selected($month==$m)>{{ \Carbon\Carbon::create(null,$m)->translatedFormat('F') }}</option>@endfor</select>
        <select name="year" class="form-select form-select-sm">@for($y=now()->year-1;$y<=now()->year+1;$y++)<option value="{{ $y }}" @selected($year==$y)>{{ $y }}</option>@endfor</select>
        <button class="btn btn-primary btn-sm">Tampilkan</button>
    </form>
    @if(auth()->user()->isStaff())
    <a href="{{ route('academic-calendar.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Tambah Event</a>
    @endif
</div>
<div class="row g-3">
    @forelse($events as $event)
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100" style="border-left:4px solid {{ $event->category_color }} !important">
            <div class="card-body">
                <span class="badge mb-2" style="background:{{ $event->category_color }}20;color:{{ $event->category_color }}">{{ $event->category_label }}</span>
                <h6 class="fw-bold">{{ $event->title }}</h6>
                <p class="small text-muted mb-2">
                    {{ $event->start_date->format('d M Y') }}
                    @if($event->end_date && !$event->end_date->equalTo($event->start_date))
                        — {{ $event->end_date->format('d M Y') }}
                    @endif
                </p>
                @if($event->description)<p class="small mb-0">{{ Str::limit($event->description, 100) }}</p>@endif
                @if(auth()->user()->isStaff())
                <form action="{{ route('academic-calendar.destroy', $event) }}" method="POST" class="mt-3" onsubmit="return confirm('Hapus event?')">@csrf @method('DELETE')<button class="btn btn-link btn-sm text-danger p-0">Hapus</button></form>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="col-12"><div class="card"><div class="card-body text-center text-muted py-5">Tidak ada event pada periode ini.</div></div></div>
    @endforelse
</div>
@endsection
