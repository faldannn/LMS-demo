@extends('layouts.app')
@section('title', 'Laporan Analitik')
@section('page-title', 'Laporan Analitik')

@section('content')
<div class="mb-4">
    <p class="text-muted mb-0">Pilih jenis laporan yang ingin ditampilkan atau diekspor.</p>
</div>

<div class="row g-3">
    @foreach($types as $key => $type)
    <div class="col-sm-6 col-lg-4">
        <a href="{{ route('reports.show', $key) }}" class="text-decoration-none">
            <div class="card h-100 shadow-sm border-0 rounded-2xl hover-lift" style="transition: transform .15s, box-shadow .15s;">
                <div class="card-body d-flex gap-3 align-items-start p-4">
                    <div class="rounded-xl d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:48px;height:48px;background:{{ $type['bg'] }};color:{{ $type['color'] }}">
                        <i class="bi {{ $type['icon'] }} fs-5"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1 text-slate-800">{{ $type['title'] }}</h6>
                        <p class="text-muted small mb-0">{{ $type['description'] }}</p>
                    </div>
                </div>
            </div>
        </a>
    </div>
    @endforeach
</div>

<style>
.hover-lift:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,.1) !important; }
</style>
@endsection
