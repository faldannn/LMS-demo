@extends('layouts.app')

@section('title', 'Hasil Ujian CBT — ' . $attempt->quiz?->title)

@section('content')
<div class="mx-auto max-w-3xl space-y-6 p-4 sm:p-6 lg:p-8">
    <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm text-center">
        @php
            $isPassed = $attempt->score >= ($attempt->quiz?->pass_score ?? 75);
        @endphp

        <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-3xl {{ $isPassed ? 'bg-emerald-100 text-emerald-600' : 'bg-amber-100 text-amber-600' }} mb-4 shadow-sm">
            <i class="bi {{ $isPassed ? 'bi-trophy-fill' : 'bi-award-fill' }} text-4xl"></i>
        </div>

        <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold {{ $isPassed ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
            <i class="bi bi-check-circle-fill"></i>
            <span>{{ $isPassed ? 'LULUS KKM' : 'PERLU REMEDIAL' }}</span>
        </span>

        <h1 class="text-2xl font-black text-slate-900 mt-2 sm:text-3xl">Ujian Telah Selesai</h1>
        <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto">
            Terima kasih telah mengerjakan <strong>{{ $attempt->quiz?->title }}</strong> secara jujur dan tertib.
        </p>

        {{-- SKOR AKHIR --}}
        <div class="my-6 inline-block rounded-3xl bg-slate-50 border border-slate-200/80 p-6 min-w-[240px]">
            <div class="text-xs font-bold uppercase tracking-wider text-slate-400">Nilai Akhir Anda</div>
            <div class="text-5xl font-black text-slate-900 mt-1">
                {{ $attempt->score ?? 0 }}
            </div>
            <div class="text-xs text-slate-500 mt-1">
                KKM: <strong>{{ $attempt->quiz?->pass_score ?? 75 }}</strong>
            </div>
        </div>

        {{-- RINCIAN STATISTIK PENGERJAAN --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 my-6 text-left">
            <div class="rounded-2xl border border-slate-100 bg-slate-50/50 p-4">
                <div class="text-[10px] font-bold uppercase text-slate-400">Total Soal</div>
                <div class="text-lg font-bold text-slate-800 mt-0.5">{{ $attempt->quiz?->questions()->count() ?? 0 }}</div>
            </div>
            <div class="rounded-2xl border border-slate-100 bg-slate-50/50 p-4">
                <div class="text-[10px] font-bold uppercase text-slate-400">Jawaban Benar</div>
                <div class="text-lg font-bold text-emerald-600 mt-0.5">
                    {{ $attempt->answers()->where('is_correct', true)->count() }}
                </div>
            </div>
            <div class="rounded-2xl border border-slate-100 bg-slate-50/50 p-4">
                <div class="text-[10px] font-bold uppercase text-slate-400">Waktu Mulai</div>
                <div class="text-xs font-bold text-slate-800 mt-1">
                    {{ $attempt->start_time ? $attempt->start_time->format('H:i') : '-' }} WIB
                </div>
            </div>
            <div class="rounded-2xl border border-slate-100 bg-slate-50/50 p-4">
                <div class="text-[10px] font-bold uppercase text-slate-400">Waktu Selesai</div>
                <div class="text-xs font-bold text-slate-800 mt-1">
                    {{ $attempt->end_time ? $attempt->end_time->format('H:i') : '-' }} WIB
                </div>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 flex justify-center gap-3">
            <a href="{{ route('cbt.student.index') }}" class="inline-flex items-center gap-2 rounded-2xl bg-emerald-600 px-6 py-3 text-xs font-bold text-white shadow-sm hover:bg-emerald-500 transition">
                <i class="bi bi-arrow-left"></i>
                <span>Kembali ke Portal CBT</span>
            </a>
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-6 py-3 text-xs font-bold text-slate-700 hover:bg-slate-50 transition">
                <i class="bi bi-house"></i>
                <span>Dashboard Utama</span>
            </a>
        </div>
    </div>
</div>
@endsection
