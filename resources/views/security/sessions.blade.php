@extends('layouts.app')
@section('title', 'Manajemen Sesi')
@section('page-title', 'Manajemen Sesi')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('security.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
        <form method="POST" action="{{ route('security.sessions.revoke-others') }}">
            @csrf
            <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Cabut semua sesi lain Anda?')">
                <i class="bi bi-box-arrow-right me-1"></i>Cabut Sesi Lain Saya
            </button>
        </form>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small text-muted">Filter User</label>
                    <select name="user_id" class="form-select form-select-sm">
                        <option value="">Semua user aktif</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" @selected($userId == $user->id)>{{ $user->name }} ({{ $user->username }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>User</th>
                            <th>IP</th>
                            <th>Terakhir Aktif</th>
                            <th>User Agent</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sessions as $session)
                            <tr class="{{ $session->is_current ? 'table-primary' : '' }}">
                                <td>
                                    {{ $session->user_name ?? 'Tamu' }}
                                    @if($session->is_current)<span class="badge bg-primary ms-1">Sesi ini</span>@endif
                                    <br><span class="text-muted small">{{ $session->username }}</span>
                                </td>
                                <td class="small">{{ $session->ip_address }}</td>
                                <td class="small text-nowrap">{{ $session->last_activity_at }}</td>
                                <td class="small text-muted text-truncate" style="max-width:220px">{{ Str::limit($session->user_agent, 50) }}</td>
                                <td class="text-end">
                                    @unless($session->is_current)
                                        <form method="POST" action="{{ route('security.sessions.destroy', $session->id) }}" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Cabut sesi ini?')">Cabut</button>
                                        </form>
                                    @endunless
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">Tidak ada sesi aktif.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
