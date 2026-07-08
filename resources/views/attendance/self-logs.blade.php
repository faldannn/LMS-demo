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
                <tr class="transition hover:bg-slate-50/50">
                    <td class="px-5 py-4 text-xs font-semibold text-slate-600">
                        {{ $log->checked_in_at->translatedFormat('d M Y') }}
                        <div class="text-[10px] text-slate-400 font-normal mt-0.5">{{ $log->checked_in_at->format('H:i:s') }}</div>
                    </td>
                    <td class="px-5 py-4">
                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ $log->student->photo_url }}" class="h-9 w-9 rounded-full object-cover border border-slate-200" alt="">
                            <div>
                                <div class="font-bold text-slate-900">{{ $log->student->name }}</div>
                                <div class="text-xs text-slate-500">NIS: {{ $log->student->nis }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-4">
                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-800 ring-1 ring-inset ring-slate-600/10">
                            {{ $log->mode_label }}
                        </span>
                    </td>
                    <td class="px-5 py-4 text-xs">
                        @if($log->latitude)
                            <div class="flex flex-col">
                                <span class="inline-flex items-center gap-1 font-semibold">
                                    @if($log->within_radius)
                                        <span class="text-emerald-600 font-bold">✓ Di Radius</span>
                                    @else
                                        <span class="text-rose-600 font-bold">✗ Luar Radius</span>
                                    @endif
                                </span>
                                <span class="text-slate-500 text-[11px] mt-0.5">{{ round($log->distance_meters) }}m dari sekolah</span>
                                <a href="https://www.google.com/maps/search/?api=1&query={{ $log->latitude }},{{ $log->longitude }}" target="_blank" class="text-brand-600 hover:underline text-[10px] font-semibold mt-1">
                                    <i class="bi bi-geo-alt-fill"></i> Lihat Peta
                                </a>
                            </div>
                        @else
                            <span class="text-slate-400">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-xs">
                        @if($log->face_matched)
                            <div class="flex flex-col">
                                <span class="inline-flex items-center gap-1 font-semibold text-emerald-600">
                                    ✓ Cocok
                                </span>
                                <span class="text-slate-500 text-[11px] mt-0.5">Match: {{ round(($log->face_match_confidence ?? 0)*100) }}%</span>
                            </div>
                        @elseif($log->face_detection_confidence > 0)
                            <div class="flex flex-col">
                                <span class="inline-flex items-center gap-1 font-semibold text-rose-600">
                                    ✗ Tidak Cocok
                                </span>
                                <span class="text-slate-500 text-[11px] mt-0.5">Deteksi: {{ round(($log->face_detection_confidence ?? 0)*100) }}%</span>
                            </div>
                        @else
                            <span class="text-slate-400">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-xs text-slate-500">
                        <span class="capitalize">{{ $log->device_type }}</span>
                        <div class="text-[11px] text-slate-400 mt-0.5">{{ $log->browser }}</div>
                    </td>
                    <td class="px-5 py-4 text-xs font-mono text-slate-500">
                        {{ $log->ip_address }}
                    </td>
                    <td class="px-5 py-4">
                        @if($log->photo_path)
                            <a href="{{ asset('storage/'.$log->photo_path) }}" target="_blank" title="Klik untuk memperbesar" class="inline-block hover:scale-105 transition-transform duration-200">
                                <img src="{{ asset('storage/'.$log->photo_path) }}" class="h-10 w-10 rounded-lg object-cover border border-slate-200 shadow-sm" alt="Foto">
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
