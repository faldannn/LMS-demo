{{--
    Data Table Component
    Usage: <x-data-table :headers="['Nama', 'Email', 'Aksi']">...rows...</x-data-table>
--}}
@props([
    'headers' => [],
    'striped' => true,
    'hoverable' => true,
    'emptyMessage' => 'Tidak ada data.',
    'emptyIcon' => 'bi-inbox',
])

<div class="overflow-x-auto rounded-xl border border-slate-200/80">
    <table class="w-full text-left text-sm">
        @if(count($headers) > 0)
        <thead>
            <tr class="border-b border-slate-100 bg-slate-50/80">
                @foreach($headers as $header)
                    <th class="whitespace-nowrap px-4 py-3 text-xs font-bold uppercase tracking-wider text-slate-500">{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        @endif
        <tbody class="divide-y divide-slate-100">
            {{ $slot }}
        </tbody>
    </table>
    
    @if(isset($empty) && $empty)
    <div class="py-12 text-center">
        <i class="bi {{ $emptyIcon }} text-4xl text-slate-300"></i>
        <p class="mt-2 text-sm text-slate-500">{{ $emptyMessage }}</p>
    </div>
    @endif
</div>
