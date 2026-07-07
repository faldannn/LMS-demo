@extends('layouts.app')
@section('title', $announcement->title)
@section('page-title', 'Pengumuman')

@section('content')
<div class="row justify-content-center"><div class="col-lg-8">
<a href="{{ route('announcements.index') }}" class="btn btn-sm btn-light border mb-3">← Kembali</a>
<div class="card border-0 shadow-sm"><div class="card-body p-4">
    <span class="badge bg-primary mb-2">{{ $announcement->type === 'umum' ? 'Umum' : 'Kelas ' . ($announcement->class?->name ?? '') }}</span>
    <h4 class="fw-bold">{{ $announcement->title }}</h4>
    <p class="text-muted small">{{ $announcement->creator?->name }} &bull; {{ ($announcement->published_at ?? $announcement->created_at)->format('d M Y H:i') }}</p>
    <div class="mt-3">{!! nl2br(e($announcement->content)) !!}</div>
    @if($announcement->hasAttachment())
    <div class="mt-4 pt-3 border-top">
        <a href="{{ route('announcements.download', $announcement) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-download me-1"></i>{{ $announcement->attachment_name }}</a>
    </div>
    @endif
</div></div>
@include('partials.comments', ['commentableType' => 'announcement', 'commentableId' => $announcement->id, 'comments' => $announcement->comments])
</div></div>
@endsection
