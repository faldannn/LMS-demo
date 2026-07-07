{{-- resources/views/attendance/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Daftar Absensi')
@section('page-title', 'Daftar Absensi')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-sm text-slate-500">Semua sesi absensi kelas yang tercatat</p>
        </div>
        @role('admin,guru')
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('attendance.create') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700">
                <i class="bi bi-plus-lg"></i> Input Absensi
            </a>
        </div>
        @endrole
    </div>

    {{-- Filter --}}
    <div class="mb-6 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200/60">
        <form method="GET" class="grid grid-cols-1 gap-4 sm:grid-cols-12 items-end">
            <div class="sm:col-span-3">
                <label class="block mb-1.5 text-xs font-bold uppercase tracking-wider text-slate-500">Filter Kelas</label>
                <select name="class_id" class="form-select">
                    <option value="">Semua Kelas</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="sm:col-span-3">
                <label class="block mb-1.5 text-xs font-bold uppercase tracking-wider text-slate-500">Tanggal Spesifik</label>
                <input type="date" name="date" class="form-control" value="{{ request('date') }}">
            </div>
            <div class="sm:col-span-3">
                <label class="block mb-1.5 text-xs font-bold uppercase tracking-wider text-slate-500">Bulan</label>
                <input type="month" name="month" class="form-control" value="{{ request('month') }}">
            </div>
            <div class="sm:col-span-3 flex gap-2">
                <button type="submit" class="inline-flex w-full items-center justify-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700">
                    <i class="bi bi-funnel"></i> Filter
                </button>
                <a href="{{ route('attendance.index') }}" class="inline-flex w-full items-center justify-center gap-1.5 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/80 text-xs font-bold uppercase tracking-wider text-slate-500">
                        <th class="px-5 py-3">Tanggal</th>
                        <th class="px-5 py-3">Kelas</th>
                        <th class="px-5 py-3">Sesi</th>
                        <th class="px-5 py-3">Guru / Pengajar</th>
                        <th class="px-5 py-3 text-center">Statistik Kehadiran</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($attendances as $att)
                        <tr class="transition hover:bg-slate-50/30">
                            <td class="px-5 py-4">
                                <div class="font-semibold text-slate-900">{{ $att->date->translatedFormat('d F Y') }}</div>
                                <div class="text-xs text-slate-500">{{ $att->date->translatedFormat('l') }}</div>
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center rounded-full bg-brand-50 px-2.5 py-1 text-[11px] font-bold text-brand-700">
                                    {{ $att->class?->name }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                @if($att->session === 'pagi')
                                    <span class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-1 text-[11px] font-bold text-amber-700">PAGI</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-sky-50 px-2.5 py-1 text-[11px] font-bold text-sky-700">SIANG</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <div class="font-semibold text-slate-900">{{ $att->teacher?->name ?? 'System' }}</div>
                                <div class="text-xs text-slate-500">NIP: {{ $att->teacher?->nip ?? '-' }}</div>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <div class="flex justify-center gap-3">
                                    <div class="text-center">
                                        <div class="text-[9px] font-bold uppercase tracking-wider text-slate-400 mb-0.5">Hadir</div>
                                        <span class="inline-flex items-center rounded bg-emerald-50 px-2 py-0.5 text-xs font-bold text-emerald-700">
                                            {{ $att->details->whereIn('status', ['hadir', 'terlambat'])->count() }}
                                        </span>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-[9px] font-bold uppercase tracking-wider text-slate-400 mb-0.5">Izin/Sakit</div>
                                        <span class="inline-flex items-center rounded bg-sky-50 px-2 py-0.5 text-xs font-bold text-sky-700">
                                            {{ $att->details->whereIn('status', ['izin', 'sakit'])->count() }}
                                        </span>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-[9px] font-bold uppercase tracking-wider text-slate-400 mb-0.5">Alfa</div>
                                        <span class="inline-flex items-center rounded bg-rose-50 px-2 py-0.5 text-xs font-bold text-rose-700">
                                            {{ $att->details->where('status', 'alfa')->count() }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="inline-flex items-center divide-x divide-slate-200 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                                    <a href="{{ route('attendance.show', $att) }}" class="px-2.5 py-1.5 text-slate-500 transition hover:bg-brand-50 hover:text-brand-600" title="Detail">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                    @role('admin,guru')
                                    <a href="{{ route('attendance.edit', $att) }}" class="px-2.5 py-1.5 text-slate-500 transition hover:bg-brand-50 hover:text-brand-600" title="Edit">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    @endrole
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-slate-400">
                                <div class="py-4 text-center">
                                    <i class="bi bi-clipboard-x text-4xl text-slate-300 d-block mb-2"></i>
                                    <h5 class="font-bold text-slate-800">Data absensi belum tersedia</h5>
                                    <p class="text-xs text-slate-500">Belum ada rekaman absensi untuk filter yang dipilih.</p>
                                    @role('admin,guru')
                                    <a href="{{ route('attendance.create') }}" class="btn btn-primary mt-3">
                                        <i class="bi bi-plus-lg me-1"></i>Input Absensi Baru
                                    </a>
                                    @endrole
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($attendances->hasPages())
        <div class="flex items-center justify-between border-t border-slate-100 px-5 py-4 flex-wrap gap-3">
            <div class="text-sm text-slate-500">
                Menampilkan <strong>{{ $attendances->firstItem() ?? 0 }} - {{ $attendances->lastItem() ?? 0 }}</strong> dari <strong>{{ $attendances->total() }}</strong> sesi
            </div>
            {{ $attendances->withQueryString()->links('pagination::tailwind') }}
        </div>
        @endif
    </div>
@endsection
