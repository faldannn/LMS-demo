@extends('layouts.app')
@section('title', 'Input Jadwal Mingguan Sekaligus')
@section('page-title', 'Input Jadwal Pelajaran (Matriks Mingguan)')

@section('content')
<div class="space-y-6">
    {{-- Header & Controls --}}
    <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200/70">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <i class="bi bi-calendar-week text-brand-600"></i>
                    Matriks Pengaturan Jadwal Mingguan
                </h3>
                <p class="text-xs text-slate-500">Kelola seluruh jadwal pelajaran kelas dari Senin sampai {{ $maxDay == 6 ? 'Sabtu' : 'Jumat' }} sekaligus dalam 1 halaman.</p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                {{-- Copy Schedule Modal Trigger --}}
                <button type="button" data-bs-toggle="modal" data-bs-target="#copyScheduleModal" class="inline-flex items-center gap-1.5 rounded-xl bg-slate-100 px-3.5 py-2 text-xs font-bold text-slate-700 hover:bg-slate-200 transition">
                    <i class="bi bi-copy"></i> Salin dari Kelas Lain
                </button>

                <a href="{{ route('schedules.index', ['class_id' => $classId]) }}" class="inline-flex items-center gap-1.5 rounded-xl bg-slate-100 px-3.5 py-2 text-xs font-bold text-slate-700 hover:bg-slate-200 transition">
                    <i class="bi bi-arrow-left"></i> Kembali ke Tabel
                </a>
            </div>
        </div>

        {{-- Class Switcher & Preset Buttons --}}
        <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between border-t border-slate-100 pt-4">
            <div class="flex items-center gap-3">
                <label class="text-xs font-bold text-slate-600 whitespace-nowrap">Pilih Kelas Target:</label>
                <select onchange="window.location.href = '{{ route('schedules.batch') }}?class_id=' + this.value" class="rounded-xl border border-slate-200 bg-slate-50 py-2 px-3 text-xs font-extrabold text-slate-800 focus:border-brand-500 focus:bg-white min-w-[200px]">
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ $classId == $c->id ? 'selected' : '' }}>
                            Kelas {{ $c->name }} (Grade {{ $c->grade }})
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="button" onclick="loadDefaultTimeSlots()" class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-50 px-3.5 py-2 text-xs font-bold text-emerald-700 hover:bg-emerald-100 ring-1 ring-emerald-200 transition">
                <i class="bi bi-magic"></i> ⚡ Isi Slot Jam Standard (8 Jam/Hari)
            </button>
        </div>
    </div>

    {{-- Main Batch Form --}}
    <form method="POST" action="{{ route('schedules.batch-store') }}" id="batch-form">
        @csrf
        <input type="hidden" name="class_id" value="{{ $classId }}">

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-{{ count($operationalDays) >= 6 ? '6' : '5' }}">
            @foreach($operationalDays as $dayNum => $dayName)
                @php
                    $daySchedules = $existingSchedules->get((int)$dayNum, collect());
                @endphp

                <div class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm flex flex-col justify-between">
                    <div>
                        {{-- Day Header --}}
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3">
                            <h4 class="text-sm font-extrabold text-slate-800 flex items-center gap-1.5">
                                <i class="bi bi-calendar-day text-brand-600"></i>
                                Hari {{ $dayName }}
                            </h4>
                            <span class="rounded-full bg-brand-50 px-2 py-0.5 text-[10px] font-bold text-brand-700" id="badge-count-{{ $dayNum }}">
                                {{ $daySchedules->count() }} Jam
                            </span>
                        </div>

                        {{-- Time Slots Container for this Day --}}
                        <div id="slots-container-{{ $dayNum }}" class="space-y-3">
                            @foreach($daySchedules as $idx => $sched)
                                <div class="slot-card rounded-xl border border-slate-200 bg-slate-50/50 p-3 space-y-2 relative group hover:border-brand-300 hover:bg-white transition">
                                    <input type="hidden" name="items[{{ $dayNum }}_{{ $idx }}][day_of_week]" value="{{ $dayNum }}">

                                    <div class="flex items-center justify-between gap-2">
                                        <div class="flex items-center gap-1 text-[11px] font-semibold text-slate-600 w-full">
                                            <input type="time" name="items[{{ $dayNum }}_{{ $idx }}][start_time]" 
                                                value="{{ \Carbon\Carbon::parse($sched->start_time)->format('H:i') }}" 
                                                class="rounded-lg border border-slate-200 bg-white px-2 py-1 text-xs text-slate-800 font-mono w-full focus:ring-1 focus:ring-brand-500" required>
                                            <span>-</span>
                                            <input type="time" name="items[{{ $dayNum }}_{{ $idx }}][end_time]" 
                                                value="{{ \Carbon\Carbon::parse($sched->end_time)->format('H:i') }}" 
                                                class="rounded-lg border border-slate-200 bg-white px-2 py-1 text-xs text-slate-800 font-mono w-full focus:ring-1 focus:ring-brand-500" required>
                                        </div>
                                        <button type="button" onclick="removeSlot(this, '{{ $dayNum }}')" class="text-slate-400 hover:text-red-600 transition p-1" title="Hapus Slot Jam">
                                            <i class="bi bi-x-circle-fill"></i>
                                        </button>
                                    </div>

                                    <div>
                                        <label class="text-[10px] font-bold text-slate-400 uppercase">Mata Pelajaran</label>
                                        <select name="items[{{ $dayNum }}_{{ $idx }}][subject]" onchange="filterTeachersForSlot(this)" 
                                            class="subject-select w-full rounded-lg border border-slate-200 bg-white px-2 py-1 text-xs font-bold text-slate-900 focus:border-brand-500" required>
                                            <option value="">-- Pilih Mapel --</option>
                                            @foreach($subjects as $sub)
                                                <option value="{{ $sub->name }}" {{ (strcasecmp($sched->subject, $sub->name) === 0) ? 'selected' : '' }}>
                                                    {{ $sub->name }} ({{ $sub->code }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="text-[10px] font-bold text-slate-400 uppercase">Guru Pengampu</label>
                                        <select name="items[{{ $dayNum }}_{{ $idx }}][teacher_id]" 
                                            data-selected="{{ $sched->teacher_id }}"
                                            class="teacher-select w-full rounded-lg border border-slate-200 bg-white px-2 py-1 text-xs text-slate-700 focus:border-brand-500" required>
                                            <option value="">-- Pilih Guru --</option>
                                            @foreach($teachers as $t)
                                                <option value="{{ $t['id'] }}" {{ $sched->teacher_id == $t['id'] ? 'selected' : '' }}>
                                                    {{ $t['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <input type="text" name="items[{{ $dayNum }}_{{ $idx }}][room]" value="{{ $sched->room }}" placeholder="Ruang (misal: R.101)" class="w-full rounded-lg border border-slate-200 bg-white px-2 py-1 text-[11px] text-slate-600">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Add Slot Button --}}
                    <div class="mt-4 pt-3 border-t border-slate-100">
                        <button type="button" onclick="addSlot('{{ $dayNum }}')" class="w-full inline-flex items-center justify-center gap-1 rounded-xl bg-slate-100 py-2 text-xs font-bold text-slate-700 hover:bg-slate-200 transition">
                            <i class="bi bi-plus-lg"></i> Tambah Jam Pelajaran
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Save Button Bar --}}
        <div class="sticky bottom-4 mt-6 rounded-2xl bg-slate-900 p-4 shadow-xl text-white flex items-center justify-between">
            <div class="text-xs text-slate-300">
                Menyimpan jadwal sekaligus untuk <strong class="text-white">Kelas {{ $class?->name }}</strong>
            </div>
            <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-brand-600 px-6 py-2.5 text-sm font-extrabold text-white hover:bg-brand-500 transition shadow-lg shadow-brand-600/30">
                <i class="bi bi-save-fill"></i> Simpan Semua Jadwal Kelas Ini
            </button>
        </div>
    </form>
</div>

{{-- Copy Schedule Modal --}}
<div class="modal fade" id="copyScheduleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-2xl border-0 shadow-xl">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title font-bold text-slate-900"><i class="bi bi-copy me-2 text-brand-600"></i>Salin Jadwal Pelajaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('schedules.copy') }}">
                @csrf
                <div class="modal-body space-y-4">
                    <p class="text-xs text-slate-500">Salin seluruh susunan jadwal dari satu kelas ke kelas lain secara instan.</p>
                    
                    <div>
                        <label class="form-label text-xs font-bold text-slate-700">Kelas Asal (Sumber Jadwal)</label>
                        <select name="source_class_id" class="form-select text-xs font-semibold" required>
                            <option value="">-- Pilih Kelas Asal --</option>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}" {{ $classId == $c->id ? 'selected' : '' }}>Kelas {{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="form-label text-xs font-bold text-slate-700">Kelas Tujuan (Diisi Jadwal Baru)</label>
                        <select name="target_class_id" class="form-select text-xs font-semibold" required>
                            <option value="">-- Pilih Kelas Tujuan --</option>
                            @foreach($classes as $c)
                                @if($c->id != $classId)
                                    <option value="{{ $c->id }}">Kelas {{ $c->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light text-xs font-bold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary text-xs font-bold"><i class="bi bi-copy me-1"></i>Salin Sekarang</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const teachersList = @json($teachers);
    const subjectsList = @json($subjects);
    const defaultTimes = [
        ['07:15', '08:00'],
        ['08:00', '08:45'],
        ['08:45', '09:30'],
        ['09:30', '10:15'],
        ['10:30', '11:15'],
        ['11:15', '12:00'],
        ['12:45', '13:30'],
        ['13:30', '14:15']
    ];

    function filterTeachersForSlot(subjectElem) {
        const card = subjectElem.closest('.slot-card');
        if (!card) return;

        const teacherSelect = card.querySelector('.teacher-select');
        if (!teacherSelect) return;

        const selectedSubject = (subjectElem.value || '').trim().toLowerCase();
        const currentSelectedId = teacherSelect.value || teacherSelect.getAttribute('data-selected') || '';

        teacherSelect.innerHTML = '<option value="">-- Pilih Guru --</option>';

        if (!selectedSubject) {
            // Show all teachers
            teachersList.forEach(t => {
                const opt = document.createElement('option');
                opt.value = t.id;
                opt.textContent = t.name;
                if (String(t.id) === String(currentSelectedId)) opt.selected = true;
                teacherSelect.appendChild(opt);
            });
            return;
        }

        const matchingTeachers = teachersList.filter(t => {
            if (!t.taught_subjects || !Array.isArray(t.taught_subjects)) return false;
            return t.taught_subjects.some(s => s.toLowerCase() === selectedSubject);
        });

        const otherTeachers = teachersList.filter(t => !matchingTeachers.some(m => m.id === t.id));

        if (matchingTeachers.length > 0) {
            const groupMatch = document.createElement('optgroup');
            groupMatch.label = `Guru Pengampu ${subjectElem.value}`;
            matchingTeachers.forEach(t => {
                const opt = document.createElement('option');
                opt.value = t.id;
                opt.textContent = `${t.name} ✓`;
                if (String(t.id) === String(currentSelectedId)) opt.selected = true;
                groupMatch.appendChild(opt);
            });
            teacherSelect.appendChild(groupMatch);

            if (otherTeachers.length > 0) {
                const groupOther = document.createElement('optgroup');
                groupOther.label = 'Guru Lainnya (Inval/Pengganti)';
                otherTeachers.forEach(t => {
                    const opt = document.createElement('option');
                    opt.value = t.id;
                    opt.textContent = t.name;
                    if (String(t.id) === String(currentSelectedId)) opt.selected = true;
                    groupOther.appendChild(opt);
                });
                teacherSelect.appendChild(groupOther);
            }

            // Auto-select if only 1 matching teacher and no pre-selection
            if (matchingTeachers.length === 1 && (!currentSelectedId || !matchingTeachers.some(m => String(m.id) === String(currentSelectedId)))) {
                teacherSelect.value = matchingTeachers[0].id;
            }
        } else {
            // No specific teacher mapped for this subject, list all
            teachersList.forEach(t => {
                const opt = document.createElement('option');
                opt.value = t.id;
                opt.textContent = t.name;
                if (String(t.id) === String(currentSelectedId)) opt.selected = true;
                teacherSelect.appendChild(opt);
            });
        }
    }

    function updateBadgeCount(dayNum) {
        const container = document.getElementById(`slots-container-${dayNum}`);
        const count = container ? container.children.length : 0;
        const badge = document.getElementById(`badge-count-${dayNum}`);
        if (badge) badge.textContent = `${count} Jam`;
    }

    function addSlot(dayNum, startTime = '07:15', endTime = '08:00', subject = '', teacherId = '', room = '') {
        const container = document.getElementById(`slots-container-${dayNum}`);
        if (!container) return;

        const idx = Date.now() + Math.floor(Math.random() * 1000);
        
        let subjectOptions = '<option value="">-- Pilih Mapel --</option>';
        subjectsList.forEach(sub => {
            const sel = (sub.name.toLowerCase() === subject.toLowerCase()) ? 'selected' : '';
            subjectOptions += `<option value="${sub.name}" ${sel}>${sub.name} (${sub.code})</option>`;
        });

        const slotHtml = `
            <div class="slot-card rounded-xl border border-slate-200 bg-slate-50/50 p-3 space-y-2 relative group hover:border-brand-300 hover:bg-white transition">
                <input type="hidden" name="items[${dayNum}_${idx}][day_of_week]" value="${dayNum}">

                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-1 text-[11px] font-semibold text-slate-600 w-full">
                        <input type="time" name="items[${dayNum}_${idx}][start_time]" value="${startTime}" class="rounded-lg border border-slate-200 bg-white px-2 py-1 text-xs text-slate-800 font-mono w-full focus:ring-1 focus:ring-brand-500" required>
                        <span>-</span>
                        <input type="time" name="items[${dayNum}_${idx}][end_time]" value="${endTime}" class="rounded-lg border border-slate-200 bg-white px-2 py-1 text-xs text-slate-800 font-mono w-full focus:ring-1 focus:ring-brand-500" required>
                    </div>
                    <button type="button" onclick="removeSlot(this, '${dayNum}')" class="text-slate-400 hover:text-red-600 transition p-1" title="Hapus Slot Jam">
                        <i class="bi bi-x-circle-fill"></i>
                    </button>
                </div>

                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase">Mata Pelajaran</label>
                    <select name="items[${dayNum}_${idx}][subject]" onchange="filterTeachersForSlot(this)" 
                        class="subject-select w-full rounded-lg border border-slate-200 bg-white px-2 py-1 text-xs font-bold text-slate-900 focus:border-brand-500" required>
                        ${subjectOptions}
                    </select>
                </div>

                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase">Guru Pengampu</label>
                    <select name="items[${dayNum}_${idx}][teacher_id]" data-selected="${teacherId}" 
                        class="teacher-select w-full rounded-lg border border-slate-200 bg-white px-2 py-1 text-xs text-slate-700 focus:border-brand-500" required>
                        <option value="">-- Pilih Guru --</option>
                    </select>
                </div>

                <div>
                    <input type="text" name="items[${dayNum}_${idx}][room]" value="${room}" placeholder="Ruang (misal: R.101)" class="w-full rounded-lg border border-slate-200 bg-white px-2 py-1 text-[11px] text-slate-600">
                </div>
            </div>
        `;

        container.insertAdjacentHTML('beforeend', slotHtml);
        const newCard = container.lastElementChild;
        const newSubjectSelect = newCard.querySelector('.subject-select');
        filterTeachersForSlot(newSubjectSelect);
        updateBadgeCount(dayNum);
    }

    function removeSlot(btn, dayNum) {
        const card = btn.closest('.slot-card');
        if (card) {
            card.remove();
            updateBadgeCount(dayNum);
        }
    }

    function loadDefaultTimeSlots() {
        const days = Object.keys(@json($operationalDays));
        days.forEach(dayNum => {
            const container = document.getElementById(`slots-container-${dayNum}`);
            if (container && container.children.length === 0) {
                defaultTimes.forEach(t => {
                    addSlot(dayNum, t[0], t[1]);
                });
            }
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        // Initialize teacher filters for all existing slots
        document.querySelectorAll('.slot-card .subject-select').forEach(sel => {
            filterTeachersForSlot(sel);
        });
    });
</script>
@endpush
@endsection
