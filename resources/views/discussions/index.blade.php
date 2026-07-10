@extends('layouts.app')
@section('title', 'Forum Diskusi')
@section('page-title', 'Forum: ' . $classSubject->subject->name)

@section('content')
<div class="space-y-6">
    {{-- Back button at the top left --}}
    <div class="flex">
        <a href="{{ route('elearning.index') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 shadow-sm transition hover:border-brand-300 hover:bg-brand-50 hover:text-brand-600">
            <i class="bi bi-arrow-left"></i>
            Kembali
        </a>
    </div>

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-lg font-extrabold text-slate-900">Forum Diskusi</h2>
            <p class="mt-0.5 text-xs font-medium text-slate-500">
                Kelas {{ $classSubject->class->name }} &bull; {{ $threads->total() }} Topik Diskusi
            </p>
        </div>
        <div>
            <a href="{{ route('discussions.create', $classSubject) }}" class="inline-flex items-center gap-1.5 rounded-xl bg-brand-600 px-4 py-2 text-xs font-bold text-white shadow-sm shadow-brand-500/10 transition-all hover:bg-brand-700 hover:scale-[1.02]">
                <i class="bi bi-plus-lg"></i>
                Topik Baru
            </a>
        </div>
    </div>

    {{-- Discussion List Card --}}
    <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60">
        <div class="divide-y divide-slate-100">
            @forelse($threads as $thread)
            <a href="{{ route('discussions.show', $thread) }}" class="group block p-4 transition hover:bg-slate-50/50">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-start gap-3">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600 ring-1 ring-brand-100/50">
                           <i class="bi bi-chat-left-text text-sm"></i>
                        </div>
                        <div>
                            <div class="flex flex-wrap items-center gap-1.5">
                                @if($thread->is_pinned)
                                    <span class="inline-flex items-center gap-0.5 rounded bg-amber-50 px-1.5 py-0.5 text-[10px] font-bold text-amber-700 ring-1 ring-amber-200">
                                        <i class="bi bi-pin-angle-fill text-[9px]"></i> Pin
                                    </span>
                                @endif
                                @if($thread->is_locked)
                                    <span class="inline-flex items-center gap-0.5 rounded bg-slate-100 px-1.5 py-0.5 text-[10px] font-bold text-slate-600 ring-1 ring-slate-200">
                                        <i class="bi bi-lock-fill text-[9px]"></i> Terkunci
                                    </span>
                                @endif
                                <h4 class="text-sm font-extrabold text-slate-800 group-hover:text-brand-700 transition-colors">
                                    {{ $thread->title }}
                                </h4>
                            </div>
                            <p class="mt-1 text-[11px] text-slate-500 font-medium">
                                Ditulis oleh <span class="font-semibold text-slate-700">{{ $thread->user->name }}</span> &bull; 
                                {{ $thread->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-50 px-2.5 py-1 text-[11px] font-bold text-slate-500 ring-1 ring-slate-100 group-hover:bg-brand-50 group-hover:text-brand-700 group-hover:ring-brand-100">
                            <i class="bi bi-chat text-[9px]"></i>
                            {{ $thread->all_comments_count }} Balasan
                        </span>
                        <i class="bi bi-chevron-right text-slate-300 transition-transform group-hover:translate-x-0.5 group-hover:text-brand-500"></i>
                    </div>
                </div>
            </a>
            @empty
            <div class="flex flex-col items-center justify-center p-12 text-center">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-50 text-brand-600 mb-3">
                    <i class="bi bi-chat-left-dots text-xl"></i>
                </div>
                <h4 class="text-sm font-bold text-slate-800">Belum ada diskusi</h4>
                <p class="text-xs text-slate-500 mt-1 max-w-xs">
                    Belum ada diskusi untuk kelas ini. Klik "+ Topik Baru" untuk memulai diskusi pertama!
                </p>
            </div>
            @endforelse
        </div>
        @if($threads->hasPages())
            <div class="border-t border-slate-100 p-4">
                {{ $threads->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
