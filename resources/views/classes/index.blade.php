{{-- resources/views/classes/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Data Kelas')
@section('page-title', 'Data Kelas')

@section('content')
<div class="space-y-6">
    {{-- Toolbar --}}
    <x-master-data-toolbar 
        createRoute="{{ route('classes.create') }}" 
        createLabel="Tambah Kelas" 
        :trashedRoute="route('classes.trashed')"
        :exportExcelRoute="route('classes.export.excel')"
        :exportPdfRoute="route('classes.export.pdf')"
        :importRoute="route('classes.import')"
    >
        <p class="text-sm text-slate-500">
            Total: <strong class="text-slate-800">{{ $classes->total() }}</strong> kelas
            @if($activeYear) · Tahun Ajaran Aktif: <strong class="text-slate-800">{{ $activeYear->year }}</strong> @endif
        </p>
    </x-master-data-toolbar>

    {{-- Filter --}}
    <x-card glass="true">
        <form method="GET" class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-12">
            <!-- Search -->
            <div class="lg:col-span-4">
                <label class="mb-1 block text-xs font-bold text-slate-500">Pencarian</label>
                <div class="relative">
                    <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama kelas atau ruang..."
                        class="block w-full rounded-lg border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-sm text-slate-800 placeholder:text-slate-400 transition focus:border-brand-500 focus:bg-white focus:ring-2 focus:ring-brand-500/20">
                </div>
            </div>

            <!-- Grade -->
            <div class="lg:col-span-2">
                <label class="mb-1 block text-xs font-bold text-slate-500">Tingkat</label>
                <select name="grade" class="block w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-800 transition focus:border-brand-500 focus:bg-white focus:ring-2 focus:ring-brand-500/20">
                    <option value="">Semua Tingkat</option>
                    <option value="10" {{ request('grade') === '10' ? 'selected' : '' }}>Kelas 10</option>
                    <option value="11" {{ request('grade') === '11' ? 'selected' : '' }}>Kelas 11</option>
                    <option value="12" {{ request('grade') === '12' ? 'selected' : '' }}>Kelas 12</option>
                </select>
            </div>

            <!-- Major -->
            <div class="lg:col-span-2">
                <label class="mb-1 block text-xs font-bold text-slate-500">Jurusan</label>
                <select name="major_id" class="block w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-800 transition focus:border-brand-500 focus:bg-white focus:ring-2 focus:ring-brand-500/20">
                    <option value="">Semua Jurusan</option>
                    @foreach($majors as $major)
                        <option value="{{ $major->id }}" {{ request('major_id') == $major->id ? 'selected' : '' }}>
                            {{ $major->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Academic Year -->
            <div class="lg:col-span-2">
                <label class="mb-1 block text-xs font-bold text-slate-500">Tahun Ajaran</label>
                <select name="academic_year_id" class="block w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-800 transition focus:border-brand-500 focus:bg-white focus:ring-2 focus:ring-brand-500/20">
                    <option value="">Semua TA</option>
                    @foreach($academicYears as $year)
                        <option value="{{ $year->id }}" {{ request('academic_year_id') == $year->id ? 'selected' : '' }}>
                            {{ $year->year }} {{ $year->is_active ? '(Aktif)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Button -->
            <div class="flex items-end lg:col-span-1">
                <button type="submit" class="inline-flex w-full items-center justify-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700">
                    <i class="bi bi-funnel"></i>Filter
                </button>
            </div>

            <!-- Reset Button -->
            <div class="flex items-end lg:col-span-1">
                <a href="{{ route('classes.index') }}" class="inline-flex w-full items-center justify-center gap-1.5 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                    Reset
                </a>
            </div>
        </form>
    </x-card>

    {{-- Class Cards Grid --}}
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
        @forelse($classes as $class)
            <div class="group relative flex flex-col justify-between overflow-hidden rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200/60 transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:ring-brand-500/30">
                <div>
                    <!-- Top Row: Grade Badge & Academic Year -->
                    <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                        <div class="flex items-center gap-1.5">
                            <div class="inline-flex items-center gap-1.5 rounded-full bg-brand-50 px-2.5 py-1 text-xs font-bold text-brand-700">
                                <i class="bi bi-mortarboard text-sm leading-none"></i>
                                Tingkat {{ $class->grade }}
                            </div>
                            <div class="inline-flex items-center gap-1.5 rounded-full @if(($class->session ?? 'pagi') === 'pagi') bg-amber-50 text-amber-700 @else bg-sky-50 text-sky-700 @endif px-2.5 py-1 text-xs font-extrabold uppercase tracking-wider">
                                <i class="bi @if(($class->session ?? 'pagi') === 'pagi') bi-sun-fill @else bi-moon-stars-fill @endif"></i>
                                {{ $class->session ?? 'pagi' }}
                            </div>
                        </div>
                        <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">
                            TA {{ $class->academicYear?->year ?? '-' }}
                        </span>
                    </div>

                    <!-- Class Title & Major -->
                    <h4 class="text-lg font-extrabold text-slate-900 transition duration-200 group-hover:text-brand-600">{{ $class->name }}</h4>
                    @if($class->major)
                        <p class="mt-1 flex items-center gap-1.5 text-xs text-slate-500">
                            <i class="bi bi-journal-text text-slate-400"></i>
                            {{ $class->major->name }}
                        </p>
                    @else
                        <p class="mt-1 flex items-center gap-1.5 text-xs text-slate-400 italic">
                            <i class="bi bi-journal-text"></i>
                            Umum / Tanpa Jurusan
                        </p>
                    @endif

                    <hr class="my-4 border-slate-100">

                    <!-- Homeroom Teacher -->
                    <div class="mb-4 flex items-center gap-3">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-slate-50 border border-slate-200 font-bold text-brand-600 shadow-sm">
                            @if($class->homeroomTeacher)
                                <i class="bi bi-person-fill text-brand-600"></i>
                            @else
                                <i class="bi bi-person text-slate-400"></i>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Wali Kelas</div>
                            <div class="truncate text-xs font-bold text-slate-800" title="{{ $class->homeroomTeacher?->name ?? 'Belum ada wali kelas' }}">
                                {{ $class->homeroomTeacher?->name ?? 'Belum ada wali kelas' }}
                            </div>
                        </div>
                    </div>

                    <!-- Stats Bar -->
                    <div class="grid grid-cols-2 gap-3 mb-2">
                        <!-- Siswa count -->
                        <div class="flex items-center gap-2.5 rounded-xl border border-slate-100 bg-slate-50/50 p-2">
                            <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-brand-50 text-brand-600">
                                <i class="bi bi-people-fill text-xs"></i>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-slate-900 leading-tight">{{ $class->students_count }}</div>
                                <div class="text-[9px] font-medium text-slate-500">Siswa</div>
                            </div>
                        </div>

                        <!-- Room info -->
                        <div class="flex items-center gap-2.5 rounded-xl border border-slate-100 bg-slate-50/50 p-2">
                            <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-purple-50 text-purple-600">
                                <i class="bi bi-door-closed-fill text-xs"></i>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-slate-900 leading-tight">{{ $class->room ?? '-' }}</div>
                                <div class="text-[9px] font-medium text-slate-500">Ruangan</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions Area -->
                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                    <a href="{{ route('classes.show', $class) }}" class="inline-flex flex-1 items-center justify-center gap-1.5 rounded-lg bg-brand-50 px-3 py-2 text-xs font-bold text-brand-700 transition hover:bg-brand-100">
                        <i class="bi bi-eye"></i> Detail Kelas
                    </a>
                    <div class="flex items-center gap-1.5">
                        <a href="{{ route('classes.edit', $class) }}" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-50 hover:text-brand-600" title="Edit Kelas">
                            <i class="bi bi-pencil-fill text-[11px]"></i>
                        </a>
                        <button class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 transition hover:bg-rose-50 hover:text-rose-600" 
                            data-confirm="Hapus kelas {{ $class->name }}?" 
                            data-form="del-c{{ $class->id }}" title="Hapus Kelas">
                            <i class="bi bi-trash-fill text-[11px]"></i>
                        </button>
                        <form id="del-c{{ $class->id }}" method="POST" action="{{ route('classes.destroy', $class) }}" class="hidden">
                            @csrf 
                            @method('DELETE')
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <x-card class="py-12">
                    <div class="flex flex-col items-center justify-center text-center">
                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-50 text-slate-400 mb-4">
                            <i class="bi bi-building fs-1"></i>
                        </div>
                        <h5 class="text-base font-bold text-slate-900">Belum ada data kelas</h5>
                        <p class="text-sm text-slate-500 mt-1 max-w-sm">Data kelas yang Anda cari tidak ditemukan atau belum ditambahkan.</p>
                        <a href="{{ route('classes.create') }}" class="mt-4 inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700">
                            <i class="bi bi-plus-lg"></i>Tambah Kelas
                        </a>
                    </div>
                </x-card>
            </div>
        @endforelse
    </div>

    @if($classes->hasPages())
        <div class="mt-4">
            {{ $classes->links() }}
        </div>
    @endif
</div>
@endsection
