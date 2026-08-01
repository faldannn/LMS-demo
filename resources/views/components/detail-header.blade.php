@props([
    'class' => '',
])

<div {{ $attributes->merge(['class' => trim('d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 ' . $class)]) }}>
    <div class="min-w-0 flex-grow-1">
        {{ $slot }}
    </div>

    @isset($actions)
        <div class="d-flex flex-wrap gap-2 ms-auto">
            {{ $actions }}
        </div>
    @endisset
</div>