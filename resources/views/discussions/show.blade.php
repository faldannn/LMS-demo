@extends('layouts.app')
@section('title', $thread->title)
@section('page-title', 'Diskusi')

@section('content')
<div class="mb-3"><a href="{{ route('discussions.index', $classSubject) }}" class="btn btn-sm btn-light border">← Kembali ke Forum</a></div>
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <h4 class="fw-bold">{{ $thread->title }}</h4>
        <p class="text-muted small">{{ $thread->user->name }} &bull; {{ $thread->created_at->format('d M Y H:i') }}</p>
        <div class="mt-3">{!! nl2br(e($thread->body)) !!}</div>
    </div>
</div>

@foreach($thread->comments as $comment)
<div class="card border-0 shadow-sm mb-3 ms-0 ms-md-4">
    <div class="card-body py-3">
        <strong class="small">{{ $comment->user->name }}</strong>
        <span class="text-muted small ms-2">{{ $comment->created_at->diffForHumans() }}</span>
        <p class="mb-0 mt-2 small">{{ $comment->body }}</p>
        @foreach($comment->children as $reply)
        <div class="ms-3 mt-3 ps-3 border-start">
            <strong class="small">{{ $reply->user->name }}</strong>
            <p class="mb-0 mt-1 small">{{ $reply->body }}</p>
        </div>
        @endforeach
    </div>
</div>
@endforeach

@if(!$thread->is_locked)
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('discussions.reply', $thread) }}">
            @csrf
            <label class="form-label fw-semibold">Balas Diskusi</label>
            <textarea name="body" class="form-control @error('body') is-invalid @enderror" rows="4" required>{{ old('body') }}</textarea>
            @error('body')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <button type="submit" class="btn btn-primary mt-3">Kirim Balasan</button>
        </form>
    </div>
</div>
@else
<div class="alert alert-secondary">Topik ini terkunci.</div>
@endif
@endsection
