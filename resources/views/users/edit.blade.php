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
    <div class="col-lg-8">
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
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Username <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control @error('username') is-invalid @enderror"
                                value="{{ old('username', $user->username) }}" required>
                            @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email', $user->email) }}">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Password Baru <span class="text-muted font-normal text-xs">(Kosongkan jika tidak diubah)</span></label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Ketik password baru...">
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
                            <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                                @if(auth()->user()->isSuperAdmin())
                                    <option value="superadmin" {{ old('role', $user->role->value ?? $user->role)==='superadmin' ? 'selected':'' }}>Super Admin</option>
                                @endif
                                <option value="admin" {{ old('role', $user->role->value ?? $user->role)==='admin' ? 'selected':'' }}>Admin</option>
                                <option value="guru" {{ old('role', $user->role->value ?? $user->role)==='guru' ? 'selected':'' }}>Guru</option>
                                <option value="siswa" {{ old('role', $user->role->value ?? $user->role)==='siswa' ? 'selected':'' }}>Siswa</option>
                            </select>
                            @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- Siswa Fields -->
                        <div id="siswa-fields" class="col-12 mt-3" style="display: none;">
                            <hr class="my-4 border-slate-200">
                            <h5 class="text-base font-bold text-slate-800 mb-3"><i class="bi bi-mortarboard-fill me-2 text-primary"></i>Detail Siswa</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">NIS <span class="text-danger">*</span></label>
                                    <input type="text" name="nis" class="form-control @error('nis') is-invalid @enderror" value="{{ old('nis', $user->student?->nis) }}" placeholder="Nomor Induk Siswa">
                                    @error('nis')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">NISN</label>
                                    <input type="text" name="nisn" class="form-control @error('nisn') is-invalid @enderror" value="{{ old('nisn', $user->student?->nisn) }}" placeholder="Nomor Induk Siswa Nasional">
                                    @error('nisn')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Jenis Kelamin <span class="text-danger">*</span></label>
                                    <select name="gender" class="form-select @error('gender') is-invalid @enderror">
                                        <option value="">-- Pilih --</option>
                                        <option value="L" {{ old('gender', $user->student?->gender) === 'L' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="P" {{ old('gender', $user->student?->gender) === 'P' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                    @error('gender')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Kelas <span class="text-danger">*</span></label>
                                    <select name="class_id" class="form-select @error('class_id') is-invalid @enderror">
                                        <option value="">-- Pilih Kelas --</option>
                                        @foreach($classes as $c)
                                            <option value="{{ $c->id }}" {{ old('class_id', $user->student?->class_id) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('class_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Tanggal Lahir</label>
                                    <input type="date" name="dob" class="form-control @error('dob') is-invalid @enderror" value="{{ old('dob', $user->student?->dob ? $user->student->dob->format('Y-m-d') : '') }}">
                                    @error('dob')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Tempat Lahir</label>
                                    <input type="text" name="pob" class="form-control @error('pob') is-invalid @enderror" value="{{ old('pob', $user->student?->pob) }}" placeholder="Tempat Lahir">
                                    @error('pob')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Nama Orang Tua</label>
                                    <input type="text" name="parent_name" class="form-control @error('parent_name') is-invalid @enderror" value="{{ old('parent_name', $user->student?->parent_name) }}" placeholder="Nama Ayah/Ibu">
                                    @error('parent_name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">No. HP Orang Tua</label>
                                    <input type="text" name="parent_phone" class="form-control @error('parent_phone') is-invalid @enderror" value="{{ old('parent_phone', $user->student?->parent_phone) }}" placeholder="08xxxxxxxxxx">
                                    @error('parent_phone')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Email Orang Tua</label>
                                    <input type="email" name="parent_email" class="form-control @error('parent_email') is-invalid @enderror" value="{{ old('parent_email', $user->student?->parent_email) }}" placeholder="email@contoh.com">
                                    @error('parent_email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Alamat</label>
                                    <textarea name="address" class="form-control @error('address') is-invalid @enderror" placeholder="Alamat lengkap siswa">{{ old('address', $user->student?->address) }}</textarea>
                                    @error('address')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <!-- Guru Fields -->
                        <div id="guru-fields" class="col-12 mt-3" style="display: none;">
                            <hr class="my-4 border-slate-200">
                            <h5 class="text-base font-bold text-slate-800 mb-3"><i class="bi bi-person-badge-fill me-2 text-primary"></i>Detail Guru</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">NIP</label>
                                    <input type="text" name="nip" class="form-control @error('nip') is-invalid @enderror" value="{{ old('nip', $user->teacher?->nip) }}" placeholder="Nomor Induk Pegawai">
                                    @error('nip')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Tanggal Lahir <span class="text-danger">*</span></label>
                                    <input type="date" name="dob" class="form-control @error('dob') is-invalid @enderror" value="{{ old('dob', $user->teacher?->dob ? $user->teacher->dob->format('Y-m-d') : '') }}">
                                    @error('dob')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">No. HP</label>
                                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $user->teacher?->phone) }}" placeholder="08xxxxxxxxxx">
                                    @error('phone')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Mata Pelajaran yang Diajar <span class="text-danger">*</span></label>
                                    <div class="grid grid-cols-1 gap-2 rounded-xl border border-slate-300 bg-slate-50 p-4 max-h-48 overflow-y-auto sm:grid-cols-2">
                                        @foreach($subjects as $subj)
                                            <label class="inline-flex items-center gap-2 cursor-pointer p-1 rounded hover:bg-slate-100 transition duration-150">
                                                <input type="checkbox" name="subject_ids[]" value="{{ $subj->id }}" 
                                                    {{ in_array($subj->id, old('subject_ids', $user->teacher ? $user->teacher->subjects->pluck('id')->toArray() : [])) ? 'checked' : '' }}
                                                    class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                                                <span class="text-sm text-slate-700">{{ $subj->name }} <span class="text-xs text-slate-400">({{ $subj->code }})</span></span>
                                            </label>
                                        @endforeach
                                    </div>
                                    @error('subject_ids')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-2 mt-4">
                        <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-save me-2"></i>Simpan Perubahan</button>
                        <button type="button" class="btn btn-warning text-dark"
                            onclick="if(confirm('Reset password {{ addslashes($user->name) }} ke default?')) document.getElementById('reset-pw-u{{ $user->id }}').submit();">
                            <i class="bi bi-key me-1"></i>Reset Password Default
                        </button>
                        @if($user->id !== auth()->id())
                            <button type="button" class="btn btn-outline-danger"
                                data-confirm="Hapus user {{ $user->name }}?" data-form="del-u{{ $user->id }}">
                                <i class="bi bi-trash"></i>
                            </button>
                        @endif
                    </div>
                </form>
                <form id="reset-pw-u{{ $user->id }}" method="POST" action="{{ route('users.reset-password', $user) }}" class="d-none">
                    @csrf @method('PUT')
                </form>
                @if($user->id !== auth()->id())
                    <form id="del-u{{ $user->id }}" method="POST" action="{{ route('users.destroy', $user) }}">@csrf @method('DELETE')</form>
                @endif
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const roleSelect = document.querySelector('select[name="role"]');
        const siswaFields = document.getElementById('siswa-fields');
        const guruFields = document.getElementById('guru-fields');

        function toggleFields() {
            const role = roleSelect.value;
            if (role === 'siswa') {
                siswaFields.style.display = 'block';
                guruFields.style.display = 'none';
                setFieldsRequired(siswaFields, true);
                setFieldsRequired(guruFields, false);
            } else if (role === 'guru') {
                siswaFields.style.display = 'none';
                guruFields.style.display = 'block';
                setFieldsRequired(siswaFields, false);
                setFieldsRequired(guruFields, true);
            } else {
                siswaFields.style.display = 'none';
                guruFields.style.display = 'none';
                setFieldsRequired(siswaFields, false);
                setFieldsRequired(guruFields, false);
            }
        }

        function setFieldsRequired(container, isRequired) {
            const inputs = container.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                const label = input.closest('div')?.querySelector('label');
                if (label && label.innerText.includes('*')) {
                    if (isRequired) {
                        input.setAttribute('required', 'required');
                    } else {
                        input.removeAttribute('required');
                    }
                }
            });
        }

        roleSelect.addEventListener('change', toggleFields);
        toggleFields();
    });
</script>
@endpush
@endsection
