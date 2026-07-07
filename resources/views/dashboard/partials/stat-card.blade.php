@props([
    'label',
    'value',
    'icon',
    'hint' => null,
    'tone' => 'brand',
])

@php
$toneMap = [
    'brand' => 'bg-brand-50 text-brand-600',
    'blue' => 'bg-blue-50 text-blue-600',
    'amber' => 'bg-amber-50 text-amber-600',
    'rose' => 'bg-rose-50 text-rose-600',
    'violet' => 'bg-violet-50 text-violet-600',
    'slate' => 'bg-slate-100 text-slate-600',
];
$iconClass = $toneMap[$tone] ?? $toneMap['brand'];
@endphp

<div class="group relative overflow-hidden rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200/70 transition hover:shadow-md">
    <div class="flex items-start justify-between gap-3">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $label }}</p>
            <p class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900">{{ $value }}</p>
            @if($hint)
                <p class="mt-1 text-xs font-medium text-slate-500">{{ $hint }}</p>
            @endif
        </div>
        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl {{ $iconClass }}">
            <i class="bi {{ $icon }} text-lg"></i>
        </div>
    </div>
</div>
