@extends('layouts.app')
@section('title', 'Tahun Ajaran')
@section('page-title', 'Tahun Ajaran')

@section('content')
<div class="space-y-6">
    <x-master-data-toolbar 
        :create-route="route('academic-years.create')" 
        :export-excel-route="route('academic-years.export.excel', request()->query())" 
        :export-pdf-route="route('academic-years.export.pdf', request()->query())" 
        :trashed-route="route('academic-years.trashed')" 
        create-label="Tambah Tahun Ajaran">
        <div class="flex items-center gap-3">
            <span class="text-sm text-slate-500">Total: <strong class="text-slate-800">{{ $academicYears->total() }}</strong> Periode</span>
            <button type="button" class="btn btn-sm btn-outline-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalDuplicateClassGlobal">
                <i class="bi bi-copy me-1"></i> Siapkan Kelas Baru (Salin Struktur)
            </button>
        </div>
    </x-master-data-toolbar>

    {{-- Filter & Search Card --}}
    <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200/70">
        <form method="GET" class="grid grid-cols-1 gap-3 sm:grid-cols-12">
            <div class="sm:col-span-9">
                <div class="relative">
                    <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input name="search" class="w-full rounded-xl border border-slate-300 py-2 pl-9 pr-3 text-sm text-slate-800 transition focus:border-brand-500 focus:ring-brand-500" placeholder="Cari tahun ajaran (contoh: 2024/2025)..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="sm:col-span-3 flex gap-2">
                <button type="submit" class="w-full rounded-xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700">
                    Cari
                </button>
                @if(request('search'))
                    <a href="{{ route('academic-years.index') }}" class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Table Card --}}
    <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/70 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="ps-4">Tahun Ajaran</th>
                        <th>Deskripsi</th>
                        <th class="text-center">Semester</th>
                        <th class="text-center">Jumlah Kelas</th>
                        <th class="text-center">Status</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($academicYears as $year)
                        <tr class="{{ $year->is_active ? 'bg-brand-50/30' : '' }}">
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="fw-bold fs-6 text-slate-900">{{ $year->year }}</span>
                                    @if($year->is_active)
                                        <span class="badge bg-emerald-100 text-emerald-800 border border-emerald-200 rounded-pill px-2.5 py-1 text-xs">
                                            <i class="bi bi-check-circle-fill me-1"></i> Aktif Berjalan
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="text-slate-600">{{ $year->description ?? '—' }}</td>
                            <td class="text-center font-mono">
                                <span class="badge bg-slate-100 text-slate-700 rounded-pill px-2.5 py-1">
                                    {{ $year->semesters_count ?? $year->semesters()->count() }} Semester
                                </span>
                            </td>
                            <td class="text-center font-mono">
                                <span class="badge bg-purple-50 text-purple-700 border border-purple-200 rounded-pill px-2.5 py-1">
                                    {{ $year->classes_count ?? $year->classes()->count() }} Kelas
                                </span>
                            </td>
                            <td class="text-center">
                                @if($year->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Non-Aktif</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-inline-flex align-items-center gap-1">
                                    <button type="button" class="btn btn-sm btn-outline-primary" title="Salin Kelas ke Tahun Ini" data-bs-toggle="modal" data-bs-target="#modalCopy-{{ $year->id }}">
                                        <i class="bi bi-copy"></i>
                                    </button>
                                    <a href="{{ route('academic-years.show', $year) }}" class="btn btn-sm btn-light border text-slate-700" title="Lihat Detail & Struktur Kelas">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('academic-years.edit', $year) }}" class="btn btn-sm btn-light border text-primary" title="Edit Tahun Ajaran">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('academic-years.destroy', $year) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus tahun ajaran {{ $year->year }}? Data hanya bisa dihapus bila belum memiliki kelas.')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-light border text-danger" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>

                                {{-- Modal Copy for this specific row --}}
                                <div class="modal fade text-start" id="modalCopy-{{ $year->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content rounded-2xl border-0 shadow-lg">
                                            <form action="{{ route('academic-years.duplicate-classes', $year) }}" method="POST">
                                                @csrf
                                                <div class="modal-header border-b border-slate-100 bg-slate-50 px-4 py-3">
                                                    <h6 class="modal-title fw-bold text-slate-800">
                                                        <i class="bi bi-copy me-2 text-primary"></i> Siapkan Kelas untuk TA {{ $year->year }}
                                                    </h6>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body p-4 space-y-3">
                                                    <p class="text-sm text-slate-600 mb-3">
                                                        Fitur ini akan menyalin seluruh nama kelas, jurusan, tingkat, dan ruangan dari tahun ajaran sumber ke tahun ajaran <strong>{{ $year->year }}</strong> tanpa membawa siswanya.
                                                    </p>
                                                    <div>
                                                        <label class="form-label fw-semibold text-xs uppercase text-slate-600">Salin dari Tahun Ajaran Sumber</label>
                                                        <select name="source_academic_year_id" class="form-select text-sm" required>
                                                            <option value="">-- Pilih Tahun Ajaran Sumber --</option>
                                                            @foreach($allYears as $sy)
                                                                @if($sy->id != $year->id)
                                                                    <option value="{{ $sy->id }}">
                                                                        {{ $sy->year }} ({{ $sy->classes()->count() }} Kelas) {{ $sy->is_active ? '— Aktif' : '' }}
                                                                    </option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-check mt-3">
                                                        <input class="form-check-input" type="checkbox" name="copy_homeroom" value="1" id="copyHomeroom-{{ $year->id }}" checked>
                                                        <label class="form-check-label text-sm text-slate-700" for="copyHomeroom-{{ $year->id }}">
                                                            Salin juga penugasan Wali Kelas
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-t border-slate-100 bg-slate-50 px-4 py-3">
                                                    <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-sm btn-primary">
                                                        <i class="bi bi-check-lg me-1"></i> Mulai Salin Struktur Kelas
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-8 text-slate-400">
                                <i class="bi bi-calendar-x fs-1 d-block mb-2 text-slate-300"></i>
                                Belum ada data tahun ajaran.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            {{ $academicYears->links() }}
        </div>
    </div>
</div>

{{-- Global Duplicate Modal --}}
<div class="modal fade" id="modalDuplicateClassGlobal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-2xl border-0 shadow-lg">
            <div class="modal-header border-b border-slate-100 bg-slate-50 px-4 py-3">
                <h6 class="modal-title fw-bold text-slate-800">
                    <i class="bi bi-copy me-2 text-primary"></i> Penyiapan Kelas Tahun Ajaran Baru
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-sm text-slate-600 mb-3">
                    Pilih tahun ajaran tujuan untuk menduplikasi susunan kelas, jurusan, ruangan, dan wali kelas:
                </p>
                <div class="space-y-3">
                    @foreach($allYears as $y)
                        <div class="p-3 border rounded-xl d-flex align-items-center justify-content-between hover:bg-slate-50 transition">
                            <div>
                                <div class="fw-bold text-slate-800">{{ $y->year }}</div>
                                <div class="text-xs text-slate-500">{{ $y->classes()->count() }} kelas terdaftar</div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalCopy-{{ $y->id }}" data-bs-dismiss="modal">
                                Siapkan Kelas Ini &rarr;
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer border-t border-slate-100 bg-slate-50 px-4 py-3">
                <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection
