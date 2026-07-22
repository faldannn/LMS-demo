@extends('layouts.app')
@section('title', 'Edit Siswa')
@section('page-title', 'Edit Siswa')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-slate-500">Edit data siswa: <strong class="text-slate-800">{{ $student->name }}</strong></p>
        <div class="flex items-center gap-2">
            <a href="{{ route('students.show', $student) }}" class="inline-flex items-center gap-1.5 rounded-lg border border-brand-200 bg-brand-50 px-4 py-2 text-sm font-semibold text-brand-600 shadow-sm transition hover:bg-brand-100 hover:text-brand-700">
                <i class="bi bi-eye"></i>Detail
            </a>
            <a href="{{ route('students.index') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                <i class="bi bi-arrow-left"></i>Kembali
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('students.update', $student) }}" enctype="multipart/form-data" class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        @csrf @method('PUT')
        <div class="lg:col-span-2">
            <x-card title="Data Siswa" icon="bi-pencil-square" glass="true">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <x-form-input name="nis" label="NIS" value="{{ old('nis', $student->nis) }}" required />
                    <x-form-input name="nisn" label="NISN" value="{{ old('nisn', $student->nisn) }}" />
                    
                    <div class="sm:col-span-2">
                        <x-form-input name="name" label="Nama Lengkap" value="{{ old('name', $student->name) }}" required />
                    </div>
                    
                    <x-form-input name="gender" type="select" label="Jenis Kelamin" required>
                        <option value="L" {{ old('gender', $student->gender) === 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('gender', $student->gender) === 'P' ? 'selected' : '' }}>Perempuan</option>
                    </x-form-input>
                    
                    <x-form-input name="dob" type="date" label="Tanggal Lahir" value="{{ old('dob', $student->dob?->toDateString()) }}" />
                    
                    <x-form-input name="class_id" type="select" label="Kelas" required>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ old('class_id', $student->class_id) == $c->id ? 'selected' : '' }}>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </x-form-input>

                    <x-form-input name="status" type="select" label="Status">
                        <option value="1" {{ old('status', $student->status) ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ !old('status', $student->status) ? 'selected' : '' }}>Non-Aktif</option>
                    </x-form-input>
                    
                    <x-form-input name="parent_name" label="Nama Orang Tua" value="{{ old('parent_name', $student->parent_name) }}" />
                    <x-form-input name="parent_phone" label="No. HP Orang Tua" value="{{ old('parent_phone', $student->parent_phone) }}" />
                    
                    <div class="sm:col-span-2">
                        <x-form-input name="parent_email" type="email" label="Email Orang Tua" value="{{ old('parent_email', $student->parent_email) }}" />
                    </div>
                    
                    <div class="sm:col-span-2">
                        <x-form-input name="address" type="textarea" label="Alamat">{{ old('address', $student->address) }}</x-form-input>
                    </div>
                </div>
            </x-card>
        </div>

        <div class="space-y-6 lg:col-span-1">
            <x-card title="Foto Siswa" icon="bi-camera" glass="true">
                <div class="text-center">
                    <div id="photo-preview" class="mx-auto mb-4 flex h-32 w-32 items-center justify-center overflow-hidden rounded-full border-4 border-solid border-brand-200 bg-brand-50 shadow-inner">
                        <img src="{{ $student->photo_url }}" class="h-full w-full object-cover" alt="">
                    </div>
                    <input type="file" name="photo" id="photo-input" class="hidden" accept="image/*" onchange="previewPhoto(this)">
                    <button type="button" onclick="document.getElementById('photo-input').click()" class="inline-flex items-center gap-1.5 rounded-lg border border-brand-300 bg-white px-4 py-2 text-sm font-semibold text-brand-700 shadow-sm transition hover:bg-brand-50">
                        <i class="bi bi-upload"></i>Ganti Foto
                    </button>
                </div>
            </x-card>

            <x-card title="Password Akun Siswa" icon="bi-key" glass="true">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Password Baru Login</label>
                    <input type="password" name="password" placeholder="Kosongkan jika tidak diubah" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-800 transition focus:border-brand-500 focus:ring-brand-500">
                    @error('password')<span class="mt-1 block text-xs font-semibold text-rose-500">{{ $message }}</span>@enderror
                    <span class="mt-1 block text-xs text-slate-500">Ketik password baru jika ingin merubah password login siswa ini.</span>
                </div>
            </x-card>

            <div class="space-y-3">
                <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-brand-600 px-6 py-3 text-base font-bold text-white shadow-sm transition hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2">
                    <i class="bi bi-save"></i>Simpan Perubahan
                </button>
                <button type="button" onclick="if(confirm('Reset password {{ addslashes($student->name) }} ke default (Tanggal Lahir/NIS)?')) document.getElementById('reset-pw-{{ $student->id }}').submit();"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-amber-300 bg-amber-50 px-6 py-2.5 text-sm font-bold text-amber-700 shadow-sm transition hover:bg-amber-100 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2">
                    <i class="bi bi-key-fill"></i>Reset Password ke Default
                </button>
                <button type="button" class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-rose-200 bg-white px-6 py-3 text-base font-bold text-rose-600 shadow-sm transition hover:bg-rose-50 hover:text-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2"
                    data-confirm="Hapus siswa {{ $student->name }}?" data-form="del-{{ $student->id }}">
                    <i class="bi bi-trash"></i>Hapus Siswa
                </button>
            </div>
        </div>
    </form>

    <form id="reset-pw-{{ $student->id }}" method="POST" action="{{ route('students.reset-password', $student) }}" class="hidden">
        @csrf
    </form>
    <form id="del-{{ $student->id }}" method="POST" action="{{ route('students.destroy', $student) }}" class="hidden">
        @csrf @method('DELETE')
    </form>
</div>
@endsection

@push('scripts')
<script>
function previewPhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('photo-preview').innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
