{{-- resources/views/users/edit.blade.php --}}
@extends('layouts.app')
@section('title', 'Edit User')
@section('page-title', 'Edit User')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <p class="text-muted mb-0">Edit akun: <strong>{{ $user->name }}</strong></p>
    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
</div>
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Data User</div>
            <div class="card-body">
                <form method="POST" action="{{ route('users.update', $user) }}">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $user->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email', $user->email) }}">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
                            <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                                <option value="kepala_sekolah" {{ old('role', $user->role)==='kepala_sekolah' ? 'selected':'' }}>Kepala Sekolah</option>
                                <option value="guru" {{ old('role', $user->role)==='guru' ? 'selected':'' }}>Guru</option>
                                <option value="siswa" {{ old('role', $user->role)==='siswa' ? 'selected':'' }}>Siswa</option>
                            </select>
                            @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="alert alert-info mt-3 mb-0">
                        <i class="bi bi-info-circle me-2"></i>Untuk mengubah password, gunakan fitur Reset Password di halaman daftar user.
                    </div>
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-save me-2"></i>Simpan Perubahan</button>
                        @if($user->id !== auth()->id())
                            <form id="del-u{{ $user->id }}" method="POST" action="{{ route('users.destroy', $user) }}">@csrf @method('DELETE')</form>
                            <button type="button" class="btn btn-outline-danger"
                                data-confirm="Hapus user {{ $user->name }}?" data-form="del-u{{ $user->id }}">
                                <i class="bi bi-trash"></i>
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
