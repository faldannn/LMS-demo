@extends('layouts.app')
@section('title', 'Forum Diskusi')
@section('page-title', 'Forum: ' . $classSubject->subject->name)

@php
    $groupedThreads = $threads->groupBy(function($item) {
        return $item->created_at->toDateString();
    });

    if (!function_exists('formatDateGroup')) {
        function formatDateGroup($dateStr) {
            $date = \Carbon\Carbon::parse($dateStr);
            if ($date->isToday()) {
                return 'Hari Ini';
            } elseif ($date->isYesterday()) {
                return 'Kemarin';
            } elseif ($date->isTomorrow()) {
                return 'Besok';
            } elseif ($date->isFuture()) {
                return 'Mendatang';
            }
            return $date->translatedFormat('d F Y');
        }
    }
@endphp

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
        <div class="flex items-center gap-2">
            <div class="inline-flex overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                <button type="button" onclick="toggleView('discussions', 'grid')" id="btn-discussions-grid" class="px-2.5 py-1.5 text-xs transition" title="Grid View">
                    <i class="bi bi-grid-3x3-gap-fill"></i>
                </button>
                <button type="button" onclick="toggleView('discussions', 'list')" id="btn-discussions-list" class="px-2.5 py-1.5 text-xs transition" title="List View">
                    <i class="bi bi-list-ul"></i>
                </button>
            </div>
            <a href="{{ route('discussions.create', $classSubject) }}" class="inline-flex items-center gap-1.5 rounded-xl bg-brand-600 px-4 py-2 text-xs font-bold text-white shadow-sm shadow-brand-500/10 transition-all hover:bg-brand-700 hover:scale-[1.02]">
                <i class="bi bi-plus-lg"></i>
                Topik Baru
            </a>
        </div>
    </div>

    {{-- Discussion List Card --}}
    <div class="space-y-6">
        @forelse($groupedThreads as $date => $dateThreads)
        <div>
            <div class="flex items-center gap-2 mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">
                    <i class="bi bi-calendar-check me-1"></i>{{ formatDateGroup($date) }}
                </span>
                <div class="h-px bg-slate-200 flex-1"></div>
            </div>

            {{-- Grid View --}}
            <div id="discussions-grid-{{ $date }}" class="discussions-grid-container grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($dateThreads as $thread)
                <a href="{{ route('discussions.show', $thread) }}" class="group block overflow-hidden rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200/60 transition hover:shadow-md hover:ring-brand-200">
                    <div class="flex flex-col h-full justify-between gap-3">
                        <div>
                            <div class="flex flex-wrap items-center gap-1 mb-2">
                                @if($thread->is_pinned)
                                    <span class="inline-flex items-center gap-0.5 rounded bg-amber-50 px-1.5 py-0.5 text-[9px] font-bold text-amber-700 ring-1 ring-amber-200">
                                        <i class="bi bi-pin-angle-fill text-[8px]"></i> Pin
                                    </span>
                                @endif
                                @if($thread->is_locked)
                                    <span class="inline-flex items-center gap-0.5 rounded bg-slate-100 px-1.5 py-0.5 text-[9px] font-bold text-slate-600 ring-1 ring-slate-200">
                                        <i class="bi bi-lock-fill text-[8px]"></i> Terkunci
                                    </span>
                                @endif
                            </div>
                            <h4 class="text-sm font-extrabold text-slate-800 group-hover:text-brand-700 transition-colors line-clamp-2">
                                {{ $thread->title }}
                            </h4>
                        </div>
                        <div class="flex items-center justify-between border-t border-slate-50 pt-3 mt-1">
                            <div class="min-w-0">
                                <p class="text-[10px] text-slate-500 font-semibold truncate">
                                    {{ $thread->user->name }}
                                </p>
                                <p class="text-[9px] text-slate-400 mt-0.5">
                                    Dibuat: {{ $thread->created_at->format('d M Y') }}
                                </p>
                            </div>
                            <span class="inline-flex items-center gap-1 rounded-full bg-slate-50 px-2 py-0.5 text-[10px] font-bold text-slate-500 ring-1 ring-slate-100 group-hover:bg-brand-50 group-hover:text-brand-700 group-hover:ring-brand-100">
                                <i class="bi bi-chat text-[9px]"></i>
                                {{ $thread->all_comments_count }}
                            </span>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>

            {{-- List View --}}
            <div id="discussions-list-{{ $date }}" class="discussions-list-container hidden divide-y divide-slate-100 rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60">
                @foreach($dateThreads as $thread)
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
                                    Dibuat: {{ $thread->created_at->format('d M Y') }} ({{ $thread->created_at->diffForHumans() }})
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
                @endforeach
            </div>
        </div>
        @empty
        <div class="flex flex-col items-center justify-center p-12 text-center rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60">
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
        <div class="p-4">
            {{ $threads->links() }}
        </div>
    @endif
</div>

@push('scripts')
<script>
    function toggleView(section, view) {
        localStorage.setItem('forum_' + section + '_view', view);
        applyViewPreference(section);
    }
    
    function applyViewPreference(section) {
        const view = localStorage.getItem('forum_' + section + '_view') || 'list';
        
        const gridContainers = document.querySelectorAll(`.${section}-grid-container`);
        const listContainers = document.querySelectorAll(`.${section}-list-container`);
        
        const btnGrid = document.getElementById(`btn-${section}-grid`);
        const btnList = document.getElementById(`btn-${section}-list`);
        
        if (view === 'grid') {
            gridContainers.forEach(el => el.classList.remove('hidden'));
            listContainers.forEach(el => el.classList.add('hidden'));
            
            if (btnGrid) {
                btnGrid.classList.remove('text-slate-500', 'hover:bg-slate-50');
                btnGrid.classList.add('bg-brand-600', 'text-white');
            }
            if (btnList) {
                btnList.classList.remove('bg-brand-600', 'text-white');
                btnList.classList.add('text-slate-500', 'hover:bg-slate-50');
            }
        } else {
            gridContainers.forEach(el => el.classList.add('hidden'));
            listContainers.forEach(el => el.classList.remove('hidden'));
            
            if (btnList) {
                btnList.classList.remove('text-slate-500', 'hover:bg-slate-50');
                btnList.classList.add('bg-brand-600', 'text-white');
            }
            if (btnGrid) {
                btnGrid.classList.remove('bg-brand-600', 'text-white');
                btnGrid.classList.add('text-slate-500', 'hover:bg-slate-50');
            }
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        applyViewPreference('discussions');
    });
</script>
@endpush
@endsection
