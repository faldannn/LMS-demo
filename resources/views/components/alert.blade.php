{{--
    Alert / Status Badge Component
    Usage: <x-alert type="success">Berhasil disimpan!</x-alert>
    Types: success, error, warning, info
--}}
@props([
    'type' => 'info',
    'dismissible' => true,
    'icon' => null,
])

@php
    $config = match($type) {
        'success' => ['bg' => 'bg-emerald-50', 'border' => 'border-emerald-200', 'text' => 'text-emerald-800', 'icon' => $icon ?? 'bi-check-circle-fill', 'iconColor' => 'text-emerald-500'],
        'error', 'danger' => ['bg' => 'bg-rose-50', 'border' => 'border-rose-200', 'text' => 'text-rose-800', 'icon' => $icon ?? 'bi-exclamation-triangle-fill', 'iconColor' => 'text-rose-500'],
        'warning' => ['bg' => 'bg-amber-50', 'border' => 'border-amber-200', 'text' => 'text-amber-800', 'icon' => $icon ?? 'bi-exclamation-circle-fill', 'iconColor' => 'text-amber-500'],
        default => ['bg' => 'bg-sky-50', 'border' => 'border-sky-200', 'text' => 'text-sky-800', 'icon' => $icon ?? 'bi-info-circle-fill', 'iconColor' => 'text-sky-500'],
    };
@endphp

<div {{ $attributes->merge(['class' => "relative rounded-xl border {$config['border']} {$config['bg']} px-4 py-3 text-sm {$config['text']} shadow-sm"]) }}>
    <div class="flex items-center gap-3">
        <i class="bi {{ $config['icon'] }} text-lg {{ $config['iconColor'] }} shrink-0"></i>
        <div class="flex-1 font-medium">{{ $slot }}</div>
    </div>
    @if($dismissible)
    <button type="button" class="absolute right-3 top-3 {{ $config['iconColor'] }} opacity-60 transition hover:opacity-100" onclick="this.parentElement.remove()">
        <i class="bi bi-x-lg text-xs"></i>
    </button>
    @endif
</div>
