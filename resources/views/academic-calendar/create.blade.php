@extends('layouts.app')
@section('title', 'Tambah Event')
@section('page-title', 'Tambah Event Kalender')

@section('content')
<div class="row justify-content-center"><div class="col-lg-7"><div class="card border-0 shadow-sm"><div class="card-body p-4">
<form method="POST" action="{{ route('academic-calendar.store') }}">@csrf
    <div class="mb-3"><label class="form-label">Judul *</label><input name="title" class="form-control" required></div>
    <div class="mb-3"><label class="form-label">Kategori *</label>
        <select name="category" class="form-select" required>
            <option value="akademik">Akademik</option><option value="ujian">Ujian</option>
            <option value="libur">Libur</option><option value="kegiatan">Kegiatan</option><option value="lainnya">Lainnya</option>
        </select>
    </div>
    <div class="row mb-3"><div class="col-md-6"><label class="form-label">Tanggal Mulai *</label><input type="date" name="start_date" class="form-control" required></div>
    <div class="col-md-6"><label class="form-label">Tanggal Selesai</label><input type="date" name="end_date" class="form-control"></div></div>
    <div class="mb-3"><label class="form-label">Deskripsi</label><textarea name="description" class="form-control" rows="3"></textarea></div>
    <div class="form-check mb-3"><input type="checkbox" name="is_public" value="1" class="form-check-input" id="pub" checked><label for="pub" class="form-check-label">Tampilkan untuk semua</label></div>
    <button class="btn btn-primary">Simpan</button><a href="{{ route('academic-calendar.index') }}" class="btn btn-light">Batal</a>
</form>
</div></div></div></div>
@endsection
