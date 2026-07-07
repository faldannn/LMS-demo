{{-- 
    Reusable Card Component
    Usage: <x-card title="Title" icon="bi-people" :actions="['create' => route('...')]">Content</x-card>
--}}
@props([
    'title' => null,
    'subtitle' => null,
    'icon' => null,
    'noPadding' => false,
    'glass' => false,
])

<div {{ $attributes->merge([
    'class' => 'rounded-2xl shadow-sm ring-1 ring-slate-200/60 overflow-hidden ' . 
               ($glass ? 'bg-white/80 backdrop-blur-xl' : 'bg-white')
]) }}>
    @if($title)
    <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
        <div class="flex items-center gap-2">
            @if($icon)<i class="bi {{ $icon }} text-brand-600"></i>@endif
            <div>
                <h3 class="text-base font-bold text-slate-900">{{ $title }}</h3>
                @if($subtitle)<p class="text-xs text-slate-500">{{ $subtitle }}</p>@endif
            </div>
        </div>
        @if(isset($actions))
            <div class="flex items-center gap-2">{{ $actions }}</div>
        @endif
    </div>
    @endif
    <div class="{{ $noPadding ? '' : 'p-5' }}">
        {{ $slot }}
    </div>
</div>
