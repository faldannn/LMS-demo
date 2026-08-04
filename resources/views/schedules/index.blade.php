{{-- resources/views/schedules/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Jadwal Pelajaran')
@section('page-title', 'Jadwal Pelajaran')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <p class="text-muted mb-0">Kelola jadwal pelajaran per kelas</p>
    </div>
    @role('admin,kepala_sekolah')
        <div class="d-flex gap-2 flex-wrap align-items-center">
            <a href="{{ route('schedules.batch', ['class_id' => $classId]) }}" class="btn btn-primary font-bold shadow-sm">
                <i class="bi bi-calendar-week me-1.5"></i>Input Jadwal Mingguan (Batch)
            </a>
            <a href="{{ route('schedules.create') }}" class="btn btn-outline-secondary">
                <i class="bi bi-plus-lg me-1"></i>+ 1 Jam Pelajaran
            </a>
        </div>
    @endrole
</div>

@unless(auth()->user()->isSiswa())
<div class="card mb-4 shadow-sm border-0">
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
    <div class="card mb-4 border-0 shadow-sm" style="background:linear-gradient(135deg,var(--color-brand-600),var(--color-brand-500))">
        <div class="card-body p-3.5 d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div style="width:46px;height:46px;border-radius:12px;background:rgba(255,255,255,.2);display:grid;place-items:center;font-size:20px;font-weight:800;color:#fff">
                    {{ $class->grade }}
                </div>
                <div>
                    <div class="text-white fw-bold" style="font-size:17px">{{ $class->name }}</div>
                    <div style="color:rgba(255,255,255,.8);font-size:12.5px">Wali Kelas: {{ $class->homeroomTeacher?->name ?? '-' }}</div>
                </div>
            </div>

            @role('admin,kepala_sekolah')
                <div class="d-flex align-items-center gap-2">
                    <button type="button" id="btn-clear-all-schedules" class="btn btn-sm btn-outline-light text-white font-bold border-white/40 hover:bg-white/20">
                        <i class="bi bi-trash3-fill me-1"></i> Hapus Semua Jadwal Kelas Ini
                    </button>
                </div>
            @endrole
        </div>
    </div>

    @if(auth()->user()->isSiswa())
        @php
            $studentDaysMapped = collect($operationalDays ?? ['1'=>'Senin','2'=>'Selasa','3'=>'Rabu','4'=>'Kamis','5'=>'Jumat'])
                ->map(fn($name) => ['name' => $name, 'icon' => 'bi-calendar-day']);
        @endphp

        <div class="grid grid-cols-1 gap-5 md:grid-cols-3 xl:grid-cols-{{ count($studentDaysMapped) >= 6 ? '6' : '5' }}">
            @foreach($studentDaysMapped as $dayNum => $dayInfo)
                @php
                    $daySchedules = $schedules->get((int)$dayNum, collect());
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

        {{-- BULK DELETE FORM --}}
        <form id="bulk-delete-form" method="POST" action="{{ route('schedules.bulk-delete') }}">
            @csrf
            <input type="hidden" name="class_id" value="{{ $classId }}">

            {{-- Bulk Actions Bar --}}
            @role('admin,kepala_sekolah')
                <div class="card mb-3 border-0 bg-slate-100 p-2.5 shadow-2xs">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="form-check m-0 ms-2">
                                <input type="checkbox" id="global-select-all" class="form-check-input cursor-pointer" style="width:1.25em;height:1.25em">
                                <label for="global-select-all" class="form-check-label text-xs font-bold text-slate-700 cursor-pointer ms-1">
                                    Pilih Semua Jadwal
                                </label>
                            </div>
                            <span class="badge bg-slate-200 text-slate-700 text-xs font-semibold" id="selected-badge" style="display:none;">
                                <span id="selected-count">0</span> terpilih
                            </span>
                        </div>

                        <button type="button" id="btn-delete-selected" class="btn btn-danger btn-sm font-bold opacity-50" disabled>
                            <i class="bi bi-trash me-1"></i> Hapus Terpilih (<span id="selected-btn-count">0</span>)
                        </button>
                    </div>
                </div>
            @endrole

            @forelse($schedules as $dayNum => $daySchedules)
                <div class="card mb-4 border-0 shadow-sm overflow-hidden">
                    <div class="card-header bg-slate-50 border-bottom d-flex align-items-center justify-content-between py-3">
                        <div class="d-flex align-items-center gap-2">
                            @role('admin,kepala_sekolah')
                                <input type="checkbox" class="form-check-input day-select-all cursor-pointer me-1" data-day="{{ $dayNum }}" title="Pilih Semua Hari Ini" style="width:1.15em;height:1.15em">
                            @endrole
                            <span class="fw-extrabold text-slate-800" style="font-size:15px"><i class="bi bi-calendar-day me-2 text-primary"></i>{{ $dayNames[$dayNum] }}</span>
                        </div>
                        <span class="badge bg-primary px-3 py-1.5 rounded-pill">{{ $daySchedules->count() }} mata pelajaran</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    @role('admin,kepala_sekolah')
                                        <th style="width:40px" class="text-center">#</th>
                                    @endrole
                                    <th>Jam</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Guru</th>
                                    <th>Ruang</th>
                                    <th>Durasi</th>
                                    @role('admin,kepala_sekolah,guru')<th>Aksi</th>@endrole
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($daySchedules as $sched)
                                    <tr>
                                        @role('admin,kepala_sekolah')
                                            <td class="text-center">
                                                <input type="checkbox" name="ids[]" value="{{ $sched->id }}" class="form-check-input schedule-checkbox day-check-{{ $dayNum }} cursor-pointer" style="width:1.1em;height:1.1em">
                                            </td>
                                        @endrole
                                        <td style="font-family:'JetBrains Mono',monospace;font-size:13px;white-space:nowrap" class="fw-semibold text-slate-700">
                                            {{ \Carbon\Carbon::parse($sched->start_time)->format('H:i') }} – {{ \Carbon\Carbon::parse($sched->end_time)->format('H:i') }}
                                        </td>
                                        <td><span class="fw-bold text-slate-900">{{ $sched->subject }}</span></td>
                                        <td style="font-size:13px" class="text-slate-600">{{ $sched->teacher?->name ?? '-' }}</td>
                                        <td style="font-size:13px" class="text-slate-500">{{ $sched->room ?? '-' }}</td>
                                        <td>
                                            @php
                                                $start = \Carbon\Carbon::parse($sched->start_time);
                                                $end = \Carbon\Carbon::parse($sched->end_time);
                                                $dur = $start->diffInMinutes($end);
                                            @endphp
                                            <span class="badge bg-primary bg-opacity-10 text-primary fw-bold">{{ $dur }} menit</span>
                                        </td>
                                        @role('admin,kepala_sekolah,guru')
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
                                                    @role('admin,kepala_sekolah')
                                                        <a href="{{ route('schedules.edit', $sched) }}" class="btn btn-xs btn-outline-secondary" title="Edit">
                                                            <i class="bi bi-pencil"></i> Edit
                                                        </a>
                                                        <button type="button" class="btn btn-xs btn-outline-danger btn-delete-single"
                                                            data-id="{{ $sched->id }}"
                                                            data-subject="{{ $sched->subject }}" title="Hapus">
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
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5 text-muted">
                        <i class="bi bi-calendar-x fs-1 d-block mb-2 text-slate-300"></i>
                        Belum ada jadwal untuk kelas ini.
                        @role('admin,kepala_sekolah')
                            <div class="mt-3">
                                <a href="{{ route('schedules.create', ['class_id' => $classId]) }}" class="btn btn-sm btn-primary font-bold">+ Tambah Jadwal</a>
                            </div>
                        @endrole
                    </div>
                </div>
            @endforelse
        </form>

        {{-- Single Delete Hidden Form --}}
        <form id="single-delete-form" method="POST" action="" style="display:none;">
            @csrf
            @method('DELETE')
        </form>
    @endif
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Session & Class Filter Logic
    const sessionSelect = document.getElementById('session-select');
    const classSelect = document.getElementById('class-select');
    
    if (sessionSelect && classSelect) {
        const originalOptions = Array.from(classSelect.options);
        
        function filterClasses(isInitial = false) {
            const selectedSession = sessionSelect.value;
            const currentSelectedValue = classSelect.value;
            
            classSelect.innerHTML = '';
            const filteredOptions = originalOptions.filter(option => {
                return option.getAttribute('data-session') === selectedSession;
            });
            
            filteredOptions.forEach(option => classSelect.appendChild(option));
            
            if (isInitial) {
                const hasSelectedValue = filteredOptions.some(opt => opt.value === currentSelectedValue);
                if (hasSelectedValue) {
                    classSelect.value = currentSelectedValue;
                } else if (filteredOptions.length > 0) {
                    classSelect.value = filteredOptions[0].value;
                }
            } else {
                if (filteredOptions.length > 0) {
                    classSelect.value = filteredOptions[0].value;
                    classSelect.form.submit();
                }
            }
        }
        
        const selectedOption = classSelect.options[classSelect.selectedIndex];
        if (selectedOption) {
            const classSession = selectedOption.getAttribute('data-session');
            if (classSession) {
                sessionSelect.value = classSession;
            }
        }
        
        filterClasses(true);
        sessionSelect.addEventListener('change', function() { filterClasses(false); });
    }

    // 2. Checkbox & Bulk Actions Logic
    const globalSelectAll = document.getElementById('global-select-all');
    const daySelectAlls = document.querySelectorAll('.day-select-all');
    const scheduleCheckboxes = document.querySelectorAll('.schedule-checkbox');
    const btnDeleteSelected = document.getElementById('btn-delete-selected');
    const btnClearAllSchedules = document.getElementById('btn-clear-all-schedules');
    const selectedBadge = document.getElementById('selected-badge');
    const selectedCountSpan = document.getElementById('selected-count');
    const selectedBtnCountSpan = document.getElementById('selected-btn-count');
    const bulkDeleteForm = document.getElementById('bulk-delete-form');

    function updateSelectionState() {
        const checkedCount = document.querySelectorAll('.schedule-checkbox:checked').length;
        const totalCount = scheduleCheckboxes.length;

        if (selectedCountSpan) selectedCountSpan.textContent = checkedCount;
        if (selectedBtnCountSpan) selectedBtnCountSpan.textContent = checkedCount;

        if (checkedCount > 0) {
            if (btnDeleteSelected) {
                btnDeleteSelected.disabled = false;
                btnDeleteSelected.classList.remove('opacity-50');
            }
            if (selectedBadge) selectedBadge.style.display = 'inline-block';
        } else {
            if (btnDeleteSelected) {
                btnDeleteSelected.disabled = true;
                btnDeleteSelected.classList.add('opacity-50');
            }
            if (selectedBadge) selectedBadge.style.display = 'none';
        }

        if (globalSelectAll && totalCount > 0) {
            globalSelectAll.checked = (checkedCount === totalCount);
        }

        // Update day select all states
        daySelectAlls.forEach(dayCb => {
            const dayNum = dayCb.getAttribute('data-day');
            const dayBoxes = document.querySelectorAll(`.day-check-${dayNum}`);
            const checkedDayBoxes = document.querySelectorAll(`.day-check-${dayNum}:checked`);
            if (dayBoxes.length > 0) {
                dayCb.checked = (dayBoxes.length === checkedDayBoxes.length);
            }
        });
    }

    // Global Select All Checkbox
    if (globalSelectAll) {
        globalSelectAll.addEventListener('change', function () {
            const isChecked = this.checked;
            scheduleCheckboxes.forEach(cb => cb.checked = isChecked);
            daySelectAlls.forEach(cb => cb.checked = isChecked);
            updateSelectionState();
        });
    }

    // Day Select All Checkboxes
    daySelectAlls.forEach(dayCb => {
        dayCb.addEventListener('change', function () {
            const dayNum = this.getAttribute('data-day');
            const isChecked = this.checked;
            document.querySelectorAll(`.day-check-${dayNum}`).forEach(cb => cb.checked = isChecked);
            updateSelectionState();
        });
    });

    // Individual Checkboxes
    scheduleCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateSelectionState);
    });

    // Delete Selected Button Click Handler
    if (btnDeleteSelected) {
        btnDeleteSelected.addEventListener('click', function () {
            const checkedCount = document.querySelectorAll('.schedule-checkbox:checked').length;
            if (checkedCount === 0) return;

            if (window.Swal) {
                Swal.fire({
                    title: 'Konfirmasi Hapus Terpilih',
                    html: `Apakah Anda yakin ingin menghapus <strong>${checkedCount}</strong> jadwal pelajaran yang dipilih?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Hapus Terpilih!',
                    cancelButtonText: 'Batal'
                }).then(result => {
                    if (result.isConfirmed) {
                        bulkDeleteForm.submit();
                    }
                });
            } else {
                if (confirm(`Apakah Anda yakin ingin menghapus ${checkedCount} jadwal pelajaran yang dipilih?`)) {
                    bulkDeleteForm.submit();
                }
            }
        });
    }

    // Clear All Schedules for this Class Button
    if (btnClearAllSchedules) {
        btnClearAllSchedules.addEventListener('click', function () {
            const className = "{{ $class?->name ?? 'kelas ini' }}";
            
            function executeClearAll() {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'clear_all';
                hiddenInput.value = '1';
                bulkDeleteForm.appendChild(hiddenInput);
                bulkDeleteForm.submit();
            }

            if (window.Swal) {
                Swal.fire({
                    title: 'Hapus SEMUA Jadwal Kelas?',
                    html: `Tindakan ini akan menghapus <strong>SELURUH</strong> jadwal mata pelajaran untuk <strong>${className}</strong>.<br><br><span class="text-danger font-bold">Peringatan: Tindakan ini tidak dapat dibatalkan!</span>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Hapus Semua!',
                    cancelButtonText: 'Batal'
                }).then(result => {
                    if (result.isConfirmed) {
                        executeClearAll();
                    }
                });
            } else {
                if (confirm(`PERINGATAN: Apakah Anda yakin ingin menghapus SELURUH jadwal pelajaran untuk ${className}?`)) {
                    executeClearAll();
                }
            }
        });
    }

    // Single item delete click handler
    document.querySelectorAll('.btn-delete-single').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const subject = this.getAttribute('data-subject');
            const singleForm = document.getElementById('single-delete-form');
            singleForm.action = "{{ route('schedules.destroy', '') }}/" + id;

            if (window.Swal) {
                Swal.fire({
                    title: 'Konfirmasi Hapus',
                    html: `Apakah Anda yakin ingin menghapus jadwal <strong>${subject}</strong>?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then(result => {
                    if (result.isConfirmed) {
                        singleForm.submit();
                    }
                });
            } else {
                if (confirm(`Apakah Anda yakin ingin menghapus jadwal ${subject}?`)) {
                    singleForm.submit();
                }
            }
        });
    });
});
</script>
@endpush
