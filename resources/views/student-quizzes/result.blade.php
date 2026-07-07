@extends('layouts.app')
@section('title', 'Hasil Ujian')
@section('page-title', 'Hasil Ujian')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="mb-3">
            <a href="{{ route('student.quizzes.index') }}" class="btn btn-sm btn-light border"><i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Ujian</a>
        </div>

        <div class="card border-0 shadow-sm mb-4 text-center">
            <div class="card-body p-5">
                <h4 class="fw-bold mb-4">{{ $quiz->title }}</h4>
                <div class="display-1 fw-bold {{ $attempt->score < 75 ? 'text-danger' : 'text-success' }} mb-2">
                    {{ (float)$attempt->score }}
                </div>
                <div class="text-muted mb-4">Skor Akhir Anda</div>
                
                <div class="row justify-content-center g-3">
                    <div class="col-md-3 col-6">
                        <div class="p-3 bg-light rounded">
                            <div class="text-muted small">Waktu Mulai</div>
                            <div class="fw-bold">{{ $attempt->start_time->format('H:i') }}</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="p-3 bg-light rounded">
                            <div class="text-muted small">Waktu Selesai</div>
                            <div class="fw-bold">{{ $attempt->end_time->format('H:i') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold">Detail Jawaban</h6>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @foreach($quiz->questions as $index => $question)
                        @php
                            $answer = $answers->get($question->id);
                        @endphp
                        <div class="list-group-item p-4">
                            <div class="d-flex mb-3">
                                <div class="me-3 fw-bold">{{ $index + 1 }}.</div>
                                <div class="flex-grow-1">
                                    <div class="mb-3">{!! nl2br(e($question->question_text)) !!}</div>
                                    
                                    @if($question->image_path)
                                        <img src="{{ asset('storage/' . $question->image_path) }}" class="img-fluid rounded mb-3" style="max-height: 200px;">
                                    @endif
                                    
                                    @if($question->type === 'multiple_choice')
                                        <div class="d-flex flex-column gap-2">
                                            @foreach($question->options as $option)
                                                @php
                                                    $isSelected = $answer && $answer->question_option_id === $option->id;
                                                    $isCorrect = $option->is_correct;
                                                    $class = 'bg-light text-muted border-light';
                                                    $icon = 'bi-circle';
                                                    
                                                    if ($isSelected && $isCorrect) {
                                                        $class = 'bg-success bg-opacity-10 border-success text-success';
                                                        $icon = 'bi-check-circle-fill';
                                                    } elseif ($isSelected && !$isCorrect) {
                                                        $class = 'bg-danger bg-opacity-10 border-danger text-danger';
                                                        $icon = 'bi-x-circle-fill';
                                                    } elseif (!$isSelected && $isCorrect) {
                                                        $class = 'bg-success bg-opacity-10 border-success text-success border-dashed';
                                                        $icon = 'bi-check-circle';
                                                    }
                                                @endphp
                                                <div class="border p-3 rounded {{ $class }}">
                                                    <i class="bi {{ $icon }} me-2"></i> {{ $option->option_text }}
                                                    @if($isSelected) <span class="badge bg-secondary ms-2">Jawaban Anda</span> @endif
                                                    @if($isCorrect) <span class="badge bg-success ms-2">Kunci Jawaban</span> @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="bg-light p-3 rounded mb-2">
                                            <div class="text-muted small mb-1">Jawaban Anda:</div>
                                            <div>{!! $answer ? nl2br(e($answer->essay_answer)) : '<em>Tidak dijawab</em>' !!}</div>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-primary">Poin: {{ (float)($answer->score ?? 0) }} / {{ $question->points }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
