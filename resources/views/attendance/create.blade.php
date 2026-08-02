{{-- resources/views/attendance/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail & Input Absensi Mapel')
@section('page-title', 'Detail Presensi Mata Pelajaran')

@section('content')

{{-- ── FILTER FORM (DATE RANGE & MAPEL) ── --}}
<div class="mb-4 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200/70">
    <form method="GET" action="{{ route('attendance.create') }}" id="filter-form" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-end">
        <div class="sm:col-span-1 lg:col-span-2">
            <label class="block mb-1 text-xs font-bold uppercase tracking-wider text-slate-500">Periode</label>
            <select name="period" id="input-period" class="form-select text-xs font-semibold text-slate-700 rounded-xl" onchange="onPeriodChange(this.value)">
                <option value="today" {{ ($period ?? 'today') == 'today' ? 'selected' : '' }}>Hari Ini</option>
                <option value="week" {{ ($period ?? '') == 'week' ? 'selected' : '' }}>Minggu Ini ({{ !empty($hasSaturday) ? 'Sen-Sab' : 'Sen-Jum' }})</option>
                <option value="30_days" {{ ($period ?? '') == '30_days' ? 'selected' : '' }}>30 Hari Terakhir</option>
                <option value="month" {{ ($period ?? '') == 'month' ? 'selected' : '' }}>Bulan Ini</option>
                <option value="custom" {{ ($period ?? '') == 'custom' ? 'selected' : '' }}>Atur Manual...</option>
            </select>
        </div>

        <div class="sm:col-span-1 lg:col-span-2">
            <label class="block mb-1 text-xs font-bold uppercase tracking-wider text-slate-500">Tanggal Mulai</label>
            <input type="date" name="start_date" id="input-start-date" class="form-control text-xs font-semibold text-slate-700 rounded-xl" value="{{ $startDate }}" max="{{ today()->toDateString() }}" onchange="onDateChange()">
        </div>

        <div class="sm:col-span-1 lg:col-span-2">
            <label class="block mb-1 text-xs font-bold uppercase tracking-wider text-slate-500">Tanggal Selesai</label>
            <input type="date" name="end_date" id="input-end-date" class="form-control text-xs font-semibold text-slate-700 rounded-xl" value="{{ $endDate }}" max="{{ today()->toDateString() }}" onchange="onDateChange()">
        </div>

        <div class="sm:col-span-2 lg:col-span-3">
            <label class="block mb-1 text-xs font-bold uppercase tracking-wider text-slate-500">Kelas / Mata Pelajaran</label>
            <select name="class_id" id="input-class-id" class="form-select text-xs font-semibold text-slate-700 rounded-xl">
                <option value="">-- Pilih Kelas --</option>
                @foreach($classes as $c)
                <option value="{{ $c->id }}" {{ $classId == $c->id ? 'selected' : '' }}>
                    {{ $c->name }} ({{ $c->grade_label }})
                </option>
                @endforeach
            </select>
        </div>

        <div class="sm:col-span-1 lg:col-span-2">
            <label class="block mb-1 text-xs font-bold uppercase tracking-wider text-slate-500">Sesi Jam Mapel @if(empty($isRangeMode) && !empty($dayName))<span class="text-brand-600 font-extrabold">({{ $dayName }})</span>@endif</label>
            <select name="session" id="input-session" class="form-select text-xs font-semibold text-slate-700 rounded-xl">
                @foreach($sessionLabels as $sKey => $sInfo)
                <option value="{{ $sKey }}" {{ ($session ?? 'all') == $sKey ? 'selected' : '' }}>
                    {{ is_array($sInfo) ? ($sInfo['label'] ?? $sKey) : $sInfo }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="sm:col-span-1 lg:col-span-1">
            <button type="submit" class="inline-flex w-full items-center justify-center gap-1.5 rounded-xl bg-brand-600 px-3 py-2 text-xs font-bold text-white shadow-sm hover:bg-brand-700 transition-all border-0 h-[38px]">
                <i class="bi bi-search"></i>
                <span class="lg:hidden xl:inline">Cari</span>
            </button>
        </div>
    </form>
</div>

@if($students->isNotEmpty())

@if($isRangeMode || ($session ?? 'all') === 'all')
{{-- ── MODE REKAPITULASI PRESENSI GABUNGAN / RENTANG TANGGAL ── --}}
<div class="space-y-4">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 bg-brand-50/60 p-3.5 rounded-2xl border border-brand-200">
        <div>
            <h6 class="mb-0 font-bold text-brand-900 flex items-center gap-2">
                <i class="bi bi-calendar-range text-brand-600"></i>
                Rekapitulasi Presensi Periode: {{ \Carbon\Carbon::parse($startDate)->translatedFormat('l, d M Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->translatedFormat('l, d M Y') }}
            </h6>
            <p class="text-xs text-brand-700 mb-0 mt-0.5">Menampilkan total akumulasi kehadiran seluruh siswa kelas {{ $classes->find($classId)?->name }} pada periode ini.</p>
        </div>
        <span class="badge bg-brand-600 text-white px-3 py-1.5 rounded-full text-xs font-bold self-start sm:self-auto">{{ $students->count() }} Siswa</span>
    </div>

    <div class="card shadow-sm border border-slate-200 overflow-hidden rounded-2xl">
        <div class="table-responsive overflow-x-auto">
            <table class="table table-hover align-middle mb-0 text-sm min-w-[700px]">
                <thead class="bg-slate-50/80">
                    <tr>
                        <th class="text-center" style="width: 50px">No</th>
                        <th>Nama Siswa</th>
                        <th class="text-center">🟢 Hadir</th>
                        <th class="text-center">🔴 Tidak Hadir</th>
                        <th class="text-center">🤒 / 📄 Sakit / Izin</th>
                        <th class="text-center">⏰ Terlambat</th>
                        <th class="text-center">Total Sesi</th>
                        <th class="text-center" style="width: 140px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $i => $student)
                    @php
                        $recap = $rangeRecaps[$student->id] ?? [
                            'hadir' => 0, 'alfa' => 0, 'sakit_izin' => 0, 'terlambat' => 0, 'total' => 0, 'logs' => []
                        ];
                    @endphp
                    <tr>
                        <td class="text-center font-semibold text-slate-400">{{ $i + 1 }}</td>
                        <td>
                            <div class="fw-bold text-slate-800 text-sm sm:text-base">{{ $student->name }}</div>
                            <div class="text-xs text-slate-500 font-mono">NIS: {{ $student->nis }}</div>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-emerald-100 text-emerald-800 rounded-pill px-2.5 py-1 font-bold">{{ $recap['hadir'] }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-rose-100 text-rose-800 rounded-pill px-2.5 py-1 font-bold">{{ $recap['alfa'] }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-amber-100 text-amber-800 rounded-pill px-2.5 py-1 font-bold">{{ $recap['sakit_izin'] }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-purple-100 text-purple-800 rounded-pill px-2.5 py-1 font-bold">{{ $recap['terlambat'] }}</span>
                        </td>
                        <td class="text-center font-bold text-slate-700">{{ $recap['total'] }} Pertemuan</td>
                        <td class="text-center">
                            <button type="button" 
                                    onclick="openRangeStudentModal('{{ addslashes($student->name) }}', {{ json_encode($recap['logs']) }}, {{ json_encode($recap['logs_by_date'] ?? []) }})" 
                                    class="inline-flex items-center gap-1 text-xs font-bold text-brand-600 bg-brand-50 hover:bg-brand-100 border border-brand-200 px-3 py-1.5 rounded-xl transition shadow-2xs">
                                <i class="bi bi-eye"></i> Detail Log
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@else
{{-- ── MODE SATU TANGGAL (INSTANT AUTO-SAVE ABSENSI MAPEL) ── --}}

{{-- COMPACT SUMMARY COUNTER STRIP --}}
<div class="mb-4 flex flex-wrap items-center gap-2.5 rounded-xl border border-slate-200 bg-white p-3 shadow-sm whitespace-nowrap">
    <div class="inline-flex items-center gap-2 rounded-lg bg-emerald-50 px-3 py-1.5 border border-emerald-200 text-xs sm:text-sm whitespace-nowrap">
        <span>🟢</span>
        <span class="font-bold text-emerald-800 uppercase tracking-wide">Hadir:</span>
        <span class="font-extrabold text-emerald-700 font-mono" id="sum-hadir">0</span>
    </div>

    <div class="inline-flex items-center gap-2 rounded-lg bg-rose-50 px-3 py-1.5 border border-rose-200 text-xs sm:text-sm whitespace-nowrap">
        <span>🔴</span>
        <span class="font-bold text-rose-800 uppercase tracking-wide">Tidak Hadir:</span>
        <span class="font-extrabold text-rose-700 font-mono" id="sum-alfa">0</span>
    </div>

    <div class="inline-flex items-center gap-2 rounded-lg bg-amber-50 px-3 py-1.5 border border-amber-200 text-xs sm:text-sm whitespace-nowrap">
        <span>🤒</span>
        <span class="font-bold text-amber-800 uppercase tracking-wide">Sakit/Izin:</span>
        <span class="font-extrabold text-amber-700 font-mono" id="sum-sakit-izin">0</span>
    </div>

    <div class="inline-flex items-center gap-2 rounded-lg bg-slate-50 px-3 py-1.5 border border-slate-200 text-xs sm:text-sm whitespace-nowrap">
        <span class="font-bold text-slate-500 uppercase tracking-wide">Total:</span>
        <span class="font-extrabold text-slate-800 font-mono">{{ $students->count() }} Siswa</span>
    </div>
</div>

{{-- ABSENSI MAPEL TABLE (AUTO-SAVE) --}}
<div class="card shadow-sm border border-slate-200 overflow-hidden rounded-2xl">
    <div class="card-header bg-white border-bottom py-3 px-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h5 class="mb-0 text-slate-800 fw-bold d-flex flex-wrap align-items-center text-base sm:text-lg gap-2">
                <span><i class="bi bi-journal-check text-brand-600 me-1"></i>Absensi Mapel Kelas:</span>
                <span class="text-brand-600 font-extrabold">{{ $classes->find($classId)?->name }}</span>
                @if(!empty($fullDateFormatted))
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                        <i class="bi bi-calendar3"></i> {{ $fullDateFormatted }}
                    </span>
                @endif
                @if(!empty($selectedSubjectInfo) && !empty($selectedSubjectInfo['subject']))
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-brand-50 text-brand-700 border border-brand-200 shadow-2xs">
                        <i class="bi bi-book-half"></i> {{ $selectedSubjectInfo['subject'] }}
                    </span>
                    @if(!empty($selectedSubjectInfo['slot_num']))
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-purple-50 text-purple-700 border border-purple-200 shadow-2xs">
                        <i class="bi bi-clock-history"></i> Jam ke-{{ $selectedSubjectInfo['slot_num'] }} @if(!empty($selectedSubjectInfo['time_str']))({{ $selectedSubjectInfo['time_str'] }})@endif
                    </span>
                    @endif
                @elseif(!empty($selectedSubjectName))
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-brand-50 text-brand-700 border border-brand-200 shadow-2xs">
                        <i class="bi bi-book-half"></i> {{ $selectedSubjectName }}
                    </span>
                @endif
            </h5>
            <p class="text-muted small mb-0 mt-0.5">Klik <strong>Hadir</strong> atau <strong>Tidak Hadir</strong>. Status <strong>langsung tersimpan otomatis</strong>.</p>
        </div>
        @if(($session ?? 'all') !== 'all')
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <button type="button" class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 min-h-[44px] rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200 shadow-xs text-xs sm:text-sm font-bold flex-1 sm:flex-none hover:bg-emerald-100 active:scale-95 transition-all" onclick="setAllQuick('hadir')">
                <i class="bi bi-check-all text-base"></i>Semua Hadir
            </button>
            <button type="button" class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 min-h-[44px] rounded-xl bg-rose-50 text-rose-800 border border-rose-200 shadow-xs text-xs sm:text-sm font-bold flex-1 sm:flex-none hover:bg-rose-100 active:scale-95 transition-all" onclick="setAllQuick('alfa')">
                <i class="bi bi-x-circle text-base"></i>Semua Tidak Hadir
            </button>
        </div>
        @endif
    </div>

    @if(($session ?? 'all') === 'all')
    <div class="p-3.5 bg-sky-50 border-b border-sky-100 flex items-center justify-between gap-3 text-sky-900">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-sky-600 text-white flex items-center justify-center font-bold shrink-0 shadow-2xs">
                <i class="bi bi-eye-fill text-sm"></i>
            </div>
            <div>
                <div class="font-extrabold text-xs sm:text-sm text-sky-950">Mode Lihat Rekapitulasi Gabungan (Read-Only)</div>
                <div class="text-[11px] sm:text-xs text-sky-800 font-medium mt-0.5">Untuk menginput presensi siswa, silakan pilih salah satu <strong>Mata Pelajaran</strong> pada filter dropdown di atas.</div>
            </div>
        </div>
    </div>
    @endif

    <div class="card-body p-0">
        <div class="table-responsive overflow-x-auto">
            <table class="table mb-0 align-middle w-full" style="min-width: 700px">
                <thead>
                    <tr class="text-xs uppercase tracking-wider bg-slate-50">
                        <th class="text-center" style="width: 45px">No</th>
                        <th style="width: 180px">Siswa</th>
                        <th class="text-center" style="width: 220px">Presensi Mapel</th>
                        <th style="width: 240px">Catatan Siswa</th>
                        <th class="text-center" style="width: 110px">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $i => $student)
                    @php
                        $studentExisting = $existing[$student->id] ?? null;
                        $savedStatus = $studentExisting['status'] ?? null;
                        $isHomeroomLock = !empty($studentExisting['from_homeroom']) || in_array($savedStatus, ['sakit', 'izin'], true);
                    @endphp
                    <tr class="att-row" data-student-id="{{ $student->id }}">
                        <td class="text-center align-middle font-semibold text-slate-400" style="font-size: 13px">{{ $i + 1 }}</td>
                        <td class="align-middle">
                            <div class="fw-semibold text-slate-800" style="font-size: 14px">{{ $student->name }}</div>
                            <div style="font-size: 11px; color: #64748b; font-family: 'JetBrains Mono', monospace" class="mt-0.5">
                                NIS: {{ $student->nis }}
                            </div>
                        </td>
                        <td class="align-middle text-center">
                            @if(($session ?? 'all') === 'all')
                                <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-slate-100 text-slate-600 font-bold border border-slate-200 text-xs">
                                    <i class="bi bi-funnel text-slate-400"></i> Pilih Mapel
                                </span>
                            @elseif($isHomeroomLock)
                                @if($savedStatus === 'sakit')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-100 text-amber-800 font-bold border border-amber-300 shadow-sm text-xs">
                                        🤒 Sakit (Catatan Wali Kelas)
                                    </span>
                                @elseif($savedStatus === 'izin')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-sky-100 text-sky-800 font-bold border border-sky-300 shadow-sm text-xs">
                                        📄 Izin (Catatan Wali Kelas)
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-100 text-amber-800 font-bold border border-amber-300 shadow-sm text-xs">
                                        ℹ️ {{ ucfirst($savedStatus) }} (Wali Kelas)
                                    </span>
                                @endif
                            @else
                                <div class="btn-group shadow-xs rounded-xl overflow-hidden" role="group" aria-label="Status">
                                    <button type="button" 
                                            class="btn btn-quick-save px-3.5 py-2.5 min-h-[44px] text-xs sm:text-sm font-extrabold {{ $savedStatus === 'hadir' ? 'btn-success shadow-sm' : 'btn-outline-success' }}"
                                            onclick="saveQuick('{{ $student->id }}', 'hadir', this)">
                                        🟢 Hadir
                                    </button>
                                    <button type="button" 
                                            class="btn btn-quick-save px-3.5 py-2.5 min-h-[44px] text-xs sm:text-sm font-extrabold {{ $savedStatus === 'alfa' ? 'btn-danger shadow-sm' : 'btn-outline-danger' }}"
                                            onclick="saveQuick('{{ $student->id }}', 'alfa', this)">
                                        🔴 Tidak Hadir
                                    </button>
                                </div>
                            @endif
                        </td>
                        <td class="align-middle">
                            @if($isHomeroomLock)
                                <span class="text-xs text-amber-700 font-medium italic flex items-center gap-1 bg-amber-50 px-2.5 py-1.5 rounded-lg border border-amber-200/80">
                                    <i class="bi bi-lock-fill text-amber-600"></i> Siswa {{ ucfirst($savedStatus) }} (Dicatat Wali Kelas)
                                </span>
                            @else
                                <input type="text" 
                                       id="notes-{{ $student->id }}" 
                                       class="form-control form-control-sm text-xs rounded-xl border-slate-200 bg-slate-50/70 focus:bg-white focus:border-brand-500 transition-all" 
                                       placeholder="Tulis catatan siswa (opsional)..." 
                                       value="{{ $studentExisting['notes'] ?? '' }}"
                                       onchange="saveQuickNotes('{{ $student->id }}')">
                            @endif
                        </td>
                        <td class="align-middle text-center">
                            <span class="save-status-indicator text-xs font-semibold text-slate-400" id="status-ind-{{ $student->id }}">
                                @if($isHomeroomLock)
                                    <span class="text-amber-600 font-medium">Locked</span>
                                @elseif($savedStatus === 'hadir')
                                    <span class="text-emerald-600 font-bold"><i class="bi bi-check-circle-fill me-1"></i>Hadir</span>
                                @elseif($savedStatus === 'alfa')
                                    <span class="text-rose-600 font-bold"><i class="bi bi-x-circle-fill me-1"></i>Tidak Hadir</span>
                                @else
                                    <span class="text-slate-400 font-normal">Belum Diabsen</span>
                                @endif
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Form Catatan Sesi Mapel / Jurnal Pembelajaran Guru --}}
    <div class="card shadow-sm border-slate-200/60 rounded-2xl overflow-hidden bg-white mt-4">
        <div class="card-header bg-slate-50/80 border-bottom border-slate-100 px-4 py-3 flex items-center justify-between">
            <span class="fw-bold text-slate-800 text-sm flex items-center gap-2">
                <i class="bi bi-journal-text text-brand-600 text-base"></i> Catatan Laporan KBM / Sesi Mapel Ini (Untuk Admin & Wali Kelas)
            </span>
            <span id="session-notes-status" class="text-xs font-semibold text-slate-400"></span>
        </div>
        <div class="card-body p-4 space-y-3">
            <textarea id="input-session-notes" rows="3" class="form-control text-sm rounded-xl border-slate-200 bg-slate-50/50 focus:bg-white focus:border-brand-500 transition-all w-full" placeholder="Tuliskan materi KBM yang dibahas, catatan kejadian kelas, atau laporan untuk Wali Kelas & Admin...">{{ $sessionNotes ?? '' }}</textarea>
            <div class="flex justify-end">
                <button type="button" onclick="saveSessionNotesAjax()" class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-bold text-white shadow-md hover:bg-brand-700 transition-all border-0">
                    <i class="bi bi-save"></i> Simpan Catatan Sesi
                </button>
            </div>
        </div>
    </div>
</div>
@endif

@else
{{-- EMPTY STATE --}}
<div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #f8fafc, #f1f5f9)">
    <div class="card-body text-center py-5">
        <div class="d-flex align-items-center justify-content-center mx-auto bg-white rounded-circle shadow-sm mb-4" style="width: 80px; height: 80px;">
            <i class="bi bi-calendar2-check text-primary m-0" style="font-size: 2.2rem; line-height: 1;"></i>
        </div>
        <h4 class="fw-bold text-slate-800 mb-2">Pilih Kelas Terlebih Dahulu</h4>
        <p class="text-slate-500 max-w-md mx-auto mb-0" style="max-width: 460px;">Silakan pilih tanggal mulai & selesai, kelas, dan sesi pada form di atas, lalu klik <strong>Cari</strong> untuk mengabsen atau melihat rekap presensi.</p>
    </div>
</div>
@endif

{{-- MODAL POP-UP LOG PRESENSI SISWA RENTANG TANGGAL --}}
<div id="rangeStudentLogsModal" onclick="if(event.target === this) closeRangeStudentModal()" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/80 backdrop-blur-xs p-3 sm:p-6 flex items-start justify-center pt-4 sm:pt-8">
    <div class="w-full max-w-6xl overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-slate-200 max-h-[88vh] flex flex-col my-auto">
        <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50/90 px-5 py-3.5 shrink-0">
            <h5 id="rslmTitle" class="text-sm sm:text-base font-extrabold text-slate-800 flex items-center gap-2 mb-0">
                <i class="bi bi-clock-history text-brand-600"></i> Detail Riwayat Presensi Siswa
            </h5>
            <button type="button" onclick="closeRangeStudentModal()" class="rounded-xl p-1.5 text-slate-400 hover:bg-slate-200 hover:text-slate-700 transition" title="Tutup Modal">
                <i class="bi bi-x-lg text-lg"></i>
            </button>
        </div>
        <div id="rslmBody" class="p-3 sm:p-5 overflow-y-auto max-h-[70vh] grow">
            {{-- Dynamic Table injected via JS --}}
        </div>
        <div class="border-t border-slate-200 bg-slate-50/70 px-5 py-2.5 flex items-center justify-end shrink-0">
            <button type="button" onclick="closeRangeStudentModal()" class="inline-flex items-center gap-1.5 rounded-xl bg-slate-800 px-4 py-1.5 text-xs font-bold text-white hover:bg-slate-900 transition shadow-sm">
                <i class="bi bi-x-circle"></i> Tutup Modal
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const CSRF_TOKEN = '{{ csrf_token() }}';
const QUICK_SAVE_URL = '{{ route("attendance.quick-save") }}';
const DATE_VAL = '{{ $startDate }}';
const CLASS_ID_VAL = '{{ $classId }}';
const SESSION_VAL = '{{ $session }}';

function updateSummaryCounters() {
    let hadir = 0;
    let alfa = 0;
    let sakitIzin = 0;

    document.querySelectorAll('.att-row').forEach(row => {
        const ind = row.querySelector('.save-status-indicator');
        const text = ind ? ind.textContent.trim() : '';
        
        if (text.includes('Hadir') && !text.includes('Tidak')) {
            hadir++;
        } else if (text.includes('Tidak Hadir')) {
            alfa++;
        } else if (text.includes('Sakit') || text.includes('Izin') || text.includes('Locked')) {
            sakitIzin++;
        }
    });

    const sumHadir = document.getElementById('sum-hadir');
    const sumAlfa = document.getElementById('sum-alfa');
    const sumSakitIzin = document.getElementById('sum-sakit-izin');

    if (sumHadir) sumHadir.textContent = hadir;
    if (sumAlfa) sumAlfa.textContent = alfa;
    if (sumSakitIzin) sumSakitIzin.textContent = sakitIzin;
}

function saveQuick(studentId, status, btnElement) {
    const indicator = document.getElementById(`status-ind-${studentId}`);
    const notesVal = document.getElementById(`notes-${studentId}`)?.value || '';

    if (indicator) {
        indicator.innerHTML = '<span class="text-slate-400 font-normal"><i class="bi bi-arrow-repeat spin me-1"></i>Menyimpan...</span>';
    }

    fetch(QUICK_SAVE_URL, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'Accept': 'application/json',
        },
        body: JSON.stringify({
            date: DATE_VAL,
            class_id: CLASS_ID_VAL,
            session: SESSION_VAL,
            student_id: studentId,
            status: status,
            notes: notesVal
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const btnGroup = btnElement.closest('.btn-group');
            if (btnGroup) {
                btnGroup.querySelectorAll('.btn-quick-save').forEach(b => {
                    b.classList.remove('btn-success', 'btn-danger', 'font-bold', 'shadow-sm');
                    if (b.textContent.includes('Hadir') && !b.textContent.includes('Tidak')) {
                        b.className = 'btn btn-sm btn-quick-save ' + (status === 'hadir' ? 'btn-success font-bold shadow-sm' : 'btn-outline-success');
                    } else {
                        b.className = 'btn btn-sm btn-quick-save ' + (status === 'alfa' ? 'btn-danger font-bold shadow-sm' : 'btn-outline-danger');
                    }
                });
            }

            if (indicator) {
                if (status === 'hadir') {
                    indicator.innerHTML = '<span class="text-emerald-600 font-bold"><i class="bi bi-check-circle-fill me-1"></i>Hadir</span>';
                } else {
                    indicator.innerHTML = '<span class="text-rose-600 font-bold"><i class="bi bi-x-circle-fill me-1"></i>Tidak Hadir</span>';
                }
            }
            updateSummaryCounters();
        } else {
            alert(data.message || 'Gagal menyimpan absensi.');
            if (indicator) indicator.innerHTML = '<span class="text-rose-500 font-normal">Gagal</span>';
        }
    })
    .catch(err => {
        alert('Terjadi kesalahan jaringan.');
        if (indicator) indicator.innerHTML = '<span class="text-rose-500 font-normal">Error</span>';
    });
}

function saveQuickNotes(studentId) {
    const row = document.querySelector(`.att-row[data-student-id="${studentId}"]`);
    if (!row) return;
    const btnActive = row.querySelector('.btn-quick-save.btn-success, .btn-quick-save.btn-danger');
    if (btnActive) {
        const isHadir = btnActive.textContent.includes('Hadir') && !btnActive.textContent.includes('Tidak');
        saveQuick(studentId, isHadir ? 'hadir' : 'alfa', btnActive);
    }
}

function saveSessionNotesAjax() {
    const notes = document.getElementById('input-session-notes').value;
    const statusEl = document.getElementById('session-notes-status');
    if (statusEl) statusEl.textContent = 'Menyimpan...';

    fetch('{{ route("attendance.save-session-notes") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            date: DATE_VAL,
            class_id: CLASS_ID_VAL,
            session: SESSION_VAL,
            notes: notes
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            if (statusEl) statusEl.innerHTML = '<span class="text-emerald-600 font-bold"><i class="bi bi-check-circle me-1"></i>Catatan Sesi Tersimpan!</span>';
            setTimeout(() => { if (statusEl) statusEl.textContent = ''; }, 3000);
        } else {
            if (statusEl) statusEl.innerHTML = '<span class="text-rose-600 font-bold">Gagal menyimpan</span>';
        }
    })
    .catch(err => {
        if (statusEl) statusEl.innerHTML = '<span class="text-rose-600 font-bold">Gagal koneksi</span>';
    });
}

function setAllQuick(status) {
    document.querySelectorAll('.att-row').forEach(row => {
        const btn = row.querySelector(`.btn-quick-save[onclick*="'${status}'"]`);
        const studentId = row.dataset.studentId;
        if (btn && studentId) {
            saveQuick(studentId, status, btn);
        }
    });
}

let currentModalStudentName = '';
let currentModalLogs = [];
let currentModalLogsByDate = [];
let currentSortOrder = 'asc'; // 'asc': Terlama -> Terbaru, 'desc': Terbaru -> Terlama

function toggleModalSortOrder() {
    currentSortOrder = (currentSortOrder === 'asc') ? 'desc' : 'asc';
    renderModalLogsTable();
}

function openRangeStudentModal(studentName, logs, logsByDate) {
    currentModalStudentName = studentName;
    currentModalLogs = logs || [];
    currentModalLogsByDate = logsByDate || [];
    currentSortOrder = 'asc';
    renderModalLogsTable();
    document.getElementById('rangeStudentLogsModal').classList.remove('hidden');
}

function renderModalLogsTable() {
    document.getElementById('rslmTitle').innerHTML = `
        <div class="flex items-center justify-between w-full pr-4 gap-2 flex-wrap sm:flex-nowrap">
            <span class="text-slate-800 font-extrabold flex items-center gap-1.5 truncate">
                <i class="bi bi-person-badge text-brand-600"></i> Detail Presensi Harian & Mapel: 
                <span class="text-brand-700 font-black ml-1">${currentModalStudentName}</span>
            </span>
            <button type="button" onclick="toggleModalSortOrder()" class="inline-flex items-center gap-1.5 text-xs font-bold bg-white text-slate-700 hover:bg-slate-100 border border-slate-300 px-3 py-1 rounded-xl shadow-2xs transition">
                <i class="bi bi-arrow-down-up text-brand-600"></i>
                <span>Urutan: ${currentSortOrder === 'asc' ? 'Terlama → Terbaru (Senin-Jumat)' : 'Terbaru → Terlama'}</span>
            </button>
        </div>
    `;

    const container = document.getElementById('rslmBody');
    container.innerHTML = '';

    if (currentModalLogsByDate && currentModalLogsByDate.length > 0) {
        let sortedLogsByDate = [...currentModalLogsByDate];
        sortedLogsByDate.sort((a, b) => {
            return currentSortOrder === 'asc' 
                ? a.raw_date.localeCompare(b.raw_date) 
                : b.raw_date.localeCompare(a.raw_date);
        });

        let tableRows = '';
        let currentWeek = null;
        let itemIndex = 0;

        sortedLogsByDate.forEach((dayData) => {
            if (dayData.week_num !== currentWeek) {
                currentWeek = dayData.week_num;
                tableRows += `
                    <tr class="bg-slate-200/90 border-y-2 border-brand-500">
                        <td colspan="7" class="py-2.5 px-4 bg-slate-100">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <span class="bg-brand-600 text-white font-extrabold px-3 py-1 rounded-lg text-xs tracking-wider shadow-2xs">
                                        <i class="bi bi-calendar3-week me-1"></i> MINGGU KE-${currentWeek}
                                    </span>
                                    <span class="text-slate-800 font-extrabold text-xs">Rekap Presensi Harian & Mapel (Senin — Jumat)</span>
                                </div>
                                <span class="text-[11px] font-bold text-brand-700 bg-brand-50 border border-brand-200 px-2.5 py-0.5 rounded-md">Pembatas Mingguan</span>
                            </div>
                        </td>
                    </tr>
                `;
            }

            itemIndex++;
            const bgRow = itemIndex % 2 === 0 ? 'bg-white' : 'bg-slate-50/60';

            let pagiHtml = '<span class="text-slate-300 text-xs">-</span>';
            if (dayData.pagi) {
                let pStyle = 'bg-slate-100 text-slate-700 border-slate-200';
                if (dayData.pagi.status === 'hadir') pStyle = 'bg-emerald-50 text-emerald-800 border-emerald-200 font-bold';
                if (dayData.pagi.status === 'terlambat') pStyle = 'bg-purple-50 text-purple-800 border-purple-200 font-bold';
                if (dayData.pagi.status === 'sakit' || dayData.pagi.status === 'izin') pStyle = 'bg-amber-50 text-amber-800 border-amber-200 font-bold';
                if (dayData.pagi.status === 'alfa') pStyle = 'bg-rose-50 text-rose-800 border-rose-200 font-bold';

                pagiHtml = `
                    <div class="inline-flex flex-col gap-0.5 items-center">
                        <span class="rounded-full ${pStyle} border px-2.5 py-0.5 text-[11px] font-bold">${dayData.pagi.status_label}</span>
                        ${dayData.pagi.check_in ? `<span class="text-[10px] text-slate-500 font-mono font-bold">${dayData.pagi.check_in}</span>` : ''}
                    </div>
                `;
            }

            let mapelColsHtml = '';
            for (let m = 1; m <= 4; m++) {
                const mData = dayData.mapels ? dayData.mapels.find(x => x.session_key === `mapel_${m}`) : null;
                if (mData) {
                    let mStyle = 'bg-slate-100 text-slate-700 border-slate-200';
                    let badgeBg = 'bg-slate-600 text-white';
                    if (mData.status === 'hadir') { 
                        mStyle = 'bg-emerald-50/90 border-emerald-200/90 text-emerald-950'; 
                        badgeBg = 'bg-emerald-600 text-white font-extrabold shadow-2xs'; 
                    }
                    if (mData.status === 'terlambat') { 
                        mStyle = 'bg-amber-100/90 border-amber-300 text-amber-950 font-semibold ring-1 ring-amber-400/50'; 
                        badgeBg = 'bg-amber-500 text-white font-black shadow-xs'; 
                    }
                    if (mData.status === 'sakit') { 
                        mStyle = 'bg-amber-100/90 border-amber-300 text-amber-950 font-semibold ring-1 ring-amber-400/50'; 
                        badgeBg = 'bg-amber-600 text-white font-black shadow-xs'; 
                    }
                    if (mData.status === 'izin') { 
                        mStyle = 'bg-sky-100/90 border-sky-300 text-sky-950 font-semibold ring-1 ring-sky-400/50'; 
                        badgeBg = 'bg-sky-600 text-white font-black shadow-xs'; 
                    }
                    if (mData.status === 'alfa') { 
                        mStyle = 'bg-rose-100/95 border-rose-400 text-rose-950 font-bold ring-2 ring-rose-300/80 shadow-xs'; 
                        badgeBg = 'bg-rose-600 text-white font-black shadow-xs ring-2 ring-rose-300/60'; 
                    }

                    let subTitle = mData.session.includes(':') ? mData.session.split(':')[1].trim() : mData.session;
                    let timeBadgeHtml = mData.time_str ? `<div class="text-[10px] text-purple-800 font-extrabold flex items-center gap-1 bg-purple-50/90 border border-purple-200/80 px-1.5 py-0.5 rounded-md mt-0.5 w-fit"><i class="bi bi-clock-history text-purple-600"></i> ${mData.time_str}</div>` : '';

                    mapelColsHtml += `
                        <td class="py-2 px-2 align-top">
                            <div class="rounded-xl border ${mStyle} p-2 space-y-1 shadow-2xs">
                                <div class="flex items-center justify-between gap-1">
                                    <span class="text-xs font-black text-slate-800 truncate" title="${subTitle}">${subTitle}</span>
                                    <span class="badge ${badgeBg} text-white text-[10px] px-1.5 py-0.5 rounded font-extrabold shrink-0">${mData.status_label}</span>
                                </div>
                                ${timeBadgeHtml}
                                <div class="text-[10px] text-slate-600 font-semibold truncate flex items-center gap-1">
                                    <i class="bi bi-person text-brand-600"></i> ${mData.teacher_name}
                                </div>
                                <div class="text-[10px] text-slate-500 font-medium italic border-t border-slate-200/60 pt-0.5 mt-0.5 truncate" title="${mData.notes}">
                                    💬 ${mData.notes}
                                </div>
                            </div>
                        </td>
                    `;
                } else {
                    mapelColsHtml += `<td class="py-2 px-2 text-center text-slate-300 text-xs align-middle">-</td>`;
                }
            }

            tableRows += `
                <tr class="${bgRow} hover:bg-brand-50/30 transition-colors">
                    <td class="text-center font-semibold text-slate-400 text-xs py-2 px-2.5 align-middle">${itemIndex}</td>
                    <td class="whitespace-nowrap text-xs font-extrabold text-slate-800 py-2 px-2.5 align-middle">${dayData.day_date}</td>
                    <td class="text-center whitespace-nowrap py-2 px-2.5 align-middle">${pagiHtml}</td>
                    ${mapelColsHtml}
                </tr>
            `;
        });

        container.innerHTML = `
            <div class="overflow-x-auto rounded-xl border border-slate-200 shadow-2xs">
                <table class="w-full text-left align-middle mb-0 min-w-[850px]">
                    <thead class="bg-slate-100/90 text-[11px] font-bold text-slate-600 uppercase tracking-wider border-b border-slate-200 sticky top-0 z-10">
                        <tr>
                            <th class="py-2.5 px-2.5 text-center" style="width: 40px">No</th>
                            <th class="py-2.5 px-2.5">Tanggal & Hari</th>
                            <th class="py-2.5 px-2.5 text-center">Presensi Pagi</th>
                            <th class="py-2.5 px-2" style="width: 20%">Jam Mapel 1</th>
                            <th class="py-2.5 px-2" style="width: 20%">Jam Mapel 2</th>
                            <th class="py-2.5 px-2" style="width: 20%">Jam Mapel 3</th>
                            <th class="py-2.5 px-2" style="width: 20%">Jam Mapel 4</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        ${tableRows}
                    </tbody>
                </table>
            </div>
        `;
        return;
    }

    if (!currentModalLogs || currentModalLogs.length === 0) {
        container.innerHTML = `
            <div class="text-center text-slate-400 py-10 space-y-2">
                <i class="bi bi-inbox text-4xl block text-slate-300"></i>
                <p class="text-sm font-semibold mb-0">Belum ada data presensi untuk siswa ini dalam periode terpilih.</p>
            </div>
        `;
        return;
    }

    let sortedLogs = [...currentModalLogs];
    sortedLogs.sort((a, b) => {
        const dateA = a.raw_date || a.date || '';
        const dateB = b.raw_date || b.date || '';
        const comp = currentSortOrder === 'desc' ? dateB.localeCompare(dateA) : dateA.localeCompare(dateB);
        if (comp !== 0) {
            return comp;
        }
        const wA = a.sess_weight ?? 0;
        const wB = b.sess_weight ?? 0;
        return wA - wB; // Always Mapel 1 -> Mapel 2 -> Mapel 3 -> Mapel 4 per hari
    });

    let tableRows = '';
    let currentWeek = null;
    let itemIndex = 0;

    sortedLogs.forEach((log) => {
        if (log.week_num !== currentWeek) {
            currentWeek = log.week_num;
            tableRows += `
                <tr class="bg-slate-200/90 border-y-2 border-brand-500">
                    <td colspan="6" class="py-2.5 px-4 bg-slate-100">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <span class="bg-brand-600 text-white font-extrabold px-3 py-1 rounded-lg text-xs tracking-wider shadow-2xs">
                                    <i class="bi bi-calendar3-week me-1"></i> MINGGU KE-${currentWeek}
                                </span>
                                <span class="text-slate-800 font-extrabold text-xs">Urutan Presensi Pelajaran (Senin — Jumat)</span>
                            </div>
                            <span class="text-[11px] font-bold text-brand-700 bg-brand-50 border border-brand-200 px-2.5 py-0.5 rounded-md">Pembatas Mingguan</span>
                        </div>
                    </td>
                </tr>
            `;
        }

        itemIndex++;
        let badgeHtml = '';
        const st = (log.status || '').toLowerCase();

        if (st === 'hadir') {
            badgeHtml = '<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-600 text-white font-extrabold text-xs shadow-2xs"><i class="bi bi-check-circle-fill"></i> Hadir</span>';
        } else if (st === 'terlambat') {
            badgeHtml = '<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-500 text-white font-black text-xs shadow-xs"><i class="bi bi-clock-fill"></i> Terlambat</span>';
        } else if (st === 'sakit') {
            badgeHtml = '<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-600 text-white font-black text-xs shadow-xs"><i class="bi bi-hospital-fill"></i> Sakit</span>';
        } else if (st === 'izin') {
            badgeHtml = '<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-sky-600 text-white font-black text-xs shadow-xs"><i class="bi bi-file-earmark-text-fill"></i> Izin</span>';
        } else if (st === 'alfa') {
            badgeHtml = '<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-600 text-white font-black text-xs shadow-xs ring-2 ring-rose-300/60"><i class="bi bi-x-circle-fill"></i> Alfa (Tanpa Keterangan)</span>';
        } else {
            badgeHtml = `<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-600 text-white font-bold text-xs">${log.status_label || log.status || '-'}</span>`;
        }

        const bgRow = (st === 'alfa') 
            ? 'bg-rose-50/95 border-l-4 border-l-rose-600 font-bold' 
            : (st === 'izin')
                ? 'bg-sky-50/90 border-l-4 border-l-sky-500 font-semibold'
                : (st === 'sakit')
                    ? 'bg-amber-50/90 border-l-4 border-l-amber-500 font-semibold'
                    : (st === 'terlambat')
                        ? 'bg-amber-50/90 border-l-4 border-l-amber-500 font-semibold'
                        : (itemIndex % 2 === 0 ? 'bg-white' : 'bg-slate-50/60');

        tableRows += `
            <tr class="${bgRow} hover:bg-brand-50/30 transition-colors border-b border-slate-100">
                <td class="text-center font-mono text-slate-400 text-xs py-2.5 px-3 font-bold">${itemIndex}</td>
                <td class="whitespace-nowrap text-xs font-black text-slate-900 py-2.5 px-3">
                    <i class="bi bi-calendar3 text-brand-600 me-1.5"></i>${log.day_date || log.date}
                </td>
                <td class="text-xs text-slate-800 font-bold py-2.5 px-3">
                    <span class="badge bg-slate-200 text-slate-800 text-[11px] font-black uppercase px-2.5 py-1 rounded-lg">${log.session}</span>
                </td>
                <td class="text-center whitespace-nowrap text-xs font-mono font-bold text-slate-800 py-2.5 px-3">${log.check_in || '-'}</td>
                <td class="text-center whitespace-nowrap py-2.5 px-3">
                    ${badgeHtml}
                </td>
                <td class="text-xs text-slate-700 italic py-2.5 px-3">💬 ${log.notes || 'Tidak ada catatan'}</td>
            </tr>
        `;
    });

    container.innerHTML = `
        <div class="overflow-x-auto rounded-xl border border-slate-200 shadow-2xs">
            <table class="w-full text-left align-middle mb-0 min-w-[750px]">
                <thead class="bg-slate-100/90 text-[11px] font-bold text-slate-600 uppercase tracking-wider border-b border-slate-200 sticky top-0 z-10">
                    <tr>
                        <th class="py-2.5 px-3 text-center" style="width: 40px">No</th>
                        <th class="py-2.5 px-3">Tanggal & Hari</th>
                        <th class="py-2.5 px-3">Sesi / Mata Pelajaran</th>
                        <th class="py-2.5 px-3 text-center">Jam Masuk</th>
                        <th class="py-2.5 px-3 text-center">Status</th>
                        <th class="py-2.5 px-3">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    ${tableRows}
                </tbody>
            </table>
        </div>
    `;
}

function closeRangeStudentModal() {
    document.getElementById('rangeStudentLogsModal').classList.add('hidden');
}

function getLocalDateString(dateObj) {
    const year = dateObj.getFullYear();
    const month = String(dateObj.getMonth() + 1).padStart(2, '0');
    const day = String(dateObj.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

function onPeriodChange(period) {
    const startInput = document.getElementById('input-start-date');
    const endInput = document.getElementById('input-end-date');
    const today = new Date();

    if (!startInput || !endInput) return;

    if (period === 'today') {
        const t = getLocalDateString(today);
        startInput.value = t;
        endInput.value   = t;
    } else if (period === '30_days') {
        const start = new Date(today);
        start.setDate(today.getDate() - 30);
        startInput.value = getLocalDateString(start);
        endInput.value   = getLocalDateString(today);
    } else if (period === 'week') {
        const dayOfWeek = today.getDay();
        const diffToMon = dayOfWeek === 0 ? -6 : 1 - dayOfWeek;
        const mon = new Date(today);
        mon.setDate(today.getDate() + diffToMon);
        const endDay = new Date(mon);
        const daysToAdd = {{ !empty($hasSaturday) ? 5 : 4 }};
        endDay.setDate(mon.getDate() + daysToAdd);
        startInput.value = getLocalDateString(mon);
        endInput.value   = getLocalDateString(endDay);
    } else if (period === 'month') {
        const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
        const lastDay  = new Date(today.getFullYear(), today.getMonth() + 1, 0);
        startInput.value = getLocalDateString(firstDay);
        endInput.value   = getLocalDateString(lastDay);
    }
}

function onDateChange() {
    const periodSelect = document.getElementById('input-period');
    if (periodSelect && periodSelect.value !== 'custom') {
        periodSelect.value = 'custom';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    updateSummaryCounters();
    
    const form = document.getElementById('filter-form');
    if (form) {
        form.addEventListener('submit', function() {
            const period = document.getElementById('input-period')?.value;
            const startDate = document.getElementById('input-start-date')?.value;
            const endDate = document.getElementById('input-end-date')?.value;
            const todayStr = getLocalDateString(new Date());

            // Bersihkan parameter redundan di URL jika memilih hari ini
            if (period === 'today' && startDate === todayStr && endDate === todayStr) {
                document.getElementById('input-period').disabled = true;
                document.getElementById('input-start-date').disabled = true;
                document.getElementById('input-end-date').disabled = true;
            }
        });
    }
});

</script>
<style>
.spin {
    animation: spin 1s linear infinite;
    display: inline-block;
}
@keyframes spin { 100% { transform: rotate(360deg); } }
</style>
@endpush