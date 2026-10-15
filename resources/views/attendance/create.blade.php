{{-- resources/views/attendance/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail & Input Absensi Mapel')
@section('page-title', 'Detail Presensi Mata Pelajaran')

@section('content')

{{-- ── 1. ACTIVE CLASS BANNER & SCHEDULE TOGGLE ── --}}
<div class="mb-4 rounded-2xl bg-white p-4 sm:p-5 shadow-sm border border-slate-200">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div>
            <div class="flex flex-wrap items-center gap-2 mb-1">
                <span class="text-xs font-extrabold uppercase tracking-wider text-brand-600 bg-brand-50 px-2.5 py-1 rounded-lg border border-brand-200">
                    <i class="bi bi-journal-check me-1"></i> Presensi Kelas
                </span>
                @if(!empty($fullDateFormatted))
                    <span class="text-xs font-bold text-slate-600 bg-slate-100 px-2.5 py-1 rounded-lg border border-slate-200">
                        <i class="bi bi-calendar3 me-1"></i> {{ $fullDateFormatted }}
                    </span>
                @endif
            </div>

            <h4 class="text-xl sm:text-2xl font-black text-slate-900 mb-1 flex flex-wrap items-center gap-2">
                <span>{{ $classes->firstWhere('id', $classId)?->name ?? 'Pilih Kelas' }}</span>
                @if(!empty($selectedSubjectInfo) && !empty($selectedSubjectInfo['subject']))
                    <span class="text-base font-bold text-brand-700 bg-brand-50 px-3 py-0.5 rounded-full border border-brand-200">
                        📚 {{ $selectedSubjectInfo['subject'] }}
                    </span>
                    @if(!empty($selectedSubjectInfo['slot_num']))
                    <span class="text-xs font-extrabold text-purple-700 bg-purple-50 px-2.5 py-1 rounded-full border border-purple-200">
                        ⏰ Jam ke-{{ $selectedSubjectInfo['slot_num'] }} @if(!empty($selectedSubjectInfo['time_str']))({{ $selectedSubjectInfo['time_str'] }})@endif
                    </span>
                    @endif
                @elseif(!empty($selectedSubjectName))
                    <span class="text-base font-bold text-brand-700 bg-brand-50 px-3 py-0.5 rounded-full border border-brand-200">
                        📚 {{ $selectedSubjectName }}
                    </span>
                @endif
            </h4>
            <p class="text-xs text-slate-500 font-medium mb-0">Klik tombol 🟢 <strong>HADIR</strong> atau 🔴 <strong>ABSEN</strong>. Perubahan tersimpan **secara instan**.</p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('attendance.scan-qr') }}" 
               class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs px-3.5 py-2.5 shadow-sm transition-all shrink-0">
                <i class="bi bi-camera-fill text-sm"></i>
                <span>Scan QR Siswa</span>
            </a>
            <button type="button" 
                    onclick="toggleScheduleBlock()" 
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs px-4 py-2.5 border border-slate-300 transition-all shrink-0">
                <i class="bi bi-calendar-week text-brand-600 text-sm"></i>
                <span>⚡ Ganti Jadwal / Filter Kelas</span>
                <i class="bi bi-chevron-down text-xs transition-transform" id="sched-toggle-icon"></i>
            </button>
        </div>
    </div>
</div>

{{-- ── 2. COLLAPSIBLE SCHEDULE SELECTOR & MANUAL FILTER ── --}}
<div id="schedule-selector-block" class="hidden mb-5 space-y-4">
    {{-- JADWAL MENGAJAR GURU QUICK CARDS --}}
    @if(!empty($mySchedules) && $mySchedules->isNotEmpty())
    <div class="rounded-2xl bg-white p-4 shadow-sm border border-slate-200">
        <div class="flex items-center justify-between gap-2 mb-3">
            <div class="flex items-center gap-2">
                <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-brand-50 text-brand-600 font-extrabold text-sm border border-brand-200">
                    ⚡
                </div>
                <h6 class="text-xs sm:text-sm font-extrabold uppercase tracking-wider text-slate-800 mb-0">
                    Jadwal Mengajar {{ auth()->user()->isGuru() ? 'Saya' : 'Sekolah' }} (Klik 1x Langsung Buka Presensi)
                </h6>
            </div>
            <span class="text-[11px] font-bold text-slate-400">Pilih jadwal di bawah tanpa pilih dropdown</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
            @foreach($mySchedules->filter(fn($s) => $s->class != null) as $mSched)
            @php
                $dayNames = ['', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
                $dName = $dayNames[$mSched->day_of_week] ?? '';
                $timeStr = ($mSched->start_time && $mSched->end_time) ? $mSched->start_time->format('H:i') . ' - ' . $mSched->end_time->format('H:i') : '';
                $isCurrentActive = ($classId == $mSched->class_id && ($session == "sched_{$mSched->id}" || $selectedSubjectName == $mSched->subject));
            @endphp
            <a href="{{ route('attendance.create', ['class_id' => $mSched->class_id, 'session' => 'sched_' . $mSched->id, 'period' => 'today']) }}" 
               class="group relative flex flex-col justify-between rounded-xl p-3.5 text-left transition-all border {{ $isCurrentActive ? 'bg-brand-600 border-brand-700 text-white shadow-md ring-2 ring-brand-500' : 'bg-slate-50 hover:bg-brand-50/60 border-slate-200 hover:border-brand-300 text-slate-800 shadow-2xs' }}">
                <div>
                    <div class="flex items-center justify-between gap-1 mb-1.5">
                        <span class="inline-flex items-center gap-1 rounded-md px-2.5 py-0.5 text-[10px] font-black uppercase tracking-wider {{ $isCurrentActive ? 'bg-white/20 text-white' : 'bg-slate-200/80 text-slate-700' }}">
                            📅 {{ $dName }}
                        </span>
                        <span class="text-[11px] font-mono font-bold {{ $isCurrentActive ? 'text-brand-100' : 'text-slate-500' }}">
                            {{ $timeStr }}
                        </span>
                    </div>
                    <h6 class="text-base font-black truncate mb-1 leading-snug {{ $isCurrentActive ? 'text-white' : 'text-slate-900 group-hover:text-brand-700' }}">
                        {{ $mSched->subject }}
                    </h6>
                    <div class="text-xs font-bold truncate flex items-center gap-1.5 {{ $isCurrentActive ? 'text-brand-100' : 'text-slate-600' }}">
                        <i class="bi bi-building text-xs"></i> Kelas: {{ $mSched->class?->name ?? 'Kelas' }}
                        @if(!auth()->user()->isGuru() && $mSched->teacher)
                            <span class="text-slate-400 font-normal">({{ $mSched->teacher->name }})</span>
                        @endif
                    </div>
                </div>

                <div class="mt-3 flex items-center justify-between text-[11px] font-extrabold border-t pt-2 transition-colors {{ $isCurrentActive ? 'border-white/20 text-white' : 'border-slate-200 text-brand-600 group-hover:text-brand-700' }}">
                    <span>{{ $isCurrentActive ? '🟢 Sedang Dibuka' : '🖐️ Buka Presensi Ini' }}</span>
                    <i class="bi bi-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- FILTER FORM DROPDOWN MANUAL --}}
    <div class="rounded-2xl bg-white p-4 shadow-sm border border-slate-200">
        <div class="text-xs font-extrabold uppercase tracking-wider text-slate-800 mb-3 flex items-center gap-1.5">
            <i class="bi bi-funnel-fill text-brand-600"></i> Filter Custom Kelas, Tanggal & Periode
        </div>
        <form method="GET" action="{{ route('attendance.create') }}" id="filter-form" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-end">
            <div class="sm:col-span-1 lg:col-span-2">
                <label class="block mb-1 text-xs font-bold uppercase tracking-wider text-slate-600">Periode</label>
                <select name="period" id="input-period" class="form-select text-xs font-semibold text-slate-700 rounded-xl" onchange="onPeriodChange(this.value)">
                    <option value="today" {{ ($period ?? 'today') == 'today' ? 'selected' : '' }}>Hari Ini</option>
                    <option value="week" {{ ($period ?? '') == 'week' ? 'selected' : '' }}>Minggu Ini ({{ !empty($hasSaturday) ? 'Sen-Sab' : 'Sen-Jum' }})</option>
                    <option value="30_days" {{ ($period ?? '') == '30_days' ? 'selected' : '' }}>30 Hari Terakhir</option>
                    <option value="month" {{ ($period ?? '') == 'month' ? 'selected' : '' }}>Bulan Ini</option>
                    <option value="custom" {{ ($period ?? '') == 'custom' ? 'selected' : '' }}>Atur Manual...</option>
                </select>
            </div>

            <div class="sm:col-span-1 lg:col-span-2">
                <label class="block mb-1 text-xs font-bold uppercase tracking-wider text-slate-600">Tanggal Mulai</label>
                <input type="date" name="start_date" id="input-start-date" class="form-control text-xs font-semibold text-slate-700 rounded-xl" value="{{ $startDate }}" max="{{ today()->toDateString() }}" onchange="onDateChange()">
            </div>

            <div class="sm:col-span-1 lg:col-span-2">
                <label class="block mb-1 text-xs font-bold uppercase tracking-wider text-slate-600">Tanggal Selesai</label>
                <input type="date" name="end_date" id="input-end-date" class="form-control text-xs font-semibold text-slate-700 rounded-xl" value="{{ $endDate }}" max="{{ today()->toDateString() }}" onchange="onDateChange()">
            </div>

            <div class="sm:col-span-2 lg:col-span-3">
                <label class="block mb-1 text-xs font-bold uppercase tracking-wider text-slate-600">Kelas</label>
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
                <label class="block mb-1 text-xs font-bold uppercase tracking-wider text-slate-600">Sesi Jam Mapel @if(empty($isRangeMode) && !empty($dayName))<span class="text-brand-600 font-extrabold">({{ $dayName }})</span>@endif</label>
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
</div>

@if($students->isNotEmpty())

@if($isRangeMode || ($session ?? 'all') === 'all')
{{-- ── 3. MODE REKAPITULASI PRESENSI GABUNGAN / RENTANG TANGGAL ── --}}
<div class="space-y-4">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 bg-brand-50/60 p-3.5 rounded-2xl border border-brand-200">
        <div>
            <h6 class="mb-0 font-bold text-brand-900 flex items-center gap-2">
                <i class="bi bi-calendar-range text-brand-600"></i>
                Rekapitulasi Presensi Periode: {{ \Carbon\Carbon::parse($startDate)->translatedFormat('l, d M Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->translatedFormat('l, d M Y') }}
            </h6>
            <p class="text-xs text-brand-700 mb-0 mt-0.5">Menampilkan total akumulasi kehadiran seluruh siswa kelas {{ $classes->firstWhere('id', $classId)?->name }}.</p>
        </div>
        <span class="badge bg-brand-600 text-white px-3 py-1.5 rounded-full text-xs font-bold self-start sm:self-auto">{{ $students->count() }} Siswa</span>
    </div>

    <div class="card shadow-sm border border-slate-200 overflow-hidden rounded-2xl bg-white">
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
                        <th class="text-center" style="width: 170px">Aksi</th>
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
                        <td class="text-center align-middle font-semibold text-slate-400">{{ $i + 1 }}</td>
                        <td class="align-middle">
                            <div class="font-bold text-slate-800">{{ $student->name }}</div>
                            <div class="text-xs font-mono text-slate-500">NISN: {{ $student->nis }}</div>
                        </td>
                        <td class="text-center align-middle font-extrabold text-emerald-600 text-base">{{ $recap['hadir'] }}</td>
                        <td class="text-center align-middle font-extrabold text-rose-600 text-base">{{ $recap['alfa'] }}</td>
                        <td class="text-center align-middle font-bold text-amber-600">{{ $recap['sakit_izin'] }}</td>
                        <td class="text-center align-middle font-bold text-amber-500">{{ $recap['terlambat'] }}</td>
                        <td class="text-center align-middle font-bold text-slate-700">{{ $recap['total'] }}</td>
                        <td class="text-center align-middle">
                            <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                <button type="button" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-amber-50 text-amber-700 hover:bg-amber-100 hover:text-amber-800 transition font-bold text-xs border border-amber-200 shadow-2xs" 
                                        onclick="openEditFromRecap({{ $student->id }}, '{{ addslashes($student->name) }}', {{ json_encode($recap['logs']) }})">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </button>
                                <button type="button" class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl bg-slate-100 text-slate-700 hover:bg-brand-50 hover:text-brand-700 transition font-bold text-xs border border-slate-200" 
                                        onclick="openRangeStudentModal('{{ addslashes($student->name) }}', {{ $student->id }}, {{ json_encode($recap['logs']) }})">
                                    <i class="bi bi-eye"></i> Detail Log
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@else
{{-- ── 4. MODE SATU TANGGAL: CLEAN SEPARATED CONTROL BARS & ATTENDANCE ── --}}
<div class="space-y-4 mb-5">
    {{-- CONTROL BAR A: SUMMARY COUNTERS & BULK ACTIONS --}}
    <div class="rounded-2xl bg-white p-3.5 sm:p-4 shadow-sm border border-slate-200 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
        {{-- SUMMARY COUNTERS --}}
        <div class="flex flex-wrap items-center gap-2">
            <div class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-50 px-3 py-1.5 border border-emerald-200 text-xs">
                <span>🟢</span>
                <span class="font-bold text-emerald-900 uppercase">Hadir:</span>
                <span class="font-black text-emerald-700 font-mono text-sm" id="sum-hadir">0</span>
            </div>

            <div class="inline-flex items-center gap-1.5 rounded-xl bg-rose-50 px-3 py-1.5 border border-rose-200 text-xs">
                <span>🔴</span>
                <span class="font-bold text-rose-900 uppercase">Tidak Hadir:</span>
                <span class="font-black text-rose-700 font-mono text-sm" id="sum-alfa">0</span>
            </div>

            <div class="inline-flex items-center gap-1.5 rounded-xl bg-amber-50 px-3 py-1.5 border border-amber-200 text-xs">
                <span>🤒</span>
                <span class="font-bold text-amber-900 uppercase">Sakit/Izin:</span>
                <span class="font-black text-amber-700 font-mono text-sm" id="sum-sakit-izin">0</span>
            </div>

            <div class="inline-flex items-center gap-1.5 rounded-xl bg-slate-100 px-3 py-1.5 border border-slate-200 text-xs">
                <span class="font-bold text-slate-600 uppercase">Total:</span>
                <span class="font-black text-slate-900 font-mono text-sm">{{ $students->count() }} Siswa</span>
            </div>
        </div>

        {{-- BULK ACTIONS --}}
        <div class="flex items-center gap-2 shrink-0">
            <button type="button" class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 text-white shadow-sm hover:bg-emerald-700 active:scale-95 transition-all text-xs font-black" onclick="setAllQuick('hadir')">
                <i class="bi bi-check-all text-base"></i> Semuanya Hadir
            </button>
            <button type="button" class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl bg-rose-600 text-white shadow-sm hover:bg-rose-700 active:scale-95 transition-all text-xs font-black" onclick="setAllQuick('alfa')">
                <i class="bi bi-x-circle text-base"></i> Semuanya Absen
            </button>
        </div>
    </div>

    {{-- CONTROL BAR B: INSTANT SEARCH & VIEW MODE SWITCHER --}}
    <div class="rounded-2xl bg-white p-3 shadow-sm border border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-3">
        {{-- INSTANT SEARCH INPUT --}}
        <div class="relative w-full sm:w-80">
            <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input type="text" id="student-search-input" oninput="filterStudents(this.value)" placeholder="Cari nama atau NISN siswa..." class="w-full rounded-xl border border-slate-300 bg-slate-50 py-2 pl-9 pr-3 text-xs font-semibold text-slate-800 shadow-2xs focus:bg-white focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 transition-all">
        </div>

        {{-- VIEW MODE SWITCHER --}}
        <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl border border-slate-200 w-full sm:w-auto justify-center">
            <button type="button" id="btn-view-grid" onclick="switchViewMode('grid')" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-extrabold transition-all bg-brand-600 text-white shadow-xs">
                <i class="bi bi-grid-fill"></i> Mode Kartu (Grid)
            </button>
            <button type="button" id="btn-view-table" onclick="switchViewMode('table')" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-extrabold transition-all text-slate-600 hover:text-slate-900 hover:bg-white">
                <i class="bi bi-table"></i> Mode Tabel (List)
            </button>
        </div>
    </div>

    {{-- ATTENDANCE CONTAINER CARD --}}
    <div class="card shadow-sm border border-slate-200 overflow-hidden rounded-2xl bg-white">
        {{-- VIEW MODE 1: GRID VIEW (KARTU SISWA EFFISIEN) --}}
        <div id="attendance-grid-container" class="p-4 bg-slate-50/50">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3.5">
                @foreach($students as $i => $student)
                @php
                    $studentExisting = $existing[$student->id] ?? null;
                    $savedStatus = $studentExisting['status'] ?? null;
                    $isHomeroomLock = !empty($studentExisting['from_homeroom']) || in_array($savedStatus, ['sakit', 'izin'], true);
                @endphp
                <div class="student-att-card rounded-2xl border-2 p-3.5 transition-all flex flex-col justify-between {{ $savedStatus === 'hadir' ? 'border-emerald-500 bg-emerald-50/40 shadow-sm' : ($savedStatus === 'alfa' ? 'border-rose-500 bg-rose-50/40 shadow-sm' : ($isHomeroomLock ? 'border-amber-300 bg-amber-50/40' : 'border-slate-200 bg-white hover:border-slate-300 shadow-2xs')) }}" 
                     id="card-student-{{ $student->id }}" 
                     data-student-id="{{ $student->id }}" 
                     data-student-name="{{ strtolower($student->name) }}" 
                     data-student-nis="{{ strtolower($student->nis) }}">
                    
                    <div>
                        {{-- CARD HEADER: AVATAR, NAMA & STATUS BADGE --}}
                        <div class="flex items-center justify-between gap-2 mb-2.5">
                            <div class="flex items-center gap-2.5 overflow-hidden">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-600 text-white font-black text-sm shadow-xs">
                                    {{ strtoupper(substr($student->name, 0, 1)) }}
                                </div>
                                <div class="truncate">
                                    <h6 class="font-extrabold text-slate-900 text-sm leading-snug truncate mb-0">{{ $student->name }}</h6>
                                    <span class="text-[11px] font-mono text-slate-500 font-semibold">NISN: {{ $student->nis }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- STATUS INDICATOR --}}
                        <div class="mb-3 text-center" id="card-status-ind-{{ $student->id }}">
                            @if($isHomeroomLock)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold bg-amber-100 text-amber-900 border border-amber-300 w-full justify-center">
                                    🤒 {{ strtoupper($savedStatus) }} (Wali Kelas)
                                </span>
                            @elseif($savedStatus === 'hadir')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-black bg-emerald-600 text-white shadow-xs w-full justify-center">
                                    🟢 HADIR
                                </span>
                            @elseif($savedStatus === 'alfa')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-black bg-rose-600 text-white shadow-xs w-full justify-center">
                                    🔴 TIDAK HADIR
                                </span>
                            @elseif($savedStatus === 'sakit')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-black bg-orange-500 text-white shadow-xs w-full justify-center">
                                    🤒 SAKIT
                                </span>
                            @elseif($savedStatus === 'izin')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-black bg-sky-500 text-white shadow-xs w-full justify-center">
                                    📄 IZIN
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold bg-slate-100 text-slate-500 border border-slate-200 w-full justify-center">
                                    ⚪ Belum Diabsen
                                </span>
                            @endif
                        </div>

                        {{-- ACTION BUTTONS --}}
                        @if(!$isHomeroomLock)
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button" 
                                    class="btn-card-save flex items-center justify-center gap-1 rounded-xl py-2.5 px-2 text-xs font-black transition-all active:scale-95 min-h-[44px] {{ $savedStatus === 'hadir' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30 ring-2 ring-emerald-600' : 'bg-emerald-50 text-emerald-800 border border-emerald-200 hover:bg-emerald-100' }}"
                                    onclick="saveQuick('{{ $student->id }}', 'hadir', this)">
                                🟢 HADIR
                            </button>
                            <button type="button" 
                                    class="btn-card-save flex items-center justify-center gap-1 rounded-xl py-2.5 px-2 text-xs font-black transition-all active:scale-95 min-h-[44px] {{ $savedStatus === 'alfa' ? 'bg-rose-600 text-white shadow-md shadow-rose-600/30 ring-2 ring-rose-600' : 'bg-rose-50 text-rose-800 border border-rose-200 hover:bg-rose-100' }}"
                                    onclick="saveQuick('{{ $student->id }}', 'alfa', this)">
                                🔴 ABSEN
                            </button>
                        </div>
                        @endif

                        {{-- TOMBOL EDIT untuk semua siswa yang sudah memiliki status --}}
                        @if($savedStatus)
                        <div class="mt-2">
                            <button type="button"
                                    onclick="openEditModal('{{ $student->id }}', '{{ addslashes($student->name) }}', '{{ $savedStatus }}', '{{ addslashes($studentExisting['notes'] ?? '') }}')"
                                    class="w-full flex items-center justify-center gap-1.5 rounded-xl py-1.5 px-2 text-xs font-bold text-slate-600 bg-slate-100 border border-slate-200 hover:bg-indigo-50 hover:text-indigo-700 hover:border-indigo-300 transition-all">
                                <i class="bi bi-pencil-square text-indigo-500"></i> Edit Keterangan
                            </button>
                        </div>
                        @endif
                    </div>

                    {{-- CATATAN INDIVIDUAL SISWA --}}
                    @if(!$isHomeroomLock)
                    <div class="mt-3 pt-2.5 border-t border-slate-200/80">
                        <input type="text" 
                               id="card-notes-{{ $student->id }}" 
                               class="form-control text-[11px] rounded-xl border-slate-200 bg-white focus:border-brand-500 transition-all py-1.5 px-2.5 w-full" 
                               placeholder="Catatan khusus (opsional)..." 
                               value="{{ $studentExisting['notes'] ?? '' }}"
                               onchange="saveQuickNotes('{{ $student->id }}')">
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>

        {{-- VIEW MODE 2: TABLE VIEW (TABEL LIST RAPI) --}}
        <div id="attendance-table-container" class="card-body p-0 hidden">
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
                        <tr class="att-row" data-student-id="{{ $student->id }}" data-student-name="{{ strtolower($student->name) }}" data-student-nis="{{ strtolower($student->nis) }}">
                            <td class="text-center align-middle font-semibold text-slate-400" style="font-size: 13px">{{ $i + 1 }}</td>
                            <td class="align-middle">
                                <div class="fw-semibold text-slate-800" style="font-size: 14px">{{ $student->name }}</div>
                                <div style="font-size: 11px; color: #64748b; font-family: 'JetBrains Mono', monospace" class="mt-0.5">
                                    NISN: {{ $student->nis }}
                                </div>
                            </td>
                            <td class="align-middle text-center">
                                @if($isHomeroomLock)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-100 text-amber-800 font-bold border border-amber-300 shadow-sm text-xs">
                                        🤒 {{ strtoupper($savedStatus) }} (Wali Kelas)
                                    </span>
                                @else
                                    <div class="btn-group shadow-xs rounded-xl overflow-hidden" role="group">
                                        <button type="button" 
                                                class="btn btn-quick-save px-3.5 py-2.5 min-h-[44px] text-xs font-extrabold {{ $savedStatus === 'hadir' ? 'btn-success shadow-sm' : 'btn-outline-success' }}"
                                                onclick="saveQuick('{{ $student->id }}', 'hadir', this)">
                                            🟢 Hadir
                                        </button>
                                        <button type="button" 
                                                class="btn btn-quick-save px-3.5 py-2.5 min-h-[44px] text-xs font-extrabold {{ $savedStatus === 'alfa' ? 'btn-danger shadow-sm' : 'btn-outline-danger' }}"
                                                onclick="saveQuick('{{ $student->id }}', 'alfa', this)">
                                            🔴 Tidak Hadir
                                        </button>
                                    </div>
                                @endif
                                @if($savedStatus)
                                <button type="button"
                                        onclick="openEditModal('{{ $student->id }}', '{{ addslashes($student->name) }}', '{{ $savedStatus }}', '{{ addslashes($studentExisting['notes'] ?? '') }}')"
                                        class="mt-1 inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-bold rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-200 hover:bg-indigo-100 transition">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </button>
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
        <div class="card shadow-sm border-t border-slate-200/80 rounded-b-2xl overflow-hidden bg-white">
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

{{-- FLOATING TOAST NOTIFICATION CONTAINER --}}
<div id="toast-container" class="fixed bottom-5 right-5 z-50 flex flex-col gap-2 pointer-events-none"></div>

{{-- ── MODAL EDIT ABSENSI SISWA ── --}}
<div id="editAttModal" onclick="if(event.target===this)closeEditModal()" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/80 backdrop-blur-sm p-4 flex items-center justify-center">
    <div class="w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-slate-200">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 bg-slate-50">
            <div class="flex items-center gap-2.5">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600">
                    <i class="bi bi-pencil-square text-lg"></i>
                </div>
                <div>
                    <h5 class="text-sm font-extrabold text-slate-900 mb-0">Edit Keterangan Absen</h5>
                    <p id="editModalStudentName" class="text-xs text-slate-500 mb-0">–</p>
                    <p id="editModalSubInfo" class="text-[11px] font-semibold text-brand-600 mb-0 hidden"></p>
                </div>
            </div>
            <button type="button" onclick="closeEditModal()" class="rounded-xl p-1.5 text-slate-400 hover:bg-slate-200 hover:text-slate-700 transition">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="p-5 space-y-4">
            <input type="hidden" id="editModalStudentId">
            {{-- STATUS PICKER --}}
            <div>
                <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-600 mb-2">Status Kehadiran</label>
                <div class="grid grid-cols-2 gap-2" id="editStatusPicker">
                    <button type="button" data-status="hadir" onclick="selectEditStatus('hadir')"
                        class="edit-status-btn flex items-center justify-center gap-1.5 rounded-xl py-3 text-xs font-black border-2 transition-all border-slate-200 bg-white text-slate-700 hover:border-emerald-400 hover:bg-emerald-50">
                        🟢 Hadir
                    </button>
                    <button type="button" data-status="alfa" onclick="selectEditStatus('alfa')"
                        class="edit-status-btn flex items-center justify-center gap-1.5 rounded-xl py-3 text-xs font-black border-2 transition-all border-slate-200 bg-white text-slate-700 hover:border-rose-400 hover:bg-rose-50">
                        🔴 Tidak Hadir
                    </button>
                    <button type="button" data-status="sakit" onclick="selectEditStatus('sakit')"
                        class="edit-status-btn flex items-center justify-center gap-1.5 rounded-xl py-3 text-xs font-black border-2 transition-all border-slate-200 bg-white text-slate-700 hover:border-orange-400 hover:bg-orange-50">
                        🤒 Sakit
                    </button>
                    <button type="button" data-status="izin" onclick="selectEditStatus('izin')"
                        class="edit-status-btn flex items-center justify-center gap-1.5 rounded-xl py-3 text-xs font-black border-2 transition-all border-slate-200 bg-white text-slate-700 hover:border-sky-400 hover:bg-sky-50">
                        📄 Izin
                    </button>
                    <button type="button" data-status="terlambat" onclick="selectEditStatus('terlambat')"
                        class="edit-status-btn col-span-2 flex items-center justify-center gap-1.5 rounded-xl py-3 text-xs font-black border-2 transition-all border-slate-200 bg-white text-slate-700 hover:border-amber-400 hover:bg-amber-50">
                        ⏰ Terlambat
                    </button>
                </div>
            </div>
            {{-- CATATAN --}}
            <div>
                <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-600 mb-1.5" for="editModalNotes">Catatan / Keterangan <span class="text-slate-400 font-normal normal-case">(opsional)</span></label>
                <input type="text" id="editModalNotes" placeholder="Contoh: Izin dokter gigi, surat dari orang tua..."
                    class="form-control text-xs rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 transition-all">
            </div>
            {{-- ALERT --}}
            <div id="editModalAlert" class="hidden rounded-xl px-4 py-2.5 text-xs font-medium"></div>
        </div>
        <div class="flex items-center justify-end gap-2.5 border-t border-slate-100 px-5 py-3.5 bg-slate-50">
            <button type="button" onclick="closeEditModal()" class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-100 transition">Batal</button>
            <button type="button" id="btnSaveEdit" onclick="submitEditAttendance()"
                class="inline-flex items-center gap-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 px-5 py-2 text-xs font-extrabold text-white shadow-sm transition active:scale-95">
                <i class="bi bi-check-circle-fill"></i> Simpan Perubahan
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const CSRF_TOKEN = '{{ csrf_token() }}';
const QUICK_SAVE_URL = '{{ route("attendance.quick-save") }}';
const UPDATE_DETAIL_URL = '{{ route("attendance.update-detail") }}';
const DATE_VAL = '{{ $startDate }}';
const CLASS_ID_VAL = '{{ $classId }}';
const SESSION_VAL = '{{ $session }}';

function toggleScheduleBlock() {
    const block = document.getElementById('schedule-selector-block');
    const icon = document.getElementById('sched-toggle-icon');
    if (!block) return;
    
    if (block.classList.contains('hidden')) {
        block.classList.remove('hidden');
        if (icon) icon.style.transform = 'rotate(180deg)';
    } else {
        block.classList.add('hidden');
        if (icon) icon.style.transform = 'rotate(0deg)';
    }
}

function showToast(message, type = 'success') {
    const container = document.getElementById('toast-container');
    if (!container) return;

    const toast = document.createElement('div');
    toast.className = `pointer-events-auto flex items-center gap-2 rounded-xl px-4 py-3 text-xs font-extrabold text-white shadow-xl transition-all duration-300 transform translate-y-4 opacity-0 ${type === 'success' ? 'bg-emerald-600' : 'bg-rose-600'}`;
    toast.innerHTML = `<i class="bi ${type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill'} text-base"></i> <span>${message}</span>`;
    
    container.appendChild(toast);

    setTimeout(() => {
        toast.classList.remove('translate-y-4', 'opacity-0');
    }, 10);

    setTimeout(() => {
        toast.classList.add('opacity-0', 'translate-y-2');
        setTimeout(() => toast.remove(), 300);
    }, 2500);
}

function switchViewMode(mode) {
    const gridContainer = document.getElementById('attendance-grid-container');
    const tableContainer = document.getElementById('attendance-table-container');
    const btnGrid = document.getElementById('btn-view-grid');
    const btnTable = document.getElementById('btn-view-table');

    if (!gridContainer || !tableContainer) return;

    if (mode === 'grid') {
        gridContainer.classList.remove('hidden');
        tableContainer.classList.add('hidden');
        
        btnGrid.className = 'inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-extrabold transition-all bg-brand-600 text-white shadow-xs';
        btnTable.className = 'inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-extrabold transition-all text-slate-600 hover:text-slate-900 hover:bg-white';
        localStorage.setItem('att_view_mode', 'grid');
    } else {
        gridContainer.classList.add('hidden');
        tableContainer.classList.remove('hidden');

        btnTable.className = 'inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-extrabold transition-all bg-brand-600 text-white shadow-xs';
        btnGrid.className = 'inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-extrabold transition-all text-slate-600 hover:text-slate-900 hover:bg-white';
        localStorage.setItem('att_view_mode', 'table');
    }
}

function filterStudents(query) {
    const q = query.toLowerCase().trim();
    
    // Filter Cards
    document.querySelectorAll('.student-att-card').forEach(card => {
        const name = card.dataset.studentName || '';
        const nis = card.dataset.studentNis || '';
        if (!q || name.includes(q) || nis.includes(q)) {
            card.classList.remove('hidden');
        } else {
            card.classList.add('hidden');
        }
    });

    // Filter Table Rows
    document.querySelectorAll('.att-row').forEach(row => {
        const name = row.dataset.studentName || '';
        const nis = row.dataset.studentNis || '';
        if (!q || name.includes(q) || nis.includes(q)) {
            row.classList.remove('hidden');
        } else {
            row.classList.add('hidden');
        }
    });
}

function updateSummaryCounters() {
    let hadir = 0;
    let alfa = 0;
    let sakitIzin = 0;

    document.querySelectorAll('.student-att-card').forEach(card => {
        const statusInd = card.querySelector('#card-status-ind-' + card.dataset.studentId);
        const text = statusInd ? statusInd.textContent.trim() : '';

        if (text.includes('HADIR') || text.includes('Hadir')) {
            hadir++;
        } else if (text.includes('TIDAK HADIR') || text.includes('Alfa') || text.includes('Tidak Hadir')) {
            alfa++;
        } else if (text.includes('SAKIT') || text.includes('IZIN') || text.includes('Sakit') || text.includes('Izin') || text.includes('Locked')) {
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
    const cardEl = document.getElementById(`card-student-${studentId}`);
    const cardStatusInd = document.getElementById(`card-status-ind-${studentId}`);
    const cardNotes = document.getElementById(`card-notes-${studentId}`)?.value || '';
    const tableNotes = document.getElementById(`notes-${studentId}`)?.value || '';
    const notesVal = cardNotes || tableNotes;

    // Sync input notes
    if (document.getElementById(`card-notes-${studentId}`)) document.getElementById(`card-notes-${studentId}`).value = notesVal;
    if (document.getElementById(`notes-${studentId}`)) document.getElementById(`notes-${studentId}`).value = notesVal;

    const tableInd = document.getElementById(`status-ind-${studentId}`);

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
            // Update Card Element UI
            if (cardEl) {
                if (status === 'hadir') {
                    cardEl.className = 'student-att-card rounded-2xl border-2 p-3.5 transition-all flex flex-col justify-between border-emerald-500 bg-emerald-50/40 shadow-sm';
                    if (cardStatusInd) cardStatusInd.innerHTML = '<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-black bg-emerald-600 text-white shadow-xs w-full justify-center">🟢 HADIR</span>';
                } else {
                    cardEl.className = 'student-att-card rounded-2xl border-2 p-3.5 transition-all flex flex-col justify-between border-rose-500 bg-rose-50/40 shadow-sm';
                    if (cardStatusInd) cardStatusInd.innerHTML = '<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-black bg-rose-600 text-white shadow-xs w-full justify-center">🔴 TIDAK HADIR</span>';
                }

                cardEl.querySelectorAll('.btn-card-save').forEach(b => {
                    if (b.textContent.includes('HADIR')) {
                        b.className = 'btn-card-save flex items-center justify-center gap-1 rounded-xl py-2.5 px-2 text-xs font-black transition-all active:scale-95 min-h-[44px] ' + (status === 'hadir' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30 ring-2 ring-emerald-600' : 'bg-emerald-50 text-emerald-800 border border-emerald-200 hover:bg-emerald-100');
                    } else {
                        b.className = 'btn-card-save flex items-center justify-center gap-1 rounded-xl py-2.5 px-2 text-xs font-black transition-all active:scale-95 min-h-[44px] ' + (status === 'alfa' ? 'bg-rose-600 text-white shadow-md shadow-rose-600/30 ring-2 ring-rose-600' : 'bg-rose-50 text-rose-800 border border-rose-200 hover:bg-rose-100');
                    }
                });
            }

            // Update Table Row UI
            const rowEl = document.querySelector(`.att-row[data-student-id="${studentId}"]`);
            if (rowEl) {
                rowEl.querySelectorAll('.btn-quick-save').forEach(b => {
                    if (b.textContent.includes('Hadir') && !b.textContent.includes('Tidak')) {
                        b.className = 'btn btn-sm btn-quick-save ' + (status === 'hadir' ? 'btn-success font-bold shadow-sm' : 'btn-outline-success');
                    } else {
                        b.className = 'btn btn-sm btn-quick-save ' + (status === 'alfa' ? 'btn-danger font-bold shadow-sm' : 'btn-outline-danger');
                    }
                });
            }

            if (tableInd) {
                if (status === 'hadir') {
                    tableInd.innerHTML = '<span class="text-emerald-600 font-bold"><i class="bi bi-check-circle-fill me-1"></i>Hadir</span>';
                } else {
                    tableInd.innerHTML = '<span class="text-rose-600 font-bold"><i class="bi bi-x-circle-fill me-1"></i>Tidak Hadir</span>';
                }
            }

            updateSummaryCounters();
            showToast(status === 'hadir' ? 'Status dicatat HADIR' : 'Status dicatat ABSEN', status === 'hadir' ? 'success' : 'error');
        } else {
            showToast(data.message || 'Gagal menyimpan absensi.', 'error');
        }
    })
    .catch(err => {
        showToast('Terjadi kesalahan koneksi.', 'error');
    });
}

function saveQuickNotes(studentId) {
    const cardEl = document.getElementById(`card-student-${studentId}`);
    if (!cardEl) return;
    const btnActive = cardEl.querySelector('.btn-card-save.bg-emerald-600, .btn-card-save.bg-rose-600');
    if (btnActive) {
        const isHadir = btnActive.textContent.includes('HADIR');
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
            showToast('Catatan jurnal KBM berhasil disimpan!', 'success');
            setTimeout(() => { if (statusEl) statusEl.textContent = ''; }, 3000);
        } else {
            if (statusEl) statusEl.innerHTML = '<span class="text-rose-600 font-bold">Gagal menyimpan</span>';
            showToast('Gagal menyimpan catatan sesi', 'error');
        }
    })
    .catch(err => {
        if (statusEl) statusEl.innerHTML = '<span class="text-rose-600 font-bold">Gagal koneksi</span>';
    });
}

function setAllQuick(status) {
    document.querySelectorAll('.student-att-card').forEach(card => {
        const btn = card.querySelector(`.btn-card-save[onclick*="'${status}'"]`);
        const studentId = card.dataset.studentId;
        if (btn && studentId) {
            saveQuick(studentId, status, btn);
        }
    });
}

let currentModalStudentName = '';
let currentModalStudentId = null;
let currentModalLogs = [];

function openRangeStudentModal(studentName, studentId, logs) {
    currentModalStudentName = studentName;
    currentModalStudentId = studentId;
    currentModalLogs = logs || [];

    const titleEl = document.getElementById('rslmTitle');
    const bodyEl = document.getElementById('rslmBody');
    const modalEl = document.getElementById('rangeStudentLogsModal');

    if (titleEl) titleEl.innerHTML = `<i class="bi bi-clock-history text-brand-600"></i> Detail Riwayat Presensi: <strong>${studentName}</strong>`;

    if (!currentModalLogs || currentModalLogs.length === 0) {
        bodyEl.innerHTML = '<div class="p-8 text-center text-slate-400 font-medium">Belum ada riwayat catatan presensi untuk siswa ini pada periode terpilih.</div>';
    } else {
        let html = `
        <div class="overflow-x-auto">
            <table class="table table-hover align-middle mb-0 text-xs sm:text-sm">
                <thead class="bg-slate-100/80">
                    <tr>
                        <th style="width: 140px">Tanggal</th>
                        <th>Mata Pelajaran / Sesi</th>
                        <th>Guru Pengampu</th>
                        <th class="text-center" style="width: 120px">Status</th>
                        <th>Catatan</th>
                        <th class="text-center" style="width: 90px">Aksi</th>
                    </tr>
                </thead>
                <tbody>`;

        currentModalLogs.forEach(log => {
            let stBadge = '';
            if (log.status === 'hadir') {
                stBadge = '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800">🟢 Hadir</span>';
            } else if (log.status === 'alfa') {
                stBadge = '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-extrabold bg-rose-100 text-rose-800">🔴 Tidak Hadir</span>';
            } else if (log.status === 'sakit') {
                stBadge = '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-extrabold bg-amber-100 text-amber-800">🤒 Sakit</span>';
            } else if (log.status === 'izin') {
                stBadge = '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-extrabold bg-sky-100 text-sky-800">📄 Izin</span>';
            } else if (log.status === 'terlambat') {
                stBadge = '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-extrabold bg-amber-100 text-amber-800">⏰ Terlambat</span>';
            } else {
                stBadge = `<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-extrabold bg-slate-100 text-slate-700">${log.status}</span>`;
            }

            const rawDate = log.raw_date || log.date || DATE_VAL;
            const sessKey = log.session_key || 'mapel_1';
            const safeNotes = (log.notes || '').replace(/'/g, "\\'");
            const safeName = (studentName || '').replace(/'/g, "\\'");

            html += `
                <tr>
                    <td class="font-bold text-slate-700">${log.date_formatted || log.day_date || log.date}</td>
                    <td class="font-bold text-brand-700">${log.subject_name || log.session}</td>
                    <td class="text-slate-600">${log.teacher_name || '-'}</td>
                    <td class="text-center">${stBadge}</td>
                    <td class="text-slate-500 italic">${log.notes || '-'}</td>
                    <td class="text-center">
                        <button type="button" 
                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-50 text-amber-700 hover:bg-amber-100 font-bold text-xs border border-amber-200 transition"
                            onclick="closeRangeStudentModal(); openEditModalForLog(${studentId}, '${safeName}', '${log.status}', '${safeNotes}', '${rawDate}', '${sessKey}')">
                            <i class="bi bi-pencil-square"></i> Edit
                        </button>
                    </td>
                </tr>`;
        });

        html += `
                </tbody>
            </table>
        </div>`;
        bodyEl.innerHTML = html;
    }

    modalEl.classList.remove('hidden');
}

function closeRangeStudentModal() {
    const modalEl = document.getElementById('rangeStudentLogsModal');
    if (modalEl) modalEl.classList.add('hidden');
}

document.addEventListener('DOMContentLoaded', () => {
    updateSummaryCounters();
    const savedMode = localStorage.getItem('att_view_mode') || 'grid';
    switchViewMode(savedMode);
});

function onPeriodChange(val) {
    const startInput = document.getElementById('input-start-date');
    const endInput = document.getElementById('input-end-date');
    const todayStr = '{{ today()->toDateString() }}';

    if (val === 'today') {
        startInput.value = todayStr;
        endInput.value = todayStr;
    } else if (val === 'week') {
        startInput.value = '{{ today()->startOfWeek()->toDateString() }}';
        endInput.value = '{{ today()->endOfWeek()->toDateString() }}';
    } else if (val === '30_days') {
        startInput.value = '{{ today()->subDays(30)->toDateString() }}';
        endInput.value = todayStr;
    } else if (val === 'month') {
        startInput.value = '{{ today()->startOfMonth()->toDateString() }}';
        endInput.value = '{{ today()->endOfMonth()->toDateString() }}';
    }
}

function onDateChange() {
    const periodSelect = document.getElementById('input-period');
    if (periodSelect) periodSelect.value = 'custom';
}

// ── EDIT ABSENSI MODAL FUNCTIONS ──────────────────────────────────────────
let editModalSelectedStatus = null;
let editOverrideDate = null;
let editOverrideSession = null;
let isEditingFromRecap = false;

const STATUS_META = {
    hadir:     { color: 'emerald', label: '🟢 Hadir',       border: 'border-emerald-500 bg-emerald-50 text-emerald-800' },
    alfa:      { color: 'rose',    label: '🔴 Tidak Hadir', border: 'border-rose-500 bg-rose-50 text-rose-800' },
    sakit:     { color: 'orange',  label: '🤒 Sakit',       border: 'border-orange-500 bg-orange-50 text-orange-800' },
    izin:      { color: 'sky',     label: '📄 Izin',        border: 'border-sky-500 bg-sky-50 text-sky-800' },
    terlambat: { color: 'amber',   label: '⏰ Terlambat',   border: 'border-amber-500 bg-amber-50 text-amber-800' },
};

function openEditModalForLog(studentId, studentName, currentStatus, currentNotes, logDate, logSession) {
    editOverrideDate = logDate;
    editOverrideSession = logSession;
    isEditingFromRecap = true;

    const subInfo = document.getElementById('editModalSubInfo');
    if (subInfo) {
        subInfo.textContent = `Tanggal: ${logDate} · Sesi: ${logSession}`;
        subInfo.classList.remove('hidden');
    }

    openEditModal(studentId, studentName, currentStatus, currentNotes);
}

function openEditFromRecap(studentId, studentName, logs) {
    if (!logs || logs.length === 0) {
        openEditModalForLog(studentId, studentName, 'alfa', '', DATE_VAL, (SESSION_VAL === 'all' ? 'mapel_1' : SESSION_VAL));
        return;
    }

    if (logs.length === 1) {
        const l = logs[0];
        openEditModalForLog(studentId, studentName, l.status, l.notes || '', l.raw_date || DATE_VAL, l.session_key || (SESSION_VAL === 'all' ? 'mapel_1' : SESSION_VAL));
    } else {
        openRangeStudentModal(studentName, studentId, logs);
    }
}

function openEditModal(studentId, studentName, currentStatus, currentNotes) {
    if (!isEditingFromRecap) {
        editOverrideDate = null;
        editOverrideSession = null;
        const subInfo = document.getElementById('editModalSubInfo');
        if (subInfo) subInfo.classList.add('hidden');
    }

    editModalSelectedStatus = currentStatus;
    document.getElementById('editModalStudentId').value = studentId;
    document.getElementById('editModalStudentName').textContent = studentName;
    document.getElementById('editModalNotes').value = currentNotes || '';
    document.getElementById('editModalAlert').classList.add('hidden');

    const btn = document.getElementById('btnSaveEdit');
    if (btn) { btn.disabled = false; btn.innerHTML = '<i class="bi bi-check-circle-fill"></i> Simpan Perubahan'; }

    // Reset & highlight current
    selectEditStatus(currentStatus);

    document.getElementById('editAttModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editAttModal').classList.add('hidden');
    isEditingFromRecap = false;
    editOverrideDate = null;
    editOverrideSession = null;
    const subInfo = document.getElementById('editModalSubInfo');
    if (subInfo) subInfo.classList.add('hidden');
}

function selectEditStatus(status) {
    editModalSelectedStatus = status;
    document.querySelectorAll('.edit-status-btn').forEach(btn => {
        const s = btn.dataset.status;
        const meta = STATUS_META[s];
        if (s === status) {
            btn.className = `edit-status-btn flex items-center justify-center gap-1.5 rounded-xl py-3 text-xs font-black border-2 transition-all ${meta ? meta.border : 'border-slate-400 bg-slate-100 text-slate-800'} col-span-${btn.classList.contains('col-span-2') ? '2' : '1'}`;
        } else {
            btn.className = `edit-status-btn flex items-center justify-center gap-1.5 rounded-xl py-3 text-xs font-black border-2 transition-all border-slate-200 bg-white text-slate-700 hover:border-slate-300 col-span-${btn.classList.contains('col-span-2') ? '2' : '1'}`;
        }
    });
}

async function submitEditAttendance() {
    if (!editModalSelectedStatus) {
        showEditAlert('Pilih status kehadiran terlebih dahulu.', 'warning');
        return;
    }

    const studentId = document.getElementById('editModalStudentId').value;
    const notes     = document.getElementById('editModalNotes').value;
    const btn       = document.getElementById('btnSaveEdit');

    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-arrow-repeat" style="display:inline-block;animation:spin 1s linear infinite;"></i> Menyimpan...';

    const targetDate = editOverrideDate || DATE_VAL;
    let targetSession = editOverrideSession || SESSION_VAL;
    if (targetSession === 'all') {
        targetSession = 'mapel_1';
    }

    try {
        const res = await fetch(UPDATE_DETAIL_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                date: targetDate,
                class_id: CLASS_ID_VAL,
                session: targetSession,
                student_id: studentId,
                status: editModalSelectedStatus,
                notes: notes,
            }),
        });

        const data = await res.json();
        if (res.ok && data.success) {
            const wasRecap = isEditingFromRecap;
            closeEditModal();
            showToast('Absen diperbarui: ' + (STATUS_META[editModalSelectedStatus]?.label || editModalSelectedStatus), 'success');

            const isRecapMode = {{ ($isRangeMode || ($session ?? 'all') === 'all') ? 'true' : 'false' }};
            if (wasRecap || isRecapMode) {
                setTimeout(() => {
                    window.location.reload();
                }, 500);
                return;
            }

            // Update card UI (single-day mode)
            const cardEl        = document.getElementById(`card-student-${studentId}`);
            const cardStatusInd = document.getElementById(`card-status-ind-${studentId}`);
            const tableInd      = document.getElementById(`status-ind-${studentId}`);
            const meta          = STATUS_META[editModalSelectedStatus];

            const badgeClass = {
                hadir:     'inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-black bg-emerald-600 text-white shadow-xs w-full justify-center',
                alfa:      'inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-black bg-rose-600 text-white shadow-xs w-full justify-center',
                sakit:     'inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-black bg-orange-500 text-white shadow-xs w-full justify-center',
                izin:      'inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-black bg-sky-500 text-white shadow-xs w-full justify-center',
                terlambat: 'inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-black bg-amber-500 text-white shadow-xs w-full justify-center',
            };

            if (cardStatusInd) {
                cardStatusInd.innerHTML = `<span class="${badgeClass[editModalSelectedStatus] || ''}">${meta?.label || editModalSelectedStatus}</span>`;
            }
            if (cardEl) {
                const borderMap = { hadir:'border-emerald-500 bg-emerald-50/40', alfa:'border-rose-500 bg-rose-50/40', sakit:'border-orange-400 bg-orange-50/40', izin:'border-sky-400 bg-sky-50/40', terlambat:'border-amber-400 bg-amber-50/40' };
                cardEl.className = `student-att-card rounded-2xl border-2 p-3.5 transition-all flex flex-col justify-between ${borderMap[editModalSelectedStatus] || 'border-slate-200 bg-white'} shadow-sm`;
            }
            if (tableInd) {
                const tColor = { hadir:'text-emerald-600', alfa:'text-rose-600', sakit:'text-orange-600', izin:'text-sky-600', terlambat:'text-amber-600' };
                tableInd.innerHTML = `<span class="font-bold ${tColor[editModalSelectedStatus] || ''}">${meta?.label || editModalSelectedStatus}</span>`;
            }
            updateSummaryCounters();
        } else {
            showEditAlert(data.message || 'Gagal menyimpan perubahan.', 'danger');
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check-circle-fill"></i> Simpan Perubahan';
        }
    } catch (e) {
        showEditAlert('Terjadi kesalahan koneksi: ' + e.message, 'danger');
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check-circle-fill"></i> Simpan Perubahan';
    }
}

function showEditAlert(msg, type) {
    const el = document.getElementById('editModalAlert');
    const colorMap = { danger: 'rose', success: 'emerald', warning: 'amber' };
    const c = colorMap[type] || 'slate';
    el.className = `rounded-xl px-4 py-2.5 text-xs font-medium bg-${c}-100 text-${c}-800 border border-${c}-200`;
    el.textContent = msg;
    el.classList.remove('hidden');
}
</script>
@endpush