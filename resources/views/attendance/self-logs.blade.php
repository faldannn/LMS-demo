@extends('layouts.app')
@section('title', 'Log Presensi Mandiri')
@section('page-title', 'Log Presensi Wajah & GPS')

@section('content')
<div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50/80 text-xs font-bold uppercase tracking-wider text-slate-500">
                    <th class="px-5 py-3">Waktu</th>
                    <th class="px-5 py-3">Siswa</th>
                    <th class="px-5 py-3">Mode</th>
                    <th class="px-5 py-3">GPS</th>
                    <th class="px-5 py-3">Wajah</th>
                    <th class="px-5 py-3">Device</th>
                    <th class="px-5 py-3">IP Address</th>
                    <th class="px-5 py-3">Foto</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($logs as $log)
                <tr class="transition hover:bg-slate-50/30">
                    <td class="px-5 py-4 text-xs font-medium text-slate-500">
                        {{ $log->checked_in_at->format('d/m/Y H:i:s') }}
                    </td>
                    <td class="px-5 py-4">
                        <div class="font-semibold text-slate-900">{{ $log->student->name }}</div>
                        <div class="text-xs text-slate-500">NIS: {{ $log->student->nis }}</div>
                    </td>
                    <td class="px-5 py-4">
                        <span class="inline-flex items-center rounded-full bg-slate-50 px-2.5 py-0.5 text-xs font-semibold text-slate-700 ring-1 ring-inset ring-slate-600/10">
                            {{ $log->mode_label }}
                        </span>
                    </td>
                    <td class="px-5 py-4 text-xs text-slate-700">
                        @if($log->latitude)
                            <span class="inline-flex items-center gap-1">
                                @if($log->within_radius)
                                    <span class="text-emerald-600 font-bold">✓</span>
                                @else
                                    <span class="text-rose-600 font-bold">✗</span>
                                @endif
                                {{ round($log->distance_meters) }}m
                            </span>
                        @else
                            <span class="text-slate-400">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-xs text-slate-700">
                        @if($log->face_matched)
                            <span class="inline-flex items-center gap-1">
                                <span class="text-emerald-600 font-bold">✓</span>
                                {{ round(($log->face_match_confidence ?? 0)*100) }}%
                            </span>
                        @else
                            <span class="text-slate-400">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-xs text-slate-500">
                        {{ $log->device_type }} / {{ $log->browser }}
                    </td>
                    <td class="px-5 py-4 text-xs font-mono text-slate-500">
                        {{ $log->ip_address }}
                    </td>
                    <td class="px-5 py-4">
                        @if($log->photo_path)
                            <a href="{{ asset('storage/'.$log->photo_path) }}" target="_blank" class="inline-flex items-center gap-1 rounded-lg border border-brand-300 bg-white px-2.5 py-1 text-xs font-semibold text-brand-700 shadow-sm transition hover:bg-brand-50">
                                <i class="bi bi-eye"></i> Lihat
                            </a>
                        @else
                            <span class="text-xs text-slate-400">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-5 py-8 text-center text-slate-400">
                        <i class="bi bi-person-x text-3xl d-block mb-2 text-slate-300"></i>
                        Belum ada log presensi mandiri.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
        <div class="border-t border-slate-100 px-5 py-4">
            {{ $logs->links() }}
        </div>
    @endif
</div>
@endsection
