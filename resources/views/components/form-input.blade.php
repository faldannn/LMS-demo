{{--
    Form Input Group Component
    Usage: <x-form-input name="email" label="Email" type="email" :value="$user->email" required />
--}}
@props([
    'name',
    'label' => null,
    'type' => 'text',
    'value' => null,
    'placeholder' => null,
    'required' => false,
    'disabled' => false,
    'hint' => null,
])

<div {{ $attributes->only('class')->merge(['class' => 'mb-4']) }}>
    @if($label)
    <label for="{{ $name }}" class="mb-1.5 block text-sm font-semibold text-slate-700">
        {{ $label }}
        @if($required)<span class="text-rose-500">*</span>@endif
    </label>
    @endif
    
    @if($type === 'textarea')
        <textarea 
            name="{{ $name }}" 
            id="{{ $name }}" 
            rows="4"
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
            {{ $disabled ? 'disabled' : '' }}
            {{ $attributes->except('class')->merge([
                'class' => 'block w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 transition focus:border-brand-500 focus:bg-white focus:ring-2 focus:ring-brand-500/20 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-500' . ($errors->has($name) ? ' border-rose-400 ring-2 ring-rose-500/20' : '')
            ]) }}
        >{{ old($name, $value) }}</textarea>
    @elseif($type === 'select')
        <select 
            name="{{ $name }}" 
            id="{{ $name }}"
            {{ $required ? 'required' : '' }}
            {{ $disabled ? 'disabled' : '' }}
            {{ $attributes->except('class')->merge([
                'class' => 'block w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm text-slate-800 transition focus:border-brand-500 focus:bg-white focus:ring-2 focus:ring-brand-500/20 disabled:cursor-not-allowed disabled:bg-slate-100' . ($errors->has($name) ? ' border-rose-400 ring-2 ring-rose-500/20' : '')
            ]) }}
        >{{ $slot }}</select>
    @else
        <input 
            type="{{ $type }}" 
            name="{{ $name }}" 
            id="{{ $name }}" 
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
            {{ $disabled ? 'disabled' : '' }}
            {{ $attributes->except('class')->merge([
                'class' => 'block w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 transition focus:border-brand-500 focus:bg-white focus:ring-2 focus:ring-brand-500/20 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-500' . ($errors->has($name) ? ' border-rose-400 ring-2 ring-rose-500/20' : '')
            ]) }}
        >
    @endif
    
    @if($hint)
        <p class="mt-1 text-xs text-slate-400">{{ $hint }}</p>
    @endif
    
    @error($name)
        <p class="mt-1.5 text-sm font-medium text-rose-600"><i class="bi bi-exclamation-circle mr-1"></i>{{ $message }}</p>
    @enderror
</div>
