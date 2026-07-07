@extends('layouts.app')
@section('title', 'Tambah Siswa')
@section('page-title', 'Tambah Siswa')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <p class="text-sm text-slate-500">Isi form di bawah untuk menambah data siswa baru</p>
        <a href="{{ route('students.index') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
            <i class="bi bi-arrow-left"></i>Kembali
        </a>
    </div>

    <form method="POST" action="{{ route('students.store') }}" enctype="multipart/form-data" class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        @csrf
        <div class="lg:col-span-2">
            <x-card title="Data Siswa" icon="bi-person-plus" glass="true">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <x-form-input name="nis" label="NIS" value="{{ old('nis') }}" placeholder="Nomor Induk Siswa" required />
                    <x-form-input name="nisn" label="NISN" value="{{ old('nisn') }}" placeholder="Nomor Induk Siswa Nasional" />
                    
                    <div class="sm:col-span-2">
                        <x-form-input name="name" label="Nama Lengkap" value="{{ old('name') }}" placeholder="Nama lengkap siswa" required />
                    </div>
                    
                    <x-form-input name="gender" type="select" label="Jenis Kelamin" required>
                        <option value="">-- Pilih --</option>
                        <option value="L" {{ old('gender') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('gender') === 'P' ? 'selected' : '' }}>Perempuan</option>
                    </x-form-input>
                    
                    <x-form-input name="dob" type="date" label="Tanggal Lahir" value="{{ old('dob') }}" />
                    
                    <x-form-input name="class_id" type="select" label="Kelas" required>
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ old('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </x-form-input>
                    
                    <x-form-input name="religion" type="select" label="Agama">
                        <option value="">-- Pilih --</option>
                        @foreach(['Islam','Kristen','Katolik','Hindu','Buddha','Konghucu'] as $r)
                            <option value="{{ $r }}" {{ old('religion') === $r ? 'selected' : '' }}>{{ $r }}</option>
                        @endforeach
                    </x-form-input>
                    
                    <x-form-input name="parent_name" label="Nama Orang Tua" value="{{ old('parent_name') }}" placeholder="Nama ayah/ibu" />
                    <x-form-input name="parent_phone" label="No. HP Orang Tua" value="{{ old('parent_phone') }}" placeholder="08xxxxxxxxxx" />
                    
                    <div class="sm:col-span-2">
                        <x-form-input name="parent_email" type="email" label="Email Orang Tua" value="{{ old('parent_email') }}" placeholder="email@contoh.com" />
                    </div>
                    
                    <div class="sm:col-span-2">
                        <x-form-input name="address" type="textarea" label="Alamat" placeholder="Alamat lengkap">{{ old('address') }}</x-form-input>
                    </div>
                </div>
            </x-card>
        </div>

        <div class="space-y-6 lg:col-span-1">
            <x-card title="Foto Siswa" icon="bi-camera" glass="true">
                <div class="text-center">
                        <div id="photo-preview" class="mx-auto mb-4 flex h-32 w-32 items-center justify-center overflow-hidden rounded-full border-4 border-dashed border-brand-100 bg-brand-50 transition-all">
                        <i class="bi bi-person text-5xl text-brand-500"></i>
                    </div>
                    <input type="file" name="photo" id="photo-input" class="hidden" accept="image/*" onchange="previewPhoto(this)">
                        <button type="button" onclick="document.getElementById('photo-input').click()" class="inline-flex items-center gap-1.5 rounded-lg border border-brand-100 bg-white px-4 py-2 text-sm font-semibold text-brand-700 shadow-sm transition hover:bg-brand-50">
                        <i class="bi bi-upload"></i>Upload Foto
                    </button>
                </div>
            </x-card>

            <x-card title="Akun Sistem" icon="bi-shield-check" glass="true">
                <label class="relative flex cursor-pointer items-start gap-3">
                    <div class="flex h-6 items-center">
                        <input type="checkbox" name="create_account" id="create_account" class="h-5 w-5 rounded border-slate-300 text-brand-600 transition focus:ring-brand-500" {{ old('create_account') ? 'checked' : '' }}>
                    </div>
                    <div>
                        <span class="block text-sm font-semibold text-slate-800">Buat Akun Siswa</span>
                        <span class="block text-xs text-slate-500">Jika diaktifkan, akun login akan dibuat otomatis dengan username = NIS dan password = NIS.</span>
                    </div>
                </label>
            </x-card>

            <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-brand-600 px-6 py-3.5 text-base font-bold text-white shadow-sm transition hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2">
                <i class="bi bi-save"></i>Simpan Data Siswa
            </button>
        </div>
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
            document.getElementById('photo-preview').classList.remove('border-dashed', 'border-brand-200');
            document.getElementById('photo-preview').classList.add('border-solid', 'border-brand-500');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
