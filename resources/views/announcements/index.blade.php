@extends('layouts.app')
@section('title', 'Pengumuman')
@section('page-title', 'Pengumuman Sekolah')

@section('content')
<div class="row">
    @if(auth()->user()->isStaff())
    <div class="col-12 mb-4">
        <a href="{{ route('announcements.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Buat Pengumuman Baru
        </a>
    </div>
    @endif

    @forelse($announcements as $announcement)
        <div class="col-12 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <a href="{{ route('announcements.show', $announcement) }}" class="h5 fw-bold mb-0 text-primary text-decoration-none">{{ $announcement->title }}</a>
                        @if(auth()->user()->isStaff())
                        <form action="{{ route('announcements.destroy', $announcement) }}" method="POST" data-confirm="Hapus pengumuman ini?">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger border-0"><i class="bi bi-trash"></i></button>
                        </form>
                        @endif
                    </div>
                    
                    <div class="small text-muted mb-3">
                        <i class="bi bi-person me-1"></i> {{ $announcement->creator->name }} &bull; 
                        <i class="bi bi-clock me-1"></i> {{ $announcement->created_at->diffForHumans() }} &bull;
                        @if($announcement->type === 'umum')
                            <span class="badge bg-success bg-opacity-10 text-success">Umum</span>
                        @else
                            <span class="badge bg-warning bg-opacity-10 text-warning">Kelas: {{ $announcement->class->name ?? 'Semua' }}</span>
                        @endif
                    </div>
                    
                    <div class="bg-light p-3 rounded">
                        {!! nl2br(e($announcement->content)) !!}
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5 text-muted">
            <i class="bi bi-megaphone display-1 mb-3 text-light"></i>
            <h5>Belum Ada Pengumuman</h5>
            <p>Tidak ada pengumuman yang ditampilkan saat ini.</p>
        </div>
    @endforelse
    
    <div class="col-12 mt-3">
        {{ $announcements->links() }}
    </div>
</div>
@endsection
