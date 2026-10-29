{{-- resources/views/students/show.blade.php --}}
@extends('layouts.app')
@section('title', 'Detail Siswa - ' . $student->name)
@section('page-title', 'Detail Siswa')

@section('content')
<div class="space-y-6">
    <x-detail-header>
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Detail Siswa</span>
                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-bold border {{ $student->student_status_badge }}">
                    {{ $student->student_status_label }}
                </span>
                @if($student->isPindahan())
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Siswa Pindahan
                    </span>
                @else
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold bg-slate-100 text-slate-600">
                        Siswa Baru
                    </span>
                @endif
            </div>
            <h4 class="mb-1 text-slate-900 fw-bold">{{ $student->name }}</h4>
            <div class="text-muted" style="font-size:13px">
                NIS: <code>{{ $student->nis }}</code> · NISN: <code>{{ $student->nisn ?? '-' }}</code> · {{ $student->class?->name ?? 'Belum ada kelas' }}
            </div>
        </div>

        <x-slot:actions>
            @if($student->status && !$student->isMutasiKeluar())
                <button type="button" class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-amber-300 bg-amber-50 px-3.5 py-2 text-sm font-semibold text-amber-800 shadow-sm transition hover:bg-amber-100" data-bs-toggle="modal" data-bs-target="#modalMutateOut">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Catat Mutasi / Keluar</span>
                </button>
            @elseif($student->isMutasiKeluar())
                <form action="{{ route('students.cancel-mutation', $student) }}" method="POST" class="d-inline" onsubmit="return confirm('Kembalikan status siswa ini menjadi aktif?')">
                    @csrf
                    <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-emerald-300 bg-emerald-50 px-3.5 py-2 text-sm font-semibold text-emerald-800 shadow-sm transition hover:bg-emerald-100">
                        <i class="bi bi-arrow-counterclockwise"></i>
                        <span>Batalkan Status Mutasi</span>
                    </button>
                </form>
            @endif

            <a href="{{ route('students.attendance-history', $student) }}" class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-sky-500 bg-white px-3.5 py-2 text-sm font-semibold text-sky-600 shadow-sm transition hover:bg-sky-50">
                <i class="bi bi-calendar3"></i>
                <span>Riwayat Absensi</span>
            </a>
            <a href="{{ route('students.edit', $student) }}" class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-brand-600 px-3.5 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700">
                <i class="bi bi-pencil"></i>
                <span>Edit</span>
            </a>
            <a href="{{ route('students.index') }}" class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                <i class="bi bi-arrow-left"></i>
                <span>Kembali</span>
            </a>
        </x-slot:actions>
    </x-detail-header>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Sisi Kiri: Profil & Info Pribadi --}}
        <div class="lg:col-span-1 space-y-4">
            {{-- Profile Card --}}
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70 text-center">
                <div class="mx-auto mb-4">
                    <img src="{{ $student->photo_url }}" alt="{{ $student->name }}"
                        class="mx-auto h-24 w-24 rounded-full border-4 border-brand-50 object-cover sm:h-28 sm:w-28 shadow-sm">
                </div>
                <h3 class="text-lg font-extrabold text-slate-900">{{ $student->name }}</h3>
                <p class="mb-2 text-sm text-slate-500 font-mono">NIS: {{ $student->nis }}</p>

                <div class="flex flex-wrap justify-center gap-2 mb-3">
                    <span class="inline-flex items-center rounded-full bg-brand-50 px-3 py-1 text-xs font-semibold text-brand-700 border border-brand-200">
                        {{ $student->class?->name ?? 'Tanpa Kelas' }}
                    </span>
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $student->gender === 'L' ? 'bg-sky-50 text-sky-700 border border-sky-200' : 'bg-pink-50 text-pink-700 border border-pink-200' }}">
                        {{ $student->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
                    </span>
                </div>

                @if($student->isMutasiKeluar())
                    <div class="rounded-xl border border-rose-200 bg-rose-50 p-3 text-start text-xs text-rose-800">
                        <div class="fw-bold mb-1"><i class="bi bi-exclamation-octagon me-1"></i> Siswa Sudah Mutasi / Keluar</div>
                        <div><strong>Tanggal:</strong> {{ $student->exit_date ? $student->exit_date->translatedFormat('d F Y') : '-' }}</div>
                        @if($student->destination_school)
                            <div><strong>Sekolah Tujuan:</strong> {{ $student->destination_school }}</div>
                        @endif
                        <div><strong>Alasan:</strong> {{ $student->exit_reason ?: '-' }}</div>
                    </div>
                @endif
            </div>

            {{-- Info Pendaftaran & Mutasi --}}
            <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/70">
                <div class="border-b border-slate-100 bg-slate-50/80 px-5 py-3.5 font-bold text-slate-800 flex items-center gap-2">
                    <i class="bi bi-journal-bookmark text-brand-600"></i>
                    Status Masuk & Mutasi
                </div>
                <div class="p-5">
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Jalur Masuk</dt>
                            <dd class="mt-1 text-sm font-semibold text-slate-900">
                                {{ $student->isPindahan() ? 'Siswa Pindahan (Mutasi Masuk)' : 'Siswa Baru (Reguler)' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Tanggal Masuk / Terdaftar</dt>
                            <dd class="mt-1 text-sm font-semibold text-slate-900">
                                {{ $student->entry_date ? $student->entry_date->translatedFormat('d F Y') : ($student->created_at ? $student->created_at->translatedFormat('d F Y') : '-') }}
                            </dd>
                        </div>
                        @if($student->isPindahan())
                            <div>
                                <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Sekolah Asal</dt>
                                <dd class="mt-1 text-sm font-semibold text-indigo-700">
                                    <i class="bi bi-building me-1"></i> {{ $student->origin_school ?: 'Tidak dicantumkan' }}
                                </dd>
                            </div>
                        @endif
                        @if($student->isMutasiKeluar())
                            <div class="border-t border-slate-100 pt-2">
                                <dt class="text-xs font-semibold text-rose-600 uppercase tracking-wider">Tanggal Mutasi Keluar</dt>
                                <dd class="mt-1 text-sm font-semibold text-slate-900">
                                    {{ $student->exit_date ? $student->exit_date->translatedFormat('d F Y') : '-' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold text-rose-600 uppercase tracking-wider">Sekolah Tujuan</dt>
                                <dd class="mt-1 text-sm font-semibold text-slate-900">
                                    {{ $student->destination_school ?: '-' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold text-rose-600 uppercase tracking-wider">Alasan Keluar</dt>
                                <dd class="mt-1 text-sm text-slate-700">
                                    {{ $student->exit_reason ?: '-' }}
                                </dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>

            {{-- Info Pribadi --}}
            <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/70">
                <div class="border-b border-slate-100 bg-slate-50/80 px-5 py-3.5 font-bold text-slate-800 flex items-center gap-2">
                    <i class="bi bi-person-lines-fill text-brand-600"></i>
                    Info Pribadi & Kontak
                </div>
                <div class="p-5">
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider">NISN</dt>
                            <dd class="mt-1 text-sm font-semibold text-slate-900 font-mono">{{ $student->nisn ?? '-' }}</dd>
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
                            <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Wali Kelas Saat Ini</dt>
                            <dd class="mt-1 text-sm font-semibold text-slate-900">{{ $student->class?->homeroomTeacher?->name ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Orang Tua / Wali</dt>
                            <dd class="mt-1 text-sm font-semibold text-slate-900">{{ $student->parent_name ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider">No. HP Orang Tua</dt>
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

        {{-- Sisi Kanan: Riwayat Akademik & Absensi --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- REKAM RIWAYAT AKADEMIK --}}
            <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/70 overflow-hidden">
                <div class="border-b border-slate-100 bg-slate-50/80 px-5 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-mortarboard-fill text-brand-600"></i>
                        <span class="font-bold text-slate-800">Riwayat Akademik Siswa</span>
                    </div>
                    <span class="badge bg-purple-50 text-purple-700 border border-purple-200">
                        {{ $student->academicHistories->count() }} Periode Tercatat
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-slate-600">
                        <thead class="bg-slate-50 text-xs uppercase text-slate-500 border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-3 font-semibold">Tahun Ajaran</th>
                                <th class="px-4 py-3 font-semibold">Kelas & Jurusan</th>
                                <th class="px-4 py-3 font-semibold">Wali Kelas</th>
                                <th class="px-4 py-3 font-semibold">Status Akademik</th>
                                <th class="px-4 py-3 font-semibold">Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($student->academicHistories as $history)
                                <tr class="hover:bg-slate-50/60 transition">
                                    <td class="px-4 py-3 font-bold text-slate-900">
                                        {{ $history->academicYear?->year ?? '-' }}
                                        @if($history->academicYear?->is_active)
                                            <span class="badge bg-emerald-100 text-emerald-700 text-2xs ms-1">Aktif</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-semibold text-slate-800">{{ $history->class?->name ?? 'Kelas dihapus' }}</div>
                                        <div class="text-xs text-slate-500">Tingkat {{ $history->class?->grade ?? '-' }} · {{ $history->class?->major?->name ?? 'Umum' }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-xs text-slate-700">
                                        {{ $history->class?->homeroomTeacher?->name ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $history->status_badge_class }}">
                                            {{ $history->status_label }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-xs text-slate-600">
                                        {{ $history->notes ?: '—' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-6 text-center text-slate-400">
                                        <i class="bi bi-clock-history fs-3 d-block mb-1 text-slate-300"></i>
                                        Belum ada riwayat akademik terekam. Riwayat otomatis dicatat saat siswa ditempatkan di kelas atau terjadi proses kenaikan kelas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- STATISTIK ABSENSI --}}
            @php
                $total = $monthlyStats->sum();
                $hadir = $monthlyStats->get('hadir', 0) + $monthlyStats->get('terlambat', 0);
                $pct = $total > 0 ? round($hadir / $total * 100) : 0;
            @endphp
            
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

{{-- Modal Catat Mutasi Keluar --}}
<div class="modal fade" id="modalMutateOut" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-2xl border-0 shadow-lg">
            <form action="{{ route('students.mutate-out', $student) }}" method="POST">
                @csrf
                <div class="modal-header border-b border-slate-100 bg-slate-50 px-4 py-3">
                    <h6 class="modal-title fw-bold text-slate-800">
                        <i class="bi bi-box-arrow-right me-2 text-amber-600"></i> Catat Mutasi Siswa Keluar
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 space-y-3">
                    <div class="alert alert-warning text-xs mb-3">
                        <i class="bi bi-info-circle me-1"></i> Mencatat mutasi keluar akan menonaktifkan akun login LMS siswa serta memperbarui statusnya pada riwayat akademik tahun ajaran berjalan.
                    </div>
                    <div>
                        <label class="form-label fw-semibold text-xs uppercase text-slate-600">Jenis Mutasi</label>
                        <select name="student_status" class="form-select text-sm" required onchange="toggleDestSchool(this.value)">
                            <option value="mutasi_keluar">Pindah Sekolah (Mutasi Keluar)</option>
                            <option value="drop_out">Mengundurkan Diri / Keluar (Drop Out)</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label fw-semibold text-xs uppercase text-slate-600">Tanggal Efektif Keluar</label>
                        <input type="date" name="exit_date" class="form-control text-sm" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div id="wrapper_destination_school">
                        <label class="form-label fw-semibold text-xs uppercase text-slate-600">Sekolah Tujuan</label>
                        <input type="text" name="destination_school" class="form-control text-sm" placeholder="Contoh: SMAN 1 Jakarta">
                    </div>
                    <div>
                        <label class="form-label fw-semibold text-xs uppercase text-slate-600">Alasan Keluar</label>
                        <textarea name="exit_reason" class="form-control text-sm" rows="2" placeholder="Contoh: Mengikuti domisili orang tua" required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-t border-slate-100 bg-slate-50 px-4 py-3">
                    <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin memproses mutasi siswa {{ addslashes($student->name) }}?')">
                        <i class="bi bi-check-lg me-1"></i> Simpan Mutasi Keluar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleDestSchool(val) {
    const wrap = document.getElementById('wrapper_destination_school');
    if (wrap) {
        if (val === 'drop_out') {
            wrap.style.display = 'none';
        } else {
            wrap.style.display = 'block';
        }
    }
}
</script>
@endpush
