@extends('layouts.app')
@section('title', 'Raport Saya')
@section('page-title', 'Raport Digital')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        
        <div class="alert alert-info border-0 shadow-sm">
            <i class="bi bi-info-circle-fill me-2"></i> Raport yang ditampilkan di sini adalah raport resmi yang telah dipublikasikan oleh pihak sekolah.
        </div>

        <h5 class="fw-bold mb-3 mt-4"><i class="bi bi-file-earmark-text text-primary me-2"></i> Daftar Raport Tersedia</h5>
        
        <div class="row g-3">
            @forelse($reportCards as $rc)
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h6 class="fw-bold mb-1">Semester {{ ucfirst($rc->semester) }}</h6>
                                    <div class="text-muted small">Tahun Ajaran {{ $rc->academicYear->name }}</div>
                                </div>
                                <i class="bi bi-award text-warning" style="font-size: 24px;"></i>
                            </div>
                            
                            <hr class="text-muted border-dashed my-3">
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="small text-muted">
                                    <i class="bi bi-calendar-check me-1"></i> Kehadiran: 
                                    <span class="text-success fw-bold">{{ $rc->attendance_hadir }}</span> H, 
                                    <span class="text-danger fw-bold">{{ $rc->attendance_alfa }}</span> A
                                </div>
                                <a href="{{ route('student.report-cards.show', $rc) }}" target="_blank" class="btn btn-sm btn-primary">
                                    <i class="bi bi-eye me-1"></i> Lihat Raport
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5 bg-white rounded shadow-sm border border-dashed">
                        <i class="bi bi-folder-x display-4 text-light mb-3"></i>
                        <h6 class="text-muted">Belum Ada Raport</h6>
                        <p class="small text-muted mb-0">Belum ada raport yang dipublikasikan untuk Anda pada saat ini.</p>
                    </div>
                </div>
            @endforelse
        </div>

    </div>
</div>
@endsection
