@extends('layouts.app')
@section('title', $announcement->title)
@section('page-title', 'Pengumuman')

@section('content')
<div class="row justify-content-center"><div class="col-lg-8">
<x-detail-header>
    <div>
        <p class="mb-1 text-xs font-bold uppercase tracking-wider text-slate-400">Pengumuman</p>
        <h4 class="fw-bold mb-1">{{ $announcement->title }}</h4>
        <p class="text-muted small mb-0">{{ $announcement->creator?->name }} &bull; {{ ($announcement->published_at ?? $announcement->created_at)->format('d M Y H:i') }}</p>
    </div>

    <x-slot:actions>
        <a href="{{ route('announcements.index') }}" class="btn btn-sm btn-light border"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
    </x-slot:actions>
</x-detail-header>
<div class="card border-0 shadow-sm"><div class="card-body p-4">
    <span class="badge bg-primary mb-3">{{ $announcement->type === 'umum' ? 'Umum' : 'Kelas ' . ($announcement->class?->name ?? '') }}</span>
    <div class="mt-1" style="line-height:1.8">{!! nl2br(e($announcement->content)) !!}</div>
    @if($announcement->hasAttachment())
    <div class="mt-4 pt-3 border-top">
        <a href="{{ route('announcements.download', $announcement) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-download me-1"></i>{{ $announcement->attachment_name }}</a>
    </div>
    @endif
</div></div>
@include('partials.comments', ['commentableType' => 'announcement', 'commentableId' => $announcement->id, 'comments' => $announcement->comments])
</div></div>
@endsection
