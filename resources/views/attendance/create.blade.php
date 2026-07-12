{{-- resources/views/attendance/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Input Absensi')
@section('page-title', 'Input Absensi Harian')

@section('content')

{{-- ── FILTER FORM ── --}}
<div class="card mb-4 shadow-sm" style="border-top: 4px solid var(--color-brand-600)">
    <div class="card-body p-3 p-md-4">
        <form method="GET" action="{{ route('attendance.create') }}" id="filter-form">
            <div class="row g-3">
                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold">Tanggal</label>
                    <input type="date" name="date" class="form-control" value="{{ $date }}" max="{{ today()->toDateString() }}">
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Kelas</label>
                    <select name="class_id" class="form-select" id="class-select">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ $classId == $c->id ? 'selected' : '' }}>
                            {{ $c->name }} ({{ $c->grade_label }})
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <label class="form-label fw-semibold">Sesi</label>
                    <select name="session" class="form-select">
                        <option value="pagi" {{ ($session ?? 'pagi') == 'pagi' ? 'selected' : '' }}>🌅 Pagi</option>
                        <option value="siang" {{ ($session ?? 'pagi') == 'siang' ? 'selected' : '' }}>🌇 Siang</option>
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    {{-- Trik label tersembunyi agar button sejajar presisi secara vertikal --}}
                    <label class="form-label d-none d-md-block" style="visibility: hidden;">Aksi</label>
                    {{-- Class w-100 dihapus dan diganti px-4 agar lebar tombol hanya selebar kontennya --}}
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-search me-1"></i> Tampilkan Siswa
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@if($students->isNotEmpty())

{{-- ── SUMMARY CARDS ── --}}
<div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-4" id="summary-cards">
    @foreach([
        'hadir' => ['Hadir', '✅', '#ecfdf5', '#a7f3d0', '#047857'],
        'sakit' => ['Sakit', '🤒', '#fffbeb', '#fde68a', '#b45309'],
        'izin' => ['Izin', '📄', '#f0f9ff', '#bae6fd', '#0369a1'],
        'alfa' => ['Alfa', '❌', '#fef2f2', '#fecaca', '#b91c1c'],
        'terlambat' => ['Terlambat', '⏰', '#faf5ff', '#ddd6fe', '#6d28d9']
    ] as $status => [$label, $icon, $bg, $border, $color])
    <div class="text-center p-3 rounded-xl border summary-card" style="background: {{ $bg }}; border-color: {{ $border }} !important;">
        <div style="font-size: 26px; font-weight: 800; color: {{ $color }}; font-family: 'JetBrains Mono', monospace" id="sum-{{ $status }}">0</div>
        <div style="font-size: 11px; font-weight: 700; color: {{ $color }}; text-transform: uppercase; letter-spacing: 0.5px" class="mt-1">
            {{ $icon }} {{ $label }}
        </div>
    </div>
    @endforeach
</div>

{{-- ── ATTENDANCE FORM ── --}}
<form method="POST" action="{{ route('attendance.store') }}" id="att-form">
    @csrf
    <input type="hidden" name="date" value="{{ $date }}">
    <input type="hidden" name="class_id" value="{{ $classId }}">
    <input type="hidden" name="session" value="{{ $session }}">

    <div class="card shadow-sm border border-slate-200">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <h5 class="mb-0 text-slate-800 fw-bold d-flex align-items-center">
                    <i class="bi bi-clipboard-check text-primary me-2" style="font-size: 1.25rem"></i>
                    Daftar Siswa Kelas <span class="text-primary ms-2">{{ $classes->find($classId)?->name }}</span>
                </h5>
                <p class="text-muted small mb-0 mt-1">Total: <strong>{{ $students->count() }} siswa</strong> aktif terdaftar</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-sm bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 shadow-sm" onclick="setAll('hadir')">
                    <i class="bi bi-check-all me-1"></i>Semua Hadir
                </button>
                <button type="button" class="btn btn-sm bg-rose-50 hover:bg-rose-100 text-rose-800 border border-rose-200 shadow-sm" onclick="setAll('alfa')">
                    <i class="bi bi-x-circle me-1"></i>Semua Alfa
                </button>
                <button type="submit" class="btn btn-sm btn-primary px-3 shadow-sm" id="save-btn">
                    <i class="bi bi-save me-2"></i>Simpan Absensi
                </button>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 60px">No</th>
                            <th style="min-width: 200px">Siswa</th>
                            <th style="min-width: 480px">Status Kehadiran</th>
                            <th style="width: 130px" class="text-center">Jam Masuk</th>
                            <th style="width: 250px">Keterangan / Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $i => $student)
                        @php
                            $studentExisting = $existing[$student->id] ?? null;
                            $savedStatus = $studentExisting['status'] ?? 'alfa';
                            $savedCheckIn = $studentExisting['check_in'] ?? now()->format('H:i');
                            $savedNotes = $studentExisting['notes'] ?? '';
                        @endphp
                        <tr class="att-row" data-student="{{ $student->id }}">
                            <td class="text-center align-middle font-semibold text-slate-400" style="font-size: 13px">{{ $i + 1 }}</td>
                            <td class="align-middle">
                                <div class="fw-semibold text-slate-800" style="font-size: 14px">{{ $student->name }}</div>
                                <div style="font-size: 11px; color: #64748b; font-family: 'JetBrains Mono', monospace" class="mt-1">
                                    NIS: {{ $student->nis }}
                                    @if($student->gender === 'P') 
                                        <span class="badge ms-1 bg-pink-50 text-pink-700 border border-pink-200" style="font-size: 9px; padding: 2px 6px">Perempuan</span>
                                    @else 
                                        <span class="badge ms-1 bg-blue-50 text-blue-700 border border-blue-200" style="font-size: 9px; padding: 2px 6px">Laki-laki</span>
                                    @endif
                                </div>
                            </td>
                            <td class="align-middle">
                                <div class="d-flex gap-1 status-group" role="group">
                                    @foreach([
                                        'hadir' => ['Hadir', '✅'],
                                        'sakit' => ['Sakit', '🤒'],
                                        'izin' => ['Izin', '📄'],
                                        'alfa' => ['Alfa', '❌'],
                                        'terlambat' => ['Terlambat', '⏰']
                                    ] as $statusKey => [$label, $emoji])
                                    <input type="radio" class="btn-check status-radio"
                                        name="details[{{ $student->id }}][status]"
                                        id="st-{{ $student->id }}-{{ $statusKey }}"
                                        value="{{ $statusKey }}"
                                        {{ $savedStatus === $statusKey ? 'checked' : '' }}
                                        onchange="updateSummary()">
                                    <label class="status-btn" 
                                           for="st-{{ $student->id }}-{{ $statusKey }}">
                                        <span class="me-1">{{ $emoji }}</span> {{ $label }}
                                    </label>
                                    @endforeach
                                </div>
                            </td>
                            <td class="align-middle">
                                <input type="time" name="details[{{ $student->id }}][check_in]"
                                    class="form-control form-control-sm text-center font-semibold text-slate-700"
                                    value="{{ $savedCheckIn }}">
                            </td>
                            <td class="align-middle">
                                <input type="text" name="details[{{ $student->id }}][notes]"
                                    class="form-control form-control-sm"
                                    placeholder="Tulis keterangan..."
                                    value="{{ $savedNotes }}">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer d-flex justify-content-between align-items-center py-3 px-4">
            <span class="text-muted" style="font-size: 13px">
                Total: <strong>{{ $students->count() }} siswa</strong>
            </span>
            <button type="submit" class="btn btn-primary px-4 shadow-sm text-nowrap">
                <i class="bi bi-save me-2"></i>Simpan Absensi
            </button>
        </div>
    </div>
</form>

@else
{{-- ── EMPTY STATE ── --}}
<div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #f8fafc, #f1f5f9)">
    <div class="card-body text-center py-5">
        <div class="d-flex align-items-center justify-content-center mx-auto bg-white rounded-circle shadow-sm mb-4" style="width: 80px; height: 80px;">
            <i class="bi bi-calendar2-check text-primary m-0" style="font-size: 2.2rem; line-height: 1;"></i>
        </div>
        <h4 class="fw-bold text-slate-800 mb-2">Pilih Kelas Terlebih Dahulu</h4>
        <p class="text-slate-500 max-w-md mx-auto mb-0" style="max-width: 460px;">Silakan pilih tanggal, kelas, dan sesi pada form di atas, lalu klik <strong>Tampilkan Siswa</strong> untuk mulai mengisi absensi.</p>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
function updateSummary() {
    const counts = { hadir: 0, sakit: 0, izin: 0, alfa: 0, terlambat: 0 };
    document.querySelectorAll('.status-radio:checked').forEach(r => {
        counts[r.value] = (counts[r.value] || 0) + 1;
    });
    Object.keys(counts).forEach(s => {
        const el = document.getElementById(`sum-${s}`);
        if (el) el.textContent = counts[s];
    });
}

function setAll(status) {
    document.querySelectorAll(`.status-radio[value="${status}"]`).forEach(r => {
        r.checked = true;
    });
    updateSummary();
}

document.addEventListener('DOMContentLoaded', updateSummary);

document.getElementById('att-form')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const unchecked = document.querySelectorAll('.att-row').length
        - document.querySelectorAll('.status-radio:checked').length;

    if (unchecked > 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Masih Ada yang Belum Terabsensi',
            text: `${unchecked} siswa belum dipilih statusnya.`,
            confirmButtonText: 'Lanjutkan Simpan',
            showCancelButton: true,
            cancelButtonText: 'Batal',
        }).then(r => { if (r.isConfirmed) this.submit(); });
    } else {
        this.submit();
    }
});
</script>
@endpush