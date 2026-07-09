@extends('layouts.app')
@section('title', 'Materi Pelajaran')
@section('page-title', 'Materi Pelajaran')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-extrabold text-slate-900">Materi Kelas</h2>
            <p class="mt-1 text-sm text-slate-500">Materi pelajaran yang dipublikasikan oleh guru Anda.</p>
        </div>
    </div>

    @if($classSubjects->isNotEmpty())
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3">
        @foreach($classSubjects as $classSubject)
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/70">
            <div class="border-b border-brand-500/10 bg-gradient-to-br from-brand-500 to-brand-700 px-5 py-4 text-white">
                <h3 class="text-lg font-bold">{{ $classSubject->subject->name }}</h3>
                <p class="mt-1 text-xs font-medium text-brand-100">{{ $classSubject->teacher->name }}
                    @if($classSubject->teacher->subjects->isNotEmpty())
                        ({{ $classSubject->teacher->subjects->pluck('name')->join(', ') }})
                    @elseif($classSubject->teacher->subject)
                        ({{ $classSubject->teacher->subject }})
                    @endif
                </p>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($classSubject->materials as $material)
                <a href="{{ route('materials.show', $material) }}" class="flex items-center gap-4 px-5 py-4 transition-colors hover:bg-slate-50">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-xl text-brand-600">
                        <i class="bi {{ $material->type_icon }}"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h4 class="truncate font-bold text-slate-900">{{ $material->title }}</h4>
                        <div class="mt-1 flex items-center gap-2 text-xs text-slate-500">
                            <span class="truncate">{{ $material->topic ?: 'Umum' }}</span>
                            <span>&bull;</span>
                            <span class="shrink-0">{{ $material->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </a>
                @empty
                <div class="px-5 py-6 text-center">
                    <p class="text-sm font-medium text-slate-500">Belum ada materi dipublikasikan.</p>
                </div>
                @endforelse
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="rounded-2xl bg-white p-12 text-center shadow-sm ring-1 ring-slate-200/70">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-50">
            <i class="bi bi-journal-x text-3xl text-slate-400"></i>
        </div>
        <h3 class="mt-4 text-base font-bold text-slate-900">Belum Ada Pelajaran</h3>
        <p class="mt-2 text-sm text-slate-500">Anda belum terdaftar di kelas atau mata pelajaran apapun.</p>
    </div>
    @endif
</div>
@endsection
