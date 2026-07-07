@extends('layouts.app')
@section('title', 'Buat Ujian CBT')
@section('page-title', 'Buat Ujian CBT')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('quizzes.store', $classSubject) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Judul Ujian *</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bank Soal</label>
                        <select name="question_bank_id" class="form-select">
                            <option value="">— Pilih bank —</option>
                            @foreach($banks as $bank)
                            <option value="{{ $bank->id }}">{{ $bank->title }}</option>
                            @endforeach
                        </select>
                        <div class="form-text">Wajib untuk mode acak / semua soal bank.</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Durasi (menit) *</label>
                            <input type="number" name="duration" class="form-control" min="1" value="60" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Mulai</label>
                            <input type="datetime-local" name="start_time" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tutup</label>
                            <input type="datetime-local" name="end_time" class="form-control">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Mode Pemilihan Soal *</label>
                            <select name="selection_mode" id="selectionMode" class="form-select">
                                <option value="fixed">Tetap (pilih manual dari bank)</option>
                                <option value="random">Acak N soal dari bank</option>
                                <option value="all_bank">Semua soal aktif di bank</option>
                            </select>
                        </div>
                        <div class="col-md-3" id="randomCountGroup">
                            <label class="form-label">Jumlah Soal Acak</label>
                            <input type="number" name="random_question_count" class="form-control" min="1" placeholder="40">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Soal per halaman</label>
                            <input type="number" name="questions_per_page" class="form-control" min="1" max="10" value="1">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label">Nilai Lulus</label>
                            <input type="number" name="pass_score" class="form-control" min="0" max="100" value="75" step="0.01">
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-check form-switch"><input type="hidden" name="shuffle_questions" value="0"><input class="form-check-input" type="checkbox" name="shuffle_questions" value="1" id="sq"><label for="sq">Acak urutan soal</label></div>
                            <div class="form-check form-switch"><input type="hidden" name="shuffle_answers" value="0"><input class="form-check-input" type="checkbox" name="shuffle_answers" value="1" id="sa"><label for="sa">Acak urutan jawaban</label></div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch"><input type="hidden" name="auto_submit_on_timeout" value="0"><input class="form-check-input" type="checkbox" name="auto_submit_on_timeout" value="1" id="auto" checked><label for="auto">Auto submit saat waktu habis</label></div>
                            <div class="form-check form-switch"><input type="hidden" name="show_results" value="0"><input class="form-check-input" type="checkbox" name="show_results" value="1" id="sr" checked><label for="sr">Tampilkan hasil ke siswa</label></div>
                            <div class="form-check form-switch"><input type="hidden" name="is_published" value="0"><input class="form-check-input" type="checkbox" name="is_published" value="1" id="pub"><label for="pub">Publikasikan</label></div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('quizzes.index', $classSubject) }}" class="btn btn-light">Batal</a>
                        <button type="submit" class="btn btn-primary px-4">Simpan Ujian</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
document.getElementById('selectionMode').addEventListener('change', function() {
    document.getElementById('randomCountGroup').classList.toggle('d-none', this.value !== 'random');
});
document.getElementById('selectionMode').dispatchEvent(new Event('change'));
</script>
@endpush
@endsection
