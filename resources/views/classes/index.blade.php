{{-- resources/views/classes/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Data Kelas')
@section('page-title', 'Data Kelas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <p class="text-muted mb-0">
            Total: <strong>{{ $classes->total() }}</strong> kelas
            @if($activeYear) · Tahun Ajaran Aktif: <strong>{{ $activeYear->year }}</strong> @endif
        </p>
    </div>
    <a href="{{ route('classes.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Tambah Kelas
    </a>
</div>

<div class="row g-4">
    @forelse($classes as $class)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100" style="transition:transform .2s,box-shadow .2s" onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 8px 30px rgba(26,86,219,.12)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div style="width:44px;height:44px;border-radius:10px;background:linear-gradient(135deg,var(--color-brand-600),var(--color-brand-500));display:grid;place-items:center;font-size:18px;font-weight:800;color:#fff;font-family:'JetBrains Mono',monospace">
                            {{ $class->grade }}
                        </div>
                        <span class="badge {{ $class->academicYear?->is_active ? 'bg-success' : 'bg-secondary' }}">
                            {{ $class->academicYear?->year ?? '-' }}
                        </span>
                    </div>
                    <h5 class="fw-bold mb-1">{{ $class->name }}</h5>
                    @if($class->major)
                        <div class="text-muted mb-2" style="font-size:12px">{{ $class->major->name }}</div>
                    @endif
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-person-badge text-muted"></i>
                        <span style="font-size:13px">{{ $class->homeroomTeacher?->name ?? 'Belum ada wali kelas' }}</span>
                    </div>
                    <div class="row g-2">
                        <div class="col-4 text-center">
                            <div style="background:var(--color-brand-50);border-radius:8px;padding:8px">
                                <div style="font-size:20px;font-weight:800;color:var(--color-brand-500);font-family:'JetBrains Mono',monospace">{{ $class->students_count }}</div>
                                <div style="font-size:10px;color:#64748b">Siswa</div>
                            </div>
                        </div>
                        <div class="col-4 text-center">
                            <div style="background:#f0fdf4;border-radius:8px;padding:8px">
                                <div style="font-size:20px;font-weight:800;color:var(--color-brand-500);font-family:'JetBrains Mono',monospace">{{ $class->grade }}</div>
                                <div style="font-size:10px;color:#64748b">Tingkat</div>
                            </div>
                        </div>
                        <div class="col-4 text-center">
                            <div style="background:#fdf4ff;border-radius:8px;padding:8px">
                                <div style="font-size:14px;font-weight:700;color:#8b5cf6">{{ $class->room ?? '-' }}</div>
                                <div style="font-size:10px;color:#64748b">Ruang</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex gap-2">
                    <a href="{{ route('classes.show', $class) }}" class="btn btn-sm btn-outline-info flex-grow-1">
                        <i class="bi bi-eye me-1"></i>Detail
                    </a>
                    <a href="{{ route('classes.edit', $class) }}" class="btn btn-sm btn-outline-primary flex-grow-1">
                        <i class="bi bi-pencil me-1"></i>Edit
                    </a>
                    <form id="del-c{{ $class->id }}" method="POST" action="{{ route('classes.destroy', $class) }}">@csrf @method('DELETE')</form>
                    <button class="btn btn-sm btn-outline-danger" data-confirm="Hapus kelas {{ $class->name }}?" data-form="del-c{{ $class->id }}">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5 text-muted">
                    <i class="bi bi-building fs-1 d-block mb-2"></i>
                    Belum ada data kelas.
                    <a href="{{ route('classes.create') }}" class="btn btn-sm btn-primary mt-2">+ Tambah Kelas</a>
                </div>
            </div>
        </div>
    @endforelse
</div>
<div class="mt-3">{{ $classes->links() }}</div>
@endsection
