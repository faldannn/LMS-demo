{{-- resources/views/users/profile.blade.php --}}
@extends('layouts.app')
@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')

@section('content')
<div class="row g-4 justify-content-center">
    @if($user->needsPasswordChange())
        <div class="col-12">
            <div class="alert alert-danger border-2 border-danger shadow-sm">
                <h5 class="alert-heading fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i> PERHATIAN: Wajib Ganti Password Bawaan</h5>
                <p class="mb-0">
                    Anda saat ini masih menggunakan password bawaan. Demi keamanan akun Anda, <strong>Anda diwajibkan mengubah password terlebih dahulu</strong> pada form Ganti Password di bawah sebelum dapat mengakses fitur LMS lainnya.
                </p>
            </div>
        </div>
    @endif
    <div class="col-md-4">

        <div class="card text-center">
            <div class="card-body p-4">
                @if($user->avatar)
                    <img src="{{ Storage::url($user->avatar) }}" alt=""
                        style="width:100px;height:100px;border-radius:50%;object-fit:cover;border:4px solid var(--color-brand-50);margin-bottom:12px">
                @else
                    <div style="width:100px;height:100px;border-radius:50%;background:linear-gradient(135deg,var(--color-brand-600),var(--color-brand-500));display:grid;place-items:center;font-size:36px;font-weight:800;color:#fff;margin:0 auto 12px">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
                <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                <div class="text-muted mb-2" style="font-size:13px">{{ $user->username }}</div>
                <span class="badge bg-primary">{{ $user->getRoleLabel() }}</span>
            </div>
        </div>

        @if($user->role === 'siswa' && $user->student)
            <div class="card mt-4">
                <div class="card-header"><i class="bi bi-person-lines-fill me-2 text-primary"></i>Info Siswa</div>
                <div class="card-body">
                    <dl class="mb-0">
                        <dt class="text-muted small">NIS</dt>
                        <dd class="fw-semibold mb-2"><code>{{ $user->student->nis }}</code></dd>
                        <dt class="text-muted small">Kelas</dt>
                        <dd class="fw-semibold mb-2">{{ $user->student->class?->name ?? '-' }}</dd>
                        <dt class="text-muted small">Kehadiran Bulan Ini</dt>
                        <dd class="fw-semibold mb-0">
                            <span class="{{ $user->student->attendance_percentage >= 75 ? 'text-success' : 'text-danger' }}">
                                {{ $user->student->attendance_percentage }}%
                            </span>
                        </dd>
                    </dl>
                </div>
            </div>
            <div class="d-grid mt-3">
                <a href="{{ route('attendance.mine') }}" class="btn btn-outline-primary">
                    <i class="bi bi-calendar-check me-2"></i>Lihat Absensi Saya
                </a>
            </div>
        @elseif($user->role === 'guru' && $user->teacher)
            <div class="card mt-4">
                <div class="card-header"><i class="bi bi-person-badge me-2 text-primary"></i>Info Guru</div>
                <div class="card-body">
                    <dl class="mb-0">
                        <dt class="text-muted small">NIP</dt>
                        <dd class="fw-semibold mb-2"><code>{{ $user->teacher->nip ?? '-' }}</code></dd>
                        <dt class="text-muted small">Mata Pelajaran</dt>
                        <dd class="fw-semibold mb-0">
                            <div class="flex flex-wrap gap-1 mt-1">
                                @forelse($user->teacher->subjects as $subj)
                                    <span class="badge" style="background:#ede9fe;color:#5b21b6">{{ $subj->name }}</span>
                                @empty
                                    {{ $user->teacher->subject ?? '-' }}
                                @endforelse
                            </div>
                        </dd>
                    </dl>
                </div>
            </div>
        @endif
    </div>

    <div class="col-md-7">
        {{-- Edit Profil --}}
        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-person-gear me-2 text-primary"></i>Edit Profil</div>
            <div class="card-body">
                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $user->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Avatar</label>
                            <input type="file" name="avatar" class="form-control" accept="image/*">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email', $user->email) }}">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="d-grid mt-3">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-2"></i>Simpan Profil</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Ganti Password --}}
        <div class="card">
            <div class="card-header"><i class="bi bi-shield-lock me-2 text-primary"></i>Ganti Password</div>
            <div class="card-body">
                <form method="POST" action="{{ route('profile.password') }}">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Password Lama <span class="text-danger">*</span></label>
                            <div class="relative">
                                <input type="password" name="current_password" id="current_password" class="form-control pr-10 @error('current_password') is-invalid @enderror" required>
                                <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none" onclick="togglePasswordVisibility('current_password', this)">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            @error('current_password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Password Baru <span class="text-danger">*</span></label>
                            <div class="relative">
                                <input type="password" name="password" id="password" class="form-control pr-10 @error('password') is-invalid @enderror" required minlength="6">
                                <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none" onclick="togglePasswordVisibility('password', this)">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <div class="form-text text-[11px] text-slate-500 mt-1">
                                Password minimal 8 karakter.
                            </div>
                            @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Konfirmasi Password <span class="text-danger">*</span></label>
                            <div class="relative">
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control pr-10" required>
                                <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none" onclick="togglePasswordVisibility('password_confirmation', this)">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="d-grid mt-3">
                        <button type="submit" class="btn btn-warning"><i class="bi bi-key me-2"></i>Ganti Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
