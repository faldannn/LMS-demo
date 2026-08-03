@extends('layouts.app')
@section('title', 'Detail Absensi')
@section('page-title', 'Detail Absensi')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-primary px-3 py-2 text-xs font-bold rounded-xl shadow-2xs">{{ ucfirst($attendance->session) }}</span>
                <span class="fw-bold text-slate-800" style="font-size:16px">{{ $attendance->date->translatedFormat('l, d F Y') }}</span>
            </div>
            <span class="text-muted" style="font-size:13px">{{ $attendance->class?->name ?? '-' }} · {{ $attendance->teacher?->name ?? '-' }}</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            @role('admin,guru')
                <a href="{{ route('attendance.homeroom-create', ['class_id' => $attendance->class_id, 'date' => $attendance->date->toDateString()]) }}" class="btn btn-primary btn-sm px-3 py-2 text-xs font-bold rounded-xl shadow-sm text-decoration-none d-inline-flex align-items-center gap-1.5">
                    <i class="bi bi-pencil-square"></i> Edit / Input Absensi Kelas
                </a>
            @endrole
            @if(auth()->user()->isGuru())
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm px-3 py-2 text-xs font-bold rounded-xl shadow-sm text-decoration-none d-inline-flex align-items-center gap-1.5">
                    <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
                </a>
            @else
                <a href="{{ route('attendance.index') }}" class="btn btn-outline-secondary btn-sm px-3 py-2 text-xs font-bold rounded-xl shadow-sm text-decoration-none d-inline-flex align-items-center gap-1.5">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            @endif
        </div>
    </div>

    {{-- ── 4 STAT CARDS ── --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-slate-200/60 rounded-2xl p-3 text-center bg-white mb-0">
                <div class="stat-icon mx-auto mb-2" style="background:var(--color-brand-50);color:var(--color-brand-500)"><i class="bi bi-check-circle-fill"></i></div>
                <div style="font-size:24px;font-weight:800;font-family:'JetBrains Mono',monospace;color:var(--color-brand-500)">{{ $attendance->details->where('status','hadir')->count() }}</div>
                <div class="text-muted text-xs font-bold uppercase tracking-wider mt-1">Hadir</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-slate-200/60 rounded-2xl p-3 text-center bg-white mb-0">
                <div class="stat-icon mx-auto mb-2" style="background:#fef3c7;color:#f59e0b"><i class="bi bi-clock-fill"></i></div>
                <div style="font-size:24px;font-weight:800;font-family:'JetBrains Mono',monospace;color:#f59e0b">{{ $attendance->details->where('status','terlambat')->count() }}</div>
                <div class="text-muted text-xs font-bold uppercase tracking-wider mt-1">Terlambat</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-slate-200/60 rounded-2xl p-3 text-center bg-white mb-0">
                <div class="stat-icon mx-auto mb-2" style="background:#fee2e2;color:#ef4444"><i class="bi bi-x-circle-fill"></i></div>
                <div style="font-size:24px;font-weight:800;font-family:'JetBrains Mono',monospace;color:#ef4444">{{ $attendance->details->where('status','alfa')->count() }}</div>
                <div class="text-muted text-xs font-bold uppercase tracking-wider mt-1">Alfa</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-slate-200/60 rounded-2xl p-3 text-center bg-white mb-0">
                <div class="stat-icon mx-auto mb-2" style="background:var(--color-brand-50);color:var(--color-brand-600)"><i class="bi bi-file-earmark-text-fill"></i></div>
                <div style="font-size:24px;font-weight:800;font-family:'JetBrains Mono',monospace;color:var(--color-brand-600)">{{ $attendance->details->whereIn('status',['sakit','izin'])->count() }}</div>
                <div class="text-muted text-xs font-bold uppercase tracking-wider mt-1 whitespace-nowrap">Sakit / Izin</div>
            </div>
        </div>
    </div>

    {{-- ── MAIN CONTENT GRID ── --}}
    <div class="row g-3 items-start mb-4">
        {{-- SIDEBAR LEFT --}}
        <div class="col-12 col-md-4 col-lg-3">
            {{-- Filter Rekapitulasi Card --}}
            <div class="card shadow-sm border-slate-200/60 rounded-2xl overflow-hidden bg-white mb-3 w-full">
                <div class="card-header bg-slate-50/80 border-bottom border-slate-100 px-4 py-3 fw-bold text-slate-800 flex items-center justify-between h-[52px]">
                    <span class="flex items-center gap-2 text-sm">
                        <i class="bi bi-funnel-fill text-brand-600"></i> Filter Rekapitulasi Siswa
                    </span>
                </div>
                <div class="card-body p-4">
                    <form method="GET" action="{{ route('attendance.show', $attendance) }}" id="filterForm">
                        <div class="mb-3">
                            <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-500">Periode</label>
                            <select name="period" id="periodSelect" class="form-select text-xs font-semibold text-slate-700 rounded-xl" onchange="onPeriodChange(this.value)">
                                <option value="today" {{ request('period') === 'today' ? 'selected' : '' }}>Hari Ini</option>
                                <option value="30_days" {{ (request('period', '30_days') === '30_days') ? 'selected' : '' }}>30 Hari Terakhir</option>
                                <option value="week" {{ request('period') === 'week' ? 'selected' : '' }}>Minggu Ini ({{ !empty($hasSaturday) ? 'Sen-Sab' : 'Sen-Jum' }})</option>
                                <option value="month" {{ request('period') === 'month' ? 'selected' : '' }}>Bulan Ini</option>
                                <option value="custom" {{ request('period') === 'custom' ? 'selected' : '' }}>Atur Manual...</option>
                            </select>
                        </div>
                        <div class="mb-3" id="dateRangeFields">
                            <div class="mb-2">
                                <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-500">Tanggal Mulai</label>
                                <input type="date" name="start_date" id="startDateInput" class="form-control text-xs font-semibold text-slate-700 rounded-xl" value="{{ $periodStart->toDateString() }}" onchange="onDateChange()">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-500">Tanggal Selesai</label>
                                <input type="date" name="end_date" id="endDateInput" class="form-control text-xs font-semibold text-slate-700 rounded-xl" value="{{ $periodEnd->toDateString() }}" onchange="onDateChange()">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-500">Filter Sesi / Mapel</label>
                            <select name="filter_session" class="form-select text-xs font-semibold text-slate-700 rounded-xl">
                                <option value="all" {{ $filterSession === 'all' ? 'selected' : '' }}>Semua Absensi Wali Kelas</option>
                                <option value="all_mapel" {{ $filterSession === 'all_mapel' ? 'selected' : '' }}>Semua Absensi Mapel</option>
                                @foreach($allSessionsInPeriod as $sess)
                                    <option value="{{ $sess }}" {{ $filterSession === $sess ? 'selected' : '' }}>
                                        {{ str_replace('_', ' ', ucwords(str_replace('mapel_', 'Mapel ', $sess))) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="d-grid gap-2 pt-1">
                            <button type="submit" class="btn btn-primary btn-sm px-3 py-2 text-xs font-bold rounded-xl shadow-sm border-0 d-inline-flex align-items-center justify-content-center gap-1.5">
                                <i class="bi bi-funnel"></i> Tampilkan Filter
                            </button>
                            <a href="{{ route('attendance.export.students.excel', array_merge(request()->all(), ['class_id' => $attendance->class_id, 'start_date' => $periodStart->toDateString(), 'end_date' => $periodEnd->toDateString()])) }}" target="_blank" class="btn btn-success btn-sm px-3 py-2 text-xs font-bold rounded-xl shadow-sm border-0 text-decoration-none d-inline-flex align-items-center justify-content-center gap-1.5">
                                <i class="bi bi-file-earmark-excel"></i> Export Excel
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Info Sesi Card --}}
            <div class="card shadow-sm border-slate-200/60 rounded-2xl overflow-hidden bg-white w-full">
                <div class="card-header bg-slate-50/80 border-bottom border-slate-100 px-4 py-3 fw-bold text-slate-800 flex items-center h-[52px]">
                    <span class="flex items-center gap-2 text-sm">
                        <i class="bi bi-info-circle-fill text-brand-600"></i> Info Sesi
                    </span>
                </div>
                <div class="card-body p-4">
                    <dl class="mb-0 text-xs">
                        <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                            <dt class="text-slate-500 font-semibold mb-0">Kelas</dt>
                            <dd class="fw-bold text-slate-800 mb-0">{{ $attendance->class?->name ?? '-' }}</dd>
                        </div>
                        <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                            <dt class="text-slate-500 font-semibold mb-0">Guru Pengisi</dt>
                            <dd class="fw-bold text-slate-800 mb-0">{{ $attendance->teacher?->name ?? '-' }}</dd>
                        </div>
                        <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                            <dt class="text-slate-500 font-semibold mb-0">Sesi</dt>
                            <dd class="fw-bold text-slate-800 mb-0">{{ ucfirst($attendance->session) }}</dd>
                        </div>
                        <div class="d-flex justify-content-between mb-0">
                            <dt class="text-slate-500 font-semibold mb-0">Tanggal</dt>
                            <dd class="fw-bold text-slate-800 mb-0">{{ $attendance->date->translatedFormat('d F Y') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        {{-- CONTENT RIGHT --}}
        <div class="col-12 col-md-8 col-lg-9 space-y-3">
            {{-- Tabel Absensi Sesi Ini --}}
            <div class="card shadow-sm border-slate-200/60 rounded-2xl overflow-hidden bg-white mb-3 w-full">
                <div class="card-header bg-slate-50/80 border-bottom border-slate-100 px-4 py-3 flex items-center justify-between h-[52px]">
                    <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2 mb-0">
                        <i class="bi bi-list-check text-brand-600"></i> Daftar Kehadiran Sesi Ini ({{ $attendance->date->translatedFormat('d F Y') }})
                    </h3>
                    <span class="rounded-full bg-brand-100 px-3 py-1 text-xs font-extrabold text-brand-800 border border-brand-200">
                        {{ $attendance->details->count() }} Siswa
                    </span>
                </div>
                <div class="table-responsive overflow-x-auto w-full">
                    <table class="table table-hover align-middle mb-0 text-sm w-full" style="min-width:650px">
                        <thead class="table-light">
                            <tr>
                                <th style="width:64px">No</th>
                                <th>Nama Siswa</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Jam Masuk</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attendance->details->sortBy('student.name')->values() as $i => $detail)
                                @php $student = $detail->student; @endphp
                                <tr>
                                    <td class="fw-semibold text-muted">{{ $i + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3 min-w-[240px]">
                                            <img src="{{ $student?->photo_url ?? 'https://ui-avatars.com/api/?name=Siswa&background=e2e8f0&color=475569' }}" class="h-10 w-10 rounded-full object-cover border border-slate-200" alt="Foto siswa">
                                            <div class="min-w-0">
                                                <div class="fw-bold text-slate-900 truncate">{{ $student?->name ?? 'Data siswa tidak ditemukan' }}</div>
                                                <div class="text-muted small">NIS: {{ $student?->nis ?? '-' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-status-{{ $detail->status }}">
                                            {{ $detail->status_label }}
                                        </span>
                                    </td>
                                    <td class="text-center"><code>{{ $detail->check_in ? $detail->check_in->format('H:i') : '-' }}</code></td>
                                    <td class="small text-muted">{{ $detail->notes ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-4">Belum ada detail absensi.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>


            {{-- Tabel Rekap Kehadiran Siswa Per Periode --}}
            <div class="rounded-2xl border border-slate-200/80 bg-white shadow-xs overflow-hidden">
                <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50/80 px-4 py-3 min-h-[52px]">
                    <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2 mb-0">
                        <i class="bi bi-bar-chart-line-fill text-emerald-600"></i> Rekapitulasi Periode
                        <span class="text-slate-500 font-normal text-xs">({{ $periodStart->translatedFormat('d M Y') }} - {{ $periodEnd->translatedFormat('d M Y') }})</span>
                    </h3>
                    <div class="d-flex align-items-center gap-2">
                        @if($filterSession !== 'all')
                            <span class="badge bg-indigo-100 text-indigo-800 border border-indigo-200 rounded-pill px-2.5 py-1 text-[10px] font-bold">
                                <i class="bi bi-funnel-fill me-1"></i>
                                {{ $filterSession === 'all_mapel' ? 'Semua Mapel' : str_replace('_', ' ', ucwords(str_replace('mapel_', 'Mapel ', $filterSession))) }}
                            </span>
                        @endif
                        <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-extrabold text-emerald-800 border border-emerald-200">
                            {{ $studentRecaps->count() }} Siswa
                        </span>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 text-sm">
                        <thead class="table-light">
                            <tr>
                                <th style="width:64px">No</th>
                                <th>Nama Siswa</th>
                                <th class="text-center">Hadir</th>
                                <th class="text-center">Terlambat</th>
                                <th class="text-center">Izin/Sakit</th>
                                <th class="text-center">Alfa</th>
                                <th class="text-center">Total Pertemuan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($studentRecaps as $idx => $recap)
                                <tr>
                                    <td class="fw-semibold text-muted">{{ $idx + 1 }}</td>
                                    <td>
                                        <div class="fw-bold text-slate-900">{{ $recap['student']->name }}</div>
                                        <div class="text-muted small">NIS: {{ $recap['student']->nis }}</div>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" onclick="openStudentStatusModal('{{ addslashes($recap['student']->name) }}', 'Siswa Hadir', {{ json_encode($recap['logs']->where('status', 'hadir')->values()) }})" class="badge bg-emerald-100 text-emerald-800 rounded-pill px-2.5 py-1 border-0 hover:scale-110 transition cursor-pointer" title="Klik untuk lihat rincian">
                                            {{ $recap['hadir'] }}
                                        </button>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" onclick="openStudentStatusModal('{{ addslashes($recap['student']->name) }}', 'Siswa Terlambat', {{ json_encode($recap['logs']->where('status', 'terlambat')->values()) }})" class="badge bg-amber-100 text-amber-800 rounded-pill px-2.5 py-1 border-0 hover:scale-110 transition cursor-pointer" title="Klik untuk lihat alasan terlambat">
                                            {{ $recap['terlambat'] }}
                                        </button>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" onclick="openStudentStatusModal('{{ addslashes($recap['student']->name) }}', 'Siswa Izin / Sakit', {{ json_encode($recap['logs']->whereIn('status', ['izin', 'sakit'])->values()) }})" class="badge bg-sky-100 text-sky-800 rounded-pill px-2.5 py-1 border-0 hover:scale-110 transition cursor-pointer" title="Klik untuk lihat surat / alasan izin/sakit">
                                            {{ $recap['izin_sakit'] }}
                                        </button>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" onclick="openStudentStatusModal('{{ addslashes($recap['student']->name) }}', 'Siswa Alfa', {{ json_encode($recap['logs']->where('status', 'alfa')->values()) }})" class="badge bg-rose-100 text-rose-800 rounded-pill px-2.5 py-1 border-0 hover:scale-110 transition cursor-pointer" title="Klik untuk lihat detail alfa">
                                            {{ $recap['alfa'] }}
                                        </button>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" onclick="openStudentStatusModal('{{ addslashes($recap['student']->name) }}', 'Semua Pertemuan', {{ json_encode($recap['logs']->values()) }})" class="btn btn-sm btn-outline-secondary rounded-pill px-3 font-bold" title="Lihat semua riwayat">
                                            {{ $recap['total'] }} Pertemuan
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada data rekap siswa.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL POP-UP ALASAN / KETERANGAN PRESENSI SISWA --}}
    <div id="studentLogsModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/80 backdrop-blur-sm p-3 sm:p-6 flex items-start justify-center pt-6 sm:pt-10">
        <div class="w-full max-w-4xl overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-slate-200/80 max-h-[85vh] flex flex-col my-auto">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 sm:px-6 py-3.5 bg-slate-50/80 shrink-0">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-brand-600 text-white flex items-center justify-center font-bold text-base shadow-2xs">
                        <i class="bi bi-file-earmark-person-fill"></i>
                    </div>
                    <div>
                        <h5 id="slmTitle" class="text-sm sm:text-base font-black text-slate-900 mb-0">Detail Riwayat Presensi</h5>
                        <p class="text-[11px] text-slate-500 font-semibold mb-0">Rincian tanggal, sesi mapel, status, jam masuk, dan catatan alasan siswa</p>
                    </div>
                </div>
                <button type="button" onclick="closeStudentLogsModal()" class="rounded-xl p-2 text-slate-400 hover:bg-slate-200/60 hover:text-slate-800 transition-all">
                    <i class="bi bi-x-lg text-base"></i>
                </button>
            </div>
            <div id="slmBody" class="p-4 sm:p-6 overflow-y-auto">
                {{-- Dynamic Table Contents --}}
            </div>
            <div class="border-t border-slate-100 px-6 py-3 bg-slate-50/50 flex justify-end shrink-0">
                <button type="button" onclick="closeStudentLogsModal()" class="btn btn-secondary btn-sm px-4 py-2 text-xs font-bold rounded-xl shadow-2xs">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function openStudentStatusModal(studentName, category, logs) {
            document.getElementById('slmTitle').textContent = category + ' — ' + studentName;
            const container = document.getElementById('slmBody');
            container.innerHTML = '';

            if (!logs || logs.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-10 space-y-2">
                        <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-xl">
                            <i class="bi bi-folder-x"></i>
                        </div>
                        <p class="text-slate-500 text-xs font-bold">Tidak ada riwayat presensi untuk kategori ini.</p>
                    </div>
                `;
            } else {
                let sortedLogs = [...logs];
                sortedLogs.sort((a, b) => {
                    const dateA = a.raw_date || a.date || '';
                    const dateB = b.raw_date || b.date || '';
                    const comp = dateA.localeCompare(dateB); // ASCENDING: Tanggal 01 Juli (Minggu Ke-1) di ATAS -> Tanggal 31 Juli di BAWAH
                    if (comp !== 0) return comp;
                    const wA = a.sess_weight ?? 0;
                    const wB = b.sess_weight ?? 0;
                    return wA - wB; // Sesi Jam Mapel 1 -> Mapel 2 -> Mapel 3 -> Mapel 4 per hari
                });

                let tableRowsHtml = '';
                let currentWeek = null;

                sortedLogs.forEach((log, index) => {
                    if (log.week_num && log.week_num !== currentWeek) {
                        currentWeek = log.week_num;
                        tableRowsHtml += `
                            <tr class="bg-slate-200/90 border-y-2 border-brand-500">
                                <td colspan="6" class="py-2.5 px-4 bg-slate-100">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2.5">
                                            <span class="bg-brand-600 text-white font-extrabold px-3 py-1 rounded-lg text-xs tracking-wider shadow-2xs">
                                                <i class="bi bi-calendar3-week me-1"></i> MINGGU KE-${currentWeek}
                                            </span>
                                            <span class="text-slate-800 font-extrabold text-xs">Urutan Presensi Pelajaran</span>
                                        </div>
                                        <span class="text-[11px] font-bold text-brand-700 bg-brand-50 border border-brand-200 px-2.5 py-0.5 rounded-md">Pembatas Mingguan</span>
                                    </div>
                                </td>
                            </tr>
                        `;
                    }

                    let rowBg = 'hover:bg-slate-50';
                    let badgeHtml = '';

                    if (log.status === 'hadir') {
                        rowBg = 'bg-white hover:bg-emerald-50/50';
                        badgeHtml = '<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-600 text-white font-extrabold text-xs shadow-2xs"><i class="bi bi-check-circle-fill"></i> Hadir</span>';
                    } else if (log.status === 'terlambat') {
                        rowBg = 'bg-amber-50/90 hover:bg-amber-100/90 border-l-4 border-l-amber-500 font-semibold';
                        badgeHtml = '<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-500 text-white font-black text-xs shadow-xs"><i class="bi bi-clock-fill"></i> Terlambat</span>';
                    } else if (log.status === 'sakit') {
                        rowBg = 'bg-amber-50/90 hover:bg-amber-100/90 border-l-4 border-l-amber-500 font-semibold';
                        badgeHtml = '<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-600 text-white font-black text-xs shadow-xs"><i class="bi bi-hospital-fill"></i> Sakit</span>';
                    } else if (log.status === 'izin') {
                        rowBg = 'bg-sky-50/90 hover:bg-sky-100/90 border-l-4 border-l-sky-500 font-semibold';
                        badgeHtml = '<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-sky-600 text-white font-black text-xs shadow-xs"><i class="bi bi-file-earmark-text-fill"></i> Izin</span>';
                    } else if (log.status === 'alfa') {
                        rowBg = 'bg-rose-50/95 hover:bg-rose-100/95 border-l-4 border-l-rose-600 font-bold';
                        badgeHtml = '<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-600 text-white font-black text-xs shadow-xs ring-2 ring-rose-300/60"><i class="bi bi-x-circle-fill"></i> Alfa (Tanpa Keterangan)</span>';
                    } else {
                        badgeHtml = `<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-600 text-white font-bold text-xs">${log.status_label || log.status || '-'}</span>`;
                    }

                    const displayDate = log.day_date || log.date || '-';
                    const sessFormatted = (log.session || '').replace('mapel_', 'Jam Mapel ').replace('_', ' ').toUpperCase();
                    const noteText = log.notes ? log.notes : 'Tidak ada catatan';
                    const checkInText = log.check_in ? log.check_in : '-';

                    tableRowsHtml += `
                        <tr class="${rowBg} transition-colors border-b border-slate-100">
                            <td class="py-3 px-3 text-center text-slate-400 font-mono text-xs font-bold">${index + 1}</td>
                            <td class="py-3 px-3 font-extrabold text-slate-900 text-xs whitespace-nowrap">
                                <i class="bi bi-calendar3 text-brand-600 me-1.5"></i>${displayDate}
                            </td>
                            <td class="py-3 px-3">
                                <span class="badge bg-slate-200 text-slate-800 text-[11px] font-black uppercase px-2.5 py-1 rounded-lg">${sessFormatted}</span>
                            </td>
                            <td class="py-3 px-3 text-center font-mono font-bold text-slate-800 text-xs whitespace-nowrap">
                                ${checkInText}
                            </td>
                            <td class="py-3 px-3 text-center whitespace-nowrap">
                                ${badgeHtml}
                            </td>
                            <td class="py-3 px-3 text-slate-700 text-xs italic">
                                💬 ${noteText}
                            </td>
                        </tr>
                    `;
                });

                container.innerHTML = `
                    <div class="overflow-hidden rounded-2xl border border-slate-200 shadow-xs">
                        <div class="max-h-[60vh] overflow-y-auto">
                            <table class="w-full text-left text-xs align-middle">
                                <thead class="bg-slate-100 text-slate-700 font-black uppercase tracking-wider text-[11px] sticky top-0 z-10 border-b border-slate-200 shadow-2xs">
                                    <tr>
                                        <th class="py-3 px-3 text-center w-12 bg-slate-100">No</th>
                                        <th class="py-3 px-3 bg-slate-100">Tanggal & Hari</th>
                                        <th class="py-3 px-3 bg-slate-100">Sesi / Jam Mapel</th>
                                        <th class="py-3 px-3 text-center bg-slate-100">Waktu Masuk</th>
                                        <th class="py-3 px-3 text-center bg-slate-100">Status</th>
                                        <th class="py-3 px-3 bg-slate-100">Alasan / Catatan KBM</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${tableRowsHtml}
                                </tbody>
                            </table>
                        </div>
                    </div>
                `;
            }

            document.getElementById('studentLogsModal').classList.remove('hidden');
        }

        function closeStudentLogsModal() {
            document.getElementById('studentLogsModal').classList.add('hidden');
        }

        // ── Auto-update date inputs saat pilih periode ──
        function getLocalDateString(date) {
            const y = date.getFullYear();
            const m = String(date.getMonth() + 1).padStart(2, '0');
            const d = String(date.getDate()).padStart(2, '0');
            return `${y}-${m}-${d}`;
        }

        function onPeriodChange(period) {
            const startInput = document.getElementById('startDateInput');
            const endInput   = document.getElementById('endDateInput');
            const today = new Date();

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
                // Senin minggu ini
                const dayOfWeek = today.getDay(); // 0=Sun, 1=Mon, ...6=Sat
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
            // 'custom': biarkan user edit manual, tidak perlu ubah tanggal
        }

        function onDateChange() {
            // Kalau user atur tanggal manual, switch period ke 'custom'
            const periodSelect = document.getElementById('periodSelect');
            if (periodSelect.value !== 'custom') {
                periodSelect.value = 'custom';
            }
        }
    </script>
    @endpush
@endsection
