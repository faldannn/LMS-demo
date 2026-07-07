@extends('layouts.app')
@section('title', 'Buat Pengumuman')
@section('page-title', 'Buat Pengumuman Baru')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('announcements.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Judul Pengumuman <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" required placeholder="Contoh: Rapat Wali Murid Semester Ganjil">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Tipe Pengumuman <span class="text-danger">*</span></label>
                        <select name="type" id="typeSelect" class="form-select" required>
                            <option value="umum">Umum (Semua Siswa & Orang Tua)</option>
                            <option value="kelas">Spesifik Kelas</option>
                        </select>
                    </div>

                    <div class="mb-3" id="classSelectDiv" style="display: none;">
                        <label class="form-label">Pilih Kelas</label>
                        <select name="class_id" class="form-select">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Isi Pengumuman <span class="text-danger">*</span></label>
                        <textarea name="content" class="form-control" rows="6" required placeholder="Tulis pesan pengumuman di sini..."></textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('announcements.index') }}" class="btn btn-light">Batal</a>
                        <button type="submit" class="btn btn-primary px-4">Siarkan Pengumuman</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('typeSelect').addEventListener('change', function() {
        document.getElementById('classSelectDiv').style.display = this.value === 'kelas' ? 'block' : 'none';
    });
</script>
@endsection
