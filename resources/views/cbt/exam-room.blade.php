<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $quiz->title }} &mdash; Ruang Ujian CBT</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Amiri:wght@400;700&family=JetBrains+Mono:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/katex.min.css">

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            user-select: none;
            -webkit-user-select: none;
        }
        .math-render {
            line-height: 2;
        }
        .math-render .katex {
            font-size: 1.15em;
        }
        .font-arabic {
            font-family: 'Amiri', serif;
            font-size: 1.4rem;
            line-height: 2.4;
            direction: rtl;
            text-align: right;
        }
        .timer-display {
            font-family: 'JetBrains Mono', monospace;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    </style>
</head>
<body class="bg-slate-100 text-slate-900 min-h-screen flex flex-col antialiased">

    {{-- TOPBAR CBT EXAM ROOM --}}
    <header class="sticky top-0 z-40 bg-slate-900 text-white shadow-md">
        <div class="px-4 sm:px-6 py-3 flex items-center justify-between gap-4">
            {{-- Info Ujian & Siswa --}}
            <div class="flex items-center gap-3 min-w-0">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-500 text-slate-950 font-black text-lg">
                    <i class="bi bi-pencil-square"></i>
                </div>
                <div class="min-w-0">
                    <div class="truncate text-sm font-bold text-white">{{ $quiz->title }}</div>
                    <div class="text-[11px] text-emerald-400 font-medium">
                        {{ $student->user?->name ?? 'Siswa' }} &bull; {{ $student->class?->name ?? 'Kelas' }} &bull; NISN: {{ $student->nisn ?? '-' }}
                    </div>
                </div>
            </div>

            {{-- LIVE COUNTDOWN TIMER & STATUS AUTOSAVE --}}
            <div class="flex items-center gap-4">
                {{-- Status Autosave --}}
                <div id="saveStatusIndicator" class="hidden sm:flex items-center gap-1.5 text-xs text-slate-300">
                    <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                    <span id="saveStatusText">Koneksi Tersimpan</span>
                </div>

                {{-- Countdown Box --}}
                <div class="flex items-center gap-2 rounded-2xl bg-white/10 px-4 py-2 border border-white/15">
                    <i class="bi bi-stopwatch text-emerald-400 text-base"></i>
                    <div>
                        <div class="text-[9px] uppercase font-bold tracking-wider text-slate-400 leading-none">Sisa Waktu</div>
                        <div id="examTimer" class="timer-display text-lg sm:text-xl font-bold text-white tracking-wider leading-tight">
                            --:--:--
                        </div>
                    </div>
                </div>

                {{-- Tombol Toggle Daftar Soal Mobile --}}
                <button type="button" onclick="toggleMobileQuestionNav()" class="lg:hidden flex h-10 w-10 items-center justify-center rounded-xl bg-white/15 text-white hover:bg-white/25">
                    <i class="bi bi-grid-3x3-gap-fill text-lg"></i>
                </button>
            </div>
        </div>

        {{-- Progress Bar Waktu Ujian --}}
        <div class="h-1.5 w-full bg-slate-800">
            <div id="examTimeProgressBar" class="h-full bg-emerald-500 transition-all duration-1000 ease-linear" style="width: 100%;"></div>
        </div>
    </header>

    {{-- MAIN CONTAINER --}}
    <div class="flex-1 max-w-7xl w-full mx-auto p-4 sm:p-6 lg:p-8 flex flex-col lg:flex-row gap-6">

        {{-- AREA SOAL UTAMA (KIRI) --}}
        <main class="flex-1 flex flex-col justify-between rounded-3xl border border-slate-200/80 bg-white p-6 sm:p-8 shadow-sm">
            <div>
                {{-- Header Soal & Zoom Font --}}
                <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-6">
                    <div class="flex items-center gap-2.5">
                        <span id="activeQuestionBadge" class="flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-600 text-sm font-bold text-white shadow-xs">
                            1
                        </span>
                        <div>
                            <div class="text-xs font-bold uppercase tracking-wider text-slate-400">Nomor Soal</div>
                            <div class="text-sm font-bold text-slate-800">
                                Soal <span id="currentQuestionNum">1</span> dari {{ $questions->count() }}
                            </div>
                        </div>
                    </div>

                    {{-- Font Size Adjuster --}}
                    <div class="flex items-center gap-1 rounded-xl bg-slate-100 p-1 text-xs">
                        <span class="px-2 text-[10px] font-bold text-slate-400 uppercase">Ukuran:</span>
                        <button type="button" onclick="changeFontSize('small')" class="px-2 py-1 font-bold text-slate-600 hover:text-slate-900 rounded hover:bg-white">A-</button>
                        <button type="button" onclick="changeFontSize('normal')" class="px-2 py-1 font-bold text-slate-600 hover:text-slate-900 rounded bg-white shadow-xs">A</button>
                        <button type="button" onclick="changeFontSize('large')" class="px-2 py-1 font-bold text-slate-600 hover:text-slate-900 rounded hover:bg-white">A+</button>
                    </div>
                </div>

                {{-- LOOP CONTAINER SEMUA SOAL --}}
                <div id="questionsContainer">
                    @foreach($questions as $idx => $q)
                    @php
                        $userAnswer = $savedAnswers->get($q->id);
                        $savedOptionId = $userAnswer?->question_option_id;
                        $savedEssay = $userAnswer?->essay_answer;
                    @endphp
                    <div id="questionPane-{{ $idx }}" class="question-pane {{ $idx === 0 ? '' : 'hidden' }}" data-index="{{ $idx }}" data-qid="{{ $q->id }}">
                        {{-- Gambar Soal Jika Ada --}}
                        @if($q->image_path)
                        <div class="mb-5">
                            <div class="inline-block relative rounded-2xl overflow-hidden border border-slate-200 bg-white p-1.5 shadow-xs">
                                <img src="{{ str_starts_with($q->image_path, 'data:') || str_starts_with($q->image_path, 'http') ? $q->image_path : asset($q->image_path) }}" alt="Gambar Soal {{ $idx + 1 }}" class="max-h-72 max-w-full rounded-xl object-contain cursor-pointer hover:opacity-95 transition" onclick="openExamImageLightbox(this.src)">
                                <div class="text-[10px] text-slate-400 mt-1 text-center font-medium">Klik gambar untuk memperbesar</div>
                            </div>
                        </div>
                        @endif

                        {{-- Teks Pertanyaan --}}
                        <div class="question-text math-render text-sm sm:text-base text-slate-800 font-medium leading-relaxed mb-6">
                            {!! nl2br(e($q->question_text)) !!}
                        </div>

                        {{-- Opsi Pilihan Ganda Biasa --}}
                        @if($q->type === 'multiple_choice' && $q->options->isNotEmpty())
                        <div class="space-y-3">
                            @php $labels = ['A', 'B', 'C', 'D', 'E']; @endphp
                            @foreach($q->options as $optIdx => $opt)
                            <label class="option-label flex items-start gap-3.5 rounded-2xl border p-4 cursor-pointer transition {{ $savedOptionId == $opt->id ? 'border-emerald-500 bg-emerald-50/60 shadow-xs' : 'border-slate-200 bg-slate-50/50 hover:bg-slate-100/70 hover:border-slate-300' }}">
                                <input type="radio" name="answer_opt_{{ $q->id }}" value="{{ $opt->id }}" @checked($savedOptionId == $opt->id) onchange="selectAnswer({{ $idx }}, {{ $q->id }}, {{ $opt->id }})" class="mt-1 h-4 w-4 text-emerald-600 focus:ring-emerald-500">
                                <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg text-xs font-bold {{ $savedOptionId == $opt->id ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-700' }}">
                                    {{ $labels[$optIdx] ?? ($optIdx + 1) }}
                                </span>
                                <div class="option-text math-render text-xs sm:text-sm text-slate-800 flex-1">
                                    {!! nl2br(e($opt->option_text)) !!}
                                </div>
                            </label>
                            @endforeach
                        </div>
                        {{-- Opsi Pilihan Ganda Kompleks (Benar / Salah) --}}
                        @elseif($q->type === 'complex_true_false' && $q->options->isNotEmpty())
                        @php
                            $savedMatching = $userAnswer?->matching_answer ?? [];
                        @endphp
                        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xs">
                            <div class="bg-gradient-to-r from-purple-50 to-indigo-50 px-4 py-3 border-b border-purple-100 flex items-center justify-between">
                                <div class="text-xs font-bold text-purple-900">
                                    Pilihlah <span class="text-emerald-700 font-extrabold">BENAR</span> atau <span class="text-rose-700 font-extrabold">SALAH</span> untuk setiap pernyataan berikut:
                                </div>
                                <span class="text-[10px] font-bold uppercase tracking-wider bg-purple-200 text-purple-800 px-2 py-0.5 rounded-md">Model Kompleks</span>
                            </div>
                            <div class="divide-y divide-slate-100">
                                @foreach($q->options as $optIdx => $opt)
                                @php
                                    $optSavedVal = isset($savedMatching[$opt->id]) ? filter_var($savedMatching[$opt->id], FILTER_VALIDATE_BOOLEAN) : null;
                                @endphp
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 hover:bg-slate-50/60 transition">
                                    <div class="flex items-start gap-3 flex-1">
                                        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-xs font-bold text-slate-600">
                                            {{ $optIdx + 1 }}
                                        </span>
                                        <div class="option-text math-render text-xs sm:text-sm text-slate-800 font-medium">
                                            {!! nl2br(e($opt->option_text)) !!}
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 pl-9 sm:pl-0 shrink-0">
                                        <label class="ctf-label flex items-center gap-1.5 rounded-xl border px-3 py-1.5 text-xs font-bold cursor-pointer transition {{ $optSavedVal === true ? 'border-emerald-500 bg-emerald-50 text-emerald-800 shadow-xs ring-1 ring-emerald-400' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50' }}">
                                            <input type="radio" name="ctf_{{ $q->id }}_{{ $opt->id }}" value="true" @checked($optSavedVal === true) onchange="selectComplexAnswer({{ $idx }}, {{ $q->id }}, {{ $opt->id }}, true)" class="text-emerald-600 focus:ring-emerald-500">
                                            <span>Benar</span>
                                        </label>
                                        <label class="ctf-label flex items-center gap-1.5 rounded-xl border px-3 py-1.5 text-xs font-bold cursor-pointer transition {{ $optSavedVal === false ? 'border-rose-500 bg-rose-50 text-rose-800 shadow-xs ring-1 ring-rose-400' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50' }}">
                                            <input type="radio" name="ctf_{{ $q->id }}_{{ $opt->id }}" value="false" @checked($optSavedVal === false) onchange="selectComplexAnswer({{ $idx }}, {{ $q->id }}, {{ $opt->id }}, false)" class="text-rose-600 focus:ring-rose-500">
                                            <span>Salah</span>
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @else
                        {{-- Essay Input --}}
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">Jawaban Uraian Anda:</label>
                            <textarea rows="6" onblur="saveEssayAnswer({{ $idx }}, {{ $q->id }}, this.value)" placeholder="Tuliskan jawaban essay Anda di sini..." class="w-full rounded-2xl border border-slate-200 bg-slate-50 p-4 text-xs sm:text-sm text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20">{{ $savedEssay }}</textarea>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- FOOTER NAVIGASI SOAL --}}
            <div class="pt-6 mt-8 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
                <button type="button" id="btnPrev" onclick="navigateQuestion(-1)" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-5 py-3 text-xs font-bold text-slate-700 shadow-sm hover:bg-slate-50 transition disabled:opacity-40">
                    <i class="bi bi-arrow-left"></i>
                    <span>Sebelumnya</span>
                </button>

                {{-- CHECKBOX RAGU-RAGU --}}
                <label class="inline-flex items-center gap-2.5 rounded-2xl bg-amber-50 border border-amber-200 px-4 py-2.5 cursor-pointer text-xs font-bold text-amber-900 hover:bg-amber-100/70 transition">
                    <input type="checkbox" id="doubtCheckbox" onchange="toggleDoubt()" class="rounded text-amber-500 focus:ring-amber-400">
                    <span>Ragu-ragu</span>
                </label>

                <div class="flex items-center gap-2">
                    <button type="button" id="btnNext" onclick="navigateQuestion(1)" class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-5 py-3 text-xs font-bold text-white shadow-sm hover:bg-slate-800 transition">
                        <span>Selanjutnya</span>
                        <i class="bi bi-arrow-right"></i>
                    </button>
                    <button type="button" id="btnFinish" onclick="openFinishModal()" class="hidden inline-flex items-center gap-2 rounded-2xl bg-emerald-600 px-6 py-3 text-xs font-bold text-white shadow-sm hover:bg-emerald-500 transition">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Selesaikan Ujian</span>
                    </button>
                </div>
            </div>
        </main>

        {{-- SIDEBAR DAFTAR NOMOR SOAL (KANAN) --}}
        <aside id="questionNavSidebar" class="hidden lg:block w-full lg:w-80 shrink-0 space-y-4">
            <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-sm sticky top-24">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                    <h3 class="text-sm font-bold text-slate-900">Navigasi Nomor Soal</h3>
                    <span id="answeredStatsBadge" class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-bold text-emerald-800">
                        {{ $savedAnswers->filter(fn($a) => $a->question_option_id || $a->essay_answer || !empty($a->matching_answer))->count() }} / {{ $questions->count() }}
                    </span>
                </div>

                {{-- GRID KOTAK NOMOR 1..N --}}
                <div class="grid grid-cols-5 gap-2 max-h-[360px] overflow-y-auto p-1">
                    @foreach($questions as $idx => $q)
                    @php
                        $userAnswer = $savedAnswers->get($q->id);
                        $isAnswered = $userAnswer && ($userAnswer->question_option_id || $userAnswer->essay_answer || !empty($userAnswer->matching_answer));
                    @endphp
                    <button type="button" id="navBtn-{{ $idx }}" onclick="jumpToQuestion({{ $idx }})" class="nav-question-btn flex h-11 items-center justify-center rounded-xl text-xs font-bold transition {{ $idx === 0 ? 'ring-2 ring-emerald-500 ' : '' }} {{ $isAnswered ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                        {{ $idx + 1 }}
                    </button>
                    @endforeach
                </div>

                {{-- LEGENDA STATUS --}}
                <div class="mt-6 pt-4 border-t border-slate-100 space-y-2 text-[11px] text-slate-600">
                    <div class="flex items-center gap-2">
                        <span class="h-3.5 w-3.5 rounded-md bg-emerald-600"></span>
                        <span>Sudah Dijawab</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="h-3.5 w-3.5 rounded-md bg-amber-400"></span>
                        <span>Ragu-ragu</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="h-3.5 w-3.5 rounded-md bg-slate-200"></span>
                        <span>Belum Dijawab</span>
                    </div>
                </div>

                {{-- TOMBOL SUBMIT DI SIDEBAR --}}
                <div class="mt-6 pt-4 border-t border-slate-100">
                    <button type="button" onclick="openFinishModal()" class="w-full inline-flex items-center justify-center gap-2 rounded-2xl bg-emerald-600 py-3 text-xs font-bold text-white shadow-sm hover:bg-emerald-500 transition">
                        <i class="bi bi-send-check-fill"></i>
                        <span>Selesaikan Ujian</span>
                    </button>
                </div>
            </div>
        </aside>
    </div>

    {{-- MODAL KONFIRMASI SUBMIT AKHIR UJIAN --}}
    <div id="finishModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm overflow-y-auto p-4 flex items-center justify-center">
        <div class="relative w-full max-w-md rounded-3xl bg-white shadow-2xl overflow-hidden p-6 text-center">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-100 text-amber-600 mb-3">
                <i class="bi bi-question-circle-fill text-3xl"></i>
            </div>

            <h3 class="text-lg font-bold text-slate-900">Apakah Anda Yakin Ingin Menyelesaikan Ujian?</h3>
            <p class="text-xs text-slate-500 mt-1">
                Periksa kembali jawaban Anda. Setelah diselesaikan, Anda tidak dapat mengubah jawaban lagi.
            </p>

            <div class="grid grid-cols-2 gap-3 my-5 rounded-2xl bg-slate-50 p-4 text-center">
                <div class="rounded-xl bg-white p-3 border border-slate-100">
                    <div class="text-[10px] font-bold uppercase text-slate-400">Terjawab</div>
                    <div id="modalAnsweredCount" class="text-xl font-black text-emerald-600">0</div>
                </div>
                <div class="rounded-xl bg-white p-3 border border-slate-100">
                    <div class="text-[10px] font-bold uppercase text-slate-400">Belum Terjawab</div>
                    <div id="modalUnansweredCount" class="text-xl font-black text-rose-500">0</div>
                </div>
            </div>

            <form method="POST" action="{{ route('cbt.student.finish', $attempt->id) }}">
                @csrf
                <div class="flex items-center justify-center gap-2">
                    <button type="button" onclick="closeFinishModal()" class="w-1/2 rounded-2xl border border-slate-200 py-3 text-xs font-semibold text-slate-700 hover:bg-slate-100 transition">
                        Lanjut Periksa
                    </button>
                    <button type="submit" class="w-1/2 inline-flex items-center justify-center gap-1.5 rounded-2xl bg-emerald-600 py-3 text-xs font-bold text-white shadow-sm hover:bg-emerald-500 transition">
                        <i class="bi bi-check-lg"></i>
                        <span>Ya, Selesaikan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- SCRIPTS: KaTeX & CBT Engine --}}
    <script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/katex.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/contrib/auto-render.min.js" onload="renderFormulas()"></script>

    <script>
        // Total soal dan state aktif
        const totalQuestions = {{ $questions->count() }};
        let currentIndex = 0;
        const doubtStates = {}; // key: index, value: boolean
        const answeredStates = {}; // key: index, value: boolean

        const complexAnswers = {
            @foreach($questions as $q)
                @if($q->type === 'complex_true_false')
                    @php $savedM = $savedAnswers->get($q->id)?->matching_answer; @endphp
                    {{ $q->id }}: @json($savedM ?: new \stdClass()),
                @endif
            @endforeach
        };

        @foreach($questions as $idx => $q)
            @php
                $userAns = $savedAnswers->get($q->id);
                $isAns = $userAns && ($userAns->question_option_id || $userAns->essay_answer || !empty($userAns->matching_answer));
            @endphp
            answeredStates[{{ $idx }}] = {{ $isAns ? 'true' : 'false' }};
        @endforeach

        // Inisialisasi KaTeX Delimiters
        function renderFormulas() {
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
            renderFormulas();
            updateNavigationUI();
        });

        // Countdown Timer Ujian
        let remainingSeconds = {{ $attempt->remaining_seconds }};
        const totalDurationSeconds = {{ $quiz->duration * 60 }};
        const examTimer = document.getElementById('examTimer');
        const examTimeProgressBar = document.getElementById('examTimeProgressBar');

        function updateExamTimerUI() {
            if (remainingSeconds <= 0) {
                examTimer.textContent = "00:00:00";
                // Otomatis submit saat waktu habis
                alert("Waktu ujian telah berakhir. Jawaban Anda akan dikirim secara otomatis.");
                window.location.href = "{{ route('cbt.student.result', $attempt->id) }}";
                return;
            }

            const h = Math.floor(remainingSeconds / 3600);
            const m = Math.floor((remainingSeconds % 3600) / 60);
            const s = remainingSeconds % 60;
            examTimer.textContent = `${String(h).padStart(2, '0')}:${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;

            const percent = Math.round((remainingSeconds / totalDurationSeconds) * 100);
            examTimeProgressBar.style.width = percent + '%';

            // Warning visual bila < 5 menit (300s)
            if (remainingSeconds <= 300) {
                examTimeProgressBar.className = "h-full bg-rose-500 transition-all duration-1000 ease-linear";
                examTimer.className = "timer-display text-lg sm:text-xl font-bold text-rose-400 tracking-wider animate-pulse";
            }
        }

        setInterval(() => {
            remainingSeconds--;
            updateExamTimerUI();
        }, 1000);
        updateExamTimerUI();

        // Navigasi Pindah Soal (Next/Prev)
        function navigateQuestion(offset) {
            jumpToQuestion(currentIndex + offset);
        }

        function jumpToQuestion(targetIndex) {
            if (targetIndex < 0 || targetIndex >= totalQuestions) return;

            // Sembunyikan soal lama
            document.getElementById(`questionPane-${currentIndex}`).classList.add('hidden');
            document.getElementById(`navBtn-${currentIndex}`).classList.remove('ring-2', 'ring-emerald-500');

            // Tampilkan soal baru
            currentIndex = targetIndex;
            document.getElementById(`questionPane-${currentIndex}`).classList.remove('hidden');
            document.getElementById(`navBtn-${currentIndex}`).classList.add('ring-2', 'ring-emerald-500');

            updateNavigationUI();
        }

        function updateNavigationUI() {
            document.getElementById('currentQuestionNum').textContent = currentIndex + 1;
            document.getElementById('activeQuestionBadge').textContent = currentIndex + 1;

            document.getElementById('btnPrev').disabled = (currentIndex === 0);
            const isLast = (currentIndex === totalQuestions - 1);
            document.getElementById('btnNext').style.display = isLast ? 'none' : 'inline-flex';
            document.getElementById('btnFinish').style.display = isLast ? 'inline-flex' : 'none';

            // Checkbox ragu-ragu
            document.getElementById('doubtCheckbox').checked = !!doubtStates[currentIndex];
        }

        // Toggle Status Ragu-Ragu
        function toggleDoubt() {
            const isDoubt = document.getElementById('doubtCheckbox').checked;
            doubtStates[currentIndex] = isDoubt;
            updateNavButtonState(currentIndex);
        }

        // Update style kotak nomor navigasi
        function updateNavButtonState(idx) {
            const btn = document.getElementById(`navBtn-${idx}`);
            const isAns = answeredStates[idx];
            const isDoubt = doubtStates[idx];

            btn.className = `nav-question-btn flex h-11 items-center justify-center rounded-xl text-xs font-bold transition ${idx === currentIndex ? 'ring-2 ring-emerald-500 ' : ''}`;

            if (isDoubt) {
                btn.className += ' bg-amber-400 text-amber-950 shadow-xs';
            } else if (isAns) {
                btn.className += ' bg-emerald-600 text-white shadow-xs';
            } else {
                btn.className += ' bg-slate-100 text-slate-700 hover:bg-slate-200';
            }

            // Update statistik
            let countAns = 0;
            for (let i = 0; i < totalQuestions; i++) {
                if (answeredStates[i]) countAns++;
            }
            document.getElementById('answeredStatsBadge').textContent = `${countAns} / ${totalQuestions}`;
        }

        // Autosave Pilihan Ganda via AJAX
        function selectAnswer(qIndex, questionId, optionId) {
            answeredStates[qIndex] = true;
            updateNavButtonState(qIndex);

            // Update visual radio styling
            const pane = document.getElementById(`questionPane-${qIndex}`);
            pane.querySelectorAll('.option-label').forEach(lbl => {
                lbl.className = 'option-label flex items-start gap-3.5 rounded-2xl border p-4 cursor-pointer transition border-slate-200 bg-slate-50/50 hover:bg-slate-100/70';
            });
            const selectedRadio = pane.querySelector(`input[value="${optionId}"]`);
            if (selectedRadio) {
                selectedRadio.closest('.option-label').className = 'option-label flex items-start gap-3.5 rounded-2xl border p-4 cursor-pointer transition border-emerald-500 bg-emerald-50/60 shadow-xs';
            }

            showSavingIndicator();

            fetch("{{ route('cbt.student.save', $attempt->id) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    question_id: questionId,
                    question_option_id: optionId
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showSavedIndicator();
                }
            })
            .catch(err => {
                console.error("Autosave error:", err);
            });
        }

        // Autosave Essay via AJAX
        function saveEssayAnswer(qIndex, questionId, text) {
            if (text.trim()) {
                answeredStates[qIndex] = true;
            }
            updateNavButtonState(qIndex);
            showSavingIndicator();

            fetch("{{ route('cbt.student.save', $attempt->id) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    question_id: questionId,
                    essay_answer: text
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showSavedIndicator();
                }
            });
        }

        // Autosave Pilihan Ganda Kompleks (Benar / Salah) via AJAX
        function selectComplexAnswer(qIndex, questionId, optionId, boolVal) {
            if (!complexAnswers[questionId]) {
                complexAnswers[questionId] = {};
            }
            complexAnswers[questionId][optionId] = boolVal;

            answeredStates[qIndex] = true;
            updateNavButtonState(qIndex);

            // Update styling radio button row
            const pane = document.getElementById(`questionPane-${qIndex}`);
            const radios = pane.querySelectorAll(`input[name="ctf_${questionId}_${optionId}"]`);
            radios.forEach(r => {
                const lbl = r.closest('.ctf-label');
                if (lbl) {
                    if (r.checked) {
                        if (r.value === 'true') {
                            lbl.className = 'ctf-label flex items-center gap-1.5 rounded-xl border px-3 py-1.5 text-xs font-bold cursor-pointer transition border-emerald-500 bg-emerald-50 text-emerald-800 shadow-xs ring-1 ring-emerald-400';
                        } else {
                            lbl.className = 'ctf-label flex items-center gap-1.5 rounded-xl border px-3 py-1.5 text-xs font-bold cursor-pointer transition border-rose-500 bg-rose-50 text-rose-800 shadow-xs ring-1 ring-rose-400';
                        }
                    } else {
                        lbl.className = 'ctf-label flex items-center gap-1.5 rounded-xl border px-3 py-1.5 text-xs font-bold cursor-pointer transition border-slate-200 bg-white text-slate-600 hover:bg-slate-50';
                    }
                }
            });

            showSavingIndicator();

            fetch("{{ route('cbt.student.save', $attempt->id) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    question_id: questionId,
                    matching_answer: complexAnswers[questionId]
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showSavedIndicator();
                }
            })
            .catch(err => {
                console.error("Autosave complex error:", err);
            });
        }

        // Exam Lightbox Modal
        function openExamImageLightbox(src) {
            let modal = document.getElementById('examImageLightbox');
            if (!modal) {
                modal = document.createElement('div');
                modal.id = 'examImageLightbox';
                modal.className = 'fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-md p-4 flex items-center justify-center';
                modal.onclick = () => modal.classList.add('hidden');
                modal.innerHTML = `
                    <div class="relative max-w-4xl max-h-[90vh] p-2" onclick="event.stopPropagation()">
                        <button type="button" onclick="document.getElementById('examImageLightbox').classList.add('hidden')" class="absolute -top-10 right-0 text-white hover:text-slate-300 text-2xl font-bold">
                            <i class="bi bi-x-circle-fill"></i>
                        </button>
                        <img id="examLightboxImg" src="${src}" alt="Preview Gambar" class="max-w-full max-h-[85vh] rounded-2xl shadow-2xl object-contain bg-slate-900 border border-slate-700">
                    </div>
                `;
                document.body.appendChild(modal);
            } else {
                document.getElementById('examLightboxImg').src = src;
                modal.classList.remove('hidden');
            }
        }

        function showSavingIndicator() {
            const ind = document.getElementById('saveStatusIndicator');
            const txt = document.getElementById('saveStatusText');
            ind.classList.remove('hidden');
            txt.textContent = 'Menyimpan...';
            ind.querySelector('span').className = 'h-2 w-2 rounded-full bg-amber-400 animate-ping';
        }

        function showSavedIndicator() {
            const ind = document.getElementById('saveStatusIndicator');
            const txt = document.getElementById('saveStatusText');
            txt.textContent = 'Jawaban Tersimpan';
            ind.querySelector('span').className = 'h-2 w-2 rounded-full bg-emerald-400';
        }

        // Font Size Switcher
        function changeFontSize(size) {
            const texts = document.querySelectorAll('.question-text, .option-text');
            texts.forEach(el => {
                el.classList.remove('text-xs', 'text-sm', 'text-base', 'text-lg', 'text-xl');
                if (size === 'small') el.classList.add('text-xs');
                else if (size === 'normal') el.classList.add('text-sm');
                else if (size === 'large') el.classList.add('text-lg');
            });
        }

        // Modal Selesai Ujian
        function openFinishModal() {
            let answered = 0;
            for (let i = 0; i < totalQuestions; i++) {
                if (answeredStates[i]) answered++;
            }
            document.getElementById('modalAnsweredCount').textContent = answered;
            document.getElementById('modalUnansweredCount').textContent = totalQuestions - answered;
            document.getElementById('finishModal').classList.remove('hidden');
        }

        function closeFinishModal() {
            document.getElementById('finishModal').classList.add('hidden');
        }

        function toggleMobileQuestionNav() {
            const sidebar = document.getElementById('questionNavSidebar');
            sidebar.classList.toggle('hidden');
        }
    </script>
</body>
</html>
