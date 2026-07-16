{{-- resources/views/users/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Manajemen User')
@section('page-title', 'Manajemen User')

@section('content')
<div class="space-y-6">
    {{-- Header Stats --}}
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
        {{-- Total Users --}}
        <div class="relative overflow-hidden rounded-2xl p-4 text-white shadow-lg" style="background: linear-gradient(135deg, #6d28d9, #7c3aed);">
            <div class="absolute -right-4 -top-4 h-16 w-16 rounded-full bg-white/10"></div>
            <div class="relative z-10 flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/15">
                    <i class="bi bi-people-fill text-lg"></i>
                </div>
                <div>
                    <div class="text-2xl font-extrabold leading-none">{{ $users->total() }}</div>
                    <div class="mt-1 text-xs font-medium opacity-80">Total User</div>
                </div>
            </div>
        </div>

        {{-- Guru --}}
        <div class="relative overflow-hidden rounded-2xl p-4 text-white shadow-lg" style="background: linear-gradient(135deg, #0369a1, #0284c7);">
            <div class="absolute -right-4 -top-4 h-16 w-16 rounded-full bg-white/10"></div>
            <div class="relative z-10 flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/15">
                    <i class="bi bi-person-badge-fill text-lg"></i>
                </div>
                <div>
                    <div class="text-2xl font-extrabold leading-none">{{ $users->getCollection()->where('role', \App\Enums\UserRole::Guru)->count() }}</div>
                    <div class="mt-1 text-xs font-medium opacity-80">Guru</div>
                </div>
            </div>
        </div>

        {{-- Siswa --}}
        <div class="relative overflow-hidden rounded-2xl p-4 text-white shadow-lg" style="background: linear-gradient(135deg, #065f46, #059669);">
            <div class="absolute -right-4 -top-4 h-16 w-16 rounded-full bg-white/10"></div>
            <div class="relative z-10 flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/15">
                    <i class="bi bi-mortarboard-fill text-lg"></i>
                </div>
                <div>
                    <div class="text-2xl font-extrabold leading-none">{{ $users->getCollection()->where('role', \App\Enums\UserRole::Siswa)->count() }}</div>
                    <div class="mt-1 text-xs font-medium opacity-80">Siswa</div>
                </div>
            </div>
        </div>

        {{-- Admin & Superadmin --}}
        <div class="relative overflow-hidden rounded-2xl p-4 text-white shadow-lg" style="background: linear-gradient(135deg, #b45309, #d97706);">
            <div class="absolute -right-4 -top-4 h-16 w-16 rounded-full bg-white/10"></div>
            <div class="relative z-10 flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/15">
                    <i class="bi bi-shield-fill-check text-lg"></i>
                </div>
                <div>
                    <div class="text-2xl font-extrabold leading-none">{{ $users->getCollection()->filter(fn($u) => $u->isAdmin() || $u->isSuperAdmin())->count() }}</div>
                    <div class="mt-1 text-xs font-medium opacity-80">Admin & Superadmin</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter & Actions Bar --}}
    <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200/70">
        <form method="GET" class="flex flex-col gap-3 sm:flex-row sm:items-center sm:gap-3">
            {{-- Search --}}
            <div class="flex-1 sm:max-w-xs">
                <div class="relative">
                    <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>
                    <input type="text" name="search" placeholder="Cari nama atau username..." value="{{ request('search') }}"
                        class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2.5 pl-9 pr-4 text-sm text-slate-900 placeholder:text-slate-400 transition focus:border-brand-500 focus:bg-white focus:ring-2 focus:ring-brand-500/20">
                </div>
            </div>

            {{-- Role Filter --}}
            <div class="sm:w-40">
                <select name="role" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-900 transition focus:border-brand-500 focus:bg-white focus:ring-2 focus:ring-brand-500/20">
                    <option value="">Semua Role</option>
                    <option value="superadmin"      {{ request('role')==='superadmin'      ? 'selected':'' }}>Super Admin</option>
                    <option value="admin"           {{ request('role')==='admin'           ? 'selected':'' }}>Admin</option>
                    <option value="guru"            {{ request('role')==='guru'            ? 'selected':'' }}>Guru</option>
                    <option value="siswa"           {{ request('role')==='siswa'           ? 'selected':'' }}>Siswa</option>
                </select>
            </div>

            {{-- Action Buttons --}}
            <div class="flex flex-col gap-2 sm:flex-row sm:gap-2">
                <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700">
                    <i class="bi bi-funnel"></i>
                    <span>Filter</span>
                </button>
                <a href="{{ route('users.index') }}" class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                    <i class="bi bi-x-lg"></i>
                    <span>Reset</span>
                </a>
            </div>

            {{-- Add User Button --}}
            <div class="flex items-center">
                <a href="{{ route('users.create') }}" class="inline-flex w-full items-center justify-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700 sm:w-auto">
                    <i class="bi bi-plus-lg"></i>
                    <span>Tambah User</span>
                </a>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/70">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 font-semibold" style="width: 48px">#</th>
                        <th class="px-4 py-3 font-semibold">User</th>
                        <th class="px-4 py-3 font-semibold">Username</th>
                        <th class="px-4 py-3 font-semibold">Role</th>
                        <th class="px-4 py-3 font-semibold hidden sm:table-cell">Email</th>
                        <th class="px-4 py-3 font-semibold text-center">Status</th>
                        <th class="px-4 py-3 font-semibold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $i => $user)
                        @php
                            $roleKey = $user->role instanceof \App\Enums\UserRole ? $user->role->value : (string) $user->role;
                            $avatarGradients = [
                                'superadmin'     => 'linear-gradient(135deg, #6366f1, #4f46e5)',
                                'admin'          => 'linear-gradient(135deg, #b45309, #d97706)',
                                'guru'           => 'linear-gradient(135deg, #0369a1, #0284c7)',
                                'siswa'          => 'linear-gradient(135deg, #065f46, #059669)',
                            ];
                            $roleColors = [
                                'superadmin'     => ['#e0e7ff', '#4338ca'],
                                'admin'          => ['#fef3c7', '#92400e'],
                                'guru'           => ['#dbeafe', '#1d4ed8'],
                                'siswa'          => ['#d1fae5', '#065f46'],
                            ];
                            $avatarBg = $avatarGradients[$roleKey] ?? 'linear-gradient(135deg, #64748b, #475569)';
                            [$roleBg, $roleFg] = $roleColors[$roleKey] ?? ['#f1f5f9', '#64748b'];
                        @endphp
                        <tr class="border-b border-slate-100 hover:bg-slate-50/50 transition-colors">
                            <td class="px-4 py-3 font-semibold text-slate-400">
                                {{ $users->firstItem() + $i }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl text-sm font-bold text-white" style="background: {{ $avatarBg }};">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="truncate font-semibold text-slate-900">{{ $user->name }}</div>
                                        @if($user->isGuru() && $user->teacher)
                                            <a href="{{ route('teachers.show', $user->teacher->id) }}"
                                               class="mt-0.5 block truncate text-xs font-medium text-indigo-600 hover:text-indigo-700">
                                                <i class="bi bi-person-badge mr-1"></i>NIP {{ $user->teacher->nip ?? '-' }}
                                            </a>
                                        @elseif($user->isSiswa() && $user->student)
                                            <a href="{{ route('students.show', $user->student->id) }}"
                                               class="mt-0.5 block truncate text-xs font-medium text-indigo-600 hover:text-indigo-700">
                                                <i class="bi bi-mortarboard mr-1"></i>NIS {{ $user->student->nis ?? '-' }}
                                            </a>
                                        @else
                                            <span class="mt-0.5 block text-xs text-slate-400">—</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <code class="rounded-lg bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-700">
                                    {{ $user->username }}
                                </code>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold" style="background: {{ $roleBg }}; color: {{ $roleFg }};">
                                    {{ $user->getRoleLabel() }}
                                </span>
                            </td>
                            <td class="px-4 py-3 hidden sm:table-cell">
                                @if($user->email)
                                    <span class="truncate text-slate-600">{{ Str::limit($user->email, 28) }}</span>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <form method="POST" action="{{ route('users.toggle-status', $user) }}" class="inline">
                                    @csrf @method('PUT')
                                    <button type="submit" title="Klik untuk ubah status"
                                        class="inline-flex items-center gap-1 rounded-full px-3 py-1.5 text-xs font-bold transition hover:opacity-80 {{ $user->status ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                        <i class="bi {{ $user->status ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }}"></i>
                                        <span>{{ $user->status ? 'Aktif' : 'Non-Aktif' }}</span>
                                    </button>
                                </form>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('users.edit', $user) }}" title="Edit User"
                                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 text-blue-600 transition hover:bg-blue-100">
                                        <i class="bi bi-pencil text-sm"></i>
                                    </a>
                                    <form method="POST" action="{{ route('users.reset-password', $user) }}" class="inline">
                                        @csrf @method('PUT')
                                        <button type="submit" title="Reset Password"
                                            onclick="return confirm('Reset password {{ addslashes($user->name) }}?')"
                                            class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-50 text-amber-600 transition hover:bg-amber-100">
                                            <i class="bi bi-key text-sm"></i>
                                        </button>
                                    </form>
                                    @if($user->id !== auth()->id())
                                        <form id="del-u{{ $user->id }}" method="POST" action="{{ route('users.destroy', $user) }}" class="hidden">
                                            @csrf @method('DELETE')
                                        </form>
                                        <button title="Hapus User"
                                            data-confirm="Hapus user {{ $user->name }}?"
                                            data-form="del-u{{ $user->id }}"
                                            class="flex h-8 w-8 items-center justify-center rounded-lg bg-rose-50 text-rose-600 transition hover:bg-rose-100">
                                            <i class="bi bi-trash text-sm"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-slate-100">
                                        <i class="bi bi-people text-3xl text-slate-400"></i>
                                    </div>
                                    <div class="font-semibold text-slate-700">Belum ada user ditemukan.</div>
                                    <div class="text-sm text-slate-500">Coba ubah kata kunci pencarian atau filter role.</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($users->total() > 0)
        <div class="flex flex-col gap-3 border-t border-slate-200 bg-slate-50 px-4 py-3 sm:flex-row sm:items-center sm:justify-between sm:px-5">
            <div class="text-sm text-slate-600">
                Menampilkan <strong class="text-slate-900">{{ $users->firstItem() }}</strong>&ndash;<strong class="text-slate-900">{{ $users->lastItem() }}</strong>
                dari <strong class="text-slate-900">{{ $users->total() }}</strong> user
            </div>
            {{ $users->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>
@endsection
