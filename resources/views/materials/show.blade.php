@extends('layouts.app')
@section('title', 'Detail Materi')
@section('page-title', 'Detail Materi')

@section('content')
<div class="mx-auto max-w-4xl space-y-6">
    {{-- Header Actions --}}
    <div class="flex items-center justify-between gap-3">
        @if(auth()->user()->isSiswa())
            <a href="{{ route('materials.mine') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                <i class="bi bi-arrow-left"></i>Kembali
            </a>
        @else
            <a href="{{ route('materials.index', $material->class_subject_id) }}" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                <i class="bi bi-arrow-left"></i>Kembali
            </a>
        @endif
        
        @if(auth()->user()->isStaff())
            <a href="{{ route('materials.edit', $material) }}" class="inline-flex items-center gap-1.5 rounded-lg bg-amber-500 px-4 py-2 text-sm font-bold text-white shadow-sm transition hover:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2">
                <i class="bi bi-pencil"></i>Edit
            </a>
        @endif
    </div>

    {{-- Main Content Card --}}
    <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/70">
        <div class="p-6 md:p-8">
            {{-- Material Header --}}
            <div class="flex items-start gap-4">
                <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-500 to-brand-700 text-2xl text-white shadow-sm">
                    <i class="bi {{ $material->type_icon }}"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-900 md:text-2xl">{{ $material->title }}</h2>
                    <div class="mt-2 flex flex-wrap items-center gap-2 text-sm text-slate-500">
                        <span class="inline-flex items-center rounded-lg border border-slate-200 bg-slate-50 px-2 py-0.5 text-xs font-semibold text-slate-700">
                            {{ $material->type_label }}
                        </span>
                        <span>{{ $material->classSubject->subject->name }}</span>
                        <span class="text-slate-300">&bull;</span>
                        <span>{{ $material->topic ?: 'Umum' }}</span>
                    </div>
                </div>
            </div>

            {{-- Description --}}
            @if($material->description)
            <div class="mt-6 rounded-xl bg-slate-50 p-4 text-sm leading-relaxed text-slate-700">
                {!! nl2br(e($material->description)) !!}
            </div>
            @endif

            {{-- Content Area --}}
            <div class="mt-6 border-t border-slate-100 pt-6">
                @if($material->type === 'youtube' && $material->youtube_embed_url)
                    <div class="relative w-full overflow-hidden rounded-xl bg-slate-900 pt-[56.25%] shadow-sm">
                        <iframe class="absolute inset-0 h-full w-full" src="{{ $material->youtube_embed_url }}" allowfullscreen title="YouTube"></iframe>
                    </div>
                    @if($material->url)
                    <div class="mt-4">
                        <a href="{{ $material->url }}" target="_blank" class="inline-flex items-center gap-2 rounded-lg border border-rose-200 bg-rose-50 px-4 py-2 text-sm font-semibold text-rose-700 transition hover:bg-rose-100">
                            <i class="bi bi-youtube text-rose-600"></i>Buka di YouTube
                        </a>
                    </div>
                    @endif

                @elseif($material->type === 'link' && $material->url)
                    <a href="{{ $material->url }}" target="_blank" class="inline-flex items-center gap-2 rounded-xl bg-brand-600 px-6 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-brand-700">
                        <i class="bi bi-box-arrow-up-right"></i>Buka Tautan
                    </a>

                @elseif($material->isDownloadable())
                    <a href="{{ route('materials.download', $material) }}" class="inline-flex items-center gap-2 rounded-xl bg-brand-600 px-6 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-brand-700">
                        <i class="bi bi-download"></i>Unduh {{ $material->type_label }} ({{ $material->file_size_formatted }})
                    </a>
                    <p class="mt-2 text-xs text-slate-500">{{ $material->file_name }} &bull; {{ $material->mime_type }}</p>
                    
                    @if($material->type === 'video')
                    <div class="mt-4 overflow-hidden rounded-xl bg-black">
                        <video controls class="w-full max-w-full" style="max-height:480px">
                            <source src="{{ asset('storage/'.$material->file_path) }}" type="{{ $material->mime_type }}">
                        </video>
                    </div>
                    @endif

                @else
                    <div class="flex items-center gap-3 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                        <i class="bi bi-exclamation-triangle-fill text-amber-500"></i>
                        <p class="font-medium">Konten materi tidak tersedia atau format tidak didukung.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Comments Section --}}
    @include('partials.comments', [
        'commentableType' => 'material',
        'commentableId' => $material->id,
        'comments' => $material->comments,
    ])
</div>
@endsection
