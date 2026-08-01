@extends('layouts.app')
@section('title', $semester->full_label)
@section('page-title', 'Detail Semester')
@section('content')
<x-detail-header>
    <div>
        <p class="mb-1 text-xs font-bold uppercase tracking-wider text-slate-400">Detail Semester</p>
        <h5 class="fw-bold mb-1">{{ $semester->full_label }}</h5>
        <span class="text-muted" style="font-size:13px">Periode: {{ $semester->start_date->format('d M Y') }} — {{ $semester->end_date->format('d M Y') }}</span>
    </div>

    <x-slot:actions>
        <a href="{{ route('semesters.edit', $semester) }}" class="btn btn-primary btn-sm">Edit</a>
        <a href="{{ route('semesters.index') }}" class="btn btn-light btn-sm border">Kembali</a>
    </x-slot:actions>
</x-detail-header>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <div class="rounded-3 bg-slate-50 p-3">
            <div class="text-muted small text-uppercase fw-semibold mb-1">Status Semester</div>
            <div class="fw-bold">{{ $semester->is_active ? 'Aktif' : 'Tidak aktif' }}</div>
        </div>
    </div>
</div>
@endsection
