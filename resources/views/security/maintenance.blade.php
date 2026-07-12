@extends('layouts.app')
@section('title', 'Backup & Pemeliharaan')
@section('page-title', 'Backup & Pemeliharaan')

@section('content')
<div class="space-y-6">
    {{-- Back Button --}}
    <div class="flex items-center">
        <a href="{{ route('security.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
            <i class="bi bi-arrow-left"></i>
            <span>Kembali</span>
        </a>
    </div>

    {{-- Backup Cards Grid --}}
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        {{-- Backup Database Card --}}
        <div class="lg:col-span-1">
            <div class="h-full rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200/70">
                <div class="flex items-center gap-2 border-b border-slate-100 pb-4 mb-4">
                    <i class="bi bi-database-down text-brand-600 text-lg"></i>
                    <h3 class="text-base font-extrabold text-slate-900">Backup Database</h3>
                </div>
                <p class="mb-4 text-sm text-slate-600">Buat salinan database secara manual. Backup otomatis dijadwalkan setiap hari pukul 02:00 (jika scheduler aktif).</p>
                <form method="POST" action="{{ route('security.maintenance.backup') }}">
                    @csrf
                    <button type="submit" class="inline-flex w-full items-center justify-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700" onclick="return confirm('Jalankan backup database sekarang?')">
                        <i class="bi bi-download"></i>
                        <span>Buat Backup Sekarang</span>
                    </button>
                </form>
            </div>
        </div>

        {{-- Backup Files Table --}}
        <div class="lg:col-span-2">
            <div class="h-full rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/70">
                <div class="flex items-center gap-2 border-b border-slate-100 bg-slate-50/80 px-5 py-4">
                    <i class="bi bi-archive text-brand-600 text-lg"></i>
                    <h3 class="text-base font-extrabold text-slate-900">File Backup Terbaru</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-slate-600">
                        <thead class="bg-slate-50 text-xs uppercase text-slate-500 border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-3 font-semibold">Nama File</th>
                                <th class="px-4 py-3 font-semibold hidden sm:table-cell">Ukuran</th>
                                <th class="px-4 py-3 font-semibold hidden sm:table-cell">Tanggal</th>
                                <th class="px-4 py-3 font-semibold text-center">Download</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($backups as $backup)
                                <tr class="border-b border-slate-100 hover:bg-slate-50/50 transition-colors">
                                    <td class="px-4 py-3 font-medium text-slate-900">{{ $backup['name'] }}</td>
                                    <td class="px-4 py-3 hidden sm:table-cell text-slate-600">{{ $backup['size'] }}</td>
                                    <td class="px-4 py-3 hidden sm:table-cell text-slate-600">{{ $backup['date'] }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <a href="{{ route('security.maintenance.download', $backup['name']) }}" class="inline-flex items-center justify-center gap-1 rounded-lg border border-emerald-300 bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-600 transition hover:bg-emerald-100">
                                            <i class="bi bi-download"></i>
                                            <span class="hidden sm:inline">Download</span>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-12 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-slate-100">
                                                <i class="bi bi-archive text-3xl text-slate-400"></i>
                                            </div>
                                            <div class="font-semibold text-slate-700">Belum ada backup.</div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Production Commands Card --}}
    <!--<div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200/70">
        <div class="flex items-center gap-2 border-b border-slate-100 pb-4 mb-4">
            <i class="bi bi-terminal text-brand-600 text-lg"></i>
            <h3 class="text-base font-extrabold text-slate-900">Perintah Produksi</h3>
        </div>
        <div class="overflow-x-auto rounded-xl bg-slate-900 p-4">
            <pre class="mb-0 text-sm text-emerald-400 font-mono"><code>php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link
php artisan db:backup</code></pre>
        </div>
    </div>-->
</div>
@endsection
