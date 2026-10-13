{{-- resources/views/students/show.blade.php --}}
@extends('layouts.app')
@section('title', 'Detail Siswa')
@section('page-title', 'Detail Siswa')

@section('content')
<div class="space-y-6">
    <x-detail-header>
        <div>
            <p class="mb-1 text-xs font-bold uppercase tracking-wider text-slate-400">Detail Siswa</p>
            <h5 class="mb-1 text-slate-900 fw-bold">{{ $student->name }}</h5>
            <div class="text-muted" style="font-size:13px">NIS {{ $student->nis }} · {{ $student->class?->name ?? '-' }}</div>
        </div>

        <x-slot:actions>
            <a href="{{ route('students.attendance-history', $student) }}" class="inline-flex items-center justify-center gap-2 rounded-lg border border-sky-500 bg-white px-4 py-2.5 text-sm font-semibold text-sky-600 shadow-sm transition hover:bg-sky-50 sm:px-4 sm:py-2">
                <i class="bi bi-calendar3"></i>
                <span>Riwayat Absensi</span>
            </a>
            <a href="{{ route('students.edit', $student) }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700 sm:px-4 sm:py-2">
                <i class="bi bi-pencil"></i>
                <span>Edit</span>
            </a>
            <a href="{{ route('students.index') }}" class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 sm:px-4 sm:py-2">
                <i class="bi bi-arrow-left"></i>
                <span>Kembali</span>
            </a>
        </x-slot:actions>
    </x-detail-header>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Info Siswa --}}
        <div class="lg:col-span-1 space-y-4">
            {{-- Profile Card --}}
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70 text-center">
                <div class="mx-auto mb-4">
                    <img src="{{ $student->photo_url }}" alt="{{ $student->name }}"
                        class="mx-auto h-24 w-24 rounded-full border-4 border-brand-50 object-cover sm:h-28 sm:w-28">
                </div>
                <h3 class="text-lg font-extrabold text-slate-900">{{ $student->name }}</h3>
                <p class="mb-3 text-sm text-slate-500">{{ $student->nis }}</p>
                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold {{ $student->status ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }} mb-3">
                    {{ $student->status ? 'Aktif' : 'Non-Aktif' }}
                </span>
                <div class="flex flex-wrap justify-center gap-2">
                    <span class="inline-flex items-center rounded-full bg-brand-50 px-3 py-1 text-xs font-semibold text-brand-700">
                        {{ $student->class?->name }}
                    </span>
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $student->gender === 'L' ? 'bg-brand-50 text-brand-700' : 'bg-pink-100 text-pink-700' }}">
                        {{ $student->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
                    </span>
                </div>
            </div>

            {{-- Info Pribadi --}}
            <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/70">
                <div class="border-b border-slate-100 bg-slate-50/80 px-5 py-4 font-bold text-slate-800 flex items-center gap-2">
                    <i class="bi bi-person-lines-fill text-brand-600"></i>
                    Info Pribadi
                </div>
                <div class="p-5">
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider">NISN</dt>
                            <dd class="mt-1 text-sm font-semibold text-slate-900">{{ $student->nisn ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Kartu RFID / UID Tap</dt>
                            <dd class="mt-1 text-sm font-semibold text-slate-900 font-mono">
                                @if($student->rfid)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        <i class="bi bi-credit-card-2-front"></i> {{ $student->rfid }}
                                    </span>
                                @else
                                    <span class="text-slate-400 font-sans text-xs">Belum di-assign</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Tanggal Lahir</dt>
                            <dd class="mt-1 text-sm font-semibold text-slate-900">{{ $student->dob?->translatedFormat('d F Y') ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Wali Kelas</dt>
                            <dd class="mt-1 text-sm font-semibold text-slate-900">{{ $student->class?->homeroomTeacher?->name ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Orang Tua</dt>
                            <dd class="mt-1 text-sm font-semibold text-slate-900">{{ $student->parent_name ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider">No. HP Ortu</dt>
                            <dd class="mt-1 text-sm font-semibold text-slate-900">{{ $student->parent_phone ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Alamat</dt>
                            <dd class="mt-1 text-sm font-semibold text-slate-900">{{ $student->address ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        {{-- Statistik Absensi --}}
        <div class="lg:col-span-2 space-y-4">
            @php
                $total = $monthlyStats->sum();
                $hadir = $monthlyStats->get('hadir', 0) + $monthlyStats->get('terlambat', 0);
                $pct = $total > 0 ? round($hadir / $total * 100) : 0;
            @endphp
            
            {{-- Attendance Stats --}}
            <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/70">
                <div class="border-b border-slate-100 bg-slate-50/80 px-5 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-bar-chart text-brand-600"></i>
                        <span class="font-bold text-slate-800">Statistik Absensi Bulan Ini</span>
                    </div>
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold {{ $pct >= 75 ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                        {{ $pct }}% Hadir
                    </span>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5 mb-4">
                        @foreach(['hadir'=>['Hadir','bg-brand-50','text-brand-600','bi-check-circle-fill'], 'terlambat'=>['Terlambat','bg-amber-50','text-amber-600','bi-clock-fill'], 'sakit'=>['Sakit','bg-sky-50','text-sky-600','bi-heart-pulse-fill'], 'izin'=>['Izin','bg-indigo-50','text-indigo-600','bi-file-earmark-text-fill'], 'alfa'=>['Alfa','bg-rose-50','text-rose-600','bi-x-circle-fill']] as $status => [$label, $bg, $textColor, $icon])
                            <div class="{{ $bg }} rounded-xl p-4 text-center transition hover:shadow-md">
                                <i class="bi {{ $icon }} text-2xl {{ $textColor }}"></i>
                                <div class="mt-2 text-2xl font-extrabold {{ $textColor }} font-mono">
                                    {{ $monthlyStats->get($status, 0) }}
                                </div>
                                <div class="mt-1 text-xs font-semibold text-slate-500">{{ $label }}</div>
                            </div>
                        @endforeach
                    </div>
                    @if($pct < 75)
                        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800 flex items-center gap-2">
                            <i class="bi bi-exclamation-triangle-fill text-rose-600"></i>
                            <span class="font-medium">Kehadiran di bawah 75%. Perlu perhatian khusus!</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Riwayat Absensi Terbaru --}}
            <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/70">
                <div class="border-b border-slate-100 bg-slate-50/80 px-5 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-clock-history text-brand-600"></i>
                        <span class="font-bold text-slate-800">Riwayat Absensi Terbaru</span>
                    </div>
                    <a href="{{ route('students.attendance-history', $student) }}" class="inline-flex items-center gap-1.5 rounded-lg border border-brand-600 bg-white px-3 py-1.5 text-xs font-semibold text-brand-600 transition hover:bg-brand-50">
                        Lihat Semua
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-slate-600">
                        <thead class="bg-slate-50 text-xs uppercase text-slate-500 border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-3 font-semibold">Tanggal</th>
                                <th class="px-4 py-3 font-semibold">Sesi</th>
                                <th class="px-4 py-3 font-semibold">Status</th>
                                <th class="px-4 py-3 font-semibold">Jam Masuk</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(($recentAttendance ?? $student->attendanceDetails)->take(10) as $detail)
                                <tr class="border-b border-slate-100 hover:bg-slate-50/50 transition-colors">
                                    <td class="px-4 py-3 font-medium text-slate-900">{{ $detail->attendance->date->translatedFormat('d M Y') }}</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-700">
                                            {{ ucfirst(str_replace('_', ' ', $detail->attendance->session ?? '')) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold badge-status-{{ $detail->status }}">
                                            {{ ucfirst($detail->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 font-mono text-slate-600">{{ $detail->check_in ? $detail->check_in->format('H:i') : '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-slate-500">
                                        <div class="flex flex-col items-center gap-2">
                                            <i class="bi bi-calendar-x text-3xl text-slate-300"></i>
                                            <span class="text-sm">Belum ada riwayat absensi.</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
