@extends('layouts.app')
@section('title', 'Pusat Generator Barcode & Kartu Presensi')
@section('page-title', 'Pusat Generator Barcode & Kartu')

@push('styles')
<style>
    .barcode-card-hover {
        transition: all 0.25s ease;
    }
    .barcode-card-hover:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 25px -5px rgba(15, 23, 42, 0.1), 0 0 0 2px rgba(16, 185, 129, 0.4);
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
@endpush

@section('content')
<div class="mx-auto max-w-7xl space-y-6">

    {{-- Breadcrumb & Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2">
        <div>
            <div class="mb-1 flex items-center gap-2">
                <a href="{{ route('attendance.index') }}" class="inline-flex items-center gap-1.5 text-xs font-extrabold text-slate-500 hover:text-brand-600 transition">
                    <i class="bi bi-arrow-left"></i> Presensi
                </a>
                <span class="text-slate-300">•</span>
                <span class="text-xs font-bold text-slate-400">Barcode Studio</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight flex items-center gap-3">
                <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-900 text-white shadow-lg shadow-slate-900/20 shrink-0">
                    <i class="bi bi-upc-scan text-xl text-emerald-400"></i>
                </span>
                Pusat Generator Barcode &amp; Kartu
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1 mb-0">
                Generate kode barcode garis (Code128 untuk scanner laser USB) dan QR Code untuk siswa maupun guru.
            </p>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            {{-- Tombol Cetak Lembar Stiker Barcode --}}
            <a href="{{ route('attendance.barcode-print-sheet', ['target' => $targetType, 'class_id' => $selectedClassId, 'format' => 'both']) }}" target="_blank"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-extrabold shadow-md transition">
                <i class="bi bi-printer-fill text-emerald-400"></i> Cetak Lembar Barcode (Stiker A4)
            </a>

            {{-- Tombol Cetak Kartu Pelajar --}}
            @if($targetType === 'student')
            <a href="{{ route('attendance.barcode-print-cards', ['class_id' => $selectedClassId]) }}" target="_blank"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-extrabold shadow-md shadow-emerald-600/20 transition">
                <i class="bi bi-person-badge-fill"></i> Cetak Kartu Pelajar Digital
            </a>
            @endif
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="rounded-3xl bg-white p-5 border border-slate-200/90 shadow-sm">
        <form method="GET" action="{{ route('attendance.barcode-center') }}" class="flex flex-col sm:flex-row flex-wrap gap-3 items-end w-full">
            
            {{-- Target: Siswa atau Guru --}}
            <div class="w-full sm:w-auto" style="flex: 1 1 200px;">
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Tipe Subjek:</label>
                <select name="target" onchange="this.form.submit()" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-xs font-bold text-slate-800 focus:border-brand-500 focus:bg-white focus:outline-none">
                    <option value="student" {{ $targetType === 'student' ? 'selected' : '' }}>Siswa / Peserta Didik</option>
                    <option value="teacher" {{ $targetType === 'teacher' ? 'selected' : '' }}>Guru / Tenaga Pendidik</option>
                </select>
            </div>

            {{-- Filter Kelas (Jika Siswa) --}}
            @if($targetType === 'student')
            <div class="w-full sm:w-auto" style="flex: 1 1 220px;">
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Pilih Kelas:</label>
                <select name="class_id" onchange="this.form.submit()" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-800 focus:border-brand-500 focus:bg-white focus:outline-none">
                    <option value="">-- Semua Kelas --</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ (string)$selectedClassId === (string)$c->id ? 'selected' : '' }}>
                            {{ $c->name }} ({{ $c->students_count ?? $c->students()->count() }} Siswa)
                        </option>
                    @endforeach
                </select>
            </div>
            @else
            <div class="w-full sm:w-auto" style="flex: 1 1 220px;">
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Kategori:</label>
                <input type="text" disabled value="Seluruh Guru Aktif" class="w-full rounded-xl border border-slate-200 bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-500 cursor-not-allowed">
            </div>
            @endif

            {{-- Pencarian --}}
            <div class="w-full sm:w-auto" style="flex: 2 1 260px;">
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Cari Nama / NIS / NIP:</label>
                <div class="relative">
                    <input type="text" name="q" value="{{ $search }}" placeholder="Ketik nama atau nomor..."
                        class="w-full rounded-xl border border-slate-300 bg-white pl-9 pr-3 py-2 text-xs font-medium text-slate-800 focus:border-brand-500 focus:outline-none">
                    <i class="bi bi-search absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                </div>
            </div>

            {{-- Tombol Filter --}}
            <div class="w-full sm:w-auto flex gap-2" style="flex: 0 0 auto;">
                <button type="submit" class="rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold py-2 px-5 text-xs transition">
                    Terapkan
                </button>
                @if($search || $selectedClassId)
                <a href="{{ route('attendance.barcode-center', ['target' => $targetType]) }}" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold transition flex items-center justify-center">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
                @endif
            </div>

        </form>
    </div>

    {{-- Cards Grid Barcode --}}
    @if($targetType === 'student')
        <div class="flex items-center justify-between">
            <h5 class="text-sm font-black text-slate-900 uppercase tracking-wider mb-0 flex items-center gap-2">
                <i class="bi bi-people-fill text-brand-600"></i>
                Daftar Barcode Siswa ({{ $students->count() }} Data)
            </h5>
            <span class="text-xs text-slate-400 font-mono">Standar Code128 &amp; QR Code</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.25rem;">
            @forelse($students as $student)
                <div class="rounded-3xl bg-white p-5 border border-slate-200/90 barcode-card-hover space-y-4 shadow-sm relative overflow-hidden">
                    
                    {{-- Student Header Info --}}
                    <div class="flex items-center gap-3">
                        <img src="{{ $student->photo_url }}" 
                            class="h-12 w-12 rounded-2xl object-cover ring-2 ring-slate-100 shadow-sm shrink-0" alt="{{ $student->name }}">
                        <div class="min-w-0 flex-1">
                            <h6 class="text-sm font-black text-slate-900 truncate mb-0">{{ $student->name }}</h6>
                            <div class="flex items-center gap-2 text-xs text-slate-500 mt-0.5">
                                <span class="font-mono font-bold text-slate-600">NIS: {{ $student->nis }}</span>
                                <span>•</span>
                                <span class="rounded bg-brand-50 px-2 py-0.5 text-[10px] font-bold text-brand-700">
                                    {{ $student->class?->name ?? 'Tanpa Kelas' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Barcode Display Area --}}
                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200/70 text-center space-y-3">
                        
                        {{-- 1D Barcode (Code128 Batang) --}}
                        <div class="bg-white p-3 rounded-xl border border-slate-200 flex flex-col items-center justify-center">
                            <svg class="barcode-svg" jsbarcode-format="CODE128" jsbarcode-value="LMS-{{ $student->nis }}" jsbarcode-textmargin="0" jsbarcode-fontoptions="bold" jsbarcode-height="45" jsbarcode-width="1.8" jsbarcode-fontsize="12"></svg>
                            <span class="text-[10px] font-mono text-slate-400 mt-1">Barcode 1D (Alat Scanner / Pink / Laser Gun)</span>
                        </div>

                        {{-- 2D QR Code --}}
                        <div class="bg-white p-3 rounded-xl border border-slate-200 flex items-center justify-center gap-4">
                            <div class="shrink-0 p-1 bg-white border border-slate-100 rounded-lg">
                                {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(80)->generate('LMS-' . $student->nis) !!}
                            </div>
                            <div class="text-left">
                                <span class="inline-block px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 font-mono text-[10px] font-bold border border-emerald-200 mb-1">
                                    QR Code HP / Kamera
                                </span>
                                <div class="text-xs font-mono font-black text-slate-800">LMS-{{ $student->nis }}</div>
                                <span class="text-[10px] text-slate-400 block mt-0.5">Scan via Kamera Siswa/Guru</span>
                            </div>
                        </div>

                    </div>

                    {{-- Footer Action Barcode --}}
                    <div class="pt-1 flex items-center justify-between text-xs">
                        <span class="text-[11px] font-mono text-slate-400">
                            RFID: {{ $student->rfid ?: 'Belum ditautkan' }}
                        </span>
                        <a href="{{ route('attendance.barcode-print-cards', ['student_id' => $student->id]) }}" target="_blank"
                            class="inline-flex items-center gap-1 font-bold text-brand-600 hover:text-brand-700">
                            <i class="bi bi-printer"></i> Cetak ID Card
                        </a>
                    </div>

                </div>
            @empty
                <div class="col-span-full py-16 text-center bg-white rounded-3xl border border-slate-200 text-slate-400">
                    <i class="bi bi-inbox text-4xl text-slate-300 block mb-2"></i>
                    Tidak ada siswa ditemukan pada kelas ini.
                </div>
            @endforelse
        </div>

    @else
        {{-- Teachers Barcode List --}}
        <div class="flex items-center justify-between">
            <h5 class="text-sm font-black text-slate-900 uppercase tracking-wider mb-0 flex items-center gap-2">
                <i class="bi bi-person-workspace text-brand-600"></i>
                Daftar Barcode Guru ({{ $teachers->count() }} Data)
            </h5>
            <span class="text-xs text-slate-400 font-mono">Standar Code128 &amp; QR Code</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.25rem;">
            @forelse($teachers as $teacher)
                @php
                    $teacherCode = 'GURU-' . ($teacher->nip ?: str_pad($teacher->id, 5, '0', STR_PAD_LEFT));
                @endphp
                <div class="rounded-3xl bg-white p-5 border border-slate-200/90 barcode-card-hover space-y-4 shadow-sm relative overflow-hidden">
                    
                    {{-- Teacher Header Info --}}
                    <div class="flex items-center gap-3">
                        <img src="{{ $teacher->user?->avatar_url ?: 'https://ui-avatars.com/api/?name=' . urlencode($teacher->name) . '&background=0284c7&color=fff' }}" 
                            class="h-12 w-12 rounded-2xl object-cover ring-2 ring-slate-100 shadow-sm shrink-0" alt="{{ $teacher->name }}">
                        <div class="min-w-0 flex-1">
                            <h6 class="text-sm font-black text-slate-900 truncate mb-0">{{ $teacher->name }}</h6>
                            <div class="flex items-center gap-2 text-xs text-slate-500 mt-0.5">
                                <span class="font-mono font-bold text-slate-600">NIP: {{ $teacher->nip ?: '-' }}</span>
                                <span>•</span>
                                <span class="rounded bg-sky-50 px-2 py-0.5 text-[10px] font-bold text-sky-700">
                                    Guru
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Barcode Display Area --}}
                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200/70 text-center space-y-3">
                        
                        {{-- 1D Barcode --}}
                        <div class="bg-white p-3 rounded-xl border border-slate-200 flex flex-col items-center justify-center">
                            <svg class="barcode-svg" jsbarcode-format="CODE128" jsbarcode-value="{{ $teacherCode }}" jsbarcode-textmargin="0" jsbarcode-fontoptions="bold" jsbarcode-height="45" jsbarcode-width="1.8" jsbarcode-fontsize="12"></svg>
                            <span class="text-[10px] font-mono text-slate-400 mt-1">Barcode Presensi Guru</span>
                        </div>

                        {{-- 2D QR Code --}}
                        <div class="bg-white p-3 rounded-xl border border-slate-200 flex items-center justify-center gap-4">
                            <div class="shrink-0 p-1 bg-white border border-slate-100 rounded-lg">
                                {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(80)->generate($teacherCode) !!}
                            </div>
                            <div class="text-left">
                                <span class="inline-block px-2 py-0.5 rounded bg-sky-50 text-sky-700 font-mono text-[10px] font-bold border border-sky-200 mb-1">
                                    QR ID Guru
                                </span>
                                <div class="text-xs font-mono font-black text-slate-800">{{ $teacherCode }}</div>
                                <span class="text-[10px] text-slate-400 block mt-0.5">Scan Presensi Kedatangan</span>
                            </div>
                        </div>

                    </div>

                </div>
            @empty
                <div class="col-span-full py-16 text-center bg-white rounded-3xl border border-slate-200 text-slate-400">
                    <i class="bi bi-inbox text-4xl text-slate-300 block mb-2"></i>
                    Tidak ada guru ditemukan.
                </div>
            @endforelse
        </div>
    @endif

</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof JsBarcode === 'function') {
            JsBarcode(".barcode-svg").init();
        }
    });
</script>
@endpush
@endsection
