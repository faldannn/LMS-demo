@extends('layouts.app')
@section('title', 'Sampah Guru')
@section('page-title', 'Tempat Sampah Guru')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-lg font-bold text-slate-800">Guru Terhapus (Sampah)</h2>
            <p class="text-sm text-slate-500">Data guru di halaman ini dapat dipulihkan atau dihapus secara permanen.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('teachers.index') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                <i class="bi bi-arrow-left"></i>Kembali ke Data Guru
            </a>
        </div>
    </div>

    {{-- Filter Search --}}
    <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200/60">
        <form method="GET" class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <div class="relative flex-1">
                <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, NIP, atau email di sampah..."
                    class="block w-full rounded-lg border border-slate-200 bg-slate-50 py-2.5 pl-9 pr-4 text-sm text-slate-800 placeholder:text-slate-400 transition focus:border-brand-500 focus:bg-white focus:ring-2 focus:ring-brand-500/20">
            </div>
            <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700">
                <i class="bi bi-funnel"></i>Cari
            </button>
            @if(request('search'))
                <a href="{{ route('teachers.trashed') }}" class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                    <i class="bi bi-x-lg"></i>Reset
                </a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 font-bold" style="width: 48px">#</th>
                        <th class="px-4 py-3 font-bold">NIP</th>
                        <th class="px-4 py-3 font-bold">Nama Guru</th>
                        <th class="px-4 py-3 font-bold text-center">Tanggal Dihapus</th>
                        <th class="px-4 py-3 font-bold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($teachers as $i => $t)
                    <tr class="transition hover:bg-slate-50/50">
                        <td class="px-4 py-3 font-semibold text-slate-400">
                            {{ $teachers->firstItem() + $i }}
                        </td>
                        <td class="px-4 py-3">
                            <code class="rounded bg-slate-100 px-2 py-0.5 text-xs font-bold text-slate-700">{{ $t->nip ?? '-' }}</code>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-semibold text-slate-900">{{ $t->name }}</div>
                            @if($t->email)
                                <div class="text-xs text-slate-400 mt-0.5">{{ $t->email }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center text-xs text-slate-500">
                            {{ $t->deleted_at ? $t->deleted_at->format('d M Y, H:i') : '—' }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <form action="{{ route('teachers.restore', $t->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-1 rounded-lg bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-700 transition hover:bg-emerald-100" title="Pulihkan Data Guru">
                                        <i class="bi bi-arrow-counterclockwise"></i>Pulihkan
                                    </button>
                                </form>
                                <form id="force-del-t{{ $t->id }}" action="{{ route('teachers.force-destroy', $t->id) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="button" data-confirm="Hapus permanen data guru {{ $t->name }}? Data yang dihapus tidak dapat dikembalikan lagi." data-form="force-del-t{{ $t->id }}" class="inline-flex items-center gap-1 rounded-lg bg-rose-50 px-3 py-1.5 text-xs font-bold text-rose-700 transition hover:bg-rose-100" title="Hapus Total / Bersih">
                                        <i class="bi bi-trash-fill"></i>Hapus Permanen
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-slate-100">
                                    <i class="bi bi-trash3 text-3xl text-slate-400"></i>
                                </div>
                                <div class="font-semibold text-slate-700">Tempat sampah kosong</div>
                                <p class="text-sm text-slate-500">Tidak ada data guru yang berada di tempat sampah saat ini.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($teachers->hasPages())
        <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 px-4 py-3">
            <div class="text-sm text-slate-500">
                Menampilkan <strong>{{ $teachers->firstItem() ?? 0 }} - {{ $teachers->lastItem() ?? 0 }}</strong> dari <strong>{{ $teachers->total() }}</strong> guru di sampah
            </div>
            {{ $teachers->withQueryString()->links('pagination::tailwind') }}
        </div>
        @endif
    </div>
</div>
@endsection
