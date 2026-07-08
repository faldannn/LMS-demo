@extends('layouts.app')
@section('title', 'Log Presensi Mandiri')
@section('page-title', 'Log Presensi Wajah & GPS')

@section('content')
<div class="card">
    <div class="card-header flex-wrap gap-2">
        <span><i class="bi bi-person-bounding-box me-2 text-primary"></i>Log Presensi Wajah & GPS</span>
        <span class="badge bg-primary">{{ $logs->total() }} log</span>
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>Siswa</th>
                    <th>Mode</th>
                    <th>GPS</th>
                    <th>Wajah</th>
                    <th>Device</th>
                    <th>IP Address</th>
                    <th>Foto</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    @php
                        $student = $log->student;
                        $matchConfidence = is_null($log->face_match_confidence) ? null : round((float) $log->face_match_confidence * 100, 1);
                        $detectionConfidence = is_null($log->face_detection_confidence) ? null : round((float) $log->face_detection_confidence * 100, 1);
                    @endphp
                    <tr>
                        <td class="whitespace-nowrap">
                            <div class="fw-semibold text-slate-700">{{ $log->checked_in_at?->translatedFormat('d M Y') ?? '-' }}</div>
                            <div class="small text-muted">{{ $log->checked_in_at?->format('H:i:s') ?? '-' }}</div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-3 min-w-[220px]">
                                <img src="{{ $student?->photo_url ?? 'https://ui-avatars.com/api/?name=Siswa&background=e2e8f0&color=475569' }}" class="h-10 w-10 rounded-full object-cover border border-slate-200" alt="Foto siswa">
                                <div class="min-w-0">
                                    <div class="fw-bold text-slate-900 truncate">{{ $student?->name ?? 'Data siswa tidak ditemukan' }}</div>
                                    <div class="small text-muted">
                                        NIS: {{ $student?->nis ?? '-' }}
                                        @if($student?->class)
                                            <span class="mx-1">|</span>{{ $student->class->name }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ $log->mode_label }}</span>
                        </td>
                        <td>
                            @if(!is_null($log->latitude) && !is_null($log->longitude))
                                <div class="space-y-1 small">
                                    @if($log->within_radius)
                                        <div class="fw-bold text-success"><i class="bi bi-check-circle-fill me-1"></i>Di Radius</div>
                                    @else
                                        <div class="fw-bold text-danger"><i class="bi bi-x-circle-fill me-1"></i>Luar Radius</div>
                                    @endif
                                    <div class="text-muted">
                                        {{ is_null($log->distance_meters) ? '-' : number_format((float) $log->distance_meters, 1, ',', '.') . ' m' }} dari sekolah
                                    </div>
                                    <a href="https://www.google.com/maps/search/?api=1&query={{ $log->latitude }},{{ $log->longitude }}" target="_blank" rel="noopener" class="text-brand-600 hover:underline fw-semibold">
                                        <i class="bi bi-geo-alt-fill me-1"></i>Lihat Peta
                                    </a>
                                </div>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($log->face_matched)
                                <div class="space-y-1 small">
                                    <div class="fw-bold text-success"><i class="bi bi-check-circle-fill me-1"></i>Cocok</div>
                                    <div class="text-muted">Match: {{ $matchConfidence ?? '-' }}%</div>
                                    <div class="text-muted">Deteksi: {{ $detectionConfidence ?? '-' }}%</div>
                                </div>
                            @elseif(!is_null($detectionConfidence) && $detectionConfidence > 0)
                                <div class="space-y-1 small">
                                    <div class="fw-bold text-danger"><i class="bi bi-x-circle-fill me-1"></i>Tidak Cocok</div>
                                    <div class="text-muted">Match: {{ $matchConfidence ?? '-' }}%</div>
                                    <div class="text-muted">Deteksi: {{ $detectionConfidence }}%</div>
                                </div>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <div class="small text-slate-600 capitalize">{{ $log->device_type ?? '-' }}</div>
                            <div class="small text-muted">{{ $log->browser ?? '-' }}</div>
                        </td>
                        <td><code>{{ $log->ip_address ?? '-' }}</code></td>
                        <td>
                            @if($log->photo_path)
                                <a href="{{ asset('storage/'.$log->photo_path) }}" target="_blank" rel="noopener" title="Klik untuk memperbesar" class="inline-block hover:scale-105 transition-transform duration-200">
                                    <img src="{{ asset('storage/'.$log->photo_path) }}" class="h-14 w-14 rounded-lg object-cover border border-slate-200 shadow-sm" alt="Foto presensi">
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-person-x fs-2 d-block mb-2"></i>
                            Belum ada log presensi mandiri.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
        <div class="card-footer">
            {{ $logs->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
