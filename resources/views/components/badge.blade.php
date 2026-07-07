{{--
    Badge Component
    Usage: <x-badge tone="success">Aktif</x-badge>
    Tones: brand, blue, emerald/success, amber/warning, rose/danger, violet, slate
--}}
@props([
    'tone' => 'brand',
])

@php
    $tones = [
        'brand' => 'bg-brand-100 text-brand-700',
        'blue' => 'bg-blue-100 text-blue-700',
        'emerald' => 'bg-emerald-100 text-emerald-700',
        'success' => 'bg-emerald-100 text-emerald-700',
        'amber' => 'bg-amber-100 text-amber-700',
        'warning' => 'bg-amber-100 text-amber-700',
        'rose' => 'bg-rose-100 text-rose-700',
        'danger' => 'bg-rose-100 text-rose-700',
        'violet' => 'bg-violet-100 text-violet-700',
        'slate' => 'bg-slate-100 text-slate-700',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-bold uppercase tracking-wide ' . ($tones[$tone] ?? $tones['brand'])]) }}>
    {{ $slot }}
</span>
