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
        <form id="cbtCreateForm" method="POST" action="{{ route('cbt.store') }}" onsubmit="return validateCbtForm(event)" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                {{-- Mata Pelajaran & Guru Pengampu --}}
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Mata Pelajaran &amp; Guru Pengampu <span class="text-rose-500">*</span>
                    </label>
                    <p class="text-[11px] text-slate-500 mb-2">Pilih mata pelajaran kurikulum dan guru yang mengampu ujian ini.</p>
                    <select name="class_subject_id" id="classSubjectSelect" required onchange="handleClassSubjectChange(this)" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-xs text-slate-900 focus:border-emerald-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-emerald-500">
                        <option value="">-- Pilih Mata Pelajaran &amp; Guru --</option>
                        @foreach($classSubjects as $cs)
                            <option value="{{ $cs->id }}" data-class-id="{{ $cs->class_id }}" data-grade="{{ $cs->class?->grade }}" @selected(old('class_subject_id') == $cs->id)>
                                {{ $cs->subject?->name ?? 'Mapel' }} &mdash; {{ $cs->teacher?->user?->name ?? $cs->teacher?->name ?? 'Guru Pengampu' }} (Basis Referensi: {{ $cs->class?->name ?? 'Kelas' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- MULTI-SELECT TARGET KELAS (CHECKBOX DENGAN FITUR SEARCH & SELECT ALL PER TINGKAT) --}}
                <div class="sm:col-span-2 space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                Target Kelas yang Mengikuti Ujian <span class="text-rose-500">*</span>
                            </label>
                            <p class="text-[11px] text-slate-500">Centang satu atau beberapa kelas (contoh: seluruh kelas di tingkat 10). Siswa dari kelas terpilih akan dapat mengerjakan ujian dengan token CBT yang sama.</p>
                        </div>
                        <div id="selectionSummaryBadge" class="self-start sm:self-auto inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700 border border-emerald-200 transition">
                            <i class="bi bi-check2-circle text-emerald-600"></i>
                            <span id="selectedCountText">0 Kelas Dipilih</span>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-4 space-y-4">
                        {{-- Toolbar: Search & Action Buttons --}}
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-200/80">
                            {{-- Search Box --}}
                            <div class="relative flex-1">
                                <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                <input type="text" id="classSearchInput" placeholder="Cari nama kelas (misal: X MIPA, IPS, dll)..." oninput="filterClasses(this.value)" class="w-full rounded-xl border border-slate-200 bg-white pl-9 pr-3 py-2 text-xs text-slate-800 placeholder-slate-400 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                            </div>

                            {{-- Global Toggle Buttons --}}
                            <div class="flex items-center gap-2 shrink-0">
                                <button type="button" onclick="selectAllClasses(true)" class="inline-flex items-center gap-1.5 rounded-xl bg-white border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100 hover:text-emerald-700 transition shadow-2xs">
                                    <i class="bi bi-check-all text-emerald-600 text-sm"></i>
                                    <span>Pilih Semua Kelas</span>
                                </button>
                                <button type="button" onclick="selectAllClasses(false)" class="inline-flex items-center gap-1.5 rounded-xl bg-white border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-500 hover:bg-slate-100 hover:text-rose-600 transition shadow-2xs">
                                    <i class="bi bi-x-circle text-rose-500"></i>
                                    <span>Kosongkan</span>
                                </button>
                            </div>
                        </div>

                        {{-- Grouped by Grade --}}
                        <div class="space-y-3.5" id="classGroupsContainer">
                            @foreach($allClasses as $grade => $classesInGrade)
                            <div class="grade-group rounded-xl border border-slate-200/80 bg-white p-3.5 shadow-2xs transition" data-grade="{{ $grade }}">
                                <div class="flex flex-wrap items-center justify-between gap-2 pb-2.5 mb-2.5 border-b border-slate-100">
                                    <div class="flex items-center gap-2">
                                        <span class="rounded-lg bg-emerald-100 px-2.5 py-0.5 text-xs font-bold text-emerald-800">
                                            Tingkat {{ $grade }} (Kelas {{ match((int)$grade) { 10 => 'X', 11 => 'XI', 12 => 'XII', default => (string)$grade } }})
                                        </span>
                                        <span class="text-xs text-slate-400 font-medium">({{ $classesInGrade->count() }} Kelas Tersedia)</span>
                                    </div>
                                    <button type="button" onclick="toggleGradeClasses({{ $grade }})" class="inline-flex items-center gap-1.5 rounded-lg bg-slate-50 px-2.5 py-1 text-[11px] font-bold text-emerald-700 hover:bg-emerald-100/70 border border-slate-200 transition">
                                        <i class="bi bi-check2-square"></i>
                                        <span>Pilih Semua Tingkat {{ $grade }}</span>
                                    </button>
                                </div>

                                {{-- Grid Card Kelas --}}
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5">
                                    @foreach($classesInGrade as $class)
                                    <label class="class-card relative flex items-center gap-3 rounded-xl border border-slate-200 p-2.5 cursor-pointer hover:border-emerald-300 transition select-none has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50/40 has-[:checked]:shadow-2xs" data-name="{{ strtolower($class->name) }}" data-grade="{{ $grade }}">
                                        <input type="checkbox" name="class_ids[]" value="{{ $class->id }}" @checked(is_array(old('class_ids')) ? in_array($class->id, old('class_ids')) : false) class="class-checkbox h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" onchange="updateSelectedCount()">
                                        <div class="min-w-0 flex-1">
                                            <div class="text-xs font-bold text-slate-800">{{ $class->name }}</div>
                                            <div class="text-[10px] text-slate-500 flex items-center gap-2 mt-0.5">
                                                <span>{{ ucfirst($class->session) }}</span>
                                                <span>&bull;</span>
                                                <span>{{ $class->room ?: 'Ruang -' }}</span>
                                            </div>
                                        </div>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>

                        {{-- Alert Jika Tidak Ada Hasil Pencarian --}}
                        <div id="noSearchMatchAlert" class="hidden text-center py-6 text-xs text-slate-500">
                            <i class="bi bi-search text-lg text-slate-400 block mb-1"></i>
                            Tidak ada kelas yang cocok dengan kata kunci pencarian.
                        </div>

                        {{-- Alert Validasi Checkbox Minimal 1 --}}
                        <div id="noClassWarning" class="hidden rounded-xl border border-rose-200 bg-rose-50 p-3 text-xs text-rose-800 flex items-center gap-2">
                            <i class="bi bi-exclamation-triangle-fill text-rose-600"></i>
                            <span>Harap centang minimal satu target kelas untuk ujian CBT ini.</span>
                        </div>
                    </div>
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

@push('scripts')
<script>
    function updateSelectedCount() {
        const checkboxes = document.querySelectorAll('input.class-checkbox:checked');
        const count = checkboxes.length;
        const countText = document.getElementById('selectedCountText');
        const badge = document.getElementById('selectionSummaryBadge');
        const warning = document.getElementById('noClassWarning');

        if (countText) {
            countText.innerText = count + (count === 1 ? ' Kelas Dipilih' : ' Kelas Dipilih');
        }

        if (badge) {
            if (count > 0) {
                badge.className = 'self-start sm:self-auto inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700 border border-emerald-200 transition';
                if (warning) warning.classList.add('hidden');
            } else {
                badge.className = 'self-start sm:self-auto inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-500 border border-slate-200 transition';
            }
        }
    }

    function selectAllClasses(checked) {
        const cards = document.querySelectorAll('.class-card');
        cards.forEach(card => {
            if (!card.classList.contains('hidden')) {
                const cb = card.querySelector('input.class-checkbox');
                if (cb) cb.checked = checked;
            }
        });
        updateSelectedCount();
    }

    function toggleGradeClasses(grade) {
        const group = document.querySelector(`.grade-group[data-grade="${grade}"]`);
        if (!group) return;

        const visibleCheckboxes = Array.from(group.querySelectorAll('.class-card:not(.hidden) input.class-checkbox'));
        if (visibleCheckboxes.length === 0) return;

        // Jika sebagian besar belum dicentang, centang semua. Jika sudah semua dicentang, kosongkan.
        const allChecked = visibleCheckboxes.every(cb => cb.checked);
        visibleCheckboxes.forEach(cb => {
            cb.checked = !allChecked;
        });

        updateSelectedCount();
    }

    function filterClasses(rawQuery) {
        const query = (rawQuery || '').toLowerCase().trim();
        const groups = document.querySelectorAll('.grade-group');
        let totalVisible = 0;

        groups.forEach(group => {
            const cards = group.querySelectorAll('.class-card');
            let groupVisibleCount = 0;

            cards.forEach(card => {
                const name = card.getAttribute('data-name') || '';
                if (!query || name.includes(query)) {
                    card.classList.remove('hidden');
                    groupVisibleCount++;
                    totalVisible++;
                } else {
                    card.classList.add('hidden');
                }
            });

            if (groupVisibleCount > 0) {
                group.classList.remove('hidden');
            } else {
                group.classList.add('hidden');
            }
        });

        const alertEl = document.getElementById('noSearchMatchAlert');
        if (alertEl) {
            if (totalVisible === 0) {
                alertEl.classList.remove('hidden');
            } else {
                alertEl.classList.add('hidden');
            }
        }
    }

    function handleClassSubjectChange(select) {
        const selectedOption = select.options[select.selectedIndex];
        if (!selectedOption || !selectedOption.value) return;

        const classId = selectedOption.getAttribute('data-class-id');
        if (classId) {
            const checkbox = document.querySelector(`input.class-checkbox[value="${classId}"]`);
            // Jika belum ada kelas yang terpilih sama sekali, centang kelas basis referensi
            const anyChecked = document.querySelectorAll('input.class-checkbox:checked').length > 0;
            if (!anyChecked && checkbox) {
                checkbox.checked = true;
                updateSelectedCount();
            }
        }
    }

    function validateCbtForm(e) {
        const checked = document.querySelectorAll('input.class-checkbox:checked');
        const warning = document.getElementById('noClassWarning');

        if (checked.length === 0) {
            if (warning) {
                warning.classList.remove('hidden');
                warning.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            alert('Harap centang minimal satu target kelas yang akan mengikuti ujian CBT.');
            if (e) e.preventDefault();
            return false;
        }

        return true;
    }

    // Jalankan kalkulasi jumlah saat halaman selesai dimuat
    document.addEventListener('DOMContentLoaded', function () {
        updateSelectedCount();
    });
</script>
@endpush
