@extends('layouts.app')
@section('title', 'Riwayat Login')
@section('page-title', 'Riwayat Login')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('security.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-3">
            <div class="stat-card">
                <div style="font-size:22px;font-weight:800;color:var(--color-brand-500)">{{ $stats['success'] }}</div>
                <div class="text-muted small">Login Sukses Hari Ini</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div style="font-size:22px;font-weight:800;color:#ef4444">{{ $stats['failed'] }}</div>
                <div class="text-muted small">Login Gagal Hari Ini</div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small text-muted">Username</label>
                    <input type="text" name="username" class="form-control form-control-sm" value="{{ request('username') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        <option value="success" @selected(request('status') === 'success')>Sukses</option>
                        <option value="failed" @selected(request('status') === 'failed')>Gagal</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">Dari</label>
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">Sampai</label>
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
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
                            <th>Waktu</th>
                            <th>Username</th>
                            <th>Status</th>
                            <th>Alasan Gagal</th>
                            <th>IP</th>
                            <th>User Agent</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td class="small text-nowrap">{{ $log->logged_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $log->username }}</td>
                                <td>
                                    <span class="badge bg-{{ $log->status === 'success' ? 'success' : 'danger' }}">
                                        {{ $log->status === 'success' ? 'Sukses' : 'Gagal' }}
                                    </span>
                                </td>
                                <td class="small text-muted">{{ $log->failure_reason ?? '—' }}</td>
                                <td class="small">{{ $log->ip_address }}</td>
                                <td class="small text-muted text-truncate" style="max-width:200px" title="{{ $log->user_agent }}">{{ Str::limit($log->user_agent, 40) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">Belum ada riwayat login.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($logs->hasPages())
            <div class="card-footer">{{ $logs->links() }}</div>
        @endif
    </div>
@endsection
