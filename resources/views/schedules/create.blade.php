{{-- resources/views/schedules/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Tambah Jadwal')
@section('page-title', 'Tambah Jadwal')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <p class="text-muted mb-0">Tambah jadwal pelajaran baru</p>
    <a href="{{ route('schedules.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
</div>
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><i class="bi bi-calendar-plus me-2 text-primary"></i>Form Jadwal Pelajaran</div>
            <div class="card-body">
                <form method="POST" action="{{ route('schedules.store') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kelas <span class="text-danger">*</span></label>
                            <select name="class_id" class="form-select @error('class_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($classes as $c)
                                    <option value="{{ $c->id }}" {{ old('class_id', request('class_id')) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                @endforeach
                            </select>
                            @error('class_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Hari <span class="text-danger">*</span></label>
                            <select name="day_of_week" class="form-select @error('day_of_week') is-invalid @enderror" required>
                                <option value="">-- Pilih Hari --</option>
                                @foreach($days as $num => $name)
                                    <option value="{{ $num }}" {{ old('day_of_week') == $num ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                            @error('day_of_week')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Mata Pelajaran <span class="text-danger">*</span></label>
                            <select name="subject" class="form-select @error('subject') is-invalid @enderror" required>
                                <option value="">-- Pilih Mata Pelajaran --</option>
                                @foreach($subjects as $s)
                                    <option value="{{ $s->name }}" {{ old('subject') == $s->name ? 'selected' : '' }}>{{ $s->name }} ({{ $s->code }})</option>
                                @endforeach
                            </select>
                            @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Guru Pengampu <span class="text-danger">*</span></label>
                            <select name="teacher_id" class="form-select @error('teacher_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Guru --</option>
                                @foreach($teachers as $t)
                                    @php
                                        $teacherSubjects = $t->subjects->pluck('name')->toArray();
                                        if ($t->subject) {
                                            $legacySubjects = array_map('trim', explode(',', $t->subject));
                                            $teacherSubjects = array_unique(array_merge($teacherSubjects, $legacySubjects));
                                        }
                                    @endphp
                                    <option value="{{ $t->id }}" data-subjects="{{ json_encode($teacherSubjects) }}" {{ old('teacher_id') == $t->id ? 'selected' : '' }}>{{ $t->name }} {{ $t->subject ? '('.$t->subject.')' : '' }}</option>
                                @endforeach
                            </select>
                            @error('teacher_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold">Jam Mulai <span class="text-danger">*</span></label>
                            <input type="time" name="start_time" class="form-control @error('start_time') is-invalid @enderror"
                                value="{{ old('start_time') }}" required>
                            @error('start_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold">Jam Selesai <span class="text-danger">*</span></label>
                            <input type="time" name="end_time" class="form-control @error('end_time') is-invalid @enderror"
                                value="{{ old('end_time') }}" required>
                            @error('end_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Ruang <span class="text-muted small">(Opsional)</span></label>
                            <input type="text" name="room" class="form-control" value="{{ old('room') }}" placeholder="R-101">
                        </div>
                    </div>
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-save me-2"></i>Simpan Jadwal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const subjectSelect = document.querySelector('select[name="subject"]');
    const teacherSelect = document.querySelector('select[name="teacher_id"]');
    
    if (subjectSelect && teacherSelect) {
        // Store all teacher options in an array (including placeholder)
        const allTeacherOptions = Array.from(teacherSelect.options);
        
        subjectSelect.addEventListener('change', function () {
            const selectedSubject = this.value;
            
            // Reset teacher select value
            teacherSelect.value = "";
            
            // Clear current options (except the first placeholder option)
            teacherSelect.innerHTML = "";
            teacherSelect.appendChild(allTeacherOptions[0]);
            
            if (!selectedSubject) {
                // If no subject selected, show all teachers
                for (let i = 1; i < allTeacherOptions.length; i++) {
                    teacherSelect.appendChild(allTeacherOptions[i]);
                }
                return;
            }
            
            // Filter and add matching teachers
            for (let i = 1; i < allTeacherOptions.length; i++) {
                const option = allTeacherOptions[i];
                let subjects = [];
                try {
                    subjects = JSON.parse(option.getAttribute('data-subjects') || '[]');
                } catch (e) {
                    subjects = [];
                }
                
                // Check if teacher teaches the selected subject
                const matches = subjects.some(subj => subj.toLowerCase() === selectedSubject.toLowerCase());
                
                if (matches) {
                    teacherSelect.appendChild(option);
                }
            }
        });

        // Trigger change event once on load to filter if subject is pre-selected
        if (subjectSelect.value) {
            subjectSelect.dispatchEvent(new Event('change'));
        }
    }
});
</script>
@endsection
