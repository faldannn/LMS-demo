@extends('layouts.app')
@section('title', 'Forum Diskusi')
@section('page-title', 'Forum: ' . $classSubject->subject->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <p class="text-muted mb-0">Kelas {{ $classSubject->class->name }} &bull; {{ $threads->total() }} topik</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('elearning.index') }}" class="btn btn-light btn-sm">Kembali</a>
        <a href="{{ route('discussions.create', $classSubject) }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Topik Baru</a>
    </div>
</div>
<div class="card border-0 shadow-sm">
    <div class="list-group list-group-flush">
        @forelse($threads as $thread)
        <a href="{{ route('discussions.show', $thread) }}" class="list-group-item list-group-item-action py-3">
            <div class="d-flex justify-content-between">
                <div>
                    @if($thread->is_pinned)<span class="badge bg-warning text-dark me-1">Pin</span>@endif
                    @if($thread->is_locked)<span class="badge bg-secondary me-1">Terkunci</span>@endif
                    <strong>{{ $thread->title }}</strong>
                    <div class="small text-muted mt-1">{{ $thread->user->name }} &bull; {{ $thread->created_at->diffForHumans() }} &bull; {{ $thread->all_comments_count }} balasan</div>
                </div>
                <i class="bi bi-chevron-right text-muted"></i>
            </div>
        </a>
        @empty
        <div class="p-5 text-center text-muted">Belum ada diskusi. Mulai topik pertama!</div>
        @endforelse
    </div>
    @if($threads->hasPages())<div class="p-3">{{ $threads->links() }}</div>@endif
</div>
@endsection
