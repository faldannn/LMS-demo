@extends('layouts.app')
@section('title', 'Mata Pelajaran')
@section('page-title', 'Mata Pelajaran')

@section('content')
<div class="space-y-6">
    <x-master-data-toolbar 
        :create-route="route('subjects.create')" 
        :import-route="route('subjects.import')" 
        :export-excel-route="route('subjects.export.excel', request()->query())" 
        :export-pdf-route="route('subjects.export.pdf', request()->query())" 
        :trashed-route="route('subjects.trashed')" 
        create-label="Tambah Mapel">
        <div class="flex flex-wrap items-center gap-4">
            <p class="text-sm text-slate-500">Total: <strong class="text-slate-800">{{ $subjects->total() }}</strong> mata pelajaran</p>
            <div class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white p-1 shadow-sm">
                <button id="btn-view-list" class="inline-flex items-center justify-center rounded-md p-1.5 text-slate-500 hover:bg-slate-50 hover:text-slate-700 transition focus:outline-none" title="Tampilan Tabel">
                    <i class="bi bi-list-ul text-lg leading-none"></i>
                </button>
                <button id="btn-view-grid" class="inline-flex items-center justify-center rounded-md p-1.5 text-slate-500 hover:bg-slate-50 hover:text-slate-700 transition focus:outline-none" title="Tampilan Kartu">
                    <i class="bi bi-grid-fill text-lg leading-none"></i>
                </button>
            </div>
        </div>
    </x-master-data-toolbar>

    <x-card glass="true">
        <form method="GET" class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-12">
            <div class="lg:col-span-6">
                <div class="relative">
                    <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode atau nama mata pelajaran..."
                        class="block w-full rounded-lg border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-sm text-slate-800 placeholder:text-slate-400 transition focus:border-brand-500 focus:bg-white focus:ring-2 focus:ring-brand-500/20">
                </div>
            </div>
            <div class="lg:col-span-3">
                <select name="status" class="block w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-800 transition focus:border-brand-500 focus:bg-white focus:ring-2 focus:ring-brand-500/20">
                    <option value="">Semua Status</option>
                    <option value="1" @selected(request('status') === '1')>Aktif</option>
                    <option value="0" @selected(request('status') === '0')>Nonaktif</option>
                </select>
            </div>
            <div class="flex items-end lg:col-span-3">
                <button type="submit" class="inline-flex w-full items-center justify-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700">
                    <i class="bi bi-funnel"></i>Filter
                </button>
            </div>
        </form>
    </x-card>

    <x-card noPadding="true" glass="true">
        <!-- List View (Table) -->
        <div id="list-view-container">
            <x-data-table :headers="['Kode', 'Nama', 'Kelompok', 'Status', 'Aksi']" :empty="$subjects->isEmpty()" emptyMessage="Belum ada data mata pelajaran." emptyIcon="bi-book">
                @foreach($subjects as $subject)
                <tr class="transition hover:bg-slate-50/50">
                    <td class="whitespace-nowrap px-4 py-3">
                        <code class="rounded-md bg-slate-100 px-2 py-0.5 text-xs font-bold text-slate-700">{{ $subject->code }}</code>
                    </td>
                    <td class="px-4 py-3 font-medium text-slate-900">{{ $subject->name }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm text-slate-600">{{ $subject->group ?? '-' }}</td>
                    <td class="whitespace-nowrap px-4 py-3">
                        <x-badge tone="{{ $subject->status ? 'success' : 'slate' }}">
                            {{ $subject->status ? 'Aktif' : 'Nonaktif' }}
                        </x-badge>
                    </td>
                    <td class="whitespace-nowrap px-4 py-3 text-right">
                        <div class="inline-flex items-center divide-x divide-slate-200 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                            <a href="{{ route('subjects.show', $subject) }}" class="px-2.5 py-1.5 text-slate-500 transition hover:bg-brand-50 hover:text-brand-600" title="Detail">
                                <i class="bi bi-eye-fill"></i>
                            </a>
                            <a href="{{ route('subjects.edit', $subject) }}" class="px-2.5 py-1.5 text-slate-500 transition hover:bg-brand-50 hover:text-brand-600" title="Edit">
                                <i class="bi bi-pencil-fill"></i>
                            </a>
                            <button class="px-2.5 py-1.5 text-slate-500 transition hover:bg-rose-50 hover:text-rose-600" 
                                data-confirm="Hapus mapel {{ $subject->name }}?" 
                                data-form="del-{{ $subject->id }}" title="Hapus">
                                <i class="bi bi-trash-fill"></i>
                            </button>
                        </div>
                        <form id="del-{{ $subject->id }}" method="POST" action="{{ route('subjects.destroy', $subject) }}" class="hidden">
                            @csrf @method('DELETE')
                        </form>
                    </td>
                </tr>
                @endforeach
            </x-data-table>
        </div>

        <!-- Grid View (Cards) -->
        <div id="grid-view-container" class="hidden p-6">
            @if($subjects->isEmpty())
                <div class="flex flex-col items-center justify-center py-12 text-slate-400">
                    <i class="bi bi-book text-5xl mb-3"></i>
                    <p class="text-sm font-medium">Belum ada data mata pelajaran.</p>
                </div>
            @else
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                    @foreach($subjects as $subject)
                    <div class="flex flex-col justify-between rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-md hover:border-brand-200">
                        <div>
                            <!-- Header Row (Icon & Status) -->
                            <div class="flex items-center justify-between w-full mb-4">
                                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-brand-500 to-brand-600 text-white font-bold text-base shadow-sm">
                                    {{ strtoupper(substr($subject->name, 0, 2)) }}
                                </div>
                                <x-badge tone="{{ $subject->status ? 'success' : 'slate' }}">
                                    {{ $subject->status ? 'Aktif' : 'Nonaktif' }}
                                </x-badge>
                            </div>
                            
                            <!-- Subject Code -->
                            <div class="mb-1">
                                <code class="rounded-md bg-slate-100 px-2 py-0.5 text-xs font-bold text-slate-700">
                                    {{ $subject->code }}
                                </code>
                            </div>

                            <!-- Subject Name -->
                            <h4 class="text-base font-bold text-slate-900 line-clamp-2 min-h-[3rem] mb-2" title="{{ $subject->name }}">
                                {{ $subject->name }}
                            </h4>

                            <!-- Subject Group -->
                            <span class="inline-flex items-center gap-1.5 text-xs text-slate-500">
                                <i class="bi bi-tags text-slate-400"></i>
                                {{ $subject->group ?? '-' }}
                            </span>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-5 pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                            <a href="{{ route('subjects.show', $subject) }}" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:bg-brand-50 hover:text-brand-600 transition" title="Detail">
                                <i class="bi bi-eye-fill text-sm"></i>
                            </a>
                            <a href="{{ route('subjects.edit', $subject) }}" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:bg-brand-50 hover:text-brand-600 transition" title="Edit">
                                <i class="bi bi-pencil-fill text-sm"></i>
                            </a>
                            <button class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:bg-rose-50 hover:text-rose-600 transition" 
                                data-confirm="Hapus mapel {{ $subject->name }}?" 
                                data-form="del-{{ $subject->id }}" title="Hapus">
                                <i class="bi bi-trash-fill text-sm"></i>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        @if($subjects->hasPages())
        <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 px-4 py-3">
            <div class="text-sm text-slate-500">
                Menampilkan <strong>{{ $subjects->firstItem() ?? 0 }} - {{ $subjects->lastItem() ?? 0 }}</strong> dari <strong>{{ $subjects->total() }}</strong>
            </div>
            {{ $subjects->withQueryString()->links('pagination::tailwind') }}
        </div>
        @endif
    </x-card>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnList = document.getElementById('btn-view-list');
    const btnGrid = document.getElementById('btn-view-grid');
    const containerList = document.getElementById('list-view-container');
    const containerGrid = document.getElementById('grid-view-container');

    if (!btnList || !btnGrid || !containerList || !containerGrid) return;

    // Load saved view or default to list
    const currentView = localStorage.getItem('subjects_view_preference') || 'list';

    function setView(view) {
        if (view === 'grid') {
            containerList.classList.add('hidden');
            containerGrid.classList.remove('hidden');
            
            // Active style for grid button
            btnGrid.classList.add('bg-brand-50', 'text-brand-600', 'font-semibold');
            btnGrid.classList.remove('text-slate-500');
            
            // Inactive style for list button
            btnList.classList.remove('bg-brand-50', 'text-brand-600', 'font-semibold');
            btnList.classList.add('text-slate-500');
        } else {
            containerGrid.classList.add('hidden');
            containerList.classList.remove('hidden');
            
            // Active style for list button
            btnList.classList.add('bg-brand-50', 'text-brand-600', 'font-semibold');
            btnList.classList.remove('text-slate-500');
            
            // Inactive style for grid button
            btnGrid.classList.remove('bg-brand-50', 'text-brand-600', 'font-semibold');
            btnGrid.classList.add('text-slate-500');
        }
        localStorage.setItem('subjects_view_preference', view);
    }

    btnList.addEventListener('click', () => setView('list'));
    btnGrid.addEventListener('click', () => setView('grid'));

    // Initialize view
    setView(currentView);
});
</script>
@endpush

@endsection
