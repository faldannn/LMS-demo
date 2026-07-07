@props([
    'createRoute' => null,
    'importRoute' => null,
    'exportExcelRoute' => null,
    'exportPdfRoute' => null,
    'trashedRoute' => null,
    'createLabel' => 'Tambah Data',
])

<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>{{ $slot }}</div>
    <div class="flex flex-wrap items-center gap-2">
        @if($trashedRoute)
            <a href="{{ $trashedRoute }}" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-sm font-medium text-slate-600 shadow-sm transition hover:bg-slate-50 hover:text-slate-800">
                <i class="bi bi-trash3"></i>Sampah
            </a>
        @endif
        @if($exportExcelRoute)
            <a href="{{ $exportExcelRoute }}" class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-300 bg-white px-3.5 py-2 text-sm font-medium text-emerald-700 shadow-sm transition hover:bg-emerald-50">
                <i class="bi bi-file-earmark-excel"></i>Excel
            </a>
        @endif
        @if($exportPdfRoute)
            <a href="{{ $exportPdfRoute }}" class="inline-flex items-center gap-1.5 rounded-lg border border-rose-300 bg-white px-3.5 py-2 text-sm font-medium text-rose-700 shadow-sm transition hover:bg-rose-50">
                <i class="bi bi-file-earmark-pdf"></i>PDF
            </a>
        @endif
        @if($importRoute)
            <button type="button" onclick="document.getElementById('importModal').classList.remove('hidden')" class="inline-flex items-center gap-1.5 rounded-lg border border-brand-300 bg-white px-3.5 py-2 text-sm font-medium text-brand-700 shadow-sm transition hover:bg-brand-50">
                <i class="bi bi-upload"></i>Import
            </button>
        @endif
        @if($createRoute)
            <a href="{{ $createRoute }}" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700">
                <i class="bi bi-plus-lg"></i>{{ $createLabel }}
            </a>
        @endif
    </div>
</div>

@if($importRoute)
{{-- Modal Import --}}
<div id="importModal" class="fixed inset-0 z-[60] hidden flex items-center justify-center bg-slate-900/50 backdrop-blur-sm" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
        <form method="POST" action="{{ $importRoute }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-900">Import Excel</h3>
                <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')" class="rounded-lg p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-600">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="mb-4">
                <input type="file" name="file" accept=".xlsx,.xls,.csv" required class="block w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-sm text-slate-700 file:mr-4 file:rounded-md file:border-0 file:bg-brand-50 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-brand-700 hover:file:bg-brand-100">
                @error('file')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-50">Batal</button>
                <button type="submit" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700">Upload</button>
            </div>
        </form>
    </div>
</div>
@endif
