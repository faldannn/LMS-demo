@extends('layouts.app')
@section('title', $major->name)
@section('page-title', 'Detail Jurusan')
@section('content')
<x-detail-header>
    <div>
        <p class="mb-1 text-xs font-bold uppercase tracking-wider text-slate-400">Detail Jurusan</p>
        <h5 class="fw-bold mb-1">{{ $major->name }}</h5>
        <span class="text-muted" style="font-size:13px">Kode {{ $major->code }}</span>
    </div>

    <x-slot:actions>
        <a href="{{ route('majors.edit', $major) }}" class="btn btn-primary btn-sm">Edit</a>
        <a href="{{ route('majors.index') }}" class="btn btn-light btn-sm border">Kembali</a>
    </x-slot:actions>
</x-detail-header>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <p class="text-muted mb-2">{{ $major->description ?? '—' }}</p>
        <div class="rounded-3 bg-slate-50 p-3">
            <div class="text-muted small text-uppercase fw-semibold">Kelas terdaftar</div>
            <div class="fw-bold fs-5">{{ $major->classes_count }}</div>
        </div>
    </div>
</div>
@endsection
