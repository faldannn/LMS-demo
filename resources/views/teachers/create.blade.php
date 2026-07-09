@extends('layouts.app')
@section('title', 'Tambah Guru')
@section('page-title', 'Tambah Guru')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <p class="text-sm text-slate-500">Tambah data guru baru ke sistem</p>
        <a href="{{ route('teachers.index') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
            <i class="bi bi-arrow-left"></i>Kembali
        </a>
    </div>

    <div class="mx-auto max-w-4xl">
        <form method="POST" action="{{ route('teachers.store') }}" enctype="multipart/form-data">
            @csrf
            <x-card title="Form Data Guru" icon="bi-person-plus" glass="true">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <x-form-input name="nip" label="NIP" value="{{ old('nip') }}" placeholder="Nomor Induk Pegawai" />
                    <x-form-input name="name" label="Nama Lengkap" value="{{ old('name') }}" placeholder="Nama lengkap guru" required />
                    <div class="sm:col-span-2">
                        <label class="mb-1.5 block text-sm font-semibold text-slate-700">Mata Pelajaran yang Diajar</label>
                        <div class="grid grid-cols-1 gap-2 rounded-xl border border-slate-300 bg-slate-50 p-4 max-h-48 overflow-y-auto sm:grid-cols-2">
                            @foreach($subjects as $subj)
                                <label class="inline-flex items-center gap-2 cursor-pointer p-1 rounded hover:bg-slate-100 transition duration-150">
                                    <input type="checkbox" name="subject_ids[]" value="{{ $subj->id }}" 
                                        {{ in_array($subj->id, old('subject_ids', [])) ? 'checked' : '' }}
                                        class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                                    <span class="text-sm text-slate-700">{{ $subj->name }} <span class="text-xs text-slate-400">({{ $subj->code }})</span></span>
                                </label>
                            @endforeach
                        </div>
                        @error('subject_ids')
                            <p class="mt-1.5 text-sm font-medium text-rose-600"><i class="bi bi-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>
                    <x-form-input name="phone" label="No. HP" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx" />
                    
                    <div class="sm:col-span-2">
                        <x-form-input name="email" type="email" label="Email" value="{{ old('email') }}" placeholder="email@contoh.com" />
                    </div>
                    
                    <div class="sm:col-span-2">
                        <label class="mb-1 block text-xs font-bold text-slate-500">Foto</label>
                        <input type="file" name="photo" accept="image/*"
                            class="block w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-800 transition focus:border-brand-500 focus:bg-white focus:ring-2 focus:ring-brand-500/20">
                    </div>
                    
                    <x-form-input name="status" type="select" label="Status">
                        <option value="1" selected>Aktif</option>
                        <option value="0">Non-Aktif</option>
                    </x-form-input>
                </div>

                <div class="mt-6">
                    <x-alert tone="info" icon="bi-info-circle">
                        Akun login guru akan dibuat otomatis. Username = NIP (atau email), password default: <strong>guru123</strong>
                    </x-alert>
                </div>

                <div class="mt-6 pt-4 border-t border-slate-100">
                    <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-brand-600 px-6 py-3.5 text-base font-bold text-white shadow-sm transition hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2">
                        <i class="bi bi-save"></i>Simpan Data Guru
                    </button>
                </div>
            </x-card>
        </form>
    </div>
</div>
@endsection
