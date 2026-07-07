{{-- resources/views/attendance/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Input Absensi')
@section('page-title', 'Input Absensi Harian')

@section('content')

{{-- ── FILTER FORM ── --}}
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('attendance.create') }}" id="filter-form">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-600">Tanggal</label>
                    <input type="date" name="date" class="form-control" value="{{ $date }}" max="{{ today()->toDateString() }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-600">Kelas</label>
                    <select name="class_id" class="form-select" id="class-select">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ $classId == $c->id ? 'selected' : '' }}>
                            {{ $c->name }} ({{ $c->grade_label }})
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-600">Sesi</label>
                    <select name="session" class="form-select">
                        <option value="pagi">🌅 Pagi</option>
                        <option value="siang">🌇 Siang</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-1"></i> Tampilkan Siswa
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@if($students->isNotEmpty())

{{-- ── SUMMARY CARDS ── --}}
<div class="row g-3 mb-4" id="summary-cards">
    @foreach(['hadir' => ['✅','var(--color-brand-50)','#0058ff'], 'sakit' => ['🤒','#fef3c7','#92400e'], 'izin' => ['📄','var(--color-brand-50)','#0047d1'], 'alfa' => ['❌','#fee2e2','#991b1b'], 'terlambat' => ['⏰','#ede9fe','#5b21b6']] as $status => [$icon, $bg, $color])
    <div class="col">
        <div class="text-center p-3 rounded-3 border" style="background:{{ $bg }};border-color:{{ $color }}20!important">
            <div style="font-size:24px;font-weight:800;color:{{ $color }};font-family:'JetBrains Mono',monospace" id="sum-{{ $status }}">0</div>
            <div style="font-size:10px;font-weight:700;color:{{ $color }};text-transform:uppercase;letter-spacing:.5px">{{ $icon }} {{ ucfirst($status) }}</div>
        </div>
    </div>
    @endforeach
</div>

{{-- ── ATTENDANCE FORM ── --}}
<form method="POST" action="{{ route('attendance.store') }}" id="att-form">
    @csrf
    <input type="hidden" name="date" value="{{ $date }}">
    <input type="hidden" name="class_id" value="{{ $classId }}">

    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <span>
                <i class="bi bi-clipboard-check me-2 text-primary"></i>
                Daftar Siswa — <strong>{{ $classes->find($classId)?->name }}</strong>
                <span class="text-muted ms-1">({{ $students->count() }} siswa)</span>
            </span>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-success" onclick="setAll('hadir')">
                    <i class="bi bi-check-all me-1"></i>Semua Hadir
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="setAll('alfa')">
                    <i class="bi bi-x-circle me-1"></i>Semua Alfa
                </button>
                <button type="submit" class="btn btn-sm btn-primary" id="save-btn">
                    <i class="bi bi-save me-1"></i>Simpan Absensi
                </button>
            </div>
        </div>

        <div class="card-body p-0">
            @foreach($students as $i => $student)
            <div class="att-row px-4 py-3 d-flex align-items-center gap-3 flex-wrap" data-student="{{ $student->id }}">
                <div class="student-num">{{ $i + 1 }}</div>

                <div class="me-auto">
                    <div class="fw-600">{{ $student->name }}</div>
                    <div style="font-size:11px;color:#64748b;font-family:'JetBrains Mono',monospace">
                        NIS: {{ $student->nis }}
                        @if($student->gender === 'P') <span class="badge ms-1" style="background:#fce7f3;color:#9d174d;font-size:9px">P</span>
                        @else <span class="badge ms-1" style="background:var(--color-brand-50);color:var(--color-brand-600);font-size:9px">L</span>
                        @endif
                    </div>
                </div>

                {{-- STATUS BUTTONS --}}
                <div class="d-flex gap-1 flex-wrap status-group" role="group">
                    @foreach(['hadir' => ['success','✅'], 'sakit' => ['warning','🤒'], 'izin' => ['info','📄'], 'alfa' => ['danger','❌'], 'terlambat' => ['secondary','⏰']] as $s => [$color, $icon])
                    <input type="radio" class="btn-check status-radio"
                        name="details[{{ $student->id }}][status]"
                        id="st-{{ $student->id }}-{{ $s }}"
                        value="{{ $s }}"
                        {{ ($existing[$student->id] ?? 'alfa') === $s ? 'checked' : '' }}
                        onchange="updateSummary()">
                    <label class="btn btn-outline-{{ $color }} btn-sm" for="st-{{ $student->id }}-{{ $s }}" style="font-size:12px;padding:4px 10px">
                        {{ $icon }} {{ ucfirst($s) }}
                    </label>
                    @endforeach
                </div>

                {{-- CHECK IN TIME --}}
                <input type="time" name="details[{{ $student->id }}][check_in]"
                    class="form-control form-control-sm" style="width:100px"
                    value="{{ now()->format('H:i') }}">

                {{-- NOTES --}}
                <input type="text" name="details[{{ $student->id }}][notes]"
                    class="form-control form-control-sm" style="width:180px"
                    placeholder="Keterangan...">
            </div>
            @endforeach
        </div>

        <div class="card-footer d-flex justify-content-between align-items-center">
            <span class="text-muted" style="font-size:13px">
                Total: <strong>{{ $students->count() }} siswa</strong>
            </span>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save me-2"></i>Simpan Absensi
            </button>
        </div>
    </div>
</form>

@else
<div class="card">
    <div class="card-body text-center py-5">
        <i class="bi bi-clipboard-x text-muted" style="font-size:3rem"></i>
        <h5 class="mt-3 text-muted">Pilih Kelas Terlebih Dahulu</h5>
        <p class="text-muted">Pilih tanggal dan kelas di atas untuk menampilkan daftar siswa.</p>
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

// Initialize summary on load
document.addEventListener('DOMContentLoaded', updateSummary);

// Confirm before submit
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