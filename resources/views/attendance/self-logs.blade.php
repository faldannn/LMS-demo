@extends('layouts.app')
@section('title', 'Log Presensi Mandiri')
@section('page-title', 'Log Presensi Wajah & GPS')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="px-3">Waktu</th>
                    <th>Siswa</th>
                    <th>Mode</th>
                    <th>GPS</th>
                    <th>Wajah</th>
                    <th>Device</th>
                    <th>IP</th>
                    <th>Foto</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td class="px-3 small">{{ $log->checked_in_at->format('d/m/Y H:i:s') }}</td>
                    <td>
                        <div class="fw-bold small">{{ $log->student->name }}</div>
                        <div class="text-muted" style="font-size:11px">{{ $log->student->nis }}</div>
                    </td>
                    <td><span class="badge bg-light text-dark border">{{ $log->mode_label }}</span></td>
                    <td class="small">
                        @if($log->latitude)
                        {{ $log->within_radius ? '✓' : '✗' }} {{ round($log->distance_meters) }}m
                        @else — @endif
                    </td>
                    <td class="small">{{ $log->face_matched ? '✓ '.round(($log->face_match_confidence ?? 0)*100).'%' : '—' }}</td>
                    <td class="small">{{ $log->device_type }} / {{ $log->browser }}</td>
                    <td class="small">{{ $log->ip_address }}</td>
                    <td>
                        @if($log->photo_path)
                        <a href="{{ asset('storage/'.$log->photo_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">Lihat</a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-4">Belum ada log presensi mandiri.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())<div class="p-3">{{ $logs->links() }}</div>@endif
</div>
@endsection
