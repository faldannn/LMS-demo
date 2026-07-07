@extends('layouts.app')
@section('title', 'Edit Mata Pelajaran')
@section('page-title', 'Edit Mata Pelajaran')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <p class="text-sm text-slate-500">Edit data mata pelajaran: <strong class="text-slate-800">{{ $subject->name }}</strong></p>
        <a href="{{ route('subjects.index') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
            <i class="bi bi-arrow-left"></i>Kembali
        </a>
    </div>

    <div class="mx-auto max-w-3xl">
        <form action="{{ route('subjects.update', $subject) }}" method="POST">
            @csrf @method('PUT')
            <x-card title="Edit Mata Pelajaran" icon="bi-pencil-square" glass="true">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <x-form-input name="code" label="Kode Mapel" value="{{ old('code', $subject->code) }}" required />
                    
                    <div class="sm:col-span-2">
                        <x-form-input name="name" label="Nama Mata Pelajaran" value="{{ old('name', $subject->name) }}" required />
                    </div>
                    
                    <x-form-input name="group" type="select" label="Kelompok Mapel">
                        <option value="">-- Pilih Kelompok --</option>
                        <option value="Muatan Nasional" {{ old('group', $subject->group) == 'Muatan Nasional' ? 'selected' : '' }}>Muatan Nasional</option>
                        <option value="Muatan Kewilayahan" {{ old('group', $subject->group) == 'Muatan Kewilayahan' ? 'selected' : '' }}>Muatan Kewilayahan</option>
                        <option value="Muatan Peminatan" {{ old('group', $subject->group) == 'Muatan Peminatan' ? 'selected' : '' }}>Muatan Peminatan</option>
                        <option value="Muatan Lokal" {{ old('group', $subject->group) == 'Muatan Lokal' ? 'selected' : '' }}>Muatan Lokal</option>
                    </x-form-input>
                    
                    <x-form-input name="grade" type="select" label="Untuk Kelas">
                        <option value="">Semua Kelas</option>
                        <option value="10" {{ old('grade', $subject->grade) == '10' ? 'selected' : '' }}>Kelas 10 (X)</option>
                        <option value="11" {{ old('grade', $subject->grade) == '11' ? 'selected' : '' }}>Kelas 11 (XI)</option>
                        <option value="12" {{ old('grade', $subject->grade) == '12' ? 'selected' : '' }}>Kelas 12 (XII)</option>
                    </x-form-input>

                    <div class="sm:col-span-2">
                        <x-form-input name="description" type="textarea" label="Deskripsi">{{ old('description', $subject->description) }}</x-form-input>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="relative flex cursor-pointer items-start gap-3">
                            <div class="flex h-6 items-center">
                                <input type="hidden" name="status" value="0">
                                <input type="checkbox" name="status" value="1" class="h-5 w-5 rounded border-slate-300 text-brand-600 transition focus:ring-brand-500" {{ old('status', $subject->status) ? 'checked' : '' }}>
                            </div>
                            <div>
                                <span class="block text-sm font-semibold text-slate-800">Status Aktif</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('subjects.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                        Batal
                    </a>
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-600 px-6 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2">
                        <i class="bi bi-save"></i>Simpan Perubahan
                    </button>
                </div>
            </x-card>
        </form>
    </div>
</div>
@endsection
