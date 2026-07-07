@extends('layouts.app')
@section('title', 'Data Siswa')
@section('page-title', 'Data Siswa')

@section('content')
<div class="space-y-6">
    {{-- Toolbar --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-slate-500">Total: <strong class="text-slate-800">{{ $students->total() }}</strong> siswa</p>
        <div class="flex flex-wrap items-center gap-2">
            <button type="button" onclick="document.getElementById('importModal').classList.remove('hidden')" class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-300 bg-white px-3.5 py-2 text-sm font-medium text-emerald-700 shadow-sm transition hover:bg-emerald-50">
                <i class="bi bi-upload"></i>Import Excel
            </button>
            <a href="{{ route('students.create') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700">
                <i class="bi bi-plus-lg"></i>Tambah Siswa
            </a>
        </div>
    </div>

    {{-- Filter --}}
    <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200/60">
        <form method="GET" class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-5">
            <div>
                <label class="mb-1 block text-xs font-bold text-slate-500">Pencarian</label>
                <div class="relative">
                    <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, NIS, atau NISN..."
                        class="block w-full rounded-lg border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-sm text-slate-800 placeholder:text-slate-400 transition focus:border-brand-500 focus:bg-white focus:ring-2 focus:ring-brand-500/20">
                </div>
            </div>
            <div>
                <label class="mb-1 block text-xs font-bold text-slate-500">Filter Kelas</label>
                <select name="class_id" class="block w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-800 transition focus:border-brand-500 focus:bg-white focus:ring-2 focus:ring-brand-500/20">
                    <option value="">Semua Kelas</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-xs font-bold text-slate-500">Jenis Kelamin</label>
                <select name="gender" class="block w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-800 transition focus:border-brand-500 focus:bg-white focus:ring-2 focus:ring-brand-500/20">
                    <option value="">Semua</option>
                    <option value="L" {{ request('gender') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="P" {{ request('gender') == 'P' ? 'selected' : '' }}>Perempuan</option>
                </select>
            </div>
            <div>
                <label class="mb-1 block text-xs font-bold text-slate-500">Status</label>
                <select name="status" class="block w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-800 transition focus:border-brand-500 focus:bg-white focus:ring-2 focus:ring-brand-500/20">
                    <option value="">Semua Status</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Non-Aktif</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="inline-flex w-full items-center justify-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700">
                    <i class="bi bi-funnel"></i>Filter
                </button>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/80">
                        <th class="whitespace-nowrap px-4 py-3 text-xs font-bold uppercase tracking-wider text-slate-500">NIS</th>
                        <th class="whitespace-nowrap px-4 py-3 text-xs font-bold uppercase tracking-wider text-slate-500">Nama Siswa</th>
                        <th class="whitespace-nowrap px-4 py-3 text-xs font-bold uppercase tracking-wider text-slate-500">Kelas</th>
                        <th class="whitespace-nowrap px-4 py-3 text-xs font-bold uppercase tracking-wider text-slate-500">JK</th>
                        <th class="whitespace-nowrap px-4 py-3 text-xs font-bold uppercase tracking-wider text-slate-500">Ortu / HP</th>
                        <th class="whitespace-nowrap px-4 py-3 text-center text-xs font-bold uppercase tracking-wider text-slate-500">% Hadir</th>
                        <th class="whitespace-nowrap px-4 py-3 text-xs font-bold uppercase tracking-wider text-slate-500">Status</th>
                        <th class="whitespace-nowrap px-4 py-3 text-right text-xs font-bold uppercase tracking-wider text-slate-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($students as $student)
                    <tr class="transition hover:bg-slate-50/50">
                        <td class="whitespace-nowrap px-4 py-3">
                            <code class="rounded-md bg-slate-100 px-2 py-0.5 text-xs font-bold text-brand-700">{{ $student->nis }}</code>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <img src="{{ $student->photo_url }}" alt="" class="h-9 w-9 shrink-0 rounded-full border-2 border-white object-cover shadow-sm">
                                <div>
                                    <div class="font-semibold text-slate-900">{{ $student->name }}</div>
                                    <div class="text-xs text-slate-500">NISN: {{ $student->nisn ?? '-' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="whitespace-nowrap px-4 py-3">
                            <span class="inline-flex items-center rounded-full bg-brand-50 px-2.5 py-1 text-[11px] font-bold text-brand-700">{{ $student->class?->name }}</span>
                        </td>
                        <td class="whitespace-nowrap px-4 py-3">
                            @if($student->gender === 'L')
                                <span class="inline-flex items-center rounded-full bg-sky-50 px-2 py-0.5 text-[11px] font-bold text-sky-700">L</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-pink-50 px-2 py-0.5 text-[11px] font-bold text-pink-700">P</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-sm font-medium text-slate-800">{{ $student->parent_name ?? '-' }}</div>
                            <div class="flex items-center gap-1 text-xs text-slate-500">
                                <i class="bi bi-whatsapp text-emerald-500"></i>{{ $student->parent_phone ?? '-' }}
                            </div>
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-center">
                            @php $pct = $student->attendance_percentage @endphp
                            <div class="flex flex-col items-center gap-1">
                                <div class="h-1.5 w-16 overflow-hidden rounded-full bg-slate-200">
                                    <div class="h-full rounded-full {{ $pct >= 75 ? 'bg-emerald-500' : 'bg-rose-500' }}" style="width: {{ min($pct, 100) }}%"></div>
                                </div>
                                <span class="text-[11px] font-bold text-slate-700">{{ $pct }}%</span>
                            </div>
                        </td>
                        <td class="whitespace-nowrap px-4 py-3">
                            @if($student->status)
                                <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-[10px] font-bold uppercase text-emerald-700">Aktif</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-[10px] font-bold uppercase text-slate-500">Non-Aktif</span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-right">
                            <div class="inline-flex items-center divide-x divide-slate-200 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                                <a href="{{ route('students.show', $student) }}" class="px-2.5 py-1.5 text-slate-500 transition hover:bg-brand-50 hover:text-brand-600" title="Detail">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                                <a href="{{ route('students.edit', $student) }}" class="px-2.5 py-1.5 text-slate-500 transition hover:bg-brand-50 hover:text-brand-600" title="Edit">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                <button class="px-2.5 py-1.5 text-slate-500 transition hover:bg-rose-50 hover:text-rose-600" 
                                    data-confirm="Hapus siswa {{ $student->name }}?" 
                                    data-form="del-{{ $student->id }}" title="Hapus">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </div>
                            <form id="del-{{ $student->id }}" method="POST" action="{{ route('students.destroy', $student) }}" class="hidden">
                                @csrf @method('DELETE')
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-16 text-center">
                            <i class="bi bi-people text-6xl text-slate-200"></i>
                            <h5 class="mt-4 text-lg font-bold text-slate-400">Tidak ada data siswa ditemukan</h5>
                            <p class="mt-1 text-sm text-slate-400">Coba sesuaikan pencarian atau filter Anda.</p>
                            <a href="{{ route('students.create') }}" class="mt-4 inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700">
                                <i class="bi bi-plus-lg"></i>Tambah Siswa Baru
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if($students->hasPages())
        <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 px-4 py-3">
            <div class="text-sm text-slate-500">
                Menampilkan <strong>{{ $students->firstItem() ?? 0 }} - {{ $students->lastItem() ?? 0 }}</strong> dari <strong>{{ $students->total() }}</strong> siswa
            </div>
            {{ $students->withQueryString()->links('pagination::tailwind') }}
        </div>
        @endif
    </div>
</div>

{{-- Import Modal --}}
<x-modal id="importModal" title="📥 Import Data Siswa" maxWidth="max-w-md">
    <form method="POST" action="{{ route('students.import') }}" enctype="multipart/form-data">
        @csrf
        <div class="mb-4 rounded-xl border border-brand-200 bg-brand-50 p-4">
            <div class="flex items-start gap-3">
                <i class="bi bi-info-circle-fill mt-0.5 text-lg text-brand-600"></i>
                <div class="text-sm text-brand-800">
                    <strong>Instruksi Format Excel:</strong><br>
                    Gunakan kolom berikut secara berurutan:<br>
                    <code class="mt-1 inline-block rounded bg-brand-100 px-2 py-0.5 text-xs">NIS, NISN, Nama, JK (L/P), Tanggal_Lahir, Kelas, Nama_Ortu, No_HP_Ortu</code>
                </div>
            </div>
        </div>

        <div class="mb-4">
            <label class="mb-1.5 block text-sm font-semibold text-slate-700">Pilih File Excel/CSV</label>
            <div class="rounded-xl border-2 border-dashed border-slate-300 bg-slate-50 p-6 text-center transition hover:border-brand-400 hover:bg-brand-50/30">
                <i class="bi bi-file-earmark-excel mb-2 text-4xl text-emerald-500"></i>
                <input type="file" name="file" accept=".xlsx,.xls,.csv" required class="mx-auto block w-full max-w-xs text-sm text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-emerald-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-emerald-700 hover:file:bg-emerald-100">
                <p class="mt-2 text-xs text-slate-400">Maksimal ukuran file: 2MB</p>
            </div>
        </div>

        <div class="flex justify-end gap-2">
            <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-50">Batal</button>
            <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">
                <i class="bi bi-cloud-upload"></i>Mulai Import
            </button>
        </div>
    </form>
</x-modal>
@endsection