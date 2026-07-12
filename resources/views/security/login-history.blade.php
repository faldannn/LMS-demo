@extends('layouts.app')
@section('title', 'Riwayat Login')
@section('page-title', 'Riwayat Login')

@section('content')
<div class="space-y-6">
    {{-- Back Button --}}
    <div class="flex items-center">
        <a href="{{ route('security.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
            <i class="bi bi-arrow-left"></i>
            <span>Kembali</span>
        </a>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
        <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200/70">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
                    <i class="bi bi-check-circle-fill text-xl"></i>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-brand-600">{{ $stats['success'] }}</div>
                    <div class="text-xs font-medium text-slate-500">Login Sukses Hari Ini</div>
                </div>
            </div>
        </div>
        <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200/70">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-rose-50 text-rose-600">
                    <i class="bi bi-x-circle-fill text-xl"></i>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-rose-600">{{ $stats['failed'] }}</div>
                    <div class="text-xs font-medium text-slate-500">Login Gagal Hari Ini</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200/70">
        <form method="GET" class="flex flex-col gap-3 sm:flex-row sm:items-end sm:gap-3">
            <div class="flex-1 sm:w-48">
                <label class="mb-1 block text-xs font-semibold text-slate-500 uppercase tracking-wider">Username</label>
                <input type="text" name="username" value="{{ request('username') }}" placeholder="Cari username..." class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 transition focus:border-brand-500 focus:bg-white focus:ring-2 focus:ring-brand-500/20">
            </div>
            <div class="sm:w-40">
                <label class="mb-1 block text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</label>
                <select name="status" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-900 transition focus:border-brand-500 focus:bg-white focus:ring-2 focus:ring-brand-500/20">
                    <option value="">Semua</option>
                    <option value="success" @selected(request('status') === 'success')>Sukses</option>
                    <option value="failed" @selected(request('status') === 'failed')>Gagal</option>
                </select>
            </div>
            <div class="sm:w-40">
                <label class="mb-1 block text-xs font-semibold text-slate-500 uppercase tracking-wider">Dari</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-900 transition focus:border-brand-500 focus:bg-white focus:ring-2 focus:ring-brand-500/20">
            </div>
            <div class="sm:w-40">
                <label class="mb-1 block text-xs font-semibold text-slate-500 uppercase tracking-wider">Sampai</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-900 transition focus:border-brand-500 focus:bg-white focus:ring-2 focus:ring-brand-500/20">
            </div>
            <div class="flex items-end">
                <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700">
                    <i class="bi bi-funnel"></i>
                    <span>Filter</span>
                </button>
            </div>
        </form>
    </div>

    {{-- Login History Table --}}
    <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/70">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Waktu</th>
                        <th class="px-4 py-3 font-semibold">Username</th>
                        <th class="px-4 py-3 font-semibold">Status</th>
                        <th class="px-4 py-3 font-semibold hidden sm:table-cell">Alasan Gagal</th>
                        <th class="px-4 py-3 font-semibold hidden sm:table-cell">IP</th>
                        <th class="px-4 py-3 font-semibold hidden lg:table-cell">User Agent</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr class="border-b border-slate-100 hover:bg-slate-50/50 transition-colors">
                            <td class="px-4 py-3 whitespace-nowrap font-mono text-xs text-slate-600">{{ $log->logged_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3 font-semibold text-slate-900">{{ $log->username }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $log->status === 'success' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                    {{ $log->status === 'success' ? 'Sukses' : 'Gagal' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 hidden sm:table-cell">
                                <span class="text-slate-600">{{ $log->failure_reason ?? '—' }}</span>
                            </td>
                            <td class="px-4 py-3 hidden sm:table-cell">
                                <span class="font-mono text-xs text-slate-500">{{ $log->ip_address }}</span>
                            </td>
                            <td class="px-4 py-3 hidden lg:table-cell">
                                <span class="truncate text-slate-500" title="{{ $log->user_agent }}">{{ Str::limit($log->user_agent, 40) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-slate-100">
                                        <i class="bi bi-door-closed text-3xl text-slate-400"></i>
                                    </div>
                                    <div class="font-semibold text-slate-700">Belum ada riwayat login.</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
            <div class="border-t border-slate-200 bg-slate-50 px-4 py-3">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
