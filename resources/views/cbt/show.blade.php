@extends('layouts.app')

@section('title', $quiz->title . ' — Kelola Soal CBT')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/katex.min.css">
<link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Scheherazade+New:wght@400;700&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
<style>
    .math-content {
        line-height: 1.8;
    }
    .math-content .katex {
        font-size: 1.1em;
    }
    .font-arabic {
        font-family: 'Amiri', 'Scheherazade New', serif;
        font-size: 1.35rem;
        line-height: 2.2;
        direction: rtl;
        text-align: right;
    }
    .quick-symbol-btn {
        padding: 4px 8px;
        font-size: 11px;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .quick-symbol-btn:hover {
        background: #e2e8f0;
        border-color: #cbd5e1;
    }
</style>
@endpush

@section('content')
<div class="space-y-6 p-4 sm:p-6 lg:p-8">
    {{-- BREADCRUMB --}}
    <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
        <a href="{{ route('cbt.index') }}" class="hover:text-emerald-600 transition">Ujian CBT</a>
        <i class="bi bi-chevron-right text-[10px]"></i>
        <span class="text-slate-900 truncate max-w-xs">{{ $quiz->title }}</span>
    </div>

    {{-- HEADER UJIAN --}}
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <div class="flex items-center gap-2">
                <span class="rounded-lg bg-emerald-100 px-2.5 py-0.5 text-xs font-bold text-emerald-800">
                    {{ $quiz->classSubject?->class?->name ?? 'Kelas' }} &bull; {{ $quiz->classSubject?->subject?->name ?? 'Mapel' }}
                </span>
                @if($quiz->is_published)
                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-[11px] font-semibold text-emerald-700 border border-emerald-200">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Aktif
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-600 border border-slate-200">
                        Draft
                    </span>
                @endif
            </div>
            <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">{{ $quiz->title }}</h1>
            <p class="mt-1 text-xs text-slate-500 sm:text-sm">
                Guru Pengampu: <strong>{{ $quiz->classSubject?->teacher?->user?->name ?? '-' }}</strong> &bull; Durasi: <strong>{{ $quiz->duration }} Menit</strong> &bull; KKM: <strong>{{ $quiz->pass_score }}</strong>
            </p>
        </div>

        {{-- ACTION BUTTONS --}}
        <div class="flex flex-wrap items-center gap-2">
            <button type="button" onclick="openImportModal()" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition">
                <i class="bi bi-cloud-arrow-up text-emerald-600 text-sm"></i>
                <span>Import Soal (Excel/Word)</span>
            </button>
            <button type="button" onclick="openAddQuestionModal()" class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 px-4 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-emerald-500 transition">
                <i class="bi bi-plus-circle text-sm"></i>
                <span>Tambah Soal Manual</span>
            </button>
            <a href="{{ route('cbt.results', $quiz->id) }}" class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-3.5 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-blue-500 transition">
                <i class="bi bi-bar-chart-fill text-sm"></i>
                <span>Rekap Nilai Siswa</span>
            </a>
        </div>
    </div>

    {{-- ALERT NOTIFIKASI --}}
    @if(session('success'))
    <div class="flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50/80 p-4 text-sm text-emerald-800 shadow-sm">
        <i class="bi bi-check-circle-fill text-xl text-emerald-600 shrink-0"></i>
        <div class="flex-1 font-medium">{{ session('success') }}</div>
    </div>
    @endif

    @if(session('warning'))
    <div class="flex items-center gap-3 rounded-2xl border border-amber-200 bg-amber-50/80 p-4 text-sm text-amber-800 shadow-sm">
        <i class="bi bi-exclamation-triangle-fill text-xl text-amber-600 shrink-0"></i>
        <div class="flex-1 font-medium">{{ session('warning') }}</div>
    </div>
    @endif

    @if(session('error'))
    <div class="flex items-center gap-3 rounded-2xl border border-rose-200 bg-rose-50/80 p-4 text-sm text-rose-800 shadow-sm">
        <i class="bi bi-x-circle-fill text-xl text-rose-600 shrink-0"></i>
        <div class="flex-1 font-medium">{{ session('error') }}</div>
    </div>
    @endif

    {{-- MINI BANNER TOKEN KHUSUS UJIAN INI --}}
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 rounded-2xl border border-emerald-200 bg-gradient-to-r from-emerald-50 via-teal-50 to-blue-50 p-4">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-600 text-white font-bold text-lg shadow-sm">
                <i class="bi bi-key-fill"></i>
            </div>
            <div>
                <div class="text-xs font-bold text-slate-800">Token Ujian Siswa (Rotasi 5 Menit)</div>
                <div class="text-[11px] text-slate-500">Berikan token ini kepada siswa di kelas saat sesi ujian dimulai.</div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <div class="rounded-xl border border-emerald-300 bg-white px-4 py-2 text-center shadow-sm">
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Token Aktif</span>
                <span id="quizTokenDisplay" class="font-mono text-xl font-extrabold text-emerald-700 tracking-wider">
                    {{ $tokenInfo['token'] }}
                </span>
            </div>
            <div class="text-xs text-slate-500">
                <span>Sisa: <strong id="quizCountdownText" class="text-emerald-600 font-mono">--:--</strong></span>
            </div>
            <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('quizTokenDisplay').textContent.trim()); alert('Token disalin!');" class="rounded-xl border border-slate-200 bg-white p-2.5 text-slate-600 hover:bg-slate-100 transition shadow-sm" title="Salin Token">
                <i class="bi bi-clipboard"></i>
            </button>
        </div>
    </div>

    {{-- INFO DAFTAR SOAL --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="border-b border-slate-200 px-6 py-4 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Daftar Butir Soal Ujian</h3>
                <p class="text-xs text-slate-500">Total: <strong>{{ $quiz->questions->count() }} Soal</strong> &bull; Total Poin: <strong>{{ $quiz->total_points }}</strong></p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('cbt.template.download', 'excel') }}" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50" title="Download Template Excel">
                    <i class="bi bi-file-earmark-excel text-emerald-600"></i>
                    <span>Template Excel</span>
                </a>
                <a href="{{ route('cbt.template.download', 'word') }}" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50" title="Download Template Word">
                    <i class="bi bi-file-earmark-word text-blue-600"></i>
                    <span>Template Word</span>
                </a>
            </div>
        </div>

        @if($quiz->questions->isEmpty())
        <div class="p-12 text-center">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 mb-3">
                <i class="bi bi-card-text text-2xl"></i>
            </div>
            <h4 class="text-base font-bold text-slate-800">Belum Ada Soal</h4>
            <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1">
                Ujian ini belum memiliki butir soal. Anda dapat menambahkan soal secara manual atau mengimpor sekaligus dari file Excel/Word.
            </p>
            <div class="mt-4 flex flex-wrap justify-center gap-2">
                <button type="button" onclick="openImportModal()" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 shadow-sm hover:bg-slate-50">
                    <i class="bi bi-cloud-arrow-up text-emerald-600"></i>
                    <span>Import File Excel / Word</span>
                </button>
                <button type="button" onclick="openAddQuestionModal()" class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-emerald-500">
                    <i class="bi bi-plus-circle"></i>
                    <span>Tambah Soal Manual</span>
                </button>
            </div>
        </div>
        @else
        <div class="divide-y divide-slate-100 p-6 space-y-6">
            @foreach($quiz->questions as $index => $q)
            <div class="group rounded-2xl border border-slate-200/80 bg-slate-50/40 p-5 hover:border-emerald-200 hover:bg-slate-50 transition">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-emerald-600 text-xs font-bold text-white shadow-sm">
                            {{ $index + 1 }}
                        </span>
                        @if($q->type === 'complex_true_false')
                            <span class="rounded-md bg-purple-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-purple-800 border border-purple-200">
                                <i class="bi bi-ui-checks"></i> Kompleks Benar / Salah
                            </span>
                        @elseif($q->type === 'essay')
                            <span class="rounded-md bg-amber-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-amber-800 border border-amber-200">
                                Essay / Uraian
                            </span>
                        @else
                            <span class="rounded-md bg-blue-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-blue-800 border border-blue-200">
                                Pilihan Ganda
                            </span>
                        @endif
                        <span class="text-xs font-semibold text-slate-500">
                            Bobot: {{ $q->points }} Poin
                        </span>
                    </div>

                    <div class="flex items-center gap-1">
                        <button type="button" onclick="openEditQuestionModal({{ $q->id }})" class="rounded-lg p-1.5 text-slate-400 hover:bg-emerald-50 hover:text-emerald-600 transition" title="Edit Soal">
                            <i class="bi bi-pencil-square text-base"></i>
                        </button>
                        <form method="POST" action="{{ route('cbt.questions.destroy', [$quiz->id, $q->id]) }}" onsubmit="return confirm('Hapus butir soal nomor {{ $index + 1 }}?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded-lg p-1.5 text-slate-400 hover:bg-rose-50 hover:text-rose-600 transition" title="Hapus Soal">
                                <i class="bi bi-trash text-base"></i>
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Gambar Soal Jika Ada --}}
                @if($q->image_path)
                <div class="mt-4">
                    <div class="inline-block relative rounded-2xl overflow-hidden border border-slate-200 bg-white p-1 shadow-xs group/img">
                        <img src="{{ str_starts_with($q->image_path, 'data:') || str_starts_with($q->image_path, 'http') ? $q->image_path : asset($q->image_path) }}" alt="Gambar Soal {{ $index + 1 }}" class="max-h-64 max-w-full rounded-xl object-contain cursor-pointer hover:opacity-95 transition" onclick="openImageLightbox(this.src)">
                        <div class="absolute bottom-2 right-2 bg-slate-900/70 text-white rounded-lg px-2 py-1 text-[10px] font-semibold flex items-center gap-1 backdrop-blur-xs pointer-events-none">
                            <i class="bi bi-zoom-in"></i> Klik untuk perbesar
                        </div>
                    </div>
                </div>
                @endif

                {{-- Teks Soal (Render KaTeX Math & Arab) --}}
                <div class="mt-3 text-sm text-slate-800 math-content whitespace-pre-line font-medium leading-relaxed">
                    {!! nl2br(e($q->question_text)) !!}
                </div>

                {{-- Opsi Pilihan Jawaban (Jika Pilihan Ganda Biasa) --}}
                @if($q->type === 'multiple_choice' && $q->options->isNotEmpty())
                <div class="mt-4 grid grid-cols-1 gap-2 sm:grid-cols-2">
                    @php $labels = ['A', 'B', 'C', 'D', 'E']; @endphp
                    @foreach($q->options as $optIdx => $opt)
                    <div class="flex items-start gap-2.5 rounded-xl border p-2.5 text-xs transition {{ $opt->is_correct ? 'border-emerald-300 bg-emerald-50/70 text-emerald-900 font-semibold shadow-xs' : 'border-slate-200 bg-white text-slate-700' }}">
                        <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-md font-bold text-[11px] {{ $opt->is_correct ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-600' }}">
                            {{ $labels[$optIdx] ?? ($optIdx + 1) }}
                        </span>
                        <div class="flex-1 math-content">
                            {!! nl2br(e($opt->option_text)) !!}
                        </div>
                        @if($opt->is_correct)
                            <span class="text-[10px] font-bold text-emerald-700 uppercase tracking-wider shrink-0 bg-emerald-100 px-1.5 py-0.5 rounded">
                                <i class="bi bi-check-lg"></i> Kunci
                            </span>
                        @endif
                    </div>
                    @endforeach
                </div>
                {{-- Opsi Pilihan Ganda Kompleks (Benar / Salah) --}}
                @elseif($q->type === 'complex_true_false' && $q->options->isNotEmpty())
                <div class="mt-4 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xs">
                    <div class="bg-gradient-to-r from-purple-50 to-indigo-50 px-4 py-2.5 border-b border-purple-100 flex items-center justify-between">
                        <span class="text-xs font-bold text-purple-900 flex items-center gap-1.5">
                            <i class="bi bi-ui-checks"></i> Tabel Pernyataan &amp; Kunci Jawaban
                        </span>
                        <span class="text-[11px] text-purple-700 font-medium">Model Benar / Salah</span>
                    </div>
                    <div class="divide-y divide-slate-100">
                        @foreach($q->options as $optIdx => $opt)
                        <div class="flex items-center justify-between gap-4 px-4 py-3 hover:bg-slate-50/70 transition">
                            <div class="flex items-start gap-3 flex-1">
                                <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-xs font-bold text-slate-600">
                                    {{ $optIdx + 1 }}
                                </span>
                                <div class="math-content text-xs font-medium text-slate-800 pt-0.5">
                                    {!! nl2br(e($opt->option_text)) !!}
                                </div>
                            </div>
                            <div class="shrink-0">
                                @if($opt->is_correct)
                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-800 border border-emerald-200">
                                    <i class="bi bi-check-circle-fill text-emerald-600"></i> BENAR
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1 rounded-full bg-rose-100 px-3 py-1 text-xs font-bold text-rose-800 border border-rose-200">
                                    <i class="bi bi-x-circle-fill text-rose-600"></i> SALAH
                                </span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @elseif($q->type === 'essay')
                <div class="mt-3 rounded-xl bg-slate-100/70 p-3 text-xs text-slate-500 italic">
                    <i class="bi bi-info-circle mr-1"></i> Soal Uraian / Essay dinilai secara manual oleh guru melalui rekap nilai ujian.
                </div>
                @endif
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

{{-- MODAL TAMBAH SOAL MANUAL --}}
<div id="addQuestionModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm overflow-y-auto p-4 flex items-center justify-center">
    <div class="relative w-full max-w-3xl rounded-3xl bg-white shadow-2xl overflow-hidden my-8">
        <div class="border-b border-slate-100 px-6 py-4 flex items-center justify-between bg-slate-50">
            <div>
                <h3 class="text-base font-bold text-slate-900">Tambah Butir Soal Baru</h3>
                <p class="text-xs text-slate-500">Mendukung Gambar, KaTeX Matematika, Kimia, Fisika, Arab, dan Kompleks Benar/Salah</p>
            </div>
            <button type="button" onclick="closeAddQuestionModal()" class="rounded-xl p-2 text-slate-400 hover:bg-slate-200 transition">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('cbt.questions.store', $quiz->id) }}" enctype="multipart/form-data" class="p-6 space-y-5">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Tipe Soal</label>
                    <select name="type" id="questionTypeSelect" onchange="toggleOptionInputs()" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-medium focus:bg-white focus:outline-none focus:ring-1 focus:ring-emerald-500">
                        <option value="multiple_choice">Pilihan Ganda Biasa (A, B, C, D, E)</option>
                        <option value="complex_true_false">Pilihan Ganda Kompleks (Benar / Salah)</option>
                        <option value="essay">Essay / Uraian</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Bobot Nilai</label>
                    <input type="number" name="points" value="1" min="1" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-medium focus:bg-white focus:outline-none focus:ring-1 focus:ring-emerald-500">
                </div>
            </div>

            {{-- UPLOAD GAMBAR SOAL DENGAN LIVE PREVIEW --}}
            <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4 space-y-3">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                    <i class="bi bi-image text-emerald-600 mr-1"></i> Gambar Soal (Opsional)
                </label>
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                    <input type="file" name="image" id="addQuestionImageInput" accept="image/*" onchange="previewQuestionImage(this, 'addQuestionImagePreviewContainer', 'addQuestionImagePreview')" class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-100 file:text-emerald-700 hover:file:bg-emerald-200 cursor-pointer">
                </div>
                <div id="addQuestionImagePreviewContainer" class="hidden relative inline-block rounded-xl border border-slate-200 bg-white p-1">
                    <img id="addQuestionImagePreview" src="" alt="Preview Gambar" class="max-h-48 rounded-lg object-contain">
                    <button type="button" onclick="clearQuestionImageInput('addQuestionImageInput', 'addQuestionImagePreviewContainer')" class="absolute -top-2 -right-2 bg-rose-500 text-white rounded-full h-6 w-6 flex items-center justify-center text-xs shadow-md hover:bg-rose-600">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
            </div>

            {{-- TOOLBAR CEPAT RUMUS & ARAB --}}
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                <div class="text-[11px] font-bold text-slate-600 mb-2 flex items-center gap-1.5">
                    <i class="bi bi-magic text-emerald-600"></i>
                    <span>Quick Formula Toolbar (Klik untuk menyisipkan rumus):</span>
                </div>
                <div class="flex flex-wrap gap-1.5">
                    <button type="button" class="quick-symbol-btn" onclick="insertSymbol('$\\frac{a}{b}$', 'questionTextArea')">Pecahan $\frac{a}{b}$</button>
                    <button type="button" class="quick-symbol-btn" onclick="insertSymbol('$\\sqrt{x}$', 'questionTextArea')">Akar $\sqrt{x}$</button>
                    <button type="button" class="quick-symbol-btn" onclick="insertSymbol('$x^2$', 'questionTextArea')">Pangkat $x^2$</button>
                    <button type="button" class="quick-symbol-btn" onclick="insertSymbol('$x_1$', 'questionTextArea')">Indeks $x_1$</button>
                    <button type="button" class="quick-symbol-btn" onclick="insertSymbol('$\\pi$', 'questionTextArea')">$\pi$</button>
                    <button type="button" class="quick-symbol-btn" onclick="insertSymbol('$\\sum_{i=1}^{n}$', 'questionTextArea')">$\sum$ Sigma</button>
                    <button type="button" class="quick-symbol-btn" onclick="insertSymbol('$\\int_a^b f(x)dx$', 'questionTextArea')">$\int$ Integral</button>
                    <button type="button" class="quick-symbol-btn" onclick="insertSymbol('$\\text{H}_2\\text{O}$', 'questionTextArea')">Kimia $\text{H}_2\text{O}$</button>
                    <button type="button" class="quick-symbol-btn" onclick="insertSymbol('$2\\text{H}_2 + \\text{O}_2 \\rightarrow 2\\text{H}_2\\text{O}$', 'questionTextArea')">Reaksi $\rightarrow$</button>
                    <button type="button" class="quick-symbol-btn" onclick="insertSymbol('بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ', 'questionTextArea')">Teks Arab</button>
                </div>
            </div>

            {{-- TEXTAREA PERTANYAAN --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Isi Pertanyaan Soal <span class="text-rose-500">*</span>
                </label>
                <textarea id="questionTextArea" name="question_text" rows="4" required oninput="renderLivePreview('questionTextArea', 'livePreviewContainer')" placeholder="Ketik atau paste soal dari Word/PDF di sini. Untuk rumus matematika gunakan tanda dollar, contoh: $\lim_{x \to 0} \frac{\sin x}{x}$..." class="w-full rounded-xl border border-slate-200 bg-slate-50 p-3 text-xs text-slate-900 focus:bg-white focus:outline-none focus:ring-1 focus:ring-emerald-500"></textarea>
            </div>

            {{-- LIVE PREVIEW BOX --}}
            <div class="rounded-xl border border-dashed border-emerald-300 bg-emerald-50/40 p-3.5">
                <div class="text-[11px] font-bold uppercase tracking-wider text-emerald-800 mb-1 flex items-center gap-1.5">
                    <i class="bi bi-eye-fill"></i>
                    <span>Live Preview (Tampilan KaTeX &amp; Font Arab):</span>
                </div>
                <div id="livePreviewContainer" class="math-content text-xs text-slate-800 min-h-[40px] italic text-slate-400">
                    Preview tampilan rumus dan teks Arab akan muncul otomatis di sini...
                </div>
            </div>

            {{-- INPUT OPSI PILIHAN GANDA BIASA (A S/D E) --}}
            <div id="multipleChoiceContainer" class="space-y-3">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                    Pilihan Jawaban &amp; Kunci Jawaban
                </label>
                @php $opsiLabels = ['A', 'B', 'C', 'D', 'E']; @endphp
                @foreach($opsiLabels as $opt)
                <div class="flex items-center gap-2">
                    <label class="flex items-center gap-1.5 rounded-lg border border-slate-200 bg-slate-100 px-2.5 py-2 text-xs font-bold text-slate-700 shrink-0 cursor-pointer" title="Jadikan Kunci Jawaban">
                        <input type="radio" name="correct_option" value="{{ $opt }}" @checked($opt === 'A') class="text-emerald-600 focus:ring-emerald-500">
                        <span>{{ $opt }}</span>
                    </label>
                    <input type="text" name="options[{{ $opt }}]" placeholder="Teks pilihan {{ $opt }} (bisa rumus $...$)..." class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-900 focus:bg-white focus:outline-none focus:ring-1 focus:ring-emerald-500">
                </div>
                @endforeach
            </div>

            {{-- INPUT OPSI KOMPLEKS BENAR / SALAH --}}
            <div id="complexTrueFalseContainer" class="space-y-3 hidden">
                <div class="flex items-center justify-between">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            Pernyataan &amp; Kunci Jawaban (Benar / Salah)
                        </label>
                        <p class="text-[11px] text-slate-500">Tentukan apakah setiap baris pernyataan bernilai Benar atau Salah.</p>
                    </div>
                    <button type="button" onclick="addStatementRow('statementsListAdd')" class="inline-flex items-center gap-1 rounded-lg bg-purple-50 border border-purple-200 px-2.5 py-1 text-xs font-semibold text-purple-700 hover:bg-purple-100 transition">
                        <i class="bi bi-plus-lg"></i> Tambah Baris Pernyataan
                    </button>
                </div>

                <div id="statementsListAdd" class="space-y-2.5">
                    {{-- Row 1 --}}
                    <div class="statement-row flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50/50 p-2.5">
                        <span class="statement-idx text-xs font-bold text-slate-400 w-5 text-center">1</span>
                        <input type="text" name="statements[0]" required placeholder="Ketik teks pernyataan 1 di sini..." class="w-full rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs text-slate-900 focus:outline-none focus:ring-1 focus:ring-purple-500">
                        <div class="flex items-center gap-1 shrink-0">
                            <label class="flex items-center gap-1 rounded-lg border border-emerald-200 bg-emerald-50 px-2 py-1 text-xs font-bold text-emerald-800 cursor-pointer">
                                <input type="radio" name="statement_answers[0]" value="true" checked class="text-emerald-600 focus:ring-emerald-500">
                                <span>Benar</span>
                            </label>
                            <label class="flex items-center gap-1 rounded-lg border border-rose-200 bg-rose-50 px-2 py-1 text-xs font-bold text-rose-800 cursor-pointer">
                                <input type="radio" name="statement_answers[0]" value="false" class="text-rose-600 focus:ring-rose-500">
                                <span>Salah</span>
                            </label>
                        </div>
                    </div>
                    {{-- Row 2 --}}
                    <div class="statement-row flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50/50 p-2.5">
                        <span class="statement-idx text-xs font-bold text-slate-400 w-5 text-center">2</span>
                        <input type="text" name="statements[1]" required placeholder="Ketik teks pernyataan 2 di sini..." class="w-full rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs text-slate-900 focus:outline-none focus:ring-1 focus:ring-purple-500">
                        <div class="flex items-center gap-1 shrink-0">
                            <label class="flex items-center gap-1 rounded-lg border border-emerald-200 bg-emerald-50 px-2 py-1 text-xs font-bold text-emerald-800 cursor-pointer">
                                <input type="radio" name="statement_answers[1]" value="true" checked class="text-emerald-600 focus:ring-emerald-500">
                                <span>Benar</span>
                            </label>
                            <label class="flex items-center gap-1 rounded-lg border border-rose-200 bg-rose-50 px-2 py-1 text-xs font-bold text-rose-800 cursor-pointer">
                                <input type="radio" name="statement_answers[1]" value="false" class="text-rose-600 focus:ring-rose-500">
                                <span>Salah</span>
                            </label>
                        </div>
                        <button type="button" onclick="removeStatementRow(this)" class="p-1 text-slate-400 hover:text-rose-600">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                    {{-- Row 3 --}}
                    <div class="statement-row flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50/50 p-2.5">
                        <span class="statement-idx text-xs font-bold text-slate-400 w-5 text-center">3</span>
                        <input type="text" name="statements[2]" required placeholder="Ketik teks pernyataan 3 di sini..." class="w-full rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs text-slate-900 focus:outline-none focus:ring-1 focus:ring-purple-500">
                        <div class="flex items-center gap-1 shrink-0">
                            <label class="flex items-center gap-1 rounded-lg border border-emerald-200 bg-emerald-50 px-2 py-1 text-xs font-bold text-emerald-800 cursor-pointer">
                                <input type="radio" name="statement_answers[2]" value="true" class="text-emerald-600 focus:ring-emerald-500">
                                <span>Benar</span>
                            </label>
                            <label class="flex items-center gap-1 rounded-lg border border-rose-200 bg-rose-50 px-2 py-1 text-xs font-bold text-rose-800 cursor-pointer">
                                <input type="radio" name="statement_answers[2]" value="false" checked class="text-rose-600 focus:ring-rose-500">
                                <span>Salah</span>
                            </label>
                        </div>
                        <button type="button" onclick="removeStatementRow(this)" class="p-1 text-slate-400 hover:text-rose-600">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeAddQuestionModal()" class="rounded-xl border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 transition">
                    Batal
                </button>
                <button type="submit" class="rounded-xl bg-emerald-600 px-5 py-2 text-xs font-semibold text-white shadow-sm hover:bg-emerald-500 transition">
                    Simpan Soal
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDIT SOAL (UPDATE) --}}
<div id="editQuestionModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm overflow-y-auto p-4 flex items-center justify-center">
    <div class="relative w-full max-w-3xl rounded-3xl bg-white shadow-2xl overflow-hidden my-8">
        <div class="border-b border-slate-100 px-6 py-4 flex items-center justify-between bg-slate-50">
            <div>
                <h3 class="text-base font-bold text-slate-900">Edit Butir Soal Ujian</h3>
                <p class="text-xs text-slate-500">Perbarui teks soal, gambar, bobot, atau kunci jawaban</p>
            </div>
            <button type="button" onclick="closeEditQuestionModal()" class="rounded-xl p-2 text-slate-400 hover:bg-slate-200 transition">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form id="editQuestionForm" method="POST" action="" enctype="multipart/form-data" class="p-6 space-y-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Tipe Soal</label>
                    <select name="type" id="editQuestionTypeSelect" onchange="toggleEditOptionInputs()" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-medium focus:bg-white focus:outline-none focus:ring-1 focus:ring-emerald-500">
                        <option value="multiple_choice">Pilihan Ganda Biasa (A, B, C, D, E)</option>
                        <option value="complex_true_false">Pilihan Ganda Kompleks (Benar / Salah)</option>
                        <option value="essay">Essay / Uraian</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Bobot Nilai</label>
                    <input type="number" name="points" id="editQuestionPoints" value="1" min="1" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-medium focus:bg-white focus:outline-none focus:ring-1 focus:ring-emerald-500">
                </div>
            </div>

            {{-- UPLOAD & PREVIEW GAMBAR SOAL --}}
            <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4 space-y-3">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                    <i class="bi bi-image text-emerald-600 mr-1"></i> Gambar Soal
                </label>

                {{-- Preview Gambar Saat Ini --}}
                <div id="editExistingImageWrapper" class="hidden">
                    <div class="text-[11px] font-semibold text-slate-500 mb-1">Gambar saat ini:</div>
                    <div class="relative inline-block rounded-xl border border-slate-200 bg-white p-1">
                        <img id="editExistingImage" src="" alt="Gambar Soal" class="max-h-48 rounded-lg object-contain">
                    </div>
                    <div class="mt-2">
                        <label class="inline-flex items-center gap-1.5 text-xs text-rose-600 font-semibold cursor-pointer">
                            <input type="checkbox" name="remove_image" id="editRemoveImageCheckbox" value="1" class="rounded text-rose-600 focus:ring-rose-500">
                            <span>Hapus gambar ini</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] text-slate-500 mb-1">Ganti gambar / Upload baru (opsional):</label>
                    <input type="file" name="image" id="editQuestionImageInput" accept="image/*" onchange="previewQuestionImage(this, 'editNewImagePreviewContainer', 'editNewImagePreview')" class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-100 file:text-emerald-700 hover:file:bg-emerald-200 cursor-pointer">
                </div>

                <div id="editNewImagePreviewContainer" class="hidden relative inline-block rounded-xl border border-slate-200 bg-white p-1">
                    <div class="text-[10px] text-emerald-700 font-bold mb-1">Preview Gambar Baru:</div>
                    <img id="editNewImagePreview" src="" alt="Preview Baru" class="max-h-48 rounded-lg object-contain">
                    <button type="button" onclick="clearQuestionImageInput('editQuestionImageInput', 'editNewImagePreviewContainer')" class="absolute -top-2 -right-2 bg-rose-500 text-white rounded-full h-6 w-6 flex items-center justify-center text-xs shadow-md hover:bg-rose-600">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
            </div>

            {{-- TOOLBAR CEPAT RUMUS & ARAB --}}
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                <div class="text-[11px] font-bold text-slate-600 mb-2 flex items-center gap-1.5">
                    <i class="bi bi-magic text-emerald-600"></i>
                    <span>Quick Formula Toolbar (Klik untuk menyisipkan rumus):</span>
                </div>
                <div class="flex flex-wrap gap-1.5">
                    <button type="button" class="quick-symbol-btn" onclick="insertSymbol('$\\frac{a}{b}$', 'editQuestionTextArea')">Pecahan $\frac{a}{b}$</button>
                    <button type="button" class="quick-symbol-btn" onclick="insertSymbol('$\\sqrt{x}$', 'editQuestionTextArea')">Akar $\sqrt{x}$</button>
                    <button type="button" class="quick-symbol-btn" onclick="insertSymbol('$x^2$', 'editQuestionTextArea')">Pangkat $x^2$</button>
                    <button type="button" class="quick-symbol-btn" onclick="insertSymbol('$x_1$', 'editQuestionTextArea')">Indeks $x_1$</button>
                    <button type="button" class="quick-symbol-btn" onclick="insertSymbol('$\\pi$', 'editQuestionTextArea')">$\pi$</button>
                    <button type="button" class="quick-symbol-btn" onclick="insertSymbol('$\\sum_{i=1}^{n}$', 'editQuestionTextArea')">$\sum$ Sigma</button>
                    <button type="button" class="quick-symbol-btn" onclick="insertSymbol('$\\int_a^b f(x)dx$', 'editQuestionTextArea')">$\int$ Integral</button>
                    <button type="button" class="quick-symbol-btn" onclick="insertSymbol('$\\text{H}_2\\text{O}$', 'editQuestionTextArea')">Kimia $\text{H}_2\text{O}$</button>
                    <button type="button" class="quick-symbol-btn" onclick="insertSymbol('$2\\text{H}_2 + \\text{O}_2 \\rightarrow 2\\text{H}_2\\text{O}$', 'editQuestionTextArea')">Reaksi $\rightarrow$</button>
                    <button type="button" class="quick-symbol-btn" onclick="insertSymbol('بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ', 'editQuestionTextArea')">Teks Arab</button>
                </div>
            </div>

            {{-- TEXTAREA PERTANYAAN --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Isi Pertanyaan Soal <span class="text-rose-500">*</span>
                </label>
                <textarea id="editQuestionTextArea" name="question_text" rows="4" required oninput="renderLivePreview('editQuestionTextArea', 'editLivePreviewContainer')" placeholder="Ketik atau paste soal di sini..." class="w-full rounded-xl border border-slate-200 bg-slate-50 p-3 text-xs text-slate-900 focus:bg-white focus:outline-none focus:ring-1 focus:ring-emerald-500"></textarea>
            </div>

            {{-- LIVE PREVIEW BOX --}}
            <div class="rounded-xl border border-dashed border-emerald-300 bg-emerald-50/40 p-3.5">
                <div class="text-[11px] font-bold uppercase tracking-wider text-emerald-800 mb-1 flex items-center gap-1.5">
                    <i class="bi bi-eye-fill"></i>
                    <span>Live Preview (Tampilan KaTeX &amp; Font Arab):</span>
                </div>
                <div id="editLivePreviewContainer" class="math-content text-xs text-slate-800 min-h-[40px] italic text-slate-400">
                    Preview tampilan rumus dan teks Arab akan muncul otomatis di sini...
                </div>
            </div>

            {{-- INPUT OPSI PILIHAN GANDA (A S/D E) --}}
            <div id="editMultipleChoiceContainer" class="space-y-3">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                    Pilihan Jawaban &amp; Kunci Jawaban
                </label>
                @foreach($opsiLabels as $opt)
                <div class="flex items-center gap-2">
                    <label class="flex items-center gap-1.5 rounded-lg border border-slate-200 bg-slate-100 px-2.5 py-2 text-xs font-bold text-slate-700 shrink-0 cursor-pointer" title="Jadikan Kunci Jawaban">
                        <input type="radio" name="correct_option" id="editOptRadio_{{ $opt }}" value="{{ $opt }}" class="text-emerald-600 focus:ring-emerald-500">
                        <span>{{ $opt }}</span>
                    </label>
                    <input type="text" name="options[{{ $opt }}]" id="editOptInput_{{ $opt }}" placeholder="Teks pilihan {{ $opt }}..." class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-900 focus:bg-white focus:outline-none focus:ring-1 focus:ring-emerald-500">
                </div>
                @endforeach
            </div>

            {{-- INPUT OPSI KOMPLEKS BENAR / SALAH (EDIT) --}}
            <div id="editComplexTrueFalseContainer" class="space-y-3 hidden">
                <div class="flex items-center justify-between">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            Pernyataan &amp; Kunci Jawaban (Benar / Salah)
                        </label>
                        <p class="text-[11px] text-slate-500">Tentukan apakah setiap baris pernyataan bernilai Benar atau Salah.</p>
                    </div>
                    <button type="button" onclick="addStatementRow('statementsListEdit')" class="inline-flex items-center gap-1 rounded-lg bg-purple-50 border border-purple-200 px-2.5 py-1 text-xs font-semibold text-purple-700 hover:bg-purple-100 transition">
                        <i class="bi bi-plus-lg"></i> Tambah Baris Pernyataan
                    </button>
                </div>

                <div id="statementsListEdit" class="space-y-2.5">
                    {{-- Diisi secara dinamis via JavaScript saat tombol edit diklik --}}
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeEditQuestionModal()" class="rounded-xl border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 transition">
                    Batal
                </button>
                <button type="submit" class="rounded-xl bg-emerald-600 px-5 py-2 text-xs font-semibold text-white shadow-sm hover:bg-emerald-500 transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL LIGHTBOX GAMBAR FULLSCREEN --}}
<div id="imageLightboxModal" class="fixed inset-0 z-50 hidden bg-slate-950/80 backdrop-blur-md p-4 flex items-center justify-center" onclick="closeImageLightbox()">
    <div class="relative max-w-4xl max-h-[90vh] p-2" onclick="event.stopPropagation()">
        <button type="button" onclick="closeImageLightbox()" class="absolute -top-10 right-0 text-white hover:text-slate-300 text-2xl font-bold">
            <i class="bi bi-x-circle-fill"></i>
        </button>
        <img id="lightboxImage" src="" alt="Full Resolution Image" class="max-w-full max-h-[85vh] rounded-2xl shadow-2xl object-contain bg-slate-900 border border-slate-700">
    </div>
</div>

{{-- MODAL IMPORT SOAL (EXCEL / WORD / CSV) --}}
<div id="importModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm overflow-y-auto p-4 flex items-center justify-center">
    <div class="relative w-full max-w-lg rounded-3xl bg-white shadow-2xl overflow-hidden">
        <div class="border-b border-slate-100 px-6 py-4 flex items-center justify-between bg-slate-50">
            <div>
                <h3 class="text-base font-bold text-slate-900">Import Soal CBT</h3>
                <p class="text-xs text-slate-500">Upload file Excel (.xlsx / .csv) atau Docs / Text (.txt)</p>
            </div>
            <button type="button" onclick="closeImportModal()" class="rounded-xl p-2 text-slate-400 hover:bg-slate-200 transition">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('cbt.questions.import', $quiz->id) }}" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf

            <div class="rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/50 p-6 text-center hover:border-emerald-400 transition">
                <i class="bi bi-file-earmark-arrow-up text-3xl text-emerald-600"></i>
                <div class="mt-2 text-xs font-bold text-slate-800">Pilih File Soal Ujian</div>
                <div class="text-[11px] text-slate-500 mt-0.5">Format didukung: .xlsx, .xls, .csv, .txt, .doc</div>
                <input type="file" name="file" required accept=".xlsx,.xls,.csv,.txt" class="mt-4 block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer">
            </div>

            <div class="rounded-xl bg-blue-50/70 p-3 text-xs text-blue-800 space-y-1">
                <div class="font-bold flex items-center gap-1">
                    <i class="bi bi-info-circle-fill"></i>
                    <span>Belum memiliki format template?</span>
                </div>
                <p class="text-[11px] text-blue-700">
                    Unduh template resmi berikut agar format kolom pertanyaan, opsi jawaban, dan kunci sesuai:
                </p>
                <div class="pt-1 flex gap-2">
                    <a href="{{ route('cbt.template.download', 'excel') }}" class="underline font-semibold hover:text-blue-900">Download Template Excel (.csv/.xlsx)</a>
                    <span>&bull;</span>
                    <a href="{{ route('cbt.template.download', 'word') }}" class="underline font-semibold hover:text-blue-900">Download Panduan Word</a>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeImportModal()" class="rounded-xl border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 transition">
                    Batal
                </button>
                <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 px-5 py-2 text-xs font-semibold text-white shadow-sm hover:bg-emerald-500 transition">
                    <i class="bi bi-cloud-arrow-up-fill"></i>
                    <span>Proses Import</span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/katex.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/contrib/auto-render.min.js" onload="initKaTeX()"></script>
<script>
    // Data Butir Soal untuk Edit Modal
    const quizQuestions = @json($quiz->questions);
    const updateQuestionBaseUrl = "{{ url('cbt/detail/' . $quiz->id . '/questions') }}";
    const appAssetBaseUrl = "{{ url('') }}";

    // KaTeX Renderer
    function initKaTeX() {
        if (typeof renderMathInElement === 'function') {
            renderMathInElement(document.body, {
                delimiters: [
                    {left: '$$', right: '$$', display: true},
                    {left: '$', right: '$', display: false},
                    {left: '\\(', right: '\\)', display: false},
                    {left: '\\[', right: '\\]', display: true}
                ],
                throwOnError: false
            });
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        if (typeof renderMathInElement === 'function') {
            initKaTeX();
        }
    });

    // Live Token Countdown Ujian
    let quizRemainingSeconds = {{ $tokenInfo['remaining_seconds'] }};
    const quizTokenDisplay = document.getElementById('quizTokenDisplay');
    const quizCountdownText = document.getElementById('quizCountdownText');

    setInterval(() => {
        quizRemainingSeconds--;
        if (quizRemainingSeconds <= 0) {
            quizRemainingSeconds = 300;
            fetch("{{ route('cbt.token.poll', ['quiz_id' => $quiz->id]) }}")
                .then(r => r.json())
                .then(res => {
                    if (res.success && res.data) {
                        quizTokenDisplay.textContent = res.data.token;
                        quizRemainingSeconds = res.data.remaining_seconds;
                    }
                });
        }
        const m = Math.floor(quizRemainingSeconds / 60);
        const s = quizRemainingSeconds % 60;
        quizCountdownText.textContent = `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
    }, 1000);

    // Modal Handlers
    function openAddQuestionModal() {
        document.getElementById('addQuestionModal').classList.remove('hidden');
    }
    function closeAddQuestionModal() {
        document.getElementById('addQuestionModal').classList.add('hidden');
    }
    function openImportModal() {
        document.getElementById('importModal').classList.remove('hidden');
    }
    function closeImportModal() {
        document.getElementById('importModal').classList.add('hidden');
    }

    // Lightbox Handlers
    function openImageLightbox(src) {
        const modal = document.getElementById('imageLightboxModal');
        const img = document.getElementById('lightboxImage');
        img.src = src;
        modal.classList.remove('hidden');
    }
    function closeImageLightbox() {
        document.getElementById('imageLightboxModal').classList.add('hidden');
    }

    // Toggle Inputs Tipe Soal (Tambah)
    function toggleOptionInputs() {
        const type = document.getElementById('questionTypeSelect').value;
        const mcContainer = document.getElementById('multipleChoiceContainer');
        const ctfContainer = document.getElementById('complexTrueFalseContainer');

        mcContainer.classList.toggle('hidden', type !== 'multiple_choice');
        ctfContainer.classList.toggle('hidden', type !== 'complex_true_false');
    }

    // Toggle Inputs Tipe Soal (Edit)
    function toggleEditOptionInputs() {
        const type = document.getElementById('editQuestionTypeSelect').value;
        const mcContainer = document.getElementById('editMultipleChoiceContainer');
        const ctfContainer = document.getElementById('editComplexTrueFalseContainer');

        mcContainer.classList.toggle('hidden', type !== 'multiple_choice');
        ctfContainer.classList.toggle('hidden', type !== 'complex_true_false');
    }

    // Preview File Gambar Sebelum Upload
    function previewQuestionImage(input, containerId, imgId) {
        const container = document.getElementById(containerId);
        const img = document.getElementById(imgId);
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                img.src = e.target.result;
                container.classList.remove('hidden');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function clearQuestionImageInput(inputId, containerId) {
        const input = document.getElementById(inputId);
        const container = document.getElementById(containerId);
        input.value = '';
        container.classList.add('hidden');
    }

    // Tambah Baris Pernyataan Benar/Salah Dinamis
    function addStatementRow(containerId, text = '', isCorrect = true) {
        const container = document.getElementById(containerId);
        const currentCount = container.children.length;
        const newIndex = currentCount;

        const row = document.createElement('div');
        row.className = 'statement-row flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50/50 p-2.5';
        row.innerHTML = `
            <span class="statement-idx text-xs font-bold text-slate-400 w-5 text-center">${newIndex + 1}</span>
            <input type="text" name="statements[${newIndex}]" value="${escapeHtml(text)}" required placeholder="Ketik teks pernyataan ${newIndex + 1} di sini..." class="w-full rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs text-slate-900 focus:outline-none focus:ring-1 focus:ring-purple-500">
            <div class="flex items-center gap-1 shrink-0">
                <label class="flex items-center gap-1 rounded-lg border border-emerald-200 bg-emerald-50 px-2 py-1 text-xs font-bold text-emerald-800 cursor-pointer">
                    <input type="radio" name="statement_answers[${newIndex}]" value="true" ${isCorrect ? 'checked' : ''} class="text-emerald-600 focus:ring-emerald-500">
                    <span>Benar</span>
                </label>
                <label class="flex items-center gap-1 rounded-lg border border-rose-200 bg-rose-50 px-2 py-1 text-xs font-bold text-rose-800 cursor-pointer">
                    <input type="radio" name="statement_answers[${newIndex}]" value="false" ${!isCorrect ? 'checked' : ''} class="text-rose-600 focus:ring-rose-500">
                    <span>Salah</span>
                </label>
            </div>
            <button type="button" onclick="removeStatementRow(this)" class="p-1 text-slate-400 hover:text-rose-600 transition">
                <i class="bi bi-trash"></i>
            </button>
        `;
        container.appendChild(row);
        updateStatementNumbers(containerId);
    }

    function removeStatementRow(btn) {
        const row = btn.closest('.statement-row');
        const container = row.parentElement;
        if (container.children.length <= 1) {
            alert('Minimal harus ada 1 pernyataan.');
            return;
        }
        row.remove();
        updateStatementNumbers(container.id);
    }

    function updateStatementNumbers(containerId) {
        const container = document.getElementById(containerId);
        const rows = container.querySelectorAll('.statement-row');
        rows.forEach((r, idx) => {
            const badge = r.querySelector('.statement-idx');
            if (badge) badge.textContent = idx + 1;
            const input = r.querySelector('input[type="text"]');
            if (input) input.name = `statements[${idx}]`;
            const radios = r.querySelectorAll('input[type="radio"]');
            radios.forEach(radio => {
                radio.name = `statement_answers[${idx}]`;
            });
        });
    }

    function escapeHtml(string) {
        return String(string || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    // Modal Edit Soal
    function openEditQuestionModal(questionId) {
        const q = quizQuestions.find(item => item.id === questionId);
        if (!q) {
            alert('Data butir soal tidak ditemukan.');
            return;
        }

        const form = document.getElementById('editQuestionForm');
        form.action = `${updateQuestionBaseUrl}/${q.id}`;

        // Set type and points
        document.getElementById('editQuestionTypeSelect').value = q.type;
        document.getElementById('editQuestionPoints').value = q.points;
        document.getElementById('editQuestionTextArea').value = q.question_text;

        // Existing image
        const imgWrapper = document.getElementById('editExistingImageWrapper');
        const imgEl = document.getElementById('editExistingImage');
        const removeImgCheck = document.getElementById('editRemoveImageCheckbox');
        removeImgCheck.checked = false;

        if (q.image_path) {
            imgEl.src = (q.image_path.startsWith('data:') || q.image_path.startsWith('http')) 
                ? q.image_path 
                : `${appAssetBaseUrl}/${q.image_path}`;
            imgWrapper.classList.remove('hidden');
        } else {
            imgEl.src = '';
            imgWrapper.classList.add('hidden');
        }

        // Reset new image input & preview
        clearQuestionImageInput('editQuestionImageInput', 'editNewImagePreviewContainer');

        // Toggle UI sections
        toggleEditOptionInputs();

        // Populate options based on type
        const labels = ['A', 'B', 'C', 'D', 'E'];
        labels.forEach(l => {
            const inp = document.getElementById(`editOptInput_${l}`);
            const rad = document.getElementById(`editOptRadio_${l}`);
            if (inp) inp.value = '';
            if (rad) rad.checked = false;
        });

        if (q.type === 'multiple_choice' && q.options) {
            q.options.forEach((opt, idx) => {
                const label = labels[idx];
                if (label) {
                    const inp = document.getElementById(`editOptInput_${label}`);
                    const rad = document.getElementById(`editOptRadio_${label}`);
                    if (inp) inp.value = opt.option_text;
                    if (rad && opt.is_correct) rad.checked = true;
                }
            });
        } else if (q.type === 'complex_true_false') {
            const listEdit = document.getElementById('statementsListEdit');
            listEdit.innerHTML = '';
            if (q.options && q.options.length > 0) {
                q.options.forEach(opt => {
                    addStatementRow('statementsListEdit', opt.option_text, !!opt.is_correct);
                });
            } else {
                addStatementRow('statementsListEdit', '', true);
                addStatementRow('statementsListEdit', '', false);
            }
        }

        renderLivePreview('editQuestionTextArea', 'editLivePreviewContainer');
        document.getElementById('editQuestionModal').classList.remove('hidden');
    }

    function closeEditQuestionModal() {
        document.getElementById('editQuestionModal').classList.add('hidden');
    }

    // Formula & Live Preview Helper
    function insertSymbol(symbol, textareaId) {
        const textarea = document.getElementById(textareaId);
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const text = textarea.value;
        textarea.value = text.substring(0, start) + symbol + text.substring(end);
        textarea.selectionStart = textarea.selectionEnd = start + symbol.length;
        textarea.focus();
        if (textareaId === 'questionTextArea') {
            renderLivePreview('questionTextArea', 'livePreviewContainer');
        } else {
            renderLivePreview('editQuestionTextArea', 'editLivePreviewContainer');
        }
    }

    function renderLivePreview(textareaId, previewContainerId) {
        const textarea = document.getElementById(textareaId);
        const preview = document.getElementById(previewContainerId);
        const text = textarea ? textarea.value : '';

        if (!text.trim()) {
            preview.innerHTML = '<span class="italic text-slate-400">Preview tampilan rumus dan teks Arab akan muncul otomatis di sini...</span>';
            return;
        }

        const hasArabic = /[\u0600-\u06FF]/.test(text);
        if (hasArabic) {
            preview.className = "math-content font-arabic";
        } else {
            preview.className = "math-content text-xs text-slate-800";
        }

        preview.textContent = text;
        if (typeof renderMathInElement === 'function') {
            renderMathInElement(preview, {
                delimiters: [
                    {left: '$$', right: '$$', display: true},
                    {left: '$', right: '$', display: false},
                    {left: '\\(', right: '\\)', display: false},
                    {left: '\\[', right: '\\]', display: true}
                ],
                throwOnError: false
            });
        }
    }
</script>
@endpush
@endsection
