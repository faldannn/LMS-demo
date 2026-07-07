@extends('layouts.app')
@section('title', 'Ujian Selesai')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card border-0 shadow-sm text-center p-5">
            <div class="card-body">
                <i class="bi bi-check-circle display-1 text-success mb-4"></i>
                <h4 class="fw-bold mb-3">Ujian Selesai</h4>
                <p class="text-muted mb-4">Terima kasih telah menyelesaikan ujian <strong>{{ $quiz->title }}</strong>. Jawaban Anda telah berhasil disimpan di dalam sistem.</p>
                
                <div class="alert alert-info small mb-4 text-start">
                    <i class="bi bi-info-circle me-2"></i> Guru menonaktifkan tampilan skor secara langsung untuk ujian ini. Anda dapat melihat hasil akhir nanti setelah diumumkan.
                </div>
                
                <a href="{{ route('student.quizzes.index') }}" class="btn btn-primary px-5 rounded-pill">Kembali ke Daftar Ujian</a>
            </div>
        </div>
    </div>
</div>
@endsection
