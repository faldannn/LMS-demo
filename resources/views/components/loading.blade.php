{{--
    Loading Spinner Component
    Usage: <x-loading /> or <x-loading size="sm" />
--}}
@props([
    'size' => 'md', // sm, md, lg
    'color' => 'brand', // brand, white, slate
    'text' => null,
])

@php
    $sizeClasses = match($size) {
        'sm' => 'h-4 w-4 border-2',
        'lg' => 'h-10 w-10 border-4',
        default => 'h-6 w-6 border-3',
    };
    
    $colorClasses = match($color) {
        'white' => 'border-white border-t-transparent',
        'slate' => 'border-slate-400 border-t-transparent',
        default => 'border-brand-500 border-t-transparent',
    };
@endphp

<div {{ $attributes->merge(['class' => 'inline-flex items-center gap-2']) }}>
    <div class="{{ $sizeClasses }} {{ $colorClasses }} animate-spin rounded-full"></div>
    @if($text)
        <span class="text-sm font-medium text-slate-600">{{ $text }}</span>
    @endif
</div>