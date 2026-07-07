@extends('layouts.app')
@section('title', 'Tambah Soal')
@section('page-title', 'Tambah Soal — ' . $questionBank->title)

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('questions.store', $questionBank) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Tipe Soal *</label>
                            <select name="type" id="questionType" class="form-select">
                                <option value="multiple_choice">Pilihan Ganda</option>
                                <option value="essay">Essay</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tingkat Kesulitan</label>
                            <select name="difficulty" class="form-select">
                                <option value="easy">Mudah</option>
                                <option value="medium" selected>Sedang</option>
                                <option value="hard">Sulit</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Pertanyaan *</label>
                        <textarea name="question_text" class="form-control" rows="4" required></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Gambar (Opsional)</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Bobot Nilai *</label>
                        <input type="number" name="points" class="form-control" min="1" value="10" required>
                    </div>
                    <div id="optionsContainer">
                        <label class="form-label mb-3">Pilihan Jawaban & Kunci</label>
                        @for($i = 0; $i < 4; $i++)
                        <div class="input-group mb-3">
                            <div class="input-group-text bg-white">
                                <input class="form-check-input mt-0" type="radio" name="correct_option" value="{{ $i }}" {{ $i === 0 ? 'required checked' : '' }}>
                            </div>
                            <input type="text" name="options[{{ $i }}]" class="form-control" placeholder="Pilihan {{ chr(65 + $i) }}" {{ $i < 2 ? 'required' : '' }}>
                        </div>
                        @endfor
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <a href="{{ route('question-banks.show', $questionBank) }}" class="btn btn-light">Batal</a>
                        <button type="submit" class="btn btn-primary px-4">Simpan ke Bank</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
document.getElementById('questionType').addEventListener('change', function() {
    const c = document.getElementById('optionsContainer');
    c.style.display = this.value === 'essay' ? 'none' : 'block';
});
</script>
@endpush
@endsection
