{{--
    Modal Component (vanilla JS, no Bootstrap dependency)
    Usage: <x-modal id="deleteModal" title="Hapus Data">...body...</x-modal>
    Open:  document.getElementById('deleteModal').classList.remove('hidden')
    Close: document.getElementById('deleteModal').classList.add('hidden')
--}}
@props([
    'id',
    'title' => 'Dialog',
    'maxWidth' => 'max-w-lg',
])

<div id="{{ $id }}" class="fixed inset-0 z-[60] hidden" role="dialog" aria-modal="true">
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" onclick="document.getElementById('{{ $id }}').classList.add('hidden')"></div>
    
    {{-- Panel --}}
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="w-full {{ $maxWidth }} rounded-2xl bg-white shadow-2xl ring-1 ring-slate-200/60">
            {{-- Header --}}
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                <h3 class="text-lg font-bold text-slate-900">{{ $title }}</h3>
                <button type="button" onclick="document.getElementById('{{ $id }}').classList.add('hidden')" class="rounded-lg p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            {{-- Body --}}
            <div class="px-6 py-5">
                {{ $slot }}
            </div>
            {{-- Footer --}}
            @if(isset($footer))
            <div class="flex items-center justify-end gap-2 border-t border-slate-100 px-6 py-4">
                {{ $footer }}
            </div>
            @endif
        </div>
    </div>
</div>
