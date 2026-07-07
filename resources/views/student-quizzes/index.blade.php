@extends('layouts.app')
@section('title', 'Kuis & Ujian Saya')
@section('page-title', 'Kuis & Ujian Saya')

@section('content')
<div class="space-y-8">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-extrabold text-slate-900">Daftar Kuis & Ujian</h2>
            <p class="mt-1 text-sm text-slate-500">Kerjakan ujian yang diberikan oleh guru Anda.</p>
        </div>
    </div>

    @forelse($classSubjects as $classSubject)
        <div>
            <h3 class="mb-4 flex items-center gap-2 border-b border-slate-200 pb-2 text-base font-bold text-slate-800">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-50 text-brand-600">
                    <i class="bi bi-book"></i>
                </div>
                {{ $classSubject->subject->name }}
            </h3>
            
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @forelse($classSubject->quizzes as $quiz)
                    @php
                        $attempt = $quiz->attempts->first();
                    @endphp
                    <div class="flex flex-col overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/70 transition hover:shadow-md hover:ring-brand-200">
                        <div class="flex-1 p-5">
                            <div class="flex items-start justify-between gap-2">
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-50 px-2 py-0.5 text-[10px] font-bold text-slate-600 ring-1 ring-slate-200">
                                    <i class="bi bi-stopwatch"></i>{{ $quiz->duration }} Menit
                                </span>

                                @if(!$attempt)
                                    <span class="inline-flex items-center rounded-full bg-blue-50 px-2 py-0.5 text-[10px] font-bold text-blue-700 ring-1 ring-blue-200">
                                        Baru
                                    </span>
                                @elseif($attempt->status === 'ongoing')
                                    <span class="inline-flex items-center rounded-full bg-amber-50 px-2 py-0.5 text-[10px] font-bold text-amber-700 ring-1 ring-amber-200">
                                        Sedang Dikerjakan
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700 ring-1 ring-emerald-200">
                                        Selesai
                                    </span>
                                @endif
                            </div>

                            <h4 class="mt-3 text-base font-bold text-slate-900">{{ $quiz->title }}</h4>
                            
                            <div class="mt-3 flex items-center gap-2 text-xs font-medium {{ $quiz->end_time && $quiz->end_time->isPast() ? 'text-rose-600' : 'text-slate-500' }}">
                                <i class="bi bi-calendar-x"></i>
                                Tutup: {{ $quiz->end_time ? $quiz->end_time->format('d M Y, H:i') : 'Tidak dibatasi' }}
                            </div>
                        </div>

                        <div class="border-t border-slate-100 bg-slate-50/50 p-4">
                            @if($attempt && $attempt->status === 'finished')
                                <a href="{{ route('student.quizzes.result', $quiz) }}" class="flex w-full items-center justify-center gap-2 rounded-xl bg-white px-4 py-2 text-xs font-bold text-emerald-600 shadow-sm ring-1 ring-emerald-200 transition hover:bg-emerald-50 hover:text-emerald-700">
                                    <i class="bi bi-clipboard-data"></i>Lihat Hasil
                                </a>
                            @elseif($attempt && $attempt->status === 'ongoing')
                                <a href="{{ route('student.quizzes.attempt', $quiz) }}" class="flex w-full items-center justify-center gap-2 rounded-xl bg-amber-500 px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-amber-600">
                                    <i class="bi bi-play-circle"></i>Lanjutkan
                                </a>
                            @else
                                <a href="{{ route('student.quizzes.show', $quiz) }}" class="flex w-full items-center justify-center gap-2 rounded-xl bg-brand-600 px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-brand-700">
                                    <i class="bi bi-play-circle"></i>Mulai Kerjakan
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full rounded-xl border border-dashed border-slate-300 bg-slate-50 px-6 py-8 text-center text-sm font-medium text-slate-500">
                        Belum ada ujian/kuis untuk mata pelajaran ini.
                    </div>
                @endforelse
            </div>
        </div>
    @empty
        <div class="rounded-2xl bg-white p-12 text-center shadow-sm ring-1 ring-slate-200/70">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-50">
                <i class="bi bi-ui-radios text-3xl text-slate-400"></i>
            </div>
            <h3 class="mt-4 text-base font-bold text-slate-900">Tidak ada jadwal ujian</h3>
            <p class="mt-2 text-sm text-slate-500">Anda tidak terdaftar dalam kelas apapun.</p>
        </div>
    @endforelse
</div>
@endsection
