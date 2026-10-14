@extends('layouts.app')
@section('title', 'Log Presensi QR Code')
@section('page-title', 'Log Presensi QR Siswa')

@section('content')
<div class="card shadow-sm border-0 rounded-2xl overflow-hidden">
    <div class="card-header bg-white border-bottom py-3.5 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
                <i class="bi bi-qr-code-scan fs-5"></i>
            </span>
            <div>
                <h6 class="mb-0 fw-bold text-slate-900">Log Presensi Scan QR Code Siswa</h6>
                <p class="mb-0 text-muted small">Riwayat verifikasi kehadiran siswa melalui pemindaian QR Code</p>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-brand-50 text-brand-700 border border-brand-200 px-3 py-1.5 rounded-pill font-mono">
                <i class="bi bi-database-check me-1"></i>{{ $logs->total() }} Log Presensi
            </span>
            <a href="{{ route('attendance.index') }}" class="btn btn-sm btn-outline-secondary rounded-xl px-3">
                <i class="bi bi-arrow-left me-1"></i>Kembali ke Rekap
            </a>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Waktu Presensi</th>
                    <th>Siswa</th>
                    <th>Metode Scan</th>
                    <th>Status Presensi</th>
                    <th>Kode / Token QR</th>
                    <th>Perangkat & Browser</th>
                    <th class="pe-4">IP Address</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    @php
                        $student = $log->student;
                        $attendanceDetail = $log->attendanceDetail;
                        $status = $attendanceDetail?->status ?? 'hadir';
                    @endphp
                    <tr>
                        <td class="ps-4 whitespace-nowrap">
                            <div class="fw-bold text-slate-800">{{ $log->checked_in_at?->translatedFormat('d M Y') ?? '-' }}</div>
                            <div class="small text-muted font-mono"><i class="bi bi-clock me-1 text-slate-400"></i>{{ $log->checked_in_at?->format('H:i:s') ?? '-' }} WIB</div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-3 min-w-[220px]">
                                <img src="{{ $student?->photo_url ?? 'https://ui-avatars.com/api/?name=Siswa&background=e2e8f0&color=475569' }}" class="h-10 w-10 rounded-full object-cover border border-slate-200 shadow-2xs" alt="Foto siswa">
                                <div class="min-w-0">
                                    <div class="fw-bold text-slate-900 truncate">{{ $student?->name ?? 'Data siswa tidak ditemukan' }}</div>
                                    <div class="small text-muted font-mono">
                                        NIS: <span class="fw-semibold">{{ $student?->nis ?? '-' }}</span>
                                        @if($student?->class)
                                            <span class="mx-1 text-slate-300">•</span><span class="badge bg-slate-100 text-slate-700 font-sans">{{ $student->class->name }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700 border border-emerald-200">
                                <i class="bi bi-qr-code text-xs"></i>{{ $log->mode_label }}
                            </span>
                        </td>
                        <td>
                            @if($status === 'terlambat')
                                <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-bold text-amber-700 border border-amber-200">
                                    <i class="bi bi-clock-history"></i> Terlambat
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700 border border-emerald-200">
                                    <i class="bi bi-check-circle-fill"></i> Hadir
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="small font-mono text-slate-700 bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-200 inline-block">
                                <i class="bi bi-upc-scan text-brand-600 me-1"></i>{{ $log->notes ?: 'Scan QR Valid' }}
                            </div>
                        </td>
                        <td>
                            <div class="small text-slate-700 font-semibold capitalize flex items-center gap-1">
                                <i class="bi {{ str_contains(strtolower($log->device_type ?? ''), 'mobile') ? 'bi-phone' : 'bi-laptop' }} text-slate-400"></i>
                                {{ $log->device_type ?: 'Desktop' }}
                            </div>
                            <div class="small text-muted font-mono text-[11px]">{{ $log->browser ?: 'Chrome' }}</div>
                        </td>
                        <td class="pe-4">
                            <code class="text-xs bg-slate-100 text-slate-700 px-2 py-0.5 rounded border border-slate-200 font-mono">{{ $log->ip_address ?: '127.0.0.1' }}</code>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-qr-code fs-1 d-block mb-2 text-slate-300"></i>
                            <div class="fw-bold text-slate-700">Belum ada riwayat log presensi QR Code</div>
                            <div class="small text-slate-400 mt-1">Presensi siswa melalui pemindaian QR Code akan otomatis tercatat di sini.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
        <div class="card-footer bg-white border-top py-3 px-4">
            {{ $logs->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
