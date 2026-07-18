{{-- resources/views/schedules/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Jadwal Pelajaran')
@section('page-title', 'Jadwal Pelajaran')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <p class="text-muted mb-0">Jadwal pelajaran per kelas</p>
    @role('kepala_sekolah')
        <a href="{{ route('schedules.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Tambah Jadwal
        </a>
    @endrole
</div>

@unless(auth()->user()->isSiswa())
<div class="card mb-4 shadow-sm">
    <div class="card-body py-3">
        <form method="GET" class="row g-3 align-items-end" id="filter-form">
            <div class="col-md-3 col-sm-6">
                <label class="form-label small fw-semibold text-muted mb-1">Sesi Kelas</label>
                <select id="session-select" class="form-select">
                    <option value="pagi">🌅 Sesi Pagi</option>
                    <option value="siang">🌇 Sesi Siang</option>
                </select>
            </div>
            <div class="col-md-4 col-sm-6">
                <label class="form-label small fw-semibold text-muted mb-1">Pilih Kelas</label>
                <select name="class_id" id="class-select" class="form-select" onchange="this.form.submit()">
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" data-session="{{ $c->session ?? 'pagi' }}" {{ $classId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>
</div>
@endunless

@if($class)
    <div class="card mb-3" style="background:linear-gradient(135deg,var(--color-brand-600),var(--color-brand-500));border:none">
        <div class="card-body p-3 d-flex align-items-center gap-3">
            <div style="width:44px;height:44px;border-radius:10px;background:rgba(255,255,255,.2);display:grid;place-items:center;font-size:20px;font-weight:800;color:#fff">
                {{ $class->grade }}
            </div>
            <div>
                <div class="text-white fw-bold" style="font-size:16px">{{ $class->name }}</div>
                <div style="color:rgba(255,255,255,.7);font-size:12px">Wali Kelas: {{ $class->homeroomTeacher?->name ?? '-' }}</div>
            </div>
        </div>
    </div>

    @if(auth()->user()->isSiswa())
        @php
            $studentDays = [
                1 => ['name' => 'Senin', 'icon' => 'bi-calendar-day'],
                2 => ['name' => 'Selasa', 'icon' => 'bi-calendar-day'],
                3 => ['name' => 'Rabu', 'icon' => 'bi-calendar-day'],
                4 => ['name' => 'Kamis', 'icon' => 'bi-calendar-day'],
                5 => ['name' => 'Jumat', 'icon' => 'bi-calendar-day']
            ];
        @endphp

        <div class="grid grid-cols-1 gap-5 md:grid-cols-3 xl:grid-cols-5">
            @foreach($studentDays as $dayNum => $dayInfo)
                @php
                    $daySchedules = $schedules->get($dayNum, collect());
                @endphp
                <div class="card flex flex-col shadow-sm border border-slate-200 rounded-2xl bg-white mb-0 h-full">
                    <div class="card-header bg-slate-50/80 px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                        <span class="font-extrabold text-brand-700 text-sm flex items-center gap-2">
                            <i class="bi {{ $dayInfo['icon'] }}"></i>
                            {{ $dayInfo['name'] }}
                        </span>
                        <span class="badge bg-primary text-[10px] font-bold">{{ $daySchedules->count() }} Pelajaran</span>
                    </div>
                    <div class="card-body p-3 flex-1 flex flex-col gap-3 min-h-[150px]">
                        @forelse($daySchedules as $sched)
                            @if($sched->class_subject_id)
                                <a href="{{ route('student.class-subjects.show', $sched->class_subject_id) }}" class="block p-3 rounded-xl border border-slate-100 bg-slate-50/50 hover:bg-white hover:border-brand-300 hover:shadow-sm hover:-translate-y-0.5 transition duration-200 group text-decoration-none text-reset">
                            @else
                                <div class="p-3 rounded-xl border border-slate-100 bg-slate-50/50 text-reset">
                            @endif
                                <div class="text-[11px] font-mono text-slate-400 mb-1">
                                    {{ \Carbon\Carbon::parse($sched->start_time)->format('H:i') }} – {{ \Carbon\Carbon::parse($sched->end_time)->format('H:i') }}
                                </div>
                                
                                <div class="font-bold text-slate-900 text-sm group-hover:text-brand-600 transition mb-1">
                                    {{ $sched->subject }}
                                </div>
                                
                                <div class="text-xs text-slate-500 flex items-center gap-1 mt-1 truncate" title="{{ $sched->teacher?->name ?? '-' }}">
                                    <i class="bi bi-person"></i>
                                    {{ $sched->teacher?->name ?? '-' }}
                                </div>
                                
                                @if($sched->room)
                                    <div class="text-[10px] text-slate-400 font-medium flex items-center gap-1 mt-1">
                                        <i class="bi bi-geo-alt"></i>
                                        Ruang: {{ $sched->room }}
                                    </div>
                                @endif
                            @if($sched->class_subject_id)
                                </a>
                            @else
                                </div>
                            @endif
                        @empty
                            <div class="flex-1 flex flex-col items-center justify-center text-center text-slate-400 p-4">
                                <i class="bi bi-calendar-x text-2xl mb-1 text-slate-300"></i>
                                <p class="text-[11px] font-medium">Tidak ada jadwal</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    @else
        @php $dayNames = ['','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu']; @endphp

        @forelse($schedules as $dayNum => $daySchedules)
            <div class="card mb-3">
                <div class="card-header d-flex align-items-center justify-content-between">
                        <span style="font-weight:700;color:var(--color-brand-600)"><i class="bi bi-calendar-day me-2"></i>{{ $dayNames[$dayNum] }}</span>
                    <span class="badge bg-primary">{{ $daySchedules->count() }} mata pelajaran</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr><th>Jam</th><th>Mata Pelajaran</th><th>Guru</th><th>Ruang</th><th>Durasi</th>@role('kepala_sekolah,guru')<th>Aksi</th>@endrole</tr>
                        </thead>
                        <tbody>
                            @foreach($daySchedules as $sched)
                                <tr>
                                    <td style="font-family:'JetBrains Mono',monospace;font-size:13px;white-space:nowrap">
                                        {{ \Carbon\Carbon::parse($sched->start_time)->format('H:i') }} – {{ \Carbon\Carbon::parse($sched->end_time)->format('H:i') }}
                                    </td>
                                    <td><span class="fw-semibold">{{ $sched->subject }}</span></td>
                                    <td style="font-size:13px">{{ $sched->teacher?->name ?? '-' }}</td>
                                    <td style="font-size:13px">{{ $sched->room ?? '-' }}</td>
                                    <td>
                                        @php
                                            $start = \Carbon\Carbon::parse($sched->start_time);
                                            $end = \Carbon\Carbon::parse($sched->end_time);
                                            $dur = $start->diffInMinutes($end);
                                        @endphp
                                        <span class="badge" style="background:var(--color-brand-50);color:var(--color-brand-600)">{{ $dur }} menit</span>
                                    </td>
                                    @role('kepala_sekolah,guru')
                                        <td>
                                            <div class="d-flex gap-1 align-items-center">
                                                @if(auth()->user()->isGuru() && $sched->class_subject_id)
                                                    <a href="{{ route('materials.create', $sched->class_subject_id) }}" class="btn btn-xs btn-outline-primary" title="Tambah Materi">
                                                        <i class="bi bi-plus-circle"></i> Materi
                                                    </a>
                                                    <a href="{{ route('assignments.create', $sched->class_subject_id) }}" class="btn btn-xs btn-outline-warning" title="Tambah Tugas">
                                                        <i class="bi bi-plus-circle"></i> Tugas
                                                    </a>
                                                @endif
                                                @role('kepala_sekolah')
                                                    <a href="{{ route('schedules.edit', $sched) }}" class="btn btn-xs btn-outline-secondary" title="Edit">
                                                        <i class="bi bi-pencil"></i> Edit
                                                    </a>
                                                    <form id="del-s{{ $sched->id }}" method="POST" action="{{ route('schedules.destroy', $sched) }}">@csrf @method('DELETE')</form>
                                                    <button class="btn btn-xs btn-outline-danger"
                                                        data-confirm="Hapus jadwal {{ $sched->subject }}?"
                                                        data-form="del-s{{ $sched->id }}" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                @endrole
                                            </div>
                                        </td>
                                    @endrole
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="card">
                <div class="card-body text-center py-5 text-muted">
                    <i class="bi bi-calendar-x fs-1 d-block mb-2"></i>
                    Belum ada jadwal untuk kelas ini.
                    @role('kepala_sekolah')
                        <a href="{{ route('schedules.create', ['class_id' => $classId]) }}" class="btn btn-sm btn-primary mt-2">+ Tambah Jadwal</a>
                    @endrole
                </div>
            </div>
        @endforelse
    @endif
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const sessionSelect = document.getElementById('session-select');
    const classSelect = document.getElementById('class-select');
    
    if (sessionSelect && classSelect) {
        const originalOptions = Array.from(classSelect.options);
        
        function filterClasses(isInitial = false) {
            const selectedSession = sessionSelect.value;
            const currentSelectedValue = classSelect.value;
            
            // Clear and filter
            classSelect.innerHTML = '';
            const filteredOptions = originalOptions.filter(option => {
                return option.getAttribute('data-session') === selectedSession;
            });
            
            filteredOptions.forEach(option => classSelect.appendChild(option));
            
            if (isInitial) {
                // On initial load, preserve current selection if it matches the session
                const hasSelectedValue = filteredOptions.some(opt => opt.value === currentSelectedValue);
                if (hasSelectedValue) {
                    classSelect.value = currentSelectedValue;
                } else if (filteredOptions.length > 0) {
                    classSelect.value = filteredOptions[0].value;
                }
            } else {
                // On user change, select first class of that session and submit
                if (filteredOptions.length > 0) {
                    classSelect.value = filteredOptions[0].value;
                    classSelect.form.submit();
                }
            }
        }
        
        // Auto-detect session on load based on currently selected class
        const selectedOption = classSelect.options[classSelect.selectedIndex];
        if (selectedOption) {
            const classSession = selectedOption.getAttribute('data-session');
            if (classSession) {
                sessionSelect.value = classSession;
            }
        }
        
        // Run filter initially without triggering form submit
        filterClasses(true);
        
        sessionSelect.addEventListener('change', function() {
            filterClasses(false);
        });
    }
});
</script>
@endpush
