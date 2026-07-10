@extends('layouts.app')
@section('title', 'Portal E-Learning')
@section('page-title', 'Portal E-Learning')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-lg font-extrabold text-slate-900">Portal E-Learning</h2>
            <p class="mt-1 text-sm text-slate-500">
                Kelola materi, tugas, forum diskusi, dan bank soal dari satu tempat.
            </p>
        </div>
    </div>

    {{-- Group class subjects by class --}}
    @php
        $grouped = $classSubjects->groupBy(function($cs) {
            return $cs->class_id ?? 0;
        });
    @endphp

    @if($grouped->isNotEmpty())
        {{-- Filter Pills/Tabs --}}
        @if($grouped->count() > 1)
        <div class="flex flex-wrap items-center gap-1.5 border-b border-slate-100 pb-3">
            <button type="button" 
                    onclick="filterClass('all')" 
                    id="btn-class-all"
                    class="class-filter-btn inline-flex items-center justify-center gap-1.5 rounded-lg px-3 py-2 text-xs font-bold ring-1 transition-all duration-200 bg-brand-600 text-white ring-brand-600 shadow-sm shadow-brand-500/10 scale-105">
                Semua Kelas
            </button>
            @foreach($grouped as $classId => $subjects)
                @php
                    $firstCs = $subjects->first();
                    $className = $firstCs->class?->name ?? 'Lainnya';
                @endphp
                <button type="button" 
                        onclick="filterClass('{{ $classId }}')" 
                        id="btn-class-{{ $classId }}"
                        class="class-filter-btn inline-flex items-center justify-center gap-1 rounded-lg bg-white px-3 py-2 text-xs font-semibold text-slate-600 ring-1 ring-slate-200/80 transition-all duration-200 hover:bg-slate-50 hover:text-slate-900 shadow-sm">
                    {{ $className }}
                </button>
            @endforeach
        </div>
        @endif

        {{-- Class Groups --}}
        <div class="space-y-8 mt-4" id="class-sections-container">
            @foreach($grouped as $classId => $subjects)
                @php
                    $firstCs = $subjects->first();
                    $class = $firstCs->class;
                    $className = $class?->name ?? 'Lainnya';
                    $classGrade = $class?->grade ? 'Tingkat ' . $class->grade : '-';
                    $majorCode = $class?->major?->code;
                @endphp
                <div data-class-id="{{ $classId }}" class="class-section transition-all duration-300 ease-out space-y-4">
                    {{-- Clean Class Header with Accent Indicator --}}
                    <div class="flex items-center gap-2 pt-4 pb-2 border-b border-slate-100">
                        <span class="inline-flex h-5 w-1 rounded-full bg-brand-500"></span>
                        <h3 class="text-sm font-extrabold text-slate-800 flex items-center gap-2">
                            Kelas {{ $className }}
                            @if($majorCode)
                                <span class="inline-flex items-center rounded bg-brand-50 px-2 py-0.5 text-[9px] font-bold text-brand-700 uppercase tracking-wider ring-1 ring-brand-100">
                                    {{ $majorCode }}
                                </span>
                            @endif
                        </h3>
                        <span class="text-[11px] font-medium text-slate-400">({{ $subjects->count() }} Mata Pelajaran &bull; {{ $classGrade }})</span>
                    </div>

                    {{-- Class Subject Grid --}}
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-3">
                        @foreach($subjects as $cs)
                        <div class="group relative flex flex-col justify-between overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/70 transition-all duration-300 hover:-translate-y-1.5 hover:shadow-md hover:ring-brand-200">
                            {{-- Accent bar --}}
                            <div class="h-1 bg-brand-500"></div>

                            <div class="flex flex-1 flex-col justify-between p-5">
                                {{-- Subject title & teacher --}}
                                <div class="mb-4">
                                    <h4 class="text-sm font-extrabold text-slate-900 group-hover:text-brand-700 transition-colors leading-tight">
                                        {{ $cs->subject?->name ?? '-' }}
                                    </h4>
                                    <p class="mt-1.5 flex items-center gap-1.5 text-xs text-slate-500">
                                        <i class="bi bi-person text-slate-400"></i>
                                        <span class="font-medium text-slate-500">{{ $cs->teacher?->name ?? 'Belum ditentukan' }}</span>
                                    </p>
                                </div>

                                {{-- Unified Action Buttons & Stats --}}
                                <div class="grid grid-cols-3 gap-2 border-t border-slate-100 pt-4">
                                    @if(auth()->user()->isSiswa())
                                    <a href="{{ route('materials.mine', ['subject' => $cs->subject_id]) }}" class="flex flex-col items-center justify-center gap-0.5 rounded-xl bg-slate-50/60 py-2 transition hover:bg-emerald-50 hover:text-emerald-700 ring-1 ring-slate-100 hover:ring-emerald-100">
                                        <span class="text-sm font-extrabold text-emerald-600">{{ $cs->materials_count }}</span>
                                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Materi</span>
                                    </a>
                                    <a href="{{ route('student.assignments.index', ['subject' => $cs->subject_id]) }}" class="flex flex-col items-center justify-center gap-0.5 rounded-xl bg-slate-50/60 py-2 transition hover:bg-blue-50 hover:text-blue-700 ring-1 ring-slate-100 hover:ring-blue-100">
                                        <span class="text-sm font-extrabold text-blue-600">{{ $cs->assignments_count }}</span>
                                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Tugas</span>
                                    </a>
                                    <a href="{{ route('discussions.index', $cs->id) }}" class="flex flex-col items-center justify-center gap-0.5 rounded-xl bg-slate-50/60 py-2 transition hover:bg-purple-50 hover:text-purple-700 ring-1 ring-slate-100 hover:ring-purple-100">
                                        <span class="text-sm font-extrabold text-purple-600"><i class="bi bi-chat-left-text text-xs"></i></span>
                                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Forum</span>
                                    </a>
                                    @else
                                    <a href="{{ route('materials.index', $cs->id) }}" class="flex flex-col items-center justify-center gap-0.5 rounded-xl bg-slate-50/60 py-2 transition hover:bg-emerald-50 hover:text-emerald-700 ring-1 ring-slate-100 hover:ring-emerald-100">
                                        <span class="text-sm font-extrabold text-emerald-600">{{ $cs->materials_count }}</span>
                                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Materi</span>
                                    </a>
                                    <a href="{{ route('assignments.index', $cs->id) }}" class="flex flex-col items-center justify-center gap-0.5 rounded-xl bg-slate-50/60 py-2 transition hover:bg-blue-50 hover:text-blue-700 ring-1 ring-slate-100 hover:ring-blue-100">
                                        <span class="text-sm font-extrabold text-blue-600">{{ $cs->assignments_count }}</span>
                                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Tugas</span>
                                    </a>
                                    <a href="{{ route('discussions.index', $cs->id) }}" class="flex flex-col items-center justify-center gap-0.5 rounded-xl bg-slate-50/60 py-2 transition hover:bg-purple-50 hover:text-purple-700 ring-1 ring-slate-100 hover:ring-purple-100">
                                        <span class="text-sm font-extrabold text-purple-600"><i class="bi bi-chat-left-text text-xs"></i></span>
                                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Forum</span>
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Filter Script --}}
        @if($grouped->count() > 1)
        @push('scripts')
        <script>
            function filterClass(classId) {
                const sections = document.querySelectorAll('.class-section');
                
                sections.forEach(section => {
                    const id = section.getAttribute('data-class-id');
                    if (classId === 'all' || id === classId) {
                        section.style.display = 'block';
                        section.offsetHeight; // Force reflow
                        section.style.opacity = '1';
                        section.style.transform = 'scale(1)';
                    } else {
                        section.style.opacity = '0';
                        section.style.transform = 'scale(0.98)';
                        section.style.display = 'none';
                    }
                });

                // Update filter buttons
                const buttons = document.querySelectorAll('.class-filter-btn');
                buttons.forEach(btn => {
                    btn.classList.remove('bg-brand-600', 'text-white', 'ring-brand-600', 'shadow-md', 'shadow-brand-500/20', 'scale-105', 'font-bold');
                    btn.classList.add('bg-white', 'text-slate-600', 'ring-slate-200/80', 'font-semibold');
                });

                const activeBtn = document.getElementById(`btn-class-${classId}`);
                if (activeBtn) {
                    activeBtn.classList.remove('bg-white', 'text-slate-600', 'ring-slate-200/80', 'font-semibold');
                    activeBtn.classList.add('bg-brand-600', 'text-white', 'ring-brand-600', 'shadow-md', 'shadow-brand-500/20', 'scale-105', 'font-bold');
                }
            }
        </script>
        @endpush
        @endif
    @else
    {{-- Empty state --}}
    <div class="rounded-2xl bg-white p-12 text-center shadow-sm ring-1 ring-slate-200/70">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-brand-50">
            <i class="bi bi-book text-3xl text-brand-500"></i>
        </div>
        <h3 class="mt-4 text-base font-bold text-slate-900">Belum ada mata pelajaran kelas</h3>
        <p class="mt-2 text-sm text-slate-500">
            Belum ada mata pelajaran kelas yang ditugaskan kepada Anda.
        </p>
        @role('kepala_sekolah')
        <p class="mt-3 text-sm text-slate-500">
            Silakan pergi ke <a href="{{ route('classes.index') }}" class="font-semibold text-brand-600 hover:underline">Data Kelas</a> untuk menambahkan mata pelajaran ke kelas.
        </p>
        @endrole
    </div>
    @endif
</div>
@endsection
