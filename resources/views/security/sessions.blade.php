@extends('layouts.app')
@section('title', 'Manajemen Sesi')
@section('page-title', 'Manajemen Sesi')

@section('content')
<div class="space-y-6">
    {{-- Header Actions --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <a href="{{ route('security.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
            <i class="bi bi-arrow-left"></i>
            <span>Kembali</span>
        </a>
        <form method="POST" action="{{ route('security.sessions.revoke-others') }}">
            @csrf
            <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg border border-rose-300 bg-white px-4 py-2.5 text-sm font-semibold text-rose-600 shadow-sm transition hover:bg-rose-50" onclick="return confirm('Cabut semua sesi lain Anda?')">
                <i class="bi bi-box-arrow-right"></i>
                <span>Cabut Sesi Lain Saya</span>
            </button>
        </form>
    </div>

    {{-- Filter Card --}}
    <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200/70">
        <form method="GET" class="flex flex-col gap-3 sm:flex-row sm:items-end sm:gap-3">
            <div class="flex-1 sm:max-w-md">
                <label class="mb-1 block text-xs font-semibold text-slate-500 uppercase tracking-wider">Filter User</label>
                <select name="user_id" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-900 transition focus:border-brand-500 focus:bg-white focus:ring-2 focus:ring-brand-500/20">
                    <option value="">Semua user aktif</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" @selected($userId == $user->id)>{{ $user->name }} ({{ $user->username }})</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700">
                    <i class="bi bi-funnel"></i>
                    <span>Filter</span>
                </button>
            </div>
        </form>
    </div>

    {{-- Sessions Table --}}
    <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/70">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 font-semibold">User</th>
                        <th class="px-4 py-3 font-semibold hidden sm:table-cell">IP</th>
                        <th class="px-4 py-3 font-semibold hidden md:table-cell">Terakhir Aktif</th>
                        <th class="px-4 py-3 font-semibold hidden lg:table-cell">User Agent</th>
                        <th class="px-4 py-3 font-semibold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sessions as $session)
                        <tr class="border-b border-slate-100 hover:bg-slate-50/50 transition-colors {{ $session->is_current ? 'bg-brand-50/50' : '' }}">
                            <td class="px-4 py-3">
                                <div class="font-semibold text-slate-900">{{ $session->user_name ?? 'Tamu' }}</div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-slate-500">{{ $session->username }}</span>
                                    @if($session->is_current)
                                        <span class="inline-flex items-center rounded-full bg-brand-100 px-2 py-0.5 text-[10px] font-bold text-brand-700">Sesi ini</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 hidden sm:table-cell">
                                <span class="font-mono text-xs text-slate-500">{{ $session->ip_address }}</span>
                            </td>
                            <td class="px-4 py-3 hidden md:table-cell">
                                <span class="text-xs text-slate-600">{{ $session->last_activity_at }}</span>
                            </td>
                            <td class="px-4 py-3 hidden lg:table-cell">
                                <span class="truncate text-slate-500">{{ Str::limit($session->user_agent, 50) }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @unless($session->is_current)
                                    <form method="POST" action="{{ route('security.sessions.destroy', $session->id) }}" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center gap-1 rounded-lg border border-rose-300 bg-white px-3 py-1.5 text-xs font-semibold text-rose-600 transition hover:bg-rose-50" onclick="return confirm('Cabut sesi ini?')">
                                            <i class="bi bi-box-arrow-right"></i>
                                            <span>Cabut</span>
                                        </button>
                                    </form>
                                @endunless
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-slate-100">
                                        <i class="bi bi-pc-display-horizontal text-3xl text-slate-400"></i>
                                    </div>
                                    <div class="font-semibold text-slate-700">Tidak ada sesi aktif.</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
