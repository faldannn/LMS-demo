@props([
    'label',
    'value',
    'icon',
    'hint' => null,
    'tone' => 'brand',
    'badge' => null,
])

@php
$toneConfig = [
    'cyan-gradient' => [
        'is_gradient' => true,
        'card' => 'bg-gradient-to-br from-sky-400 via-blue-500 to-indigo-600 text-white shadow-xl shadow-sky-500/30 border-white/40',
        'icon_wrap' => 'bg-white/25 text-white border border-white/40 shadow-sm backdrop-blur-md',
        'label' => 'text-white/85',
        'value' => 'text-white',
        'hint_bg' => 'bg-white/20 text-white border-white/30',
        'dot' => 'bg-white',
    ],
    'pink-gradient' => [
        'is_gradient' => true,
        'card' => 'bg-gradient-to-br from-pink-400 via-rose-500 to-purple-600 text-white shadow-xl shadow-pink-500/30 border-white/40',
        'icon_wrap' => 'bg-white/25 text-white border border-white/40 shadow-sm backdrop-blur-md',
        'label' => 'text-white/85',
        'value' => 'text-white',
        'hint_bg' => 'bg-white/20 text-white border-white/30',
        'dot' => 'bg-white',
    ],
    'violet' => [
        'is_gradient' => false,
        'card' => 'glass-card border-white/85',
        'icon_wrap' => 'bg-violet-500/15 text-violet-600 border border-violet-500/25 shadow-sm',
        'label' => 'text-slate-500',
        'value' => 'text-slate-900',
        'hint_bg' => 'bg-violet-50 text-violet-700 border-violet-100',
        'dot' => 'bg-violet-500',
    ],
    'brand' => [
        'is_gradient' => false,
        'card' => 'glass-card border-white/85',
        'icon_wrap' => 'bg-indigo-500/15 text-indigo-600 border border-indigo-500/25 shadow-sm',
        'label' => 'text-slate-500',
        'value' => 'text-slate-900',
        'hint_bg' => 'bg-indigo-50 text-indigo-700 border-indigo-100',
        'dot' => 'bg-indigo-500',
    ],
    'blue' => [
        'is_gradient' => false,
        'card' => 'glass-card border-white/85',
        'icon_wrap' => 'bg-blue-500/15 text-blue-600 border border-blue-500/25 shadow-sm',
        'label' => 'text-slate-500',
        'value' => 'text-slate-900',
        'hint_bg' => 'bg-blue-50 text-blue-700 border-blue-100',
        'dot' => 'bg-blue-500',
    ],
    'emerald' => [
        'is_gradient' => false,
        'card' => 'glass-card border-white/85',
        'icon_wrap' => 'bg-emerald-500/15 text-emerald-600 border border-emerald-500/25 shadow-sm',
        'label' => 'text-slate-500',
        'value' => 'text-slate-900',
        'hint_bg' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
        'dot' => 'bg-emerald-500',
    ],
    'amber' => [
        'is_gradient' => false,
        'card' => 'glass-card border-white/85',
        'icon_wrap' => 'bg-amber-500/15 text-amber-600 border border-amber-500/25 shadow-sm',
        'label' => 'text-slate-500',
        'value' => 'text-slate-900',
        'hint_bg' => 'bg-amber-50 text-amber-700 border-amber-100',
        'dot' => 'bg-amber-500',
    ],
    'rose' => [
        'is_gradient' => false,
        'card' => 'glass-card border-white/85',
        'icon_wrap' => 'bg-rose-500/15 text-rose-600 border border-rose-500/25 shadow-sm',
        'label' => 'text-slate-500',
        'value' => 'text-slate-900',
        'hint_bg' => 'bg-rose-50 text-rose-700 border-rose-100',
        'dot' => 'bg-rose-500',
    ],
    'slate' => [
        'is_gradient' => false,
        'card' => 'glass-card border-white/85',
        'icon_wrap' => 'bg-slate-500/15 text-slate-700 border border-slate-500/25 shadow-sm',
        'label' => 'text-slate-500',
        'value' => 'text-slate-900',
        'hint_bg' => 'bg-slate-100 text-slate-700 border-slate-200',
        'dot' => 'bg-slate-500',
    ],
];

$c = $toneConfig[$tone] ?? $toneConfig['brand'];
@endphp

<div class="group relative overflow-hidden rounded-3xl p-5 border transition-all duration-300 hover:-translate-y-1 {{ $c['card'] }}">
    {{-- Top header inside card: Avatar squircle + Micro status tag --}}
    <div class="flex items-center justify-between mb-3">
        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl {{ $c['icon_wrap'] }}">
            <i class="bi {{ $icon }} text-lg"></i>
        </div>
        <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider border {{ $c['hint_bg'] }}">
            <span class="h-1.5 w-1.5 rounded-full {{ $c['dot'] }} animate-pulse"></span>
            <span>{{ $badge ?? 'Aktif' }}</span>
        </div>
    </div>

    {{-- Content: Label & Value --}}
    <div>
        <p class="text-[11px] font-bold uppercase tracking-wider {{ $c['label'] }}">{{ $label }}</p>
        <p class="mt-1 text-3xl font-black tracking-tight {{ $c['value'] }}">{{ $value }}</p>
        @if($hint)
            <p class="mt-1.5 text-xs font-semibold {{ $c['label'] }} flex items-center gap-1">
                <span>{{ $hint }}</span>
            </p>
        @endif
    </div>
</div>
