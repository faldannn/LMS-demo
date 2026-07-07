{{-- resources/views/attendance/edit.blade.php --}}
@extends('layouts.app')
@section('title', 'Edit Absensi')
@section('page-title', 'Edit Absensi')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <span class="badge bg-primary me-2">{{ ucfirst($attendance->session) }}</span>
            <span class="fw-bold">{{ $attendance->class?->name }} — {{ $attendance->date->translatedFormat('d F Y') }}</span>
        </div>
        <a href="{{ route('attendance.show', $attendance) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>

    <form method="POST" action="{{ route('attendance.update', $attendance) }}">
        @csrf @method('PUT')
        <input type="hidden" name="date" value="{{ $attendance->date->toDateString() }}">
        <input type="hidden" name="class_id" value="{{ $attendance->class_id }}">
        <input type="hidden" name="session" value="{{ $attendance->session }}">

        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Kehadiran Siswa</span>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-outline-success" onclick="setAll('hadir')">Semua Hadir</button>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="setAll('alfa')">Semua Alfa</button>
                </div>
            </div>
            <div class="card-body p-0">
                @foreach($attendance->class->students as $i => $student)
                    @php $det = $existingDetails->get($student->id) @endphp
                    <div class="att-row px-4 d-flex align-items-center gap-3 flex-wrap">
                        <div class="student-num">{{ $i + 1 }}</div>
                        <div style="min-width:180px;flex:1">
                            <div class="fw-semibold">{{ $student->name }}</div>
                            <div class="text-muted" style="font-size:11px">{{ $student->nis }}</div>
                        </div>
                        <div class="status-group d-flex gap-1 flex-wrap">
                            @foreach(['hadir','terlambat','sakit','izin','alfa'] as $s)
                                <input type="radio" class="btn-check" name="details[{{ $student->id }}][status]"
                                    id="s{{ $student->id }}_{{ $s }}" value="{{ $s }}"
                                    {{ (old("details.{$student->id}.status", $det?->status ?? 'hadir') === $s) ? 'checked' : '' }}>
                                <label class="btn btn-sm btn-outline-{{ match($s) { 'hadir'=>'success','terlambat'=>'warning','sakit'=>'info','izin'=>'primary','alfa'=>'danger' } }}"
                                    for="s{{ $student->id }}_{{ $s }}">{{ ucfirst($s) }}</label>
                            @endforeach
                        </div>
                        <div style="width:120px">
                            <input type="time" name="details[{{ $student->id }}][check_in]"
                                class="form-control form-control-sm"
                                value="{{ old("details.{$student->id}.check_in", $det?->check_in) }}"
                                placeholder="Jam masuk">
                        </div>
                        <div style="width:180px">
                            <input type="text" name="details[{{ $student->id }}][notes]"
                                class="form-control form-control-sm"
                                value="{{ old("details.{$student->id}.notes", $det?->notes) }}"
                                placeholder="Keterangan">
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="card-footer d-flex justify-content-end gap-2">
                <a href="{{ route('attendance.show', $attendance) }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan Perubahan</button>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script>
function setAll(status) {
    document.querySelectorAll(`input[type="radio"][value="${status}"]`).forEach(r => r.checked = true);
}
</script>
@endpush
