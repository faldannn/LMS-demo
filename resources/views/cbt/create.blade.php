@extends('layouts.app')

@section('title', 'Buat Paket Ujian CBT Baru')

@section('content')
<div class="mx-auto max-w-4xl space-y-6 p-4 sm:p-6 lg:p-8">
    {{-- BREADCRUMB & HEADER --}}
    <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
        <a href="{{ route('cbt.index') }}" class="hover:text-emerald-600 transition">Ujian CBT</a>
        <i class="bi bi-chevron-right text-[10px]"></i>
        <span class="text-slate-900">Buat Paket Ujian Baru</span>
    </div>

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Buat Paket Ujian CBT</h1>
            <p class="mt-1 text-sm text-slate-500">Konfigurasi jadwal, durasi pengerjaan, KKM, dan proteksi token 5-menitan.</p>
        </div>
        <a href="{{ route('cbt.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition">
            <i class="bi bi-arrow-left"></i>
            <span>Kembali</span>
        </a>
    </div>

    @if($errors->any())
    <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">
        <div class="font-bold mb-1">Terdapat kesalahan input:</div>
        <ul class="list-disc list-inside text-xs space-y-0.5">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('cbt.store') }}" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                {{-- Kelas & Mata Pelajaran --}}
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        Target Kelas &amp; Mata Pelajaran <span class="text-rose-500">*</span>
                    </label>
                    <select name="class_subject_id" required class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-xs text-slate-900 focus:border-emerald-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-emerald-500">
                        <option value="">-- Pilih Kelas &amp; Mata Pelajaran --</option>
                        @foreach($classSubjects as $cs)
                            <option value="{{ $cs->id }}" @selected(old('class_subject_id') == $cs->id)>
                                {{ $cs->class?->name ?? 'Kelas' }} &mdash; {{ $cs->subject?->name ?? 'Mapel' }} ({{ $cs->teacher?->user?->name ?? $cs->teacher?->name ?? 'Guru Pengampu' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Judul Ujian --}}
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        Judul Paket Ujian <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="title" value="{{ old('title') }}" required placeholder="Contoh: Penilaian Akhir Semester (PAS) Matematika Peminatan" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-xs text-slate-900 focus:border-emerald-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-emerald-500">
                </div>

                {{-- Deskripsi / Petunjuk --}}
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        Petunjuk Pengerjaan / Deskripsi
                    </label>
                    <textarea name="description" rows="3" placeholder="Tuliskan petunjuk pengerjaan soal untuk siswa..." class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-xs text-slate-900 focus:border-emerald-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-emerald-500">{{ old('description') }}</textarea>
                </div>

                {{-- Durasi --}}
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        Durasi Ujian (Menit) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="number" name="duration" value="{{ old('duration', 90) }}" min="5" max="300" required class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-xs text-slate-900 focus:border-emerald-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-emerald-500">
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs text-slate-400 font-semibold">Menit</span>
                    </div>
                </div>

                {{-- KKM / Pass Score --}}
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        Nilai Ketuntasan Minimum (KKM) <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" name="pass_score" value="{{ old('pass_score', 75) }}" min="0" max="100" step="0.5" required class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-xs text-slate-900 focus:border-emerald-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-emerald-500">
                </div>

                {{-- Jadwal Mulai --}}
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        Waktu Mulai Ujian (Boleh Kosong)
                    </label>
                    <input type="datetime-local" name="start_time" value="{{ old('start_time') }}" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-xs text-slate-900 focus:border-emerald-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-emerald-500">
                    <span class="text-[10px] text-slate-400">Jika dikosongkan, ujian dapat dikerjakan kapan saja.</span>
                </div>

                {{-- Jadwal Selesai --}}
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        Batas Waktu Ditutup (Boleh Kosong)
                    </label>
                    <input type="datetime-local" name="end_time" value="{{ old('end_time') }}" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-xs text-slate-900 focus:border-emerald-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-emerald-500">
                    <span class="text-[10px] text-slate-400">Batas akhir siswa dapat memulai pengerjaan.</span>
                </div>
            </div>

            {{-- Opsi Pengaturan CBT --}}
            <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-4 space-y-3">
                <div class="text-xs font-bold uppercase tracking-wider text-slate-700">Fitur &amp; Keamanan Ujian CBT</div>

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <label class="flex items-start gap-3 rounded-xl border border-slate-200 bg-white p-3 cursor-pointer hover:border-emerald-300 transition">
                        <input type="checkbox" name="shuffle_questions" value="1" @checked(old('shuffle_questions', true)) class="mt-0.5 rounded text-emerald-600 focus:ring-emerald-500">
                        <div class="text-xs">
                            <span class="font-bold text-slate-800">Acak Urutan Soal</span>
                            <p class="text-[11px] text-slate-500 mt-0.5">Tiap siswa menerima urutan nomor soal yang berbeda untuk mencegah contek.</p>
                        </div>
                    </label>

                    <label class="flex items-start gap-3 rounded-xl border border-slate-200 bg-white p-3 cursor-pointer hover:border-emerald-300 transition">
                        <input type="checkbox" name="shuffle_answers" value="1" @checked(old('shuffle_answers', true)) class="mt-0.5 rounded text-emerald-600 focus:ring-emerald-500">
                        <div class="text-xs">
                            <span class="font-bold text-slate-800">Acak Opsi Pilihan (A/B/C/D/E)</span>
                            <p class="text-[11px] text-slate-500 mt-0.5">Pilihan ganda diacak secara unik pada setiap siswa.</p>
                        </div>
                    </label>

                    <label class="flex items-start gap-3 rounded-xl border border-slate-200 bg-white p-3 cursor-pointer hover:border-emerald-300 transition">
                        <input type="checkbox" name="show_results" value="1" @checked(old('show_results', true)) class="mt-0.5 rounded text-emerald-600 focus:ring-emerald-500">
                        <div class="text-xs">
                            <span class="font-bold text-slate-800">Tampilkan Nilai ke Siswa</span>
                            <p class="text-[11px] text-slate-500 mt-0.5">Siswa dapat langsung melihat skor setelah menyelesaikan ujian.</p>
                        </div>
                    </label>

                    <label class="flex items-start gap-3 rounded-xl border border-slate-200 bg-white p-3 cursor-pointer hover:border-emerald-300 transition">
                        <input type="checkbox" name="is_published" value="1" @checked(old('is_published', true)) class="mt-0.5 rounded text-emerald-600 focus:ring-emerald-500">
                        <div class="text-xs">
                            <span class="font-bold text-slate-800">Publikasikan Ujian Sekarang</span>
                            <p class="text-[11px] text-slate-500 mt-0.5">Ujian langsung muncul di portal pengerjaan CBT siswa.</p>
                        </div>
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('cbt.index') }}" class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-6 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-emerald-500 transition">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>Simpan &amp; Lanjut Tambah Soal</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
