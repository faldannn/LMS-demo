@extends('layouts.app')
@section('title', 'Edit Materi')
@section('page-title', 'Edit Materi')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <p class="text-sm text-slate-500">Edit Materi: <strong class="text-slate-800">{{ $classSubject->subject->name }}</strong></p>
        <a href="{{ route('materials.index', $classSubject) }}" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
            <i class="bi bi-arrow-left"></i>Kembali
        </a>
    </div>

    <div class="mx-auto max-w-3xl">
        <form action="{{ route('materials.update', $material) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/70">
                <div class="border-b border-slate-200 bg-slate-50/80 px-6 py-4">
                    <h3 class="flex items-center gap-2 text-base font-bold text-slate-800">
                        <i class="bi bi-pencil-square text-brand-600"></i>Form Edit Materi
                    </h3>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label class="mb-1.5 block text-sm font-medium text-slate-700">Judul Materi <span class="text-rose-500">*</span></label>
                            <input type="text" name="title" class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition-colors focus:border-brand-500 focus:ring-1 focus:ring-brand-500 @error('title') border-rose-500 focus:border-rose-500 focus:ring-rose-500 @enderror" value="{{ old('title', $material->title) }}" required>
                            @error('title')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700">Topik / Bab</label>
                            <input type="text" name="topic" class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition-colors focus:border-brand-500 focus:ring-1 focus:ring-brand-500" value="{{ old('topic', $material->topic) }}" placeholder="Bab 1">
                        </div>

                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700">Tipe Materi <span class="text-rose-500">*</span></label>
                            @php $currentType = old('type', $material->type === 'document' ? 'pdf' : $material->type); @endphp
                            <select name="type" id="typeSelect" class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition-colors focus:border-brand-500 focus:ring-1 focus:ring-brand-500" required>
                                <option value="text" @selected($currentType=='text')>Teks / Artikel (Tanpa File / Opsional)</option>
                                <option value="pdf" @selected($currentType=='pdf')>PDF</option>
                                <option value="ppt" @selected($currentType=='ppt')>PowerPoint</option>
                                <option value="video" @selected($currentType=='video')>Video</option>
                                <option value="youtube" @selected($currentType=='youtube')>YouTube</option>
                                <option value="image" @selected($currentType=='image')>Gambar</option>
                                <option value="link" @selected($currentType=='link')>Tautan</option>
                            </select>
                        </div>

                        <div class="sm:col-span-2" id="fileGroup">
                            <label class="mb-1.5 block text-sm font-medium text-slate-700">Ganti File (Opsional)</label>
                            <input type="file" name="file" class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 outline-none transition-colors focus:border-brand-500 focus:ring-1 focus:ring-brand-500 @error('file') border-rose-500 focus:border-rose-500 focus:ring-rose-500 @enderror">
                            @if($material->file_name)
                                <p class="mt-1.5 text-xs text-slate-500">File saat ini: <span class="font-semibold">{{ $material->file_name }}</span></p>
                            @endif
                            @error('file')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
                        </div>

                        <div class="hidden sm:col-span-2" id="urlGroup">
                            <label class="mb-1.5 block text-sm font-medium text-slate-700" id="urlLabel">URL</label>
                            <input type="url" name="url" class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition-colors focus:border-brand-500 focus:ring-1 focus:ring-brand-500 @error('url') border-rose-500 focus:border-rose-500 focus:ring-rose-500 @enderror" value="{{ old('url', $material->url) }}">
                            @error('url')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label class="mb-1.5 block text-sm font-medium text-slate-700">Deskripsi</label>
                            <textarea name="description" rows="4" class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 outline-none transition-colors focus:border-brand-500 focus:ring-1 focus:ring-brand-500">{{ old('description', $material->description) }}</textarea>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="relative flex cursor-pointer items-start gap-3">
                                <div class="flex h-6 items-center">
                                    <input type="hidden" name="is_published" value="0">
                                    <input type="checkbox" name="is_published" value="1" class="h-5 w-5 rounded border-slate-300 text-brand-600 transition focus:ring-brand-500" {{ old('is_published', $material->is_published) ? 'checked' : '' }}>
                                </div>
                                <div>
                                    <span class="block text-sm font-medium text-slate-800">Publikasikan ke siswa</span>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center justify-end gap-3 border-t border-slate-100 bg-slate-50/50 px-6 py-4">
                    <a href="{{ route('materials.index', $classSubject) }}" class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                        Batal
                    </a>
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-600 px-6 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2">
                        <i class="bi bi-save"></i>Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('typeSelect');
    const fileGroup = document.getElementById('fileGroup');
    const urlGroup = document.getElementById('urlGroup');
    const urlLabel = document.getElementById('urlLabel');
    
    function toggle() {
        const t = typeSelect.value;
        const needsFile = ['pdf','ppt','video','image'].includes(t);
        const needsUrl = ['youtube','link'].includes(t);
        
        if(needsFile) {
            fileGroup.classList.remove('hidden');
        } else {
            fileGroup.classList.add('hidden');
        }
        
        if(needsUrl) {
            urlGroup.classList.remove('hidden');
        } else {
            urlGroup.classList.add('hidden');
        }
        
        urlLabel.textContent = t === 'youtube' ? 'URL Video YouTube' : 'URL Tautan';
    }
    
    typeSelect.addEventListener('change', toggle);
    toggle();
});
</script>
@endpush
@endsection
