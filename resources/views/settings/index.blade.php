{{-- resources/views/settings/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Pengaturan Sistem')
@section('page-title', 'Pengaturan Sistem')

@push('styles')
    <style>
        .settings-pane {
            display: none;
        }
        .settings-pane.active {
            display: block;
        }
    </style>
@endpush

@section('content')
<div class="space-y-6">

    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Pengaturan Sistem</h1>
            <p class="text-xs text-slate-500 mt-1">
                Kelola informasi sekolah, parameter absensi presensi, integrasi RFID kartu, dan informasi server.
            </p>
        </div>
        <div class="shrink-0">
            <span class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600 border border-slate-200">
                <i class="bi bi-calendar-event text-brand-600"></i> {{ now()->translatedFormat('d F Y') }}
            </span>
        </div>
    </div>

    {{-- Tabs Navigation Bar --}}
    <div class="rounded-xl border border-slate-200 bg-white p-1.5 shadow-sm">
        <div class="flex items-center gap-1 overflow-x-auto">
            <button type="button" id="tab-btn-school" onclick="selectTab('school-pane', this)"
                class="settings-tab-btn inline-flex items-center gap-2 rounded-lg px-4 py-2 text-xs sm:text-sm font-bold transition whitespace-nowrap bg-brand-600 text-white shadow-sm">
                <i class="bi bi-building"></i> Informasi Sekolah
            </button>
            <button type="button" id="tab-btn-attendance" onclick="selectTab('attendance-pane', this)"
                class="settings-tab-btn inline-flex items-center gap-2 rounded-lg px-4 py-2 text-xs sm:text-sm font-semibold text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition whitespace-nowrap">
                <i class="bi bi-clock-history"></i> Pengaturan Absensi
            </button>
            <button type="button" id="tab-btn-kartu" onclick="selectTab('kartu-pane', this)"
                class="settings-tab-btn inline-flex items-center gap-2 rounded-lg px-4 py-2 text-xs sm:text-sm font-semibold text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition whitespace-nowrap">
                <i class="bi bi-credit-card-2-front"></i> Integrasi RFID Kartu
            </button>
            <button type="button" id="tab-btn-system" onclick="selectTab('system-pane', this)"
                class="settings-tab-btn inline-flex items-center gap-2 rounded-lg px-4 py-2 text-xs sm:text-sm font-semibold text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition whitespace-nowrap">
                <i class="bi bi-cpu"></i> Info Sistem
            </button>
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- TAB 1: INFORMASI SEKOLAH --}}
    {{-- ========================================== --}}
    <div id="school-pane" class="settings-pane active">
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-200 bg-slate-50 px-5 py-3.5">
                <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <i class="bi bi-building text-brand-600"></i> Identitas Utama Sekolah
                </h2>
                <p class="text-xs text-slate-500">Data resmi sekolah yang digunakan pada dokumen & laporan</p>
            </div>

            <div class="p-5 sm:p-6">
                <form method="POST" action="{{ route('settings.school') }}" class="space-y-5">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                        <div class="md:col-span-8">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Nama Sekolah <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="school_name" value="{{ old('school_name', $settings['school_name']) }}"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-sm text-slate-800 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 transition @error('school_name') border-red-500 @enderror"
                                placeholder="Contoh: SMA Islam As-Syafi'iyah" required>
                            @error('school_name')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-4">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Tahun Ajaran
                            </label>
                            <input type="text" name="academic_year" value="{{ old('academic_year', $settings['academic_year']) }}"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-sm text-slate-800 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 transition"
                                placeholder="Contoh: 2025/2026">
                        </div>

                        {{-- Operational Days --}}
                        <div class="md:col-span-12">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                <i class="bi bi-calendar-week text-brand-600 me-1"></i> Hari Operasional Sekolah
                            </label>
                            @php $opDays = old('school_operational_days', $settings['school_operational_days'] ?? '5_days'); @endphp
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <label id="label-op5" onclick="selectOpDays('5_days')"
                                    class="flex items-start gap-3 rounded-lg border-2 p-3.5 cursor-pointer transition {{ $opDays === '5_days' ? 'border-brand-600 bg-brand-50/40' : 'border-slate-200 bg-white hover:border-slate-300' }}">
                                    <input type="radio" name="school_operational_days" id="op5" value="5_days" {{ $opDays === '5_days' ? 'checked' : '' }} class="mt-0.5 text-brand-600 focus:ring-brand-500">
                                    <div>
                                        <span class="block text-sm font-bold text-slate-900">
                                            <i class="bi bi-calendar5 text-brand-600 me-1"></i> 5 Hari Sekolah
                                        </span>
                                        <span class="block text-xs text-slate-500 mt-0.5">Senin — Jumat &nbsp;|&nbsp; Libur: Sabtu &amp; Minggu</span>
                                    </div>
                                </label>

                                <label id="label-op6" onclick="selectOpDays('6_days')"
                                    class="flex items-start gap-3 rounded-lg border-2 p-3.5 cursor-pointer transition {{ $opDays === '6_days' ? 'border-brand-600 bg-brand-50/40' : 'border-slate-200 bg-white hover:border-slate-300' }}">
                                    <input type="radio" name="school_operational_days" id="op6" value="6_days" {{ $opDays === '6_days' ? 'checked' : '' }} class="mt-0.5 text-brand-600 focus:ring-brand-500">
                                    <div>
                                        <span class="block text-sm font-bold text-slate-900">
                                            <i class="bi bi-calendar6 text-emerald-600 me-1"></i> 6 Hari Sekolah
                                        </span>
                                        <span class="block text-xs text-slate-500 mt-0.5">Senin — Sabtu &nbsp;|&nbsp; Libur: Hanya Minggu</span>
                                    </div>
                                </label>
                            </div>
                            <p class="text-xs text-slate-500 mt-1.5">
                                <i class="bi bi-info-circle text-brand-500 me-1"></i> Digunakan oleh generator jadwal dan validasi hari efektif sekolah.
                            </p>
                        </div>

                        <div class="md:col-span-12">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Alamat Lengkap Sekolah</label>
                            <textarea name="school_address" rows="2"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-sm text-slate-800 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 transition"
                                placeholder="Alamat lengkap sekolah...">{{ old('school_address', $settings['school_address']) }}</textarea>
                        </div>

                        <div class="md:col-span-6">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">No. Telepon / Fax</label>
                            <input type="text" name="school_phone" value="{{ old('school_phone', $settings['school_phone']) }}"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-sm text-slate-800 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 transition"
                                placeholder="(021) xxxxxxx">
                        </div>

                        <div class="md:col-span-6">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Email Resmi Sekolah</label>
                            <input type="email" name="school_email" value="{{ old('school_email', $settings['school_email']) }}"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-sm text-slate-800 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 transition"
                                placeholder="info@sekolah.sch.id">
                        </div>
                    </div>

                    <div class="flex justify-end pt-4 border-t border-slate-200">
                        <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-brand-600 px-5 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-brand-700 transition">
                            <i class="bi bi-save"></i> Simpan Informasi Sekolah
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- TAB 2: PENGATURAN ABSENSI --}}
    {{-- ========================================== --}}
    <div id="attendance-pane" class="settings-pane">
        <form method="POST" action="{{ route('settings.attendance') }}" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                {{-- LEFT: SISWA & GURU --}}
                <div class="space-y-6">
                    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm space-y-4">
                        <div class="flex items-center gap-2 text-sm font-bold text-brand-600 border-b border-slate-100 pb-3">
                            <i class="bi bi-people-fill text-base"></i> 1. Presensi Siswa
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Batas Terlambat (Pagi)</label>
                                <input type="time" name="late_limit" value="{{ old('late_limit', substr($settings['late_limit'], 0, 5)) }}"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-sm text-slate-800 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 transition @error('late_limit') border-red-500 @enderror">
                                @error('late_limit')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                                <p class="text-xs text-slate-400 mt-1">Siswa pagi terlambat jika absen lewat jam ini.</p>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Batas Terlambat (Siang)</label>
                                <input type="time" name="late_limit_siang" value="{{ old('late_limit_siang', substr($settings['late_limit_siang'], 0, 5)) }}"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-sm text-slate-800 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 transition @error('late_limit_siang') border-red-500 @enderror">
                                @error('late_limit_siang')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                                <p class="text-xs text-slate-400 mt-1">Siswa siang terlambat jika absen lewat jam ini.</p>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Minimal Kehadiran Siswa (%)</label>
                            <div class="flex">
                                <input type="number" name="min_attendance" value="{{ old('min_attendance', $settings['min_attendance']) }}" min="1" max="100"
                                    class="w-full rounded-l-lg border border-r-0 border-slate-300 bg-white px-3.5 py-2 text-sm text-slate-800 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 transition @error('min_attendance') border-red-500 @enderror">
                                <span class="inline-flex items-center px-3.5 rounded-r-lg border border-slate-300 bg-slate-100 text-slate-600 font-bold text-sm">%</span>
                            </div>
                            @error('min_attendance')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-slate-400 mt-1">Batas minimal persentase kehadiran.</p>
                        </div>
                    </div>

                    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm space-y-4">
                        <div class="flex items-center gap-2 text-sm font-bold text-emerald-600 border-b border-slate-100 pb-3">
                            <i class="bi bi-person-badge-fill text-base"></i> 2. Presensi Guru
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Batas Terlambat Guru</label>
                                <input type="time" name="teacher_late_limit" value="{{ old('teacher_late_limit', substr($settings['teacher_late_limit'], 0, 5)) }}"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-sm text-slate-800 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 transition @error('teacher_late_limit') border-red-500 @enderror">
                                @error('teacher_late_limit')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                                <p class="text-xs text-slate-400 mt-1">Guru terlambat jika absen masuk lewat jam ini.</p>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Jam Mulai Absen Pulang</label>
                                <input type="time" name="teacher_checkout_start" value="{{ old('teacher_checkout_start', substr($settings['teacher_checkout_start'], 0, 5)) }}"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-sm text-slate-800 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 transition @error('teacher_checkout_start') border-red-500 @enderror">
                                @error('teacher_checkout_start')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                                <p class="text-xs text-slate-400 mt-1">Absen pulang guru diizinkan mulai jam ini.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- RIGHT: GEOFENCE & FACE DETECTION --}}
                <div class="space-y-6">
                    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm space-y-4">
                        <div class="flex items-center gap-2 text-sm font-bold text-rose-600 border-b border-slate-100 pb-3">
                            <i class="bi bi-geo-alt-fill text-base"></i> 3. Validasi Geofence GPS
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Latitude</label>
                                <input type="number" step="any" id="geofence_lat" name="geofence_lat" value="{{ old('geofence_lat', $settings['geofence_lat']) }}"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-800 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 transition" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Longitude</label>
                                <input type="number" step="any" id="geofence_lng" name="geofence_lng" value="{{ old('geofence_lng', $settings['geofence_lng']) }}"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-800 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 transition" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Radius (Meter)</label>
                                <input type="number" name="geofence_radius" value="{{ old('geofence_radius', $settings['geofence_radius']) }}" min="10" max="5000"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-800 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 transition" required>
                            </div>
                        </div>
                        <div>
                            <button type="button" id="btnGetLocation" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-slate-50 px-3 py-1.5 text-xs font-bold text-slate-700 hover:bg-slate-100 transition">
                                <i class="bi bi-crosshair text-brand-600"></i> Ambil Lokasi GPS Saat Ini
                            </button>
                            <span id="locationStatus" class="ml-2 text-xs text-slate-500 font-medium"></span>
                        </div>
                    </div>

                    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm space-y-4">
                        <div class="flex items-center gap-2 text-sm font-bold text-indigo-600 border-b border-slate-100 pb-3">
                            <i class="bi bi-person-bounding-box text-base"></i> 4. Deteksi Wajah AI
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Min. Deteksi Wajah</label>
                                <input type="number" step="0.01" name="face_detection_min" value="{{ old('face_detection_min', $settings['face_detection_min']) }}"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-sm text-slate-800 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 transition" required>
                                <p class="text-xs text-slate-400 mt-1">Deteksi wajah (0.1 - 1.0).</p>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Min. Face Match</label>
                                <input type="number" step="0.01" name="face_match_min" value="{{ old('face_match_min', $settings['face_match_min']) }}"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-sm text-slate-800 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 transition" required>
                                <p class="text-xs text-slate-400 mt-1">Matching kemiripan wajah.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SUMMARY --}}
                <div class="lg:col-span-2 rounded-xl border border-brand-200 bg-brand-50/50 p-4">
                    <div class="flex items-center gap-2 text-brand-800 text-xs font-bold mb-1">
                        <i class="bi bi-info-circle-fill"></i> Ringkasan Parameter Absensi Aktif:
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-xs text-slate-700 mt-1.5">
                        <div>• Terlambat Siswa Pagi: <strong>{{ substr($settings['late_limit'], 0, 5) }}</strong> | Siang: <strong>{{ substr($settings['late_limit_siang'], 0, 5) }}</strong></div>
                        <div>• Terlambat Guru: <strong>{{ substr($settings['teacher_late_limit'], 0, 5) }}</strong> | Pulang: <strong>{{ substr($settings['teacher_checkout_start'], 0, 5) }}</strong></div>
                        <div>• Geofence Radius: <strong>{{ $settings['geofence_radius'] }} Meter</strong> (Lat: {{ $settings['geofence_lat'] }}, Lng: {{ $settings['geofence_lng'] }})</div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-4 border-t border-slate-200">
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-brand-600 px-5 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-brand-700 transition">
                    <i class="bi bi-save"></i> Simpan Pengaturan Absensi
                </button>
            </div>
        </form>
    </div>

    {{-- ========================================== --}}
    {{-- TAB 3: INTEGRASI RFID KARTU --}}
    {{-- ========================================== --}}
    <div id="kartu-pane" class="settings-pane">
        <div class="space-y-6">
            
            {{-- Main Form Card --}}
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="border-b border-slate-200 bg-slate-50 px-5 py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                            <i class="bi bi-credit-card-2-front text-amber-500"></i> Integrasi Mesin Tap Kartu RFID (Riansa Kartu)
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Menghubungkan mesin presensi tap kartu fisik Riansa ke LMS</p>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold shrink-0 {{ $settings['kartu_enabled'] ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                        {{ $settings['kartu_enabled'] ? '● Integrasi Aktif' : '○ Nonaktif' }}
                    </span>
                </div>

                <div class="p-5 sm:p-6">
                    <form method="POST" action="{{ route('settings.kartu') }}" class="space-y-5">
                        @csrf
                        
                        {{-- Toggle --}}
                        <div class="flex items-center justify-between gap-4 rounded-lg border border-slate-200 bg-slate-50 p-4">
                            <div>
                                <span class="block text-sm font-bold text-slate-900">Aktifkan Integrasi Mesin Tap Kartu</span>
                                <span class="text-xs text-slate-500">Terima presensi otomatis dari mesin tap RFID secara real-time</span>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" name="kartu_enabled" id="kartu_enabled_checkbox" value="1" {{ $settings['kartu_enabled'] ? 'checked' : '' }}
                                    class="h-5 w-5 rounded border-slate-300 text-brand-600 focus:ring-brand-500 cursor-pointer">
                            </div>
                        </div>

                        {{-- API Key & Webhook Secret --}}
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">API Key Riansa Kartu</label>
                                <input type="text" name="kartu_api_key" value="{{ old('kartu_api_key', $settings['kartu_api_key']) }}"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-xs sm:text-sm font-mono text-slate-800 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 transition"
                                    placeholder="sk_live_xxxxxxxx...">
                                <p class="text-xs text-slate-400 mt-1">Dapatkan API Key dari dashboard Riansa Kartu (<a href="https://riansa.top/kartu/app/" target="_blank" class="text-brand-600 underline">riansa.top/kartu/app</a>).</p>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Webhook Secret Key</label>
                                <input type="text" name="kartu_webhook_secret" value="{{ old('kartu_webhook_secret', $settings['kartu_webhook_secret']) }}"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-xs sm:text-sm font-mono text-slate-800 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 transition"
                                    placeholder="Isi webhook secret dari Riansa Kartu">
                                <p class="text-xs text-slate-400 mt-1">Digunakan untuk memverifikasi keaslian pengiriman webhook.</p>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Alamat Webhook LMS (Masukkan ke Dashboard Riansa Kartu)</label>
                                <div class="flex flex-col sm:flex-row gap-2">
                                    <input type="text" id="webhook_url_input" value="{{ $settings['webhook_url'] }}" readonly
                                        class="w-full rounded-lg border border-slate-300 bg-slate-100 px-3.5 py-2 text-xs sm:text-sm font-mono text-slate-700">
                                    <button type="button" onclick="copyWebhookUrl()"
                                        class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-slate-300 bg-slate-200 hover:bg-slate-300 px-4 py-2 text-xs font-bold text-slate-800 transition shrink-0">
                                        <i class="bi bi-clipboard"></i> Salin
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end pt-4 border-t border-slate-200">
                            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-slate-900 px-5 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-slate-800 transition">
                                <i class="bi bi-save"></i> Simpan Konfigurasi Kartu
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Manual Sync Actions Card --}}
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm space-y-3">
                <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider border-b border-slate-100 pb-2.5 flex items-center gap-2">
                    <i class="bi bi-arrow-repeat text-brand-600"></i> Aksi Sinkronisasi Manual
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <form method="POST" action="{{ route('settings.kartu.sync-members') }}">
                        @csrf
                        <button type="submit" onclick="return confirm('Kirim seluruh daftar siswa ke Riansa Kartu?')"
                            class="w-full text-left p-3 rounded-lg border border-brand-200 bg-brand-50/60 hover:bg-brand-100/60 text-brand-700 font-bold text-xs flex items-center justify-between transition">
                            <span><i class="bi bi-cloud-upload me-1.5"></i> Sinkronkan Daftar Siswa ke Cloud</span>
                            <i class="bi bi-chevron-right text-xs"></i>
                        </button>
                    </form>

                    <form method="POST" action="{{ route('settings.kartu.pull-attendance') }}">
                        @csrf
                        <button type="submit"
                            class="w-full text-left p-3 rounded-lg border border-emerald-200 bg-emerald-50/60 hover:bg-emerald-100/60 text-emerald-700 font-bold text-xs flex items-center justify-between transition">
                            <span><i class="bi bi-cloud-download me-1.5"></i> Tarik Data Absensi (Pull Sync)</span>
                            <i class="bi bi-chevron-right text-xs"></i>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Instructions Card --}}
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-5 space-y-2">
                <h4 class="font-bold text-xs uppercase tracking-wider text-slate-700 flex items-center gap-2">
                    <i class="bi bi-book-half text-brand-600"></i> Petunjuk Integrasi:
                </h4>
                <ol class="text-xs text-slate-600 space-y-1.5 list-decimal ps-4 leading-relaxed">
                    <li>Buka dashboard Riansa Kartu di <a href="https://riansa.top/kartu/app/" target="_blank" class="text-brand-600 underline font-semibold">riansa.top/kartu/app</a>.</li>
                    <li>Salin <strong>API Key</strong> & <strong>Webhook Secret</strong> lalu tempel pada form di atas.</li>
                    <li>Salin <strong>Alamat Webhook LMS</strong> dan masukkan ke menu Webhook Riansa Kartu.</li>
                    <li>Klik <strong>Sinkronkan Daftar Siswa</strong> agar ID/NIS siswa dikenal oleh mesin tap.</li>
                </ol>
            </div>

        </div>
    </div>

    {{-- ========================================== --}}
    {{-- TAB 4: INFO SISTEM --}}
    {{-- ========================================== --}}
    <div id="system-pane" class="settings-pane">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Server Info --}}
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="border-b border-slate-200 bg-slate-50 px-5 py-3.5">
                    <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <i class="bi bi-cpu text-brand-600"></i> Server & Framework Environment
                    </h2>
                </div>
                <div class="divide-y divide-slate-100 text-xs">
                    <div class="px-5 py-3 flex items-center justify-between">
                        <span class="text-slate-500 font-medium">Framework</span>
                        <span class="font-bold text-slate-800">Laravel {{ app()->version() }}</span>
                    </div>
                    <div class="px-5 py-3 flex items-center justify-between">
                        <span class="text-slate-500 font-medium">PHP Version</span>
                        <span class="font-bold text-slate-800">{{ PHP_VERSION }}</span>
                    </div>
                    <div class="px-5 py-3 flex items-center justify-between">
                        <span class="text-slate-500 font-medium">Waktu Server</span>
                        <span class="font-bold text-slate-800">{{ now()->translatedFormat('d F Y H:i:s') }}</span>
                    </div>
                    <div class="px-5 py-3 flex items-center justify-between">
                        <span class="text-slate-500 font-medium">Zona Waktu</span>
                        <span class="font-bold text-slate-800">{{ config('app.timezone') }}</span>
                    </div>
                    <div class="px-5 py-3 flex items-center justify-between">
                        <span class="text-slate-500 font-medium">App Environment</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-700 font-bold text-[11px]">{{ app()->environment() }}</span>
                    </div>
                </div>
            </div>

            {{-- DB Info --}}
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="border-b border-slate-200 bg-slate-50 px-5 py-3.5">
                    <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <i class="bi bi-database text-indigo-600"></i> Database & Service Drivers
                    </h2>
                </div>
                <div class="divide-y divide-slate-100 text-xs">
                    <div class="px-5 py-3 flex items-center justify-between">
                        <span class="text-slate-500 font-medium">Database Driver</span>
                        <span class="font-bold text-slate-800">{{ strtoupper(config('database.default')) }}</span>
                    </div>
                    <div class="px-5 py-3 flex items-center justify-between">
                        <span class="text-slate-500 font-medium">DB Host</span>
                        <span class="font-bold text-slate-800">{{ config('database.connections.' . config('database.default') . '.host', '127.0.0.1') }}</span>
                    </div>
                    <div class="px-5 py-3 flex items-center justify-between">
                        <span class="text-slate-500 font-medium">DB Name</span>
                        <span class="font-bold text-slate-800">{{ config('database.connections.' . config('database.default') . '.database') }}</span>
                    </div>
                    <div class="px-5 py-3 flex items-center justify-between">
                        <span class="text-slate-500 font-medium">Broadcast Driver</span>
                        <span class="font-bold text-slate-800">{{ config('broadcasting.default') }}</span>
                    </div>
                    <div class="px-5 py-3 flex items-center justify-between">
                        <span class="text-slate-500 font-medium">Cache Driver</span>
                        <span class="font-bold text-slate-800">{{ config('cache.default') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function selectTab(targetPaneId, clickedBtn) {
    // 1. Hide all tab panes
    document.querySelectorAll('.settings-pane').forEach(pane => {
        pane.classList.remove('active');
    });

    // 2. Deactivate all buttons
    document.querySelectorAll('.settings-tab-btn').forEach(btn => {
        btn.classList.remove('bg-brand-600', 'text-white', 'shadow-sm', 'font-bold');
        btn.classList.add('text-slate-600', 'hover:bg-slate-100', 'hover:text-slate-900', 'font-semibold');
    });

    // 3. Show target pane
    const targetPane = document.getElementById(targetPaneId);
    if (targetPane) {
        targetPane.classList.add('active');
    }

    // 4. Activate clicked button
    if (clickedBtn) {
        clickedBtn.classList.remove('text-slate-600', 'hover:bg-slate-100', 'hover:text-slate-900', 'font-semibold');
        clickedBtn.classList.add('bg-brand-600', 'text-white', 'shadow-sm', 'font-bold');
    }

    // 5. Save Hash
    if (history.pushState) {
        history.pushState(null, null, '#' + targetPaneId);
    } else {
        location.hash = '#' + targetPaneId;
    }
}

function selectOpDays(val) {
    const is5 = val === '5_days';
    document.getElementById('op5').checked = is5;
    document.getElementById('op6').checked = !is5;

    const label5 = document.getElementById('label-op5');
    const label6 = document.getElementById('label-op6');

    if (is5) {
        label5.className = "flex items-start gap-3 rounded-lg border-2 p-3.5 cursor-pointer transition border-brand-600 bg-brand-50/40";
        label6.className = "flex items-start gap-3 rounded-lg border-2 p-3.5 cursor-pointer transition border-slate-200 bg-white hover:border-slate-300";
    } else {
        label6.className = "flex items-start gap-3 rounded-lg border-2 p-3.5 cursor-pointer transition border-brand-600 bg-brand-50/40";
        label5.className = "flex items-start gap-3 rounded-lg border-2 p-3.5 cursor-pointer transition border-slate-200 bg-white hover:border-slate-300";
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Hash router
    const hash = window.location.hash.replace('#', '');
    const paneMap = {
        'school-pane': 'tab-btn-school',
        'attendance-pane': 'tab-btn-attendance',
        'kartu-pane': 'tab-btn-kartu',
        'system-pane': 'tab-btn-system'
    };

    if (hash && paneMap[hash]) {
        selectTab(hash, document.getElementById(paneMap[hash]));
    } else {
        selectTab('school-pane', document.getElementById('tab-btn-school'));
    }

    // GPS Geolocation Handler
    const btn = document.getElementById('btnGetLocation');
    const status = document.getElementById('locationStatus');
    const latInput = document.getElementById('geofence_lat');
    const lngInput = document.getElementById('geofence_lng');

    if (btn) {
        btn.addEventListener('click', function() {
            if (!navigator.geolocation) {
                status.textContent = 'Geolocation tidak didukung oleh browser.';
                status.className = 'ml-2 text-xs text-red-500 font-bold';
                return;
            }

            btn.disabled = true;
            status.textContent = 'Mendapatkan lokasi GPS...';
            status.className = 'ml-2 text-xs text-slate-500 font-medium';

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    latInput.value = position.coords.latitude.toFixed(6);
                    lngInput.value = position.coords.longitude.toFixed(6);
                    status.textContent = 'Lokasi berhasil diambil!';
                    status.className = 'ml-2 text-xs text-emerald-600 font-bold';
                    btn.disabled = false;
                },
                function(error) {
                    console.error(error);
                    status.textContent = 'Gagal mengambil lokasi: ' + error.message;
                    status.className = 'ml-2 text-xs text-red-500 font-bold';
                    btn.disabled = false;
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        });
    }
});

function copyWebhookUrl() {
    const input = document.getElementById('webhook_url_input');
    if (input) {
        input.select();
        navigator.clipboard.writeText(input.value).then(function() {
            alert('URL Webhook berhasil disalin: ' + input.value);
        }).catch(function(err) {
            document.execCommand('copy');
            alert('URL Webhook berhasil disalin!');
        });
    }
}
</script>
@endpush
