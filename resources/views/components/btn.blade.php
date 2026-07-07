{{--
    Button Component
    Usage: <x-btn>Default</x-btn>
           <x-btn variant="danger" icon="bi-trash">Hapus</x-btn>
           <x-btn variant="outline" href="/link">Link</x-btn>
    Variants: primary, secondary, danger, success, warning, outline, ghost
--}}
@props([
    'variant' => 'primary',
    'size' => 'md',
    'icon' => null,
    'href' => null,
    'type' => 'button',
])

@php
    $variants = [
        'primary' => 'bg-brand-600 text-white shadow-sm hover:bg-brand-700 focus:ring-brand-500',
        'secondary' => 'bg-slate-100 text-slate-700 hover:bg-slate-200 focus:ring-slate-400',
        'danger' => 'bg-rose-600 text-white shadow-sm hover:bg-rose-700 focus:ring-rose-500',
        'success' => 'bg-emerald-600 text-white shadow-sm hover:bg-emerald-700 focus:ring-emerald-500',
        'warning' => 'bg-amber-500 text-white shadow-sm hover:bg-amber-600 focus:ring-amber-400',
        'outline' => 'border border-slate-300 bg-white text-slate-700 shadow-sm hover:bg-slate-50 focus:ring-slate-400',
        'ghost' => 'text-slate-600 hover:bg-slate-100 hover:text-slate-800',
    ];
    
    $sizes = [
        'xs' => 'px-2.5 py-1 text-xs',
        'sm' => 'px-3 py-1.5 text-sm',
        'md' => 'px-4 py-2 text-sm',
        'lg' => 'px-5 py-2.5 text-base',
    ];
    
    $base = 'inline-flex items-center justify-center gap-1.5 rounded-lg font-semibold transition focus:outline-none focus:ring-2 focus:ring-offset-1 disabled:cursor-not-allowed disabled:opacity-50';
    $variantClass = isset($variants[$variant]) ? $variants[$variant] : $variants['primary'];
    $sizeClass = isset($sizes[$size]) ? $sizes[$size] : $sizes['md'];
    $classes = "{$base} {$variantClass} {$sizeClass}";
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon)<i class="bi {{ $icon }}"></i>@endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon)<i class="bi {{ $icon }}"></i>@endif
        {{ $slot }}
    </button>
@endif
