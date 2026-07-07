@extends('layouts.app')
@section('title', 'Pengerjaan Ujian CBT')
@section('page-title', $quiz->title)

@section('content')
@php
    $items = $paginator->items();
    $offset = ($currentPage - 1) * $quiz->questions_per_page;
@endphp

<div class="flex flex-col lg:flex-row gap-6">
    {{-- AREA SOAL --}}
    <div class="flex-1 space-y-6">
        <div class="rounded-2xl bg-white/80 backdrop-blur-xl border border-slate-200/60 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <h5 class="text-lg font-bold text-slate-800">{{ $quiz->title }}</h5>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-brand-100 text-brand-700">
                    Halaman {{ $currentPage }} / {{ $paginator->lastPage() }}
                </span>
            </div>
            
            <div class="p-0">
                <form id="quizForm" action="{{ route('student.quizzes.finish', $quiz) }}" method="POST">
                    @csrf
                    @foreach($items as $idx => $aq)
                        @php
                            $question = $aq->question;
                            $qNum = $offset + $idx + 1;
                            $savedAnswer = $savedAnswers->get($question->id);
                        @endphp
                        
                        <div class="p-6 border-b border-slate-100 last:border-b-0 question-item transition hover:bg-slate-50/30" id="question-{{ $qNum }}">
                            <div class="flex gap-4">
                                <div class="flex-shrink-0 flex items-center justify-center w-8 h-8 rounded-full bg-slate-100 text-slate-700 font-bold text-sm">
                                    {{ $qNum }}
                                </div>
                                <div class="flex-1">
                                    <div class="text-slate-800 text-base leading-relaxed mb-4 prose max-w-none">
                                        {!! nl2br(e($question->question_text)) !!}
                                    </div>
                                    
                                    @if($question->image_path)
                                    <div class="mb-5 rounded-xl overflow-hidden border border-slate-200 inline-block">
                                        <img src="{{ asset('storage/'.$question->image_path) }}" class="max-h-64 object-contain" alt="Soal">
                                    </div>
                                    @endif
                                    
                                    @if($question->type === 'multiple_choice')
                                        <div class="grid gap-3">
                                            @foreach($question->options as $option)
                                            @php
                                                $isChecked = $savedAnswer && (int)$savedAnswer->question_option_id === (int)$option->id;
                                            @endphp
                                            <label class="group relative flex cursor-pointer rounded-xl border p-4 transition-all hover:border-brand-400 hover:bg-brand-50/50 option-label {{ $isChecked ? 'border-brand-500 bg-brand-50 shadow-sm ring-1 ring-brand-500' : 'border-slate-200 bg-white' }}">
                                                <div class="flex items-start w-full">
                                                    <div class="flex items-center h-5">
                                                        <input class="form-check-input w-5 h-5 border-slate-300 text-brand-600 focus:ring-brand-600 transition auto-save" 
                                                               type="radio" 
                                                               name="q_{{ $question->id }}" 
                                                               value="{{ $option->id }}" 
                                                               data-question="{{ $question->id }}" 
                                                               {{ $isChecked ? 'checked' : '' }}>
                                                    </div>
                                                    <div class="ml-3 text-sm text-slate-700 group-hover:text-slate-900 leading-6">
                                                        {{ $option->option_text }}
                                                    </div>
                                                </div>
                                            </label>
                                            @endforeach
                                        </div>
                                    @else
                                        <textarea class="w-full rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-brand-500 focus:ring-brand-500 transition-all p-4 text-sm auto-save" 
                                                  rows="5" 
                                                  data-question="{{ $question->id }}" 
                                                  placeholder="Ketik jawaban Anda di sini...">{{ $savedAnswer?->essay_answer }}</textarea>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </form>
                
                <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-100 flex items-center justify-between">
                    @if($paginator->previousPageUrl())
                        <a href="{{ $paginator->previousPageUrl() }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-slate-700 bg-white border border-slate-300 rounded-lg shadow-sm hover:bg-slate-50 transition">
                            <i class="bi bi-arrow-left"></i> Sebelumnya
                        </a>
                    @else
                        <div></div>
                    @endif
                    
                    @if($paginator->nextPageUrl())
                        <a href="{{ $paginator->nextPageUrl() }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-brand-600 rounded-lg shadow-sm hover:bg-brand-700 transition">
                            Selanjutnya <i class="bi bi-arrow-right"></i>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- SIDEBAR TIMER --}}
    <div class="w-full lg:w-80 flex-shrink-0">
        <div class="sticky top-24 rounded-2xl bg-white/80 backdrop-blur-xl border border-slate-200/60 shadow-sm p-6 text-center">
            <div class="mb-4">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-brand-100 text-brand-600 mb-3">
                    <i class="bi bi-stopwatch text-2xl"></i>
                </div>
                <h6 class="text-sm font-bold text-slate-500 uppercase tracking-wider">Sisa Waktu</h6>
            </div>
            
            <div class="font-mono text-5xl font-black tracking-tight text-slate-800 mb-2" id="timer">
                --:--:--
            </div>
            
            <div class="text-sm font-medium text-slate-400 mb-6 pb-6 border-b border-slate-100">
                <i class="bi bi-files me-1"></i> Total {{ $totalQuestions }} Soal
            </div>
            
            <button type="button" class="w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 text-sm font-bold text-white bg-slate-900 rounded-xl shadow-md hover:bg-slate-800 transition" onclick="finishQuiz()">
                <i class="bi bi-check2-all text-lg"></i> Selesaikan Ujian
            </button>
            <p class="mt-3 text-[11px] text-slate-400 font-medium">Pastikan semua soal terjawab sebelum klik Selesai.</p>
        </div>
    </div>
</div>

@push('scripts')
<script>
const saveUrl = @json(route('student.quizzes.save-answer', $quiz));
const timeUrl = @json(route('student.quizzes.time-remaining', $quiz));
const autoSubmitUrl = @json(route('student.quizzes.auto-submit', $quiz));
const csrf = @json(csrf_token());
let autoSubmitted = false;

function formatTime(sec) {
    const h = Math.floor(sec / 3600);
    const m = Math.floor((sec % 3600) / 60);
    const s = sec % 60;
    return [h,m,s].map(v => String(v).padStart(2,'0')).join(':');
}

async function syncTimer() {
    try {
        const res = await fetch(timeUrl, { headers: { 'Accept': 'application/json' } });
        const data = await res.json();
        const el = document.getElementById('timer');
        
        if (data.expired) {
            el.textContent = '00:00:00';
            el.classList.replace('text-slate-800', 'text-rose-600');
            el.classList.add('animate-pulse');
            
            if (!autoSubmitted && @json($quiz->auto_submit_on_timeout)) {
                autoSubmitted = true;
                Swal.fire({
                    title: 'Waktu Habis!',
                    text: 'Jawaban Anda sedang dikumpulkan secara otomatis.',
                    icon: 'warning',
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    timer: 3000
                });
                
                await fetch(autoSubmitUrl, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' } });
                window.location.href = @json(route('student.quizzes.result', $quiz));
            }
            return;
        }
        
        el.textContent = formatTime(data.remaining);
        
        // Peringatan jika sisa waktu < 5 menit
        if (data.remaining < 300 && !el.classList.contains('text-rose-500')) {
            el.classList.replace('text-slate-800', 'text-rose-500');
        }
    } catch (e) {
        console.error('Timer sync error:', e);
    }
}

setInterval(syncTimer, 1000);
syncTimer();

let timeoutId;
document.querySelectorAll('.auto-save').forEach(input => {
    const handler = () => {
        saveAnswer(input);
        
        // UI Update untuk Radio Buttons
        if (input.type === 'radio') {
            const groupName = input.getAttribute('name');
            document.querySelectorAll(`input[name="${groupName}"]`).forEach(r => {
                const label = r.closest('.option-label');
                if (label) {
                    label.classList.remove('border-brand-500', 'bg-brand-50', 'shadow-sm', 'ring-1', 'ring-brand-500');
                    label.classList.add('border-slate-200', 'bg-white');
                }
            });
            const selectedLabel = input.closest('.option-label');
            if (selectedLabel) {
                selectedLabel.classList.remove('border-slate-200', 'bg-white');
                selectedLabel.classList.add('border-brand-500', 'bg-brand-50', 'shadow-sm', 'ring-1', 'ring-brand-500');
            }
        }
    };
    
    input.addEventListener('change', handler);
    if (input.tagName === 'TEXTAREA') {
        input.addEventListener('keyup', () => { 
            clearTimeout(timeoutId); 
            timeoutId = setTimeout(handler, 800); 
        });
    }
});

function saveAnswer(el) {
    const qId = el.dataset.question;
    const body = { _token: csrf, question_id: qId };
    
    if (el.tagName === 'TEXTAREA') {
        body.essay_answer = el.value;
    } else {
        body.option_id = el.value;
    }
    
    fetch(saveUrl, { 
        method: 'POST', 
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' }, 
        body: JSON.stringify(body) 
    }).catch(e => console.error('Gagal menyimpan jawaban', e));
}

function finishQuiz() {
    Swal.fire({
        title: 'Konfirmasi Selesai',
        text: 'Apakah Anda yakin ingin menyelesaikan ujian? Jawaban tidak dapat diubah lagi.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#0f172a',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Selesaikan',
        cancelButtonText: 'Batal',
        customClass: { confirmButton: 'rounded-xl', cancelButton: 'rounded-xl' }
    }).then((result) => {
        if (result.isConfirmed) {
            if (document.fullscreenElement) document.exitFullscreen().catch(()=>{});
            document.getElementById('quizForm').submit();
        }
    });
}

// ═══════════════════════════════════════════════
// Anti-Cheat System (CBT Mode) — Server-Side Logging
// ═══════════════════════════════════════════════
const quizId = @json($quiz->id);
const cheatLogUrl = '/api/cbt/cheat-log';

function logViolation(type, metadata = null) {
    fetch(cheatLogUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrf,
            'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
        body: JSON.stringify({
            quiz_id: quizId,
            violation_type: type,
            metadata: metadata,
        }),
    }).catch(() => {}); // silent fail
}

// Block right-click
document.addEventListener('contextmenu', e => {
    e.preventDefault();
    logViolation('right_click');
});

// Block copy/paste/cut
['copy', 'cut', 'paste'].forEach(evt => {
    document.addEventListener(evt, e => {
        e.preventDefault();
        logViolation(evt === 'cut' ? 'copy' : evt);
    });
});

// Detect tab switch / alt-tab
let tabSwitchCount = 0;
document.addEventListener('visibilitychange', () => {
    if (document.visibilityState === 'hidden') {
        tabSwitchCount++;
        logViolation('tab_switch', { count: tabSwitchCount });

        Swal.fire({
            title: 'Pelanggaran Ujian!',
            html: `<p>Anda terdeteksi meninggalkan halaman ujian <strong>(${tabSwitchCount} kali)</strong>.</p><p class="text-sm text-slate-500 mt-2">Semua aktivitas dicatat secara otomatis oleh sistem dan akan dilaporkan ke guru pengawas.</p>`,
            icon: 'error',
            confirmButtonColor: '#e11d48',
            confirmButtonText: 'Kembali ke Ujian',
        });
    }
});

// Detect fullscreen exit
document.addEventListener('fullscreenchange', () => {
    if (!document.fullscreenElement) {
        logViolation('fullscreen_exit');
    }
});

// Detect DevTools (F12, Ctrl+Shift+I/J/C)
document.addEventListener('keydown', e => {
    if (e.key === 'F12' || 
        (e.ctrlKey && e.shiftKey && ['I','J','C'].includes(e.key.toUpperCase()))) {
        e.preventDefault();
        logViolation('devtools');
    }
});

// Fullscreen enforcer
function enterFullscreen() {
    const elem = document.documentElement;
    if (elem.requestFullscreen) elem.requestFullscreen();
    else if (elem.webkitRequestFullscreen) elem.webkitRequestFullscreen();
    else if (elem.msRequestFullscreen) elem.msRequestFullscreen();
}

document.body.addEventListener('click', function fullscreenHandler() {
    enterFullscreen();
    document.body.removeEventListener('click', fullscreenHandler);
}, { once: true });

</script>
@endpush
@endsection
