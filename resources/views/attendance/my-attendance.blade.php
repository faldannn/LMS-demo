{{-- resources/views/attendance/my-attendance.blade.php --}}
@extends('layouts.app')
@section('title', 'Absensi Saya')
@section('page-title', 'Absensi Saya')

@section('content')
    {{-- Header info siswa --}}
    <div class="mb-6 overflow-hidden rounded-2xl bg-gradient-to-br from-brand-600 to-brand-500 shadow-md">
        <div class="flex flex-col md:flex-row md:items-center justify-between p-5 text-white gap-4">
            <div class="flex items-center gap-4">
                <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full bg-white/20 text-2xl font-bold">
                    {{ strtoupper(substr($student->name, 0, 1)) }}
                </div>
                <div>
                    <h4 class="text-lg font-bold">{{ $student->name }}</h4>
                    <p class="text-sm text-white/80">{{ $student->nis }} &middot; {{ $student->class?->name }}</p>
                    <p class="text-xs text-white/70">Wali Kelas: {{ $student->class?->homeroomTeacher?->name ?? '-' }}</p>
                </div>
            </div>
            <div class="shrink-0">
                <a href="{{ route('attendance.self-checkin') }}" class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-extrabold text-brand-700 shadow-md hover:bg-brand-50 hover:scale-[1.02] active:scale-[0.98] transition-all">
                    <i class="bi bi-camera-video text-lg"></i>
                    Ambil Presensi Wajah & GPS
                </a>
            </div>
        </div>
    </div>

    {{-- Filter bulan --}}
    <div class="mb-6 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200/60">
        <form method="GET" class="grid grid-cols-1 gap-4 sm:grid-cols-12 items-end">
            <div class="sm:col-span-5">
                <label class="block mb-1.5 text-xs font-bold uppercase tracking-wider text-slate-500">Bulan</label>
                <select name="month" class="form-select">
                    @foreach(range(1,12) as $m)
                        <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create(null, (int) $m)->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="sm:col-span-4">
                <label class="block mb-1.5 text-xs font-bold uppercase tracking-wider text-slate-500">Tahun</label>
                <select name="year" class="form-select">
                    @foreach(range(date('Y'), date('Y')-2) as $y)
                        <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div class="sm:col-span-3">
                <button type="submit" class="inline-flex w-full items-center justify-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700">
                    <i class="bi bi-search"></i> Lihat
                </button>
            </div>
        </form>
    </div>

    {{-- Statistik --}}
    @php
        $total = $history->count();
        $pct = $total > 0 ? round(($stats->get('hadir',0) + $stats->get('terlambat',0)) / $total * 100) : 0;
    @endphp
    <div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
        <!-- Hadir -->
        <div class="rounded-2xl bg-white p-4 text-center shadow-sm ring-1 ring-slate-200/60">
            <div class="mx-auto mb-2 flex h-10 w-10 items-center justify-center rounded-full bg-emerald-50 text-emerald-600">
                <i class="bi bi-check-circle-fill text-lg"></i>
            </div>
            <div class="font-mono text-2xl font-bold text-slate-800">{{ $stats->get('hadir',0) }}</div>
            <div class="text-xs font-semibold text-slate-400">Hadir</div>
        </div>

        <!-- Terlambat -->
        <div class="rounded-2xl bg-white p-4 text-center shadow-sm ring-1 ring-slate-200/60">
            <div class="mx-auto mb-2 flex h-10 w-10 items-center justify-center rounded-full bg-amber-50 text-amber-600">
                <i class="bi bi-clock-fill text-lg"></i>
            </div>
            <div class="font-mono text-2xl font-bold text-slate-800">{{ $stats->get('terlambat',0) }}</div>
            <div class="text-xs font-semibold text-slate-400">Terlambat</div>
        </div>

        <!-- Alfa -->
        <div class="rounded-2xl bg-white p-4 text-center shadow-sm ring-1 ring-slate-200/60">
            <div class="mx-auto mb-2 flex h-10 w-10 items-center justify-center rounded-full bg-rose-50 text-rose-600">
                <i class="bi bi-x-circle-fill text-lg"></i>
            </div>
            <div class="font-mono text-2xl font-bold text-slate-800">{{ $stats->get('alfa',0) }}</div>
            <div class="text-xs font-semibold text-slate-400">Alfa</div>
        </div>

        <!-- Persentase Kehadiran -->
        <div class="rounded-2xl bg-white p-4 text-center shadow-sm ring-1 ring-slate-200/60">
            <div class="mx-auto mb-2 flex h-10 w-10 items-center justify-center rounded-full bg-brand-50 text-brand-600">
                <i class="bi bi-percent text-lg"></i>
            </div>
            <div class="font-mono text-2xl font-bold {{ $pct >= 75 ? 'text-brand-600' : 'text-rose-600' }}">{{ $pct }}%</div>
            <div class="text-xs font-semibold text-slate-400">Kehadiran</div>
        </div>
    </div>

    @if($pct < 75)
        <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800 shadow-sm flex items-center gap-3">
            <i class="bi bi-exclamation-triangle-fill text-lg text-rose-500 shrink-0"></i>
            <div>Persentase kehadiran Anda <strong>{{ $pct }}%</strong> — di bawah batas minimal <strong>75%</strong>. Segera hubungi wali kelas Anda.</div>
        </div>
    @endif

    {{-- Tabel riwayat --}}
    <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60">
        <div class="border-b border-slate-100 bg-slate-50/80 px-5 py-4 flex items-center gap-2">
            <i class="bi bi-calendar3 text-brand-600"></i>
            <h3 class="font-bold text-slate-800">
                Riwayat Bulan {{ \Carbon\Carbon::create(null, (int) $month)->translatedFormat('F') }} {{ $year }}
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/80 text-xs font-bold uppercase tracking-wider text-slate-500">
                        <th class="px-5 py-3">#</th>
                        <th class="px-5 py-3">Tanggal</th>
                        <th class="px-5 py-3">Hari</th>
                        <th class="px-5 py-3">Sesi</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3">Jam Masuk</th>
                        <th class="px-5 py-3">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($history as $i => $detail)
                        <tr class="transition hover:bg-slate-50/30">
                            <td class="px-5 py-3.5 text-slate-400">{{ $i + 1 }}</td>
                            <td class="px-5 py-3.5 font-mono text-xs font-semibold text-slate-700">
                                {{ $detail->attendance->date->format('d/m/Y') }}
                            </td>
                            <td class="px-5 py-3.5 font-medium text-slate-700">
                                {{ $detail->attendance->date->translatedFormat('l') }}
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center rounded bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-600">
                                    {{ ucfirst($detail->attendance->session) }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                @switch($detail->status)
                                    @case('hadir')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Hadir
                                        </span>
                                        @break
                                    @case('terlambat')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-semibold text-amber-700">
                                            <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span> Terlambat
                                        </span>
                                        @break
                                    @case('sakit')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-sky-50 px-2.5 py-0.5 text-xs font-semibold text-sky-700">
                                            <span class="h-1.5 w-1.5 rounded-full bg-sky-500"></span> Sakit
                                        </span>
                                        @break
                                    @case('izin')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-semibold text-blue-700">
                                            <span class="h-1.5 w-1.5 rounded-full bg-blue-500"></span> Izin
                                        </span>
                                        @break
                                    @case('alfa')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-rose-50 px-2.5 py-0.5 text-xs font-semibold text-rose-700">
                                            <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span> Alfa
                                        </span>
                                        @break
                                @endswitch
                            </td>
                            <td class="px-5 py-3.5 font-mono text-xs text-slate-700">
                                {{ $detail->check_in ? $detail->check_in->format('H:i') : '-' }}
                            </td>
                            <td class="px-5 py-3.5 text-xs text-slate-500">
                                {{ $detail->notes ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-8 text-center text-slate-400">
                                <i class="bi bi-calendar-x text-3xl d-block mb-2 text-slate-300"></i>
                                Tidak ada riwayat absensi pada bulan ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
