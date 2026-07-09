{{-- resources/views/users/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Manajemen User')
@section('page-title', 'Manajemen User')

@section('content')

{{-- Header Stats --}}
<div class="row g-2 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0" style="background:linear-gradient(135deg,#6d28d9,#7c3aed);color:#fff">
            <div class="card-body py-2 px-3 d-flex align-items-center gap-3">
                <div style="width:36px;height:36px;border-radius:8px;background:rgba(255,255,255,.15);display:grid;place-items:center;font-size:16px;flex-shrink:0">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <div class="fw-bold" style="font-size:20px;line-height:1">{{ $users->total() }}</div>
                    <div style="font-size:11px;opacity:.8;font-weight:500">Total User</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0" style="background:linear-gradient(135deg,#0369a1,#0284c7);color:#fff">
            <div class="card-body py-2 px-3 d-flex align-items-center gap-3">
                <div style="width:36px;height:36px;border-radius:8px;background:rgba(255,255,255,.15);display:grid;place-items:center;font-size:16px;flex-shrink:0">
                    <i class="bi bi-person-badge-fill"></i>
                </div>
                <div>
                    <div class="fw-bold" style="font-size:20px;line-height:1">{{ $users->getCollection()->where('role', \App\Enums\UserRole::Guru)->count() }}</div>
                    <div style="font-size:11px;opacity:.8;font-weight:500">Guru</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0" style="background:linear-gradient(135deg,#065f46,#059669);color:#fff">
            <div class="card-body py-2 px-3 d-flex align-items-center gap-3">
                <div style="width:36px;height:36px;border-radius:8px;background:rgba(255,255,255,.15);display:grid;place-items:center;font-size:16px;flex-shrink:0">
                    <i class="bi bi-mortarboard-fill"></i>
                </div>
                <div>
                    <div class="fw-bold" style="font-size:20px;line-height:1">{{ $users->getCollection()->where('role', \App\Enums\UserRole::Siswa)->count() }}</div>
                    <div style="font-size:11px;opacity:.8;font-weight:500">Siswa</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0" style="background:linear-gradient(135deg,#b45309,#d97706);color:#fff">
            <div class="card-body py-2 px-3 d-flex align-items-center gap-3">
                <div style="width:36px;height:36px;border-radius:8px;background:rgba(255,255,255,.15);display:grid;place-items:center;font-size:16px;flex-shrink:0">
                    <i class="bi bi-shield-fill-check"></i>
                </div>
                <div>
                    <div class="fw-bold" style="font-size:20px;line-height:1">{{ $users->getCollection()->where('role', \App\Enums\UserRole::KepalaSekolah)->count() }}</div>
                    <div style="font-size:11px;opacity:.8;font-weight:500">Kepala Sekolah</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Filter & Actions Bar --}}
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-body py-3 px-4">
        <form method="GET" class="d-flex flex-wrap gap-2 align-items-center">
            <div class="flex-grow-1" style="min-width:200px;max-width:360px">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0" style="color:#94a3b8">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0"
                        placeholder="Cari nama atau username..." value="{{ request('search') }}">
                </div>
            </div>
            <div style="min-width:170px">
                <select name="role" class="form-select">
                    <option value="">Semua Role</option>
                    <option value="kepala_sekolah" {{ request('role')==='kepala_sekolah' ? 'selected':'' }}>Kepala Sekolah</option>
                    <option value="guru"            {{ request('role')==='guru'            ? 'selected':'' }}>Guru</option>
                    <option value="siswa"           {{ request('role')==='siswa'           ? 'selected':'' }}>Siswa</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary px-4">
                <i class="bi bi-funnel me-1"></i> Filter
            </button>
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-x-lg me-1"></i> Reset
            </a>
            <div class="ms-auto">
                <a href="{{ route('users.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i> Tambah User
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Table --}}
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr style="background:#f8fafc;border-bottom:2px solid #e2e8f0">
                    <th style="padding:12px 16px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#94a3b8;width:44px">#</th>
                    <th style="padding:12px 16px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#94a3b8">User</th>
                    <th style="padding:12px 16px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#94a3b8">Username</th>
                    <th style="padding:12px 16px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#94a3b8">Role</th>
                    <th style="padding:12px 16px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#94a3b8">Email</th>
                    <th style="padding:12px 16px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#94a3b8;text-align:center">Status</th>
                    <th style="padding:12px 16px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#94a3b8;text-align:center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $i => $user)
                    @php
                        $roleKey = $user->role instanceof \App\Enums\UserRole ? $user->role->value : (string) $user->role;
                        $avatarGradients = [
                            'kepala_sekolah' => 'linear-gradient(135deg,#b45309,#d97706)',
                            'guru'           => 'linear-gradient(135deg,#0369a1,#0284c7)',
                            'siswa'          => 'linear-gradient(135deg,#065f46,#059669)',
                        ];
                        $roleColors = [
                            'kepala_sekolah' => ['#fef3c7','#92400e'],
                            'guru'           => ['#dbeafe','#1d4ed8'],
                            'siswa'          => ['#d1fae5','#065f46'],
                        ];
                        $avatarBg = $avatarGradients[$roleKey] ?? 'linear-gradient(135deg,#64748b,#475569)';
                        [$roleBg, $roleFg] = $roleColors[$roleKey] ?? ['#f1f5f9','#64748b'];
                    @endphp
                    <tr style="border-bottom:1px solid #f1f5f9">
                        <td style="padding:14px 16px;color:#94a3b8;font-size:13px;font-weight:600">
                            {{ $users->firstItem() + $i }}
                        </td>
                        <td style="padding:14px 16px">
                            <div class="d-flex align-items-center gap-3">
                                <div style="width:38px;height:38px;border-radius:10px;background:{{ $avatarBg }};display:grid;place-items:center;font-size:15px;font-weight:700;color:#fff;flex-shrink:0">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <span class="fw-semibold d-block" style="color:#1e293b;font-size:14px">{{ $user->name }}</span>
                                    @if($user->isGuru() && $user->teacher)
                                        <a href="{{ route('teachers.show', $user->teacher->id) }}"
                                           class="d-block mt-0" style="color:#6366f1;text-decoration:none;font-size:12px;font-weight:500">
                                            <i class="bi bi-person-badge me-1"></i>NIP {{ $user->teacher->nip ?? '-' }}
                                        </a>
                                    @elseif($user->isSiswa() && $user->student)
                                        <a href="{{ route('students.show', $user->student->id) }}"
                                           class="d-block mt-0" style="color:#6366f1;text-decoration:none;font-size:12px;font-weight:500">
                                            <i class="bi bi-mortarboard me-1"></i>NIS {{ $user->student->nis ?? '-' }}
                                        </a>
                                    @else
                                        <span style="color:#cbd5e1;font-size:12px">—</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td style="padding:14px 16px">
                            <code style="background:#f1f5f9;padding:3px 8px;border-radius:6px;font-size:12px;color:#334155;font-weight:600">
                                {{ $user->username }}
                            </code>
                        </td>
                        <td style="padding:14px 16px">
                            <span class="badge" style="background:{{ $roleBg }};color:{{ $roleFg }};font-size:11px;font-weight:600;padding:5px 10px;border-radius:6px">
                                {{ $user->getRoleLabel() }}
                            </span>
                        </td>
                        <td style="padding:14px 16px;font-size:13px;color:#475569">
                            @if($user->email)
                                {{ Str::limit($user->email, 28) }}
                            @else
                                <span style="color:#cbd5e1">—</span>
                            @endif
                        </td>
                        <td style="padding:14px 16px;text-align:center">
                            <form method="POST" action="{{ route('users.toggle-status', $user) }}" class="d-inline">
                                @csrf @method('PUT')
                                <button type="submit" title="Klik untuk ubah status"
                                    style="border:none;cursor:pointer;padding:4px 12px;border-radius:20px;font-size:11px;font-weight:700;
                                           background:{{ $user->status ? '#d1fae5' : '#fee2e2' }};
                                           color:{{ $user->status ? '#065f46' : '#991b1b' }}">
                                    <i class="bi {{ $user->status ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }} me-1"></i>
                                    {{ $user->status ? 'Aktif' : 'Non-Aktif' }}
                                </button>
                            </form>
                        </td>
                        <td style="padding:14px 16px;text-align:center">
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="{{ route('users.edit', $user) }}" title="Edit User"
                                    style="width:32px;height:32px;border-radius:8px;background:#eff6ff;color:#2563eb;border:none;display:inline-grid;place-items:center;text-decoration:none">
                                    <i class="bi bi-pencil" style="font-size:13px"></i>
                                </a>
                                <form method="POST" action="{{ route('users.reset-password', $user) }}" class="d-inline">
                                    @csrf @method('PUT')
                                    <button type="submit" title="Reset Password"
                                        onclick="return confirm('Reset password {{ addslashes($user->name) }}?')"
                                        style="width:32px;height:32px;border-radius:8px;background:#fffbeb;color:#d97706;border:none;display:grid;place-items:center;cursor:pointer">
                                        <i class="bi bi-key" style="font-size:13px"></i>
                                    </button>
                                </form>
                                @if($user->id !== auth()->id())
                                    <form id="del-u{{ $user->id }}" method="POST" action="{{ route('users.destroy', $user) }}" class="d-none">
                                        @csrf @method('DELETE')
                                    </form>
                                    <button title="Hapus User"
                                        data-confirm="Hapus user {{ $user->name }}?"
                                        data-form="del-u{{ $user->id }}"
                                        style="width:32px;height:32px;border-radius:8px;background:#fff1f2;color:#e11d48;border:none;display:grid;place-items:center;cursor:pointer">
                                        <i class="bi bi-trash" style="font-size:13px"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="padding:60px 16px;text-align:center;color:#94a3b8">
                            <i class="bi bi-people" style="font-size:40px;display:block;margin-bottom:12px;opacity:.4"></i>
                            <div class="fw-semibold" style="color:#64748b;margin-bottom:4px">Belum ada user ditemukan.</div>
                            <div style="font-size:13px">Coba ubah kata kunci pencarian atau filter role.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->total() > 0)
    <div class="card-footer d-flex justify-content-between align-items-center flex-wrap gap-2"
         style="background:#f8fafc;border-top:1px solid #e2e8f0;padding:12px 20px">
        <div style="font-size:13px;color:#64748b">
            Menampilkan <strong>{{ $users->firstItem() }}</strong>&ndash;<strong>{{ $users->lastItem() }}</strong>
            dari <strong>{{ $users->total() }}</strong> user
        </div>
        {{ $users->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
