@extends('layouts.app')
@section('title', 'Data Guru')
@section('page-title', 'Data Guru')

@section('content')
<div class="space-y-6">
    {{-- Toolbar --}}
    <x-master-data-toolbar 
        createRoute="{{ route('teachers.create') }}" 
        createLabel="Tambah Guru" 
        :trashedRoute="route('teachers.trashed')"
        importRoute="{{ route('teachers.import') }}"
        templateRoute="{{ route('teachers.import.template') }}"
    >
        <p class="text-sm text-slate-500">Total: <strong class="text-slate-800">{{ $teachers->total() }}</strong> guru</p>
    </x-master-data-toolbar>

    {{-- Filter --}}
    <x-card glass="true">
        <form method="GET" class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-12">
            <div class="lg:col-span-5">
                <label class="mb-1 block text-xs font-bold text-slate-500">Pencarian</label>
                <div class="relative">
                    <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, NIP, mata pelajaran..."
                        class="block w-full rounded-lg border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-sm text-slate-800 placeholder:text-slate-400 transition focus:border-brand-500 focus:bg-white focus:ring-2 focus:ring-brand-500/20">
                </div>
            </div>
            <div class="lg:col-span-3">
                <label class="mb-1 block text-xs font-bold text-slate-500">Status</label>
                <select name="status" class="block w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-800 transition focus:border-brand-500 focus:bg-white focus:ring-2 focus:ring-brand-500/20">
                    <option value="">Semua Status</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Non-Aktif</option>
                </select>
            </div>
            <div class="flex items-end lg:col-span-2">
                <button type="submit" class="inline-flex w-full items-center justify-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700">
                    <i class="bi bi-funnel"></i>Filter
                </button>
            </div>
            <div class="flex items-end lg:col-span-2">
                <a href="{{ route('teachers.index') }}" class="inline-flex w-full items-center justify-center gap-1.5 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                    Reset
                </a>
            </div>
        </form>
    </x-card>

    {{-- Table --}}
    <x-card noPadding="true" glass="true">
        <x-data-table :headers="['#', 'NIP', 'Nama Guru', 'Mata Pelajaran', 'Wali Kelas', 'No. HP', 'Status', 'Aksi']" :empty="$teachers->isEmpty()" emptyMessage="Belum ada data guru ditemukan." emptyIcon="bi-person-badge">
            @foreach($teachers as $i => $teacher)
            <tr class="transition hover:bg-slate-50/50">
                <td class="whitespace-nowrap px-4 py-3 text-slate-500">{{ $teachers->firstItem() + $i }}</td>
                <td class="whitespace-nowrap px-4 py-3">
                    <code class="rounded-md bg-slate-100 px-2 py-0.5 text-xs font-bold text-brand-700">{{ $teacher->nip ?? '-' }}</code>
                </td>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-brand-600 to-violet-600 font-bold text-white shadow-sm">
                            {{ strtoupper(substr($teacher->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="font-semibold text-slate-900">{{ $teacher->name }}</div>
                            <div class="text-xs text-slate-500">{{ $teacher->email ?? '-' }}</div>
                        </div>
                    </div>
                </td>
                <td class="whitespace-nowrap px-4 py-3">
                    <div class="flex flex-wrap gap-1">
                        @forelse($teacher->subjects as $subj)
                            <x-badge tone="violet">{{ $subj->name }}</x-badge>
                        @empty
                            <x-badge tone="violet">{{ $teacher->subject ?? '-' }}</x-badge>
                        @endforelse
                    </div>
                </td>
                <td class="whitespace-nowrap px-4 py-3">
                    <div class="flex flex-wrap gap-1">
                        @forelse($teacher->homeroomClasses as $cls)
                            <x-badge tone="blue">{{ $cls->name }}</x-badge>
                        @empty
                            <span class="text-xs text-slate-400">-</span>
                        @endforelse
                    </div>
                </td>
                <td class="whitespace-nowrap px-4 py-3">
                    <div class="text-sm text-slate-700">{{ $teacher->phone ?? '-' }}</div>
                </td>
                <td class="whitespace-nowrap px-4 py-3">
                    <x-badge tone="{{ $teacher->status ? 'success' : 'slate' }}">
                        {{ $teacher->status ? 'Aktif' : 'Non-Aktif' }}
                    </x-badge>
                </td>
                <td class="whitespace-nowrap px-4 py-3 text-right">
                    <div class="inline-flex items-center divide-x divide-slate-200 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                        <a href="{{ route('teachers.show', $teacher) }}" class="px-2.5 py-1.5 text-slate-500 transition hover:bg-brand-50 hover:text-brand-600" title="Detail">
                            <i class="bi bi-eye-fill"></i>
                        </a>
                        <a href="{{ route('teachers.edit', $teacher) }}" class="px-2.5 py-1.5 text-slate-500 transition hover:bg-brand-50 hover:text-brand-600" title="Edit">
                            <i class="bi bi-pencil-fill"></i>
                        </a>
                        <button type="button" class="px-2.5 py-1.5 text-slate-500 transition hover:bg-amber-50 hover:text-amber-600"
                            data-confirm="Reset password guru {{ $teacher->name }} ke default (format tanggal lahir YYYY-MM-DD)?"
                            data-form="reset-{{ $teacher->id }}" title="Reset Password">
                            <i class="bi bi-key-fill"></i>
                        </button>
                        <button class="px-2.5 py-1.5 text-slate-500 transition hover:bg-rose-50 hover:text-rose-600" 
                            data-confirm="Hapus guru {{ $teacher->name }}?" 
                            data-form="del-{{ $teacher->id }}" title="Hapus">
                            <i class="bi bi-trash-fill"></i>
                        </button>
                    </div>
                    <form id="reset-{{ $teacher->id }}" method="POST" action="{{ route('teachers.reset-password', $teacher) }}" class="hidden">
                        @csrf
                    </form>
                    <form id="del-{{ $teacher->id }}" method="POST" action="{{ route('teachers.destroy', $teacher) }}" class="hidden">
                        @csrf @method('DELETE')
                    </form>
                </td>
            </tr>
            @endforeach
        </x-data-table>

        @if($teachers->hasPages())
        <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 px-4 py-3">
            <div class="text-sm text-slate-500">
                Menampilkan <strong>{{ $teachers->firstItem() ?? 0 }} - {{ $teachers->lastItem() ?? 0 }}</strong> dari <strong>{{ $teachers->total() }}</strong> guru
            </div>
            {{ $teachers->withQueryString()->links('pagination::tailwind') }}
        </div>
        @endif
    </x-card>
</div>
@endsection
