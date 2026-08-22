@extends('layouts.app')
@section('title', 'Portal E-Learning')
@section('page-title', 'Portal E-Learning')

@push('styles')
<style>
    .scrollbar-none::-webkit-scrollbar { display: none; }
    .scrollbar-none { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endpush

@section('content')
<div class="space-y-5">
    {{-- Group class subjects by class --}}
    @php
        $validClassSubjects = $classSubjects->filter(fn($cs) => !empty($cs->class_id) && $cs->class != null);
        $grouped = $validClassSubjects->groupBy(function($cs) {
            return $cs->class_id;
        });
        $uniqueGrades = $validClassSubjects->pluck('class.grade')->filter()->unique()->sort()->values();
        $teachers = $validClassSubjects->pluck('teacher')->filter()->unique('id')->sortBy('name')->values();
        $uniqueSubjects = $validClassSubjects->pluck('subject')->filter()->unique('id')->sortBy('name')->values();
    @endphp

    @if($grouped->isNotEmpty())
        {{-- Ringkas & Efisien Filter Card --}}
        <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200/70">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <i class="bi bi-funnel-fill text-brand-600"></i>
                        Filter E-Learning per Kelas & Tingkat
                    </h3>
                    <p class="text-xs text-slate-500">Pilih tingkat atau kelas agar tampilan lebih ringkas, cepat, dan ringan.</p>
                </div>
                
                {{-- Quick Actions: Expand / Collapse All --}}
                <div class="flex items-center gap-2">
                    <button type="button" onclick="expandAllSections()" class="inline-flex items-center gap-1 rounded-xl bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-200 transition">
                        <i class="bi bi-arrows-expand"></i> Buka Semua
                    </button>
                    <button type="button" onclick="collapseAllSections()" class="inline-flex items-center gap-1 rounded-xl bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-200 transition">
                        <i class="bi bi-arrows-collapse"></i> Tutup Semua
                    </button>
                </div>
            </div>

            {{-- Tabs Tingkat Kelas (10, 11, 12) --}}
            <div class="mt-4 flex flex-wrap items-center gap-2 border-b border-slate-100 pb-3">
                <button type="button"
                        onclick="filterGrade('all')"
                        id="btn-grade-all"
                        class="grade-filter-btn inline-flex items-center gap-1.5 rounded-xl px-3.5 py-2 text-xs font-bold ring-1 transition-all bg-brand-600 text-white ring-brand-600 shadow-sm">
                    <i class="bi bi-grid-fill"></i> Semua Kelas ({{ $grouped->count() }})
                </button>
                @foreach($uniqueGrades as $grade)
                    @php
                        $countClassGrade = $grouped->filter(fn($items) => $items->first()?->class?->grade == $grade)->count();
                    @endphp
                    <button type="button"
                            onclick="filterGrade('{{ $grade }}')"
                            id="btn-grade-{{ $grade }}"
                            class="grade-filter-btn inline-flex items-center gap-1.5 rounded-xl bg-slate-50 px-3.5 py-2 text-xs font-semibold text-slate-600 ring-1 ring-slate-200 hover:bg-slate-100 transition">
                        Tingkat {{ $grade }} ({{ $countClassGrade }} Kelas)
                    </button>
                @endforeach
            </div>

            {{-- Filter Grid (Select Dropdown & Search) --}}
            <div class="mt-3 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                {{-- Live Search --}}
                <div class="relative">
                    <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                    <input type="text" id="elearning-search" placeholder="Cari mapel atau guru..."
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-xs text-slate-800 focus:border-brand-500 focus:bg-white focus:ring-2 focus:ring-brand-500/20">
                </div>

                {{-- Select Kelas --}}
                <select id="select-class-filter" onchange="filterClass(this.value)" class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2 px-3 text-xs text-slate-700 font-semibold focus:border-brand-500 focus:bg-white">
                    <option value="all">-- Semua Kelas --</option>
                    @foreach($grouped as $classId => $subjects)
                        @php $cName = $subjects->first()?->class?->name ?? 'Lainnya'; @endphp
                        <option value="{{ $classId }}">Kelas {{ $cName }} ({{ $subjects->count() }} Mapel)</option>
                    @endforeach
                </select>

                {{-- Select Guru --}}
                <select id="select-teacher-filter" onchange="filterTeacher(this.value)" class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2 px-3 text-xs text-slate-700 focus:border-brand-500 focus:bg-white">
                    <option value="all">-- Semua Guru --</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                    @endforeach
                </select>

                {{-- Select Mapel --}}
                <select id="select-subject-filter" onchange="filterSubject(this.value)" class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2 px-3 text-xs text-slate-700 focus:border-brand-500 focus:bg-white">
                    <option value="all">-- Semua Mata Pelajaran --</option>
                    @foreach($uniqueSubjects as $sub)
                        <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Class Accordion List --}}
        <div class="space-y-3" id="class-sections-container">
            @foreach($grouped as $classId => $subjects)
                @php
                    $firstCs = $subjects->first();
                    $class = $firstCs->class;
                    $className = $class?->name ?? 'Lainnya';
                    $gradeVal = $class?->grade ?? 10;
                    $classGrade = 'Tingkat ' . $gradeVal;
                    $majorCode = $class?->major?->code;
                @endphp
                
                <div data-class-id="{{ $classId }}" data-grade="{{ $gradeVal }}" class="class-section rounded-2xl border border-slate-200/80 bg-white shadow-sm transition-all">
                    {{-- Accordion Header (Clickable) --}}
                    <button type="button" 
                            onclick="toggleSection('{{ $classId }}')"
                            class="flex w-full items-center justify-between p-4 text-left hover:bg-slate-50/80 transition rounded-2xl">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-brand-50 text-brand-600 font-bold text-sm ring-1 ring-brand-100">
                                {{ $gradeVal }}
                            </span>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h4 class="text-sm font-extrabold text-slate-900">Kelas {{ $className }}</h4>
                                    @if($majorCode)
                                        <span class="rounded bg-brand-50 px-2 py-0.5 text-[9px] font-bold text-brand-700 uppercase ring-1 ring-brand-100">
                                            {{ $majorCode }}
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-slate-500 font-medium mt-0.5">
                                    {{ $subjects->count() }} Mata Pelajaran • {{ $classGrade }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <span class="hidden sm:inline-block rounded-full bg-slate-100 px-3 py-1 text-[11px] font-bold text-slate-600">
                                {{ $subjects->count() }} Mapel
                            </span>
                            <i id="icon-chevron-{{ $classId }}" class="bi bi-chevron-down text-slate-400 text-sm transition-transform duration-200"></i>
                        </div>
                    </button>

                    {{-- Collapsible Content Body --}}
                    <div id="content-section-{{ $classId }}" class="section-content p-4 pt-0">
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-3 pt-2 border-t border-slate-100">
                            @foreach($subjects as $cs)
                            @php
                                $attInfo = $todayAttendanceMap[$cs->id] ?? null;
                                $attStatus = $attInfo['status'] ?? null;
                            @endphp
                            <div data-subject-id="{{ $cs->subject_id }}" data-teacher-id="{{ $cs->teacher_id ?? 0 }}" class="subject-card group relative flex flex-col justify-between overflow-hidden rounded-xl bg-slate-50/50 p-3.5 ring-1 ring-slate-200/70 hover:bg-white hover:shadow-md hover:ring-brand-200 transition-all duration-200">
                                <div class="mb-2">
                                    <div class="flex items-start justify-between gap-2">
                                        <h5 class="text-xs font-extrabold text-slate-900 group-hover:text-brand-700 transition-colors">
                                            @if(auth()->user()->isSiswa())
                                                <a href="{{ route('student.class-subjects.show', $cs->id) }}" class="hover:underline">
                                                    {{ $cs->subject?->name ?? '-' }}
                                                </a>
                                            @else
                                                {{ $cs->subject?->name ?? '-' }}
                                            @endif
                                        </h5>
                                        @if(auth()->user()->isSiswa())
                                            @if(in_array($attStatus, ['hadir', 'terlambat'], true))
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-800 font-extrabold text-[10px] shrink-0">
                                                    <i class="bi bi-check-circle-fill"></i> Hadir
                                                </span>
                                            @elseif($attStatus === 'alfa')
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-rose-100 text-rose-800 font-extrabold text-[10px] shrink-0">
                                                    <i class="bi bi-x-circle-fill"></i> Tidak Hadir
                                                </span>
                                            @elseif($attStatus === 'sakit')
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-amber-100 text-amber-800 font-extrabold text-[10px] shrink-0">
                                                    <i class="bi bi-bandaid-fill"></i> Sakit
                                                </span>
                                            @elseif($attStatus === 'izin')
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-sky-100 text-sky-800 font-extrabold text-[10px] shrink-0">
                                                    <i class="bi bi-file-earmark-text-fill"></i> Izin
                                                </span>
                                            @elseif(!empty($attInfo['has_schedule_today']))
                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-slate-200/80 text-slate-600 font-semibold text-[9px] shrink-0">
                                                    <i class="bi bi-calendar-check"></i> Hari Ini
                                                </span>
                                            @endif
                                        @endif
                                    </div>
                                    <p class="mt-1 flex items-center gap-1 text-[11px] text-slate-500">
                                        <i class="bi bi-person text-slate-400"></i>
                                        <span class="font-medium text-slate-600 truncate">{{ $cs->teacher?->name ?? 'Belum ditentukan' }}</span>
                                    </p>
                                </div>

                                {{-- Action Buttons --}}
                                <div class="grid grid-cols-3 gap-1.5 pt-2.5 border-t border-slate-200/60 mt-2">
                                    @if(auth()->user()->isSiswa())
                                        <a href="{{ route('materials.mine', ['subject' => $cs->subject_id]) }}" class="flex flex-col items-center justify-center gap-0.5 rounded-lg bg-white py-1 transition hover:bg-emerald-50 hover:text-emerald-700 ring-1 ring-slate-200/80">
                                            <span class="text-xs font-extrabold text-emerald-600">{{ $cs->materials_count }}</span>
                                            <span class="text-[8px] font-bold text-slate-400 uppercase">Materi</span>
                                        </a>
                                        <a href="{{ route('student.assignments.index', ['subject' => $cs->subject_id]) }}" class="flex flex-col items-center justify-center gap-0.5 rounded-lg bg-white py-1 transition hover:bg-blue-50 hover:text-blue-700 ring-1 ring-slate-200/80">
                                            <span class="text-xs font-extrabold text-blue-600">{{ $cs->assignments_count }}</span>
                                            <span class="text-[8px] font-bold text-slate-400 uppercase">Tugas</span>
                                        </a>
                                        <a href="{{ route('discussions.index', $cs->id) }}" class="flex flex-col items-center justify-center gap-0.5 rounded-lg bg-white py-1 transition hover:bg-purple-50 hover:text-purple-700 ring-1 ring-slate-200/80">
                                            <span class="text-xs font-extrabold text-purple-600"><i class="bi bi-chat-left-text text-[10px]"></i></span>
                                            <span class="text-[8px] font-bold text-slate-400 uppercase">Forum</span>
                                        </a>
                                    @else
                                        <a href="{{ route('materials.index', $cs->id) }}" class="flex flex-col items-center justify-center gap-0.5 rounded-lg bg-white py-1 transition hover:bg-emerald-50 hover:text-emerald-700 ring-1 ring-slate-200/80">
                                            <span class="text-xs font-extrabold text-emerald-600">{{ $cs->materials_count }}</span>
                                            <span class="text-[8px] font-bold text-slate-400 uppercase">Materi</span>
                                        </a>
                                        <a href="{{ route('assignments.index', $cs->id) }}" class="flex flex-col items-center justify-center gap-0.5 rounded-lg bg-white py-1 transition hover:bg-blue-50 hover:text-blue-700 ring-1 ring-slate-200/80">
                                            <span class="text-xs font-extrabold text-blue-600">{{ $cs->assignments_count }}</span>
                                            <span class="text-[8px] font-bold text-slate-400 uppercase">Tugas</span>
                                        </a>
                                        <a href="{{ route('discussions.index', $cs->id) }}" class="flex flex-col items-center justify-center gap-0.5 rounded-lg bg-white py-1 transition hover:bg-purple-50 hover:text-purple-700 ring-1 ring-slate-200/80">
                                            <span class="text-xs font-extrabold text-purple-600"><i class="bi bi-chat-left-text text-[10px]"></i></span>
                                            <span class="text-[8px] font-bold text-slate-400 uppercase">Forum</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @push('scripts')
        <script>
            let selectedGrade = 'all';
            let selectedClassId = 'all';
            let selectedTeacherId = 'all';
            let selectedSubjectId = 'all';
            let searchQuery = '';

            document.addEventListener('DOMContentLoaded', function() {
                const searchInput = document.getElementById('elearning-search');
                if (searchInput) {
                    searchInput.addEventListener('input', function() {
                        searchQuery = this.value.toLowerCase().trim();
                        applyFilters();
                    });
                }
            });

            function toggleSection(classId) {
                const content = document.getElementById(`content-section-${classId}`);
                const icon = document.getElementById(`icon-chevron-${classId}`);
                if (content) {
                    const isHidden = content.classList.contains('hidden');
                    if (isHidden) {
                        content.classList.remove('hidden');
                        if (icon) icon.classList.add('rotate-180');
                    } else {
                        content.classList.add('hidden');
                        if (icon) icon.classList.remove('rotate-180');
                    }
                }
            }

            function expandAllSections() {
                document.querySelectorAll('.section-content').forEach(c => c.classList.remove('hidden'));
                document.querySelectorAll('[id^="icon-chevron-"]').forEach(i => i.classList.add('rotate-180'));
            }

            function collapseAllSections() {
                document.querySelectorAll('.section-content').forEach(c => c.classList.add('hidden'));
                document.querySelectorAll('[id^="icon-chevron-"]').forEach(i => i.classList.remove('rotate-180'));
            }

            function filterGrade(grade) {
                selectedGrade = grade;
                
                document.querySelectorAll('.grade-filter-btn').forEach(btn => {
                    btn.classList.remove('bg-brand-600', 'text-white', 'ring-brand-600', 'shadow-sm', 'font-bold');
                    btn.classList.add('bg-slate-50', 'text-slate-600', 'ring-slate-200', 'font-semibold');
                });

                const activeBtn = document.getElementById(`btn-grade-${grade}`);
                if (activeBtn) {
                    activeBtn.classList.remove('bg-slate-50', 'text-slate-600', 'ring-slate-200');
                    activeBtn.classList.add('bg-brand-600', 'text-white', 'ring-brand-600', 'shadow-sm', 'font-bold');
                }

                applyFilters();
            }

            function filterClass(classId) {
                selectedClassId = classId;
                const select = document.getElementById('select-class-filter');
                if (select) select.value = classId;
                applyFilters();
            }

            function filterTeacher(teacherId) {
                selectedTeacherId = teacherId;
                const select = document.getElementById('select-teacher-filter');
                if (select) select.value = teacherId;
                applyFilters();
            }

            function filterSubject(subjectId) {
                selectedSubjectId = subjectId;
                const select = document.getElementById('select-subject-filter');
                if (select) select.value = subjectId;
                applyFilters();
            }

            function applyFilters() {
                const sections = document.querySelectorAll('.class-section');

                sections.forEach(section => {
                    const classId = section.getAttribute('data-class-id');
                    const gradeVal = section.getAttribute('data-grade');
                    const cards = section.querySelectorAll('.subject-card');
                    const content = document.getElementById(`content-section-${classId}`);
                    const icon = document.getElementById(`icon-chevron-${classId}`);
                    let visibleCardsCount = 0;

                    cards.forEach(card => {
                        const teacherId = card.getAttribute('data-teacher-id');
                        const subjectId = card.getAttribute('data-subject-id');
                        const subjectName = card.querySelector('h5').textContent.toLowerCase();
                        const teacherName = card.querySelector('.truncate').textContent.toLowerCase();

                        const matchesGrade = (selectedGrade === 'all' || gradeVal === selectedGrade);
                        const matchesClass = (selectedClassId === 'all' || classId === selectedClassId);
                        const matchesTeacher = (selectedTeacherId === 'all' || teacherId === selectedTeacherId);
                        const matchesSubject = (selectedSubjectId === 'all' || subjectId === selectedSubjectId);
                        const matchesSearch = (subjectName.includes(searchQuery) || teacherName.includes(searchQuery));

                        if (matchesGrade && matchesClass && matchesTeacher && matchesSubject && matchesSearch) {
                            card.style.display = 'flex';
                            visibleCardsCount++;
                        } else {
                            card.style.display = 'none';
                        }
                    });

                    if (visibleCardsCount > 0) {
                        section.style.display = 'block';
                        // Jika difilter spesifik kelas/tingkat/search, otomatis buka sectionnya
                        if (selectedClassId !== 'all' || selectedGrade !== 'all' || searchQuery !== '') {
                            if (content) content.classList.remove('hidden');
                            if (icon) icon.classList.add('rotate-180');
                        }
                    } else {
                        section.style.display = 'none';
                    }
                });
            }
        </script>
        @endpush
    @else
        <div class="rounded-2xl bg-white p-12 text-center shadow-sm ring-1 ring-slate-200/70">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-brand-50">
                <i class="bi bi-book text-3xl text-brand-500"></i>
            </div>
            <h3 class="mt-4 text-base font-bold text-slate-900">Belum ada mata pelajaran kelas</h3>
            <p class="mt-2 text-sm text-slate-500">Belum ada mata pelajaran kelas yang ditugaskan kepada Anda.</p>
        </div>
    @endif
</div>
@endsection
