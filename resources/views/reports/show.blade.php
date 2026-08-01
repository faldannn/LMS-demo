@extends('layouts.app')
@section('title', $typeConfig['title'] . ' — Laporan')
@section('page-title', 'Laporan: ' . $typeConfig['title'])

@section('content')
{{-- Header --}}
<x-detail-header>
    <div>
        <p class="mb-1 text-xs font-bold uppercase tracking-wider text-slate-400">Laporan Analitik</p>
        <h5 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <span class="rounded-lg d-inline-flex align-items-center justify-content-center"
                  style="width:32px;height:32px;background:{{ $typeConfig['bg'] }};color:{{ $typeConfig['color'] }}">
                <i class="bi {{ $typeConfig['icon'] }}"></i>
            </span>
            {{ $typeConfig['title'] }}
        </h5>
        <span class="text-muted small">{{ $filter->periodLabel() }}</span>
    </div>

    <x-slot:actions>
        <a href="{{ route('reports.type.excel', [$type, ...$filter->toQueryArray()]) }}"
           class="btn btn-success btn-sm text-decoration-none">
            <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
        </a>
        <a href="{{ route('reports.type.pdf', [$type, ...$filter->toQueryArray()]) }}"
           class="btn btn-danger btn-sm text-decoration-none" target="_blank">
            <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
        </a>
        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </x-slot:actions>
</x-detail-header>

{{-- Filter --}}
<div class="card shadow-sm border-0 rounded-2xl mb-4">
    <div class="card-body p-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-sm-6 col-md-3">
                <label class="form-label small fw-semibold mb-1">Tanggal Mulai</label>
                <input type="date" name="date_from" class="form-control form-control-sm"
                       value="{{ $filter->dateFrom }}">
            </div>
            <div class="col-sm-6 col-md-3">
                <label class="form-label small fw-semibold mb-1">Tanggal Selesai</label>
                <input type="date" name="date_to" class="form-control form-control-sm"
                       value="{{ $filter->dateTo }}">
            </div>
            <div class="col-sm-6 col-md-3">
                <button type="submit" class="btn btn-primary btn-sm w-100">
                    <i class="bi bi-funnel me-1"></i> Tampilkan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Tabel data --}}
<div class="card shadow-sm border-0 rounded-2xl overflow-hidden">
    <div class="card-header bg-slate-50/80 border-bottom px-4 py-3 d-flex align-items-center justify-content-between">
        <h6 class="fw-bold mb-0 text-sm">{{ $typeConfig['title'] }}</h6>
        <span class="badge rounded-pill bg-primary">{{ $data['rows']->count() }} baris</span>
    </div>

    @if($data['rows']->isEmpty())
        <div class="p-6 text-center text-muted">
            <i class="bi bi-inbox fs-2 d-block mb-2 text-slate-300"></i>
            Tidak ada data untuk periode ini.
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-sm">
                <thead class="table-light">
                    <tr>
                        @foreach(array_keys($data['rows']->first()) as $col)
                            <th>{{ ucwords(str_replace('_', ' ', $col)) }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['rows'] as $i => $row)
                        <tr>
                            @foreach($row as $val)
                                <td>{{ $val }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
