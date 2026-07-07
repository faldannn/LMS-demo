@extends('layouts.app')
@section('title', 'Rekap Nilai')
@section('page-title', 'Rekap Nilai — ' . $quiz->title)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
  <a href="{{ route('quizzes.show', $quiz) }}" class="btn btn-sm btn-light border">← Kembali</a>
  <a href="{{ route('quizzes.results.export', $quiz) }}" class="btn btn-sm btn-success"><i class="bi bi-file-earmark-excel me-1"></i> Export Excel</a>
</div>

<div class="row g-3 mb-4">
  <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body text-center"><div class="text-muted small">Peserta</div><div class="fs-4 fw-bold">{{ $stats['total'] }}</div></div></div></div>
  <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body text-center"><div class="text-muted small">Selesai</div><div class="fs-4 fw-bold">{{ $stats['finished'] }}</div></div></div></div>
  <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body text-center"><div class="text-muted small">Rata-rata</div><div class="fs-4 fw-bold">{{ number_format($stats['avg'], 1) }}</div></div></div></div>
  <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body text-center"><div class="text-muted small">Lulus</div><div class="fs-4 fw-bold text-success">{{ $stats['passed'] }}</div></div></div></div>
</div>

<div class="card border-0 shadow-sm">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="bg-light">
        <tr><th>No</th><th>Siswa</th><th>Status</th><th>Nilai</th><th>Lulus</th><th>Aksi</th></tr>
      </thead>
      <tbody>
        @foreach($recap as $i => $row)
        @php $attempt = $row['attempt']; @endphp
        <tr>
          <td>{{ $i+1 }}</td>
          <td><div class="fw-bold">{{ $row['student']->name }}</div><div class="small text-muted">{{ $row['student']->nis }}</div></td>
          <td>
            @if(!$attempt)<span class="badge bg-secondary">Belum</span>
            @elseif($attempt->status==='auto_submitted')<span class="badge bg-warning text-dark">Auto Submit</span>
            @elseif($attempt->isFinished())<span class="badge bg-success">Selesai</span>
            @else<span class="badge bg-info">Berlangsung</span>@endif
            @if($attempt?->needs_grading)<span class="badge bg-danger ms-1">Perlu Koreksi</span>@endif
          </td>
          <td class="fw-bold {{ $attempt && $attempt->isFinished() && $attempt->score < $quiz->pass_score ? 'text-danger' : 'text-success' }}">
            {{ $attempt && $attempt->isFinished() ? (float)$attempt->score : '-' }}
          </td>
          <td>{{ $row['passed'] ? '✓' : '—' }}</td>
          <td>
            @if($attempt && $attempt->isFinished() && $attempt->needs_grading)
            <a href="{{ route('quizzes.grade', [$quiz, $attempt]) }}" class="btn btn-sm btn-outline-primary">Koreksi Essay</a>
            @endif
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
