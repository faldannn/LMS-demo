{{-- resources/views/schedules/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Tambah Jadwal')
@section('page-title', 'Tambah Jadwal')

@section('content')
@php
    $timeOptions = [];
    $start = \Carbon\Carbon::createFromTime(7, 0);
    $end = \Carbon\Carbon::createFromTime(18, 0);
    while ($start <= $end) {
        $timeStr = $start->format('H:i');
        
        $hour = $start->hour;
        if ($hour < 11) {
            $suffix = 'Pagi';
        } elseif ($hour < 15) {
            $suffix = 'Siang';
        } else {
            $suffix = 'Sore';
        }
        
        $displayHour = $hour > 12 ? $hour - 12 : $hour;
        $minute = $start->minute;
        $timeLabel = $displayHour . ($minute > 0 ? '.' . sprintf('%02d', $minute) : '') . ' ' . $suffix;
        
        $timeOptions[$timeStr] = "{$timeStr} ({$timeLabel})";
        $start->addMinutes(15);
    }
@endphp
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
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Sesi Kelas</label>
                            <select id="session-filter" class="form-select">
                                <option value="pagi">🌅 Sesi Pagi</option>
                                <option value="siang">🌇 Sesi Siang</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Kelas <span class="text-danger">*</span></label>
                            <select name="class_id" id="class-select" class="form-select @error('class_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($classes as $c)
                                    <option value="{{ $c->id }}" data-session="{{ $c->session ?? 'pagi' }}" {{ old('class_id', request('class_id')) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                @endforeach
                            </select>
                            @error('class_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
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
                            <select name="teacher_id" id="teacher-select" data-selected="{{ old('teacher_id') }}" class="form-select @error('teacher_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Guru --</option>
                                @foreach($teachers as $t)
                                    <option value="{{ $t->id }}" data-subjects="{{ json_encode($t->taught_subject_names) }}" {{ old('teacher_id') == $t->id ? 'selected' : '' }}>
                                        {{ $t->name }} {{ $t->subject ? '('.$t->subject.')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('teacher_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold">Jam Mulai <span class="text-danger">*</span></label>
                            <select name="start_time" class="form-select @error('start_time') is-invalid @enderror" required>
                                <option value="">-- Pilih Jam Mulai --</option>
                                @foreach($timeOptions as $val => $label)
                                    <option value="{{ $val }}" {{ old('start_time') == $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('start_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold">Jam Selesai <span class="text-danger">*</span></label>
                            <select name="end_time" class="form-select @error('end_time') is-invalid @enderror" required>
                                <option value="">-- Pilih Jam Selesai --</option>
                                @foreach($timeOptions as $val => $label)
                                    <option value="{{ $val }}" {{ old('end_time') == $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
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
    const teacherSelect = document.getElementById('teacher-select');
    
    if (subjectSelect && teacherSelect) {
        const allTeacherOptions = Array.from(teacherSelect.options).filter(opt => opt.value);
        
        function filterTeachers() {
            const selectedSubject = (subjectSelect.value || '').trim().toLowerCase();
            const currentSelectedValue = teacherSelect.value || teacherSelect.getAttribute('data-selected');
            
            teacherSelect.innerHTML = '<option value="">-- Pilih Guru --</option>';
            
            if (!selectedSubject) {
                allTeacherOptions.forEach(opt => teacherSelect.appendChild(opt.cloneNode(true)));
                if (currentSelectedValue) teacherSelect.value = currentSelectedValue;
                return;
            }
            
            const matchingOptions = [];
            const otherOptions = [];
            
            allTeacherOptions.forEach(opt => {
                let subjects = [];
                try {
                    subjects = JSON.parse(opt.getAttribute('data-subjects') || '[]');
                } catch (e) {
                    subjects = [];
                }
                
                const matches = subjects.some(subj => (subj || '').toLowerCase() === selectedSubject);
                if (matches) {
                    const cloned = opt.cloneNode(true);
                    cloned.textContent = cloned.textContent.replace(' ✓', '') + ' ✓';
                    matchingOptions.push(cloned);
                } else {
                    otherOptions.push(opt.cloneNode(true));
                }
            });
            
            if (matchingOptions.length > 0) {
                const groupMatch = document.createElement('optgroup');
                groupMatch.label = `Guru Pengampu Mapel ${subjectSelect.value}`;
                matchingOptions.forEach(opt => groupMatch.appendChild(opt));
                teacherSelect.appendChild(groupMatch);
                
                if (otherOptions.length > 0) {
                    const groupOther = document.createElement('optgroup');
                    groupOther.label = 'Guru Lainnya (Inval/Pengganti)';
                    otherOptions.forEach(opt => groupOther.appendChild(opt));
                    teacherSelect.appendChild(groupOther);
                }
                
                if (currentSelectedValue) {
                    teacherSelect.value = currentSelectedValue;
                } else if (matchingOptions.length === 1) {
                    teacherSelect.value = matchingOptions[0].value;
                }
            } else {
                allTeacherOptions.forEach(opt => teacherSelect.appendChild(opt.cloneNode(true)));
                if (currentSelectedValue) teacherSelect.value = currentSelectedValue;
            }
        }
        
        subjectSelect.addEventListener('change', () => {
            teacherSelect.removeAttribute('data-selected');
            filterTeachers();
        });
        
        filterTeachers();
    }     }
    }

    // Session filter for classes
    const sessionFilter = document.getElementById('session-filter');
    const classSelect = document.getElementById('class-select');
    
    if (sessionFilter && classSelect) {
        const originalClassOptions = Array.from(classSelect.options);
        
        function filterClasses() {
            const selectedSession = sessionFilter.value;
            const currentSelectedValue = classSelect.value;
            
            // Clear and filter
            classSelect.innerHTML = '';
            const filteredOptions = originalClassOptions.filter(option => {
                // Keep placeholder option (value === "")
                if (!option.value) return true;
                return option.getAttribute('data-session') === selectedSession;
            });
            
            filteredOptions.forEach(option => classSelect.appendChild(option));
            
            // Restore selection if it still exists
            const hasSelectedValue = filteredOptions.some(opt => opt.value === currentSelectedValue);
            if (hasSelectedValue) {
                classSelect.value = currentSelectedValue;
            } else {
                classSelect.value = '';
            }
        }
        
        // Auto-detect session on load if a class is pre-selected (like old input)
        const selectedOption = classSelect.options[classSelect.selectedIndex];
        if (selectedOption && selectedOption.value) {
            const classSession = selectedOption.getAttribute('data-session');
            if (classSession) {
                sessionFilter.value = classSession;
            }
        }
        
        // Run initial filter
        filterClasses();
        
        // Listen for changes
        sessionFilter.addEventListener('change', filterClasses);
    }
});
</script>
@endsection
