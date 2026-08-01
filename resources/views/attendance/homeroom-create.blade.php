{{-- resources/views/attendance/homeroom-create.blade.php --}}
@extends('layouts.app')

@section('title', 'Input Absensi Harian Kelas (Wali Kelas)')
@section('page-title', 'Input Absensi Harian Kelas (Wali Kelas)')

@section('content')
<div class="space-y-4">
    {{-- Header Action --}}
    <div class="flex flex-wrap items-center justify-between gap-3 mb-1">
        <p class="text-sm font-medium text-slate-500 mb-0">
            Form pencatatan presensi harian siswa kelas wali (Sakit, Izin, Alfa, Hadir & Keterangan).
        </p>
        <a href="{{ route('attendance.index', ['class_id' => $classId]) }}" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
            <i class="bi bi-arrow-left"></i>Kembali ke Detail Absensi
        </a>
    </div>

    {{-- ── FILTER FORM ── --}}
    <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200/70">
        <form method="GET" action="{{ route('attendance.homeroom-create') }}" id="filter-form" class="grid grid-cols-1 gap-3.5 sm:grid-cols-12 items-end">
            <div class="sm:col-span-3">
                <label class="block mb-1.5 text-xs font-bold uppercase tracking-wider text-slate-500">Tanggal Absensi</label>
                <input type="date" name="date" class="form-control text-sm font-semibold text-slate-700" value="{{ $date }}" max="{{ today()->toDateString() }}">
            </div>

            <div class="sm:col-span-4">
                <label class="block mb-1.5 text-xs font-bold uppercase tracking-wider text-slate-500">Kelas (Wali Kelas)</label>
                <select name="class_id" class="form-select text-sm font-semibold text-slate-700" id="class-select">
                    @foreach($classes as $c)
                    <option value="{{ $c->id }}" {{ $classId == $c->id ? 'selected' : '' }}>
                        {{ $c->name }} ({{ $c->grade_label }})
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-3">
                <label class="block mb-1.5 text-xs font-bold uppercase tracking-wider text-slate-500">Sesi Presensi</label>
                <select name="session" class="form-select text-sm font-semibold text-slate-700">
                    <option value="pagi" {{ ($session ?? 'pagi') == 'pagi' ? 'selected' : '' }}>🌅 Pagi</option>
                    <option value="siang" {{ ($session ?? 'pagi') == 'siang' ? 'selected' : '' }}>🌇 Siang</option>
                </select>
            </div>

            <div class="sm:col-span-2">
                <button type="submit" class="inline-flex w-full items-center justify-center gap-1.5 rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2">
                    <i class="bi bi-search"></i> Tampilkan
                </button>
            </div>
        </form>
    </div>

    @if($students->isNotEmpty())

    {{-- ── ATTENDANCE FORM UNTUK WALI KELAS ── --}}
    <form method="POST" action="{{ route('attendance.homeroom-store') }}" id="att-form">
        @csrf
        <input type="hidden" name="date" value="{{ $date }}">
        <input type="hidden" name="class_id" value="{{ $classId }}">
        <input type="hidden" name="session" value="{{ $session }}">

        <div class="card shadow-sm border border-slate-200 overflow-hidden rounded-2xl">
            <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    <h5 class="mb-0 text-slate-800 fw-bold d-flex align-items-center">
                        <i class="bi bi-clipboard-check text-primary me-2" style="font-size: 1.25rem"></i>
                        Absensi Harian Kelas <span class="text-primary ms-2">{{ $classes->find($classId)?->name }}</span>
                    </h5>
                    <p class="text-muted small mb-0 mt-0.5">Pilih status presensi siswa. Opsi yang terpilih akan berwarna tebal terang.</p>
                </div>
                <div class="flex items-center gap-2 flex-wrap w-full sm:w-auto">
                    <button type="button" class="btn btn-sm bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 shadow-sm font-semibold flex-1 sm:flex-none" onclick="setAll('hadir')">
                        <i class="bi bi-check-all me-1"></i>Semua Hadir
                    </button>
                    <button type="button" class="btn btn-sm bg-rose-50 hover:bg-rose-100 text-rose-800 border border-rose-200 shadow-sm font-semibold flex-1 sm:flex-none" onclick="setAll('alfa')">
                        <i class="bi bi-x-circle me-1"></i>Semua Alfa
                    </button>
                    <button type="submit" class="btn btn-sm btn-primary px-4 shadow-sm font-bold w-full sm:w-auto mt-1 sm:mt-0" id="save-btn">
                        <i class="bi bi-save me-2"></i>Simpan Absensi Kelas
                    </button>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive overflow-x-auto">
                    <table class="table mb-0 align-middle min-w-[700px]">
                        <thead>
                            <tr class="bg-slate-50/80">
                                <th class="text-center" style="width: 60px">No</th>
                                <th style="min-width: 200px">Siswa</th>
                                <th style="min-width: 480px">Status Kehadiran Harian</th>
                                <th style="width: 320px">Keterangan / Alasan (Opsional)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $i => $student)
                            @php
                                $studentExisting = $existing[$student->id] ?? null;
                                $savedStatus = $studentExisting['status'] ?? 'alfa';
                                $savedNotes = $studentExisting['notes'] ?? '';
                            @endphp
                            <tr class="att-row" data-student="{{ $student->id }}">
                                <td class="text-center align-middle font-semibold text-slate-400" style="font-size: 13px">{{ $i + 1 }}</td>
                                <td class="align-middle">
                                    <div class="fw-semibold text-slate-800" style="font-size: 14px">{{ $student->name }}</div>
                                    <div style="font-size: 11px; color: #64748b; font-family: 'JetBrains Mono', monospace" class="mt-0.5">
                                        NIS: {{ $student->nis }}
                                    </div>
                                </td>
                                <td class="align-middle">
                                    <div class="flex flex-wrap gap-1.5 items-center" role="group">
                                        @foreach([
                                            'hadir' => ['Hadir', '✅'],
                                            'sakit' => ['Sakit', '🤒'],
                                            'izin' => ['Izin', '📄'],
                                            'alfa' => ['Alfa', '❌'],
                                            'terlambat' => ['Terlambat', '⏰']
                                        ] as $statusKey => [$label, $emoji])
                                        <input type="radio" class="hidden status-radio"
                                            name="details[{{ $student->id }}][status]"
                                            id="st-{{ $student->id }}-{{ $statusKey }}"
                                            value="{{ $statusKey }}"
                                            {{ $savedStatus === $statusKey ? 'checked' : '' }}
                                            onchange="updatePills()">
                                        <label class="status-radio-item cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs transition-all duration-150" 
                                               for="st-{{ $student->id }}-{{ $statusKey }}">
                                            <span>{{ $emoji }}</span> <span>{{ $label }}</span>
                                        </label>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="align-middle">
                                    <input type="text" name="details[{{ $student->id }}][notes]"
                                        class="form-control form-control-sm text-sm"
                                        placeholder="Tulis alasan Sakit/Izin/Keterangan..."
                                        value="{{ $savedNotes }}">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-footer bg-white py-3 px-4 flex items-center justify-between">
                <span class="text-xs text-slate-500 font-medium">Total: <strong>{{ $students->count() }} siswa</strong></span>
                <button type="submit" class="btn btn-primary px-6 font-bold shadow-sm">
                    <i class="bi bi-save me-2"></i>Simpan Absensi Kelas
                </button>
            </div>
        </div>
    </form>
    @endif
</div>
@endsection

@push('scripts')
<script>
function updatePills() {
    document.querySelectorAll('.status-radio-item').forEach(label => {
        const radioId = label.getAttribute('for');
        const radio = document.getElementById(radioId);
        if (radio && radio.checked) {
            const key = radio.value;
            let activeClasses = 'text-white font-extrabold shadow-md scale-105 ';
            if (key === 'hadir') activeClasses += 'bg-emerald-600 ring-2 ring-emerald-600';
            else if (key === 'sakit') activeClasses += 'bg-amber-500 ring-2 ring-amber-500';
            else if (key === 'izin') activeClasses += 'bg-sky-600 ring-2 ring-sky-600';
            else if (key === 'alfa') activeClasses += 'bg-rose-600 ring-2 ring-rose-600';
            else if (key === 'terlambat') activeClasses += 'bg-purple-600 ring-2 ring-purple-600';
            
            label.className = 'status-radio-item cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs transition-all duration-150 ' + activeClasses;
        } else {
            label.className = 'status-radio-item cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs transition-all duration-150 bg-slate-100 text-slate-600 border border-slate-200/80 hover:bg-slate-200/80';
        }
    });
}

function setAll(status) {
    document.querySelectorAll(`.status-radio[value="${status}"]`).forEach(r => {
        r.checked = true;
    });
    updatePills();
}

document.addEventListener('DOMContentLoaded', updatePills);
</script>
@endpush
