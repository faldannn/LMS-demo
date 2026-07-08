{{-- resources/views/users/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Manajemen User')
@section('page-title', 'Manajemen User')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <p class="text-muted mb-0">Total: <strong>{{ $users->total() }}</strong> user</p>
    <a href="{{ route('users.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Tambah User
    </a>
</div>

<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control" placeholder="🔍 Cari nama atau username..."
                    value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="role" class="form-select">
                    <option value="">Semua Role</option>
                    <option value="kepala_sekolah" {{ request('role')==='kepala_sekolah' ? 'selected':'' }}>Kepala Sekolah</option>
                    <option value="guru" {{ request('role')==='guru' ? 'selected':'' }}>Guru</option>
                    <option value="siswa" {{ request('role')==='siswa' ? 'selected':'' }}>Siswa</option>
                </select>
            </div>
            <div class="col-md-2"><button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i></button></div>
            <div class="col-md-2"><a href="{{ route('users.index') }}" class="btn btn-outline-secondary w-100">Reset</a></div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>User</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $i => $user)
                    <tr>
                        <td>{{ $users->firstItem() + $i }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="user-avatar" style="width:32px;height:32px;font-size:12px">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <span class="fw-semibold d-block">{{ $user->name }}</span>
                                    @if($user->isGuru() && $user->teacher)
                                        <span class="text-xs text-slate-500 d-block mt-0.5">
                                            Guru: <a href="{{ route('teachers.show', $user->teacher->id) }}" class="text-brand-600 hover:underline fw-medium">NIP {{ $user->teacher->nip ?? '-' }}</a>
                                        </span>
                                    @elseif($user->isSiswa() && $user->student)
                                        <span class="text-xs text-slate-500 d-block mt-0.5">
                                            Siswa: <a href="{{ route('students.show', $user->student->id) }}" class="text-brand-600 hover:underline fw-medium">NIS {{ $user->student->nis ?? '-' }}</a>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td><code>{{ $user->username }}</code></td>
                        <td>
                            @php $roleColors = ['kepala_sekolah'=>'#ede9fe|#5b21b6','guru'=>'var(--color-brand-50)|var(--color-brand-600)','siswa'=>'var(--color-brand-50)|#0058ff']; @endphp
                            @php $roleKey = $user->role instanceof \App\Enums\UserRole ? $user->role->value : (string) $user->role; @endphp
                            @php [$bg,$fg] = explode('|', $roleColors[$roleKey] ?? '#f1f5f9|#64748b'); @endphp
                            <span class="badge" style="background:{{ $bg }};color:{{ $fg }}">{{ $user->getRoleLabel() }}</span>
                        </td>
                        <td style="font-size:13px">{{ $user->email ?? '-' }}</td>
                        <td>
                            <form method="POST" action="{{ route('users.toggle-status', $user) }}">
                                @csrf @method('PUT')
                                <button type="submit" class="badge border-0 {{ $user->status ? 'bg-success' : 'bg-secondary' }}" style="cursor:pointer" title="Klik untuk ubah status">
                                    {{ $user->status ? 'Aktif' : 'Non-Aktif' }}
                                </button>
                            </form>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                                <form method="POST" action="{{ route('users.reset-password', $user) }}">
                                    @csrf @method('PUT')
                                    <button type="submit" class="btn btn-sm btn-outline-warning" title="Reset Password" onclick="return confirm('Reset password {{ $user->name }}?')"><i class="bi bi-key"></i></button>
                                </form>
                                @if($user->id !== auth()->id())
                                    <form id="del-u{{ $user->id }}" method="POST" action="{{ route('users.destroy', $user) }}">@csrf @method('DELETE')</form>
                                    <button class="btn btn-sm btn-outline-danger" data-confirm="Hapus user {{ $user->name }}?" data-form="del-u{{ $user->id }}" title="Hapus"><i class="bi bi-trash"></i></button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-people fs-1 d-block mb-2"></i>Belum ada user.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <div class="text-muted" style="font-size:13px">Menampilkan {{ $users->firstItem() }}-{{ $users->lastItem() }} dari {{ $users->total() }}</div>
        {{ $users->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
