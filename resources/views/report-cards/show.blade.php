@extends('layouts.app')
@section('title', 'Detail Raport')
@section('page-title', 'Detail Raport')

@section('content')
<div class="row">
    <div class="col-12 mb-3">
        <a href="{{ route('report-cards.index', ['class_id' => $reportCard->student->class_id, 'semester' => $reportCard->semester]) }}" class="btn btn-sm btn-light border"><i class="bi bi-arrow-left me-1"></i> Kembali</a>
    </div>

    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-bold">Informasi Siswa</div>
            <div class="card-body">
                <table class="table table-borderless table-sm mb-0">
                    <tr><td class="text-muted w-50">Nama</td><td class="fw-bold">{{ $reportCard->student->name }}</td></tr>
                    <tr><td class="text-muted">NIS / NISN</td><td>{{ $reportCard->student->nis }} / {{ $reportCard->student->nisn }}</td></tr>
                    <tr><td class="text-muted">Kelas</td><td>{{ $reportCard->student->class->name }}</td></tr>
                    <tr><td class="text-muted">Tahun Ajaran</td><td>{{ $reportCard->academicYear->name }}</td></tr>
                    <tr><td class="text-muted">Semester</td><td class="text-capitalize">{{ $reportCard->semester }}</td></tr>
                </table>
            </div>
        </div>

        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-white fw-bold">Rekap Kehadiran</div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-success"><i class="bi bi-check-circle me-1"></i> Hadir</span>
                    <span class="fw-bold">{{ $reportCard->attendance_hadir }} Hari</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-primary"><i class="bi bi-info-circle me-1"></i> Izin</span>
                    <span class="fw-bold">{{ $reportCard->attendance_izin }} Hari</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-warning"><i class="bi bi-heart-pulse me-1"></i> Sakit</span>
                    <span class="fw-bold">{{ $reportCard->attendance_sakit }} Hari</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-danger"><i class="bi bi-x-circle me-1"></i> Alfa</span>
                    <span class="fw-bold">{{ $reportCard->attendance_alfa }} Hari</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <div class="fw-bold">Nilai Akademik</div>
                <a href="{{ route('report-cards.print', $reportCard) }}" target="_blank" class="btn btn-sm btn-primary">
                    <i class="bi bi-printer me-1"></i> Cetak PDF
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Mata Pelajaran</th>
                                <th class="text-center">Tugas (40%)</th>
                                <th class="text-center">Ujian (60%)</th>
                                <th class="text-center">Nilai Akhir</th>
                                <th class="text-center pe-4">Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reportCard->details as $detail)
                            <tr>
                                <td class="ps-4">{{ $detail->subject->name }}</td>
                                <td class="text-center">{{ (float)$detail->assignment_score }}</td>
                                <td class="text-center">{{ (float)$detail->quiz_score }}</td>
                                <td class="text-center fw-bold text-primary">{{ (float)$detail->final_score }}</td>
                                <td class="text-center pe-4">
                                    <span class="badge bg-dark">{{ $detail->grade }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
