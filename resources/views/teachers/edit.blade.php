@extends('layouts.app')
@section('title', 'Edit Guru')
@section('page-title', 'Edit Guru')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-slate-500">Edit data guru: <strong class="text-slate-800">{{ $teacher->name }}</strong></p>
        <div class="flex items-center gap-2">
            <a href="{{ route('teachers.show', $teacher) }}" class="inline-flex items-center gap-1.5 rounded-lg border border-brand-200 bg-brand-50 px-4 py-2 text-sm font-semibold text-brand-600 shadow-sm transition hover:bg-brand-100 hover:text-brand-700">
                <i class="bi bi-eye"></i>Detail
            </a>
            <a href="{{ route('teachers.index') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                <i class="bi bi-arrow-left"></i>Kembali
            </a>
        </div>
    </div>

    <div class="mx-auto max-w-4xl">
        <form method="POST" action="{{ route('teachers.update', $teacher) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <x-card title="Edit Data Guru" icon="bi-pencil-square" glass="true">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <x-form-input name="nip" label="NIP" value="{{ old('nip', $teacher->nip) }}" />
                    <x-form-input name="name" label="Nama Lengkap" value="{{ old('name', $teacher->name) }}" required />
                    <x-form-input name="dob" type="date" label="Tanggal Lahir (Opsional)" value="{{ old('dob', $teacher->dob ? $teacher->dob->format('Y-m-d') : '') }}" />
                    <div class="sm:col-span-2">
                        <label class="mb-1.5 block text-sm font-semibold text-slate-700">Mata Pelajaran yang Diajar</label>
                        <div class="grid grid-cols-1 gap-2 rounded-xl border border-slate-300 bg-slate-50 p-4 max-h-48 overflow-y-auto sm:grid-cols-2">
                            @foreach($subjects as $subj)
                                <label class="inline-flex items-center gap-2 cursor-pointer p-1 rounded hover:bg-slate-100 transition duration-150">
                                    <input type="checkbox" name="subject_ids[]" value="{{ $subj->id }}" 
                                        {{ in_array($subj->id, old('subject_ids', $teacher->subjects->pluck('id')->toArray())) ? 'checked' : '' }}
                                        class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                                    <span class="text-sm text-slate-700">{{ $subj->name }} <span class="text-xs text-slate-400">({{ $subj->code }})</span></span>
                                </label>
                            @endforeach
                        </div>
                        @error('subject_ids')
                            <p class="mt-1.5 text-sm font-medium text-rose-600"><i class="bi bi-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>
                    <x-form-input name="phone" label="No. HP" value="{{ old('phone', $teacher->phone) }}" />
                    
                    <div>
                        <x-form-input name="email" type="email" label="Email" value="{{ old('email', $teacher->email) }}" />
                    </div>
                    <div>
                        <x-form-input name="password" type="password" label="Ganti Password (Kosongkan jika tidak diubah)" placeholder="Masukkan password baru" />
                    </div>
                    
                    <div class="sm:col-span-2">
                        <label class="mb-1 block text-xs font-bold text-slate-500">Ganti Foto</label>
                        <input type="file" name="photo" accept="image/*"
                            class="block w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-800 transition focus:border-brand-500 focus:bg-white focus:ring-2 focus:ring-brand-500/20">
                    </div>
                    
                    <x-form-input name="status" type="select" label="Status">
                        <option value="1" {{ old('status', $teacher->status) ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ !old('status', $teacher->status) ? 'selected' : '' }}>Non-Aktif</option>
                    </x-form-input>
                </div>

                <div class="mt-6 flex flex-col gap-3 pt-4 border-t border-slate-100 sm:flex-row">
                    <button type="submit" class="inline-flex flex-1 items-center justify-center gap-2 rounded-xl bg-brand-600 px-6 py-3.5 text-base font-bold text-white shadow-sm transition hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2">
                        <i class="bi bi-save"></i>Simpan Perubahan
                    </button>
                    <button type="button" class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl border border-rose-200 bg-white px-6 py-3.5 text-base font-bold text-rose-600 shadow-sm transition hover:bg-rose-50 hover:text-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2"
                        data-confirm="Hapus guru {{ $teacher->name }}?" data-form="del-{{ $teacher->id }}">
                        <i class="bi bi-trash"></i>Hapus Guru
                    </button>
                </div>
            </x-card>
        </form>

        <form id="del-{{ $teacher->id }}" method="POST" action="{{ route('teachers.destroy', $teacher) }}" class="hidden">
            @csrf @method('DELETE')
        </form>
    </div>
</div>
@endsection
