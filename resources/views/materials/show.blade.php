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
                @include('partials.attachment-viewer', [
                    'filePath' => $material->file_path,
                    'fileName' => $material->file_name,
                    'fileSize' => $material->file_size_formatted,
                    'mimeType' => $material->mime_type,
                    'url' => $material->url,
                    'type' => $material->type,
                    'title' => $material->title,
                ])
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
