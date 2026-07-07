@extends('layouts.app')
@section('title', $meta['title'] ?? 'Laporan')
@section('page-title', $meta['title'] ?? 'Laporan')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Kembali ke Laporan
        </a>
        <span class="text-muted small">{{ $filter->periodLabel() }}</span>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <i class="bi bi-funnel me-2 text-primary"></i>Filter Laporan
        </div>
        <div class="card-body">
            @include('reports.partials.filter-form', ['action' => route('reports.show', $type)])
        </div>
    </div>

    @if(!empty($data['summary']))
        <div class="row g-3 mb-4">
            @foreach($data['summary'] as $label => $value)
                <div class="col-6 col-md-3">
                    <div class="stat-card">
                        <div style="font-size:22px;font-weight:800;font-family:'JetBrains Mono',monospace">
                            {{ is_float($value) ? number_format($value, 1) : $value }}
                        </div>
                        <div style="color:#64748b;font-size:12px">{{ ucwords(str_replace('_', ' ', $label)) }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-table me-2 text-primary"></i>{{ $data['title'] }}</span>
            <span class="badge bg-secondary">{{ $data['rows']->count() }} baris</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width:48px">#</th>
                            @foreach($data['headings'] as $heading)
                                <th>{{ $heading }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['rows'] as $index => $row)
                            <tr>
                                <td class="text-center text-muted">{{ $index + 1 }}</td>
                                @foreach($data['keys'] as $key)
                                    <td>{{ $row[$key] ?? '-' }}</td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($data['headings']) + 1 }}" class="text-center text-muted py-5">
                                    <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                    Tidak ada data untuk filter yang dipilih.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
