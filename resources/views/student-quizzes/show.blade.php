@extends('layouts.app')
@section('title', 'Detail Ujian')
@section('page-title', 'Persiapan Ujian')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card border-0 shadow-sm text-center p-4">
            <div class="card-body">
                <div class="mb-4">
                    <i class="bi bi-laptop display-1 text-primary"></i>
                </div>
                
                <h4 class="fw-bold mb-2">{{ $quiz->title }}</h4>
                <div class="text-muted mb-4">{{ $quiz->classSubject->subject->name }}</div>

                <div class="bg-light p-3 rounded mb-4 text-start">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="text-muted small">Durasi Ujian</div>
                            <div class="fw-bold">{{ $quiz->duration }} Menit</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted small">Jumlah Soal</div>
                            <div class="fw-bold">{{ $quiz->questions_count ?? 0 }} Soal</div>
                        </div>
                        <div class="col-12 border-top pt-3">
                            <div class="text-muted small">Batas Waktu Pengumpulan</div>
                            <div class="fw-bold {{ $quiz->end_time && $quiz->end_time->isPast() ? 'text-danger' : '' }}">
                                {{ $quiz->end_time ? $quiz->end_time->format('l, d F Y - H:i') : 'Tidak dibatasi' }}
                            </div>
                        </div>
                    </div>
                </div>

                @if($quiz->description)
                <div class="text-start mb-4">
                    <div class="fw-bold mb-2">Petunjuk Ujian:</div>
                    <div class="small text-muted">{!! nl2br(e($quiz->description)) !!}</div>
                </div>
                @endif

                <div class="alert alert-warning text-start small mb-4">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>Perhatian:</strong> Waktu akan mulai berjalan saat Anda menekan tombol "Mulai Ujian". Pastikan koneksi internet Anda stabil.
                </div>

                @if(!$quiz->isOpen())
                    <button class="btn btn-secondary w-100 py-3 rounded-pill" disabled>Ujian Ditutup</button>
                @else
                    <form action="{{ route('student.quizzes.start', $quiz) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-sm">
                            <i class="bi bi-play-circle me-2"></i> Mulai Ujian Sekarang
                        </button>
                    </form>
                @endif
                
                <div class="mt-3">
                    <a href="{{ route('student.quizzes.index') }}" class="text-decoration-none text-muted small">Kembali ke Daftar</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
