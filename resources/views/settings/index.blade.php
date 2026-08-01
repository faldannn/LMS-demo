{{-- resources/views/settings/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Pengaturan Sistem')
@section('page-title', 'Pengaturan Sistem')

@section('content')
<div class="row g-4">

    {{-- Pengaturan Sekolah --}}
    <div class="col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-bold py-3">
                <i class="bi bi-building me-2 text-primary"></i>Informasi Sekolah
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('settings.school') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Nama Sekolah <span class="text-danger">*</span></label>
                            <input type="text" name="school_name" class="form-control @error('school_name') is-invalid @enderror"
                                value="{{ old('school_name', $settings['school_name']) }}" required>
                            @error('school_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label fw-semibold">Alamat Sekolah</label>
                            <textarea name="school_address" class="form-control" rows="2"
                                placeholder="Alamat lengkap sekolah">{{ old('school_address', $settings['school_address']) }}</textarea>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">No. Telepon</label>
                            <input type="text" name="school_phone" class="form-control"
                                value="{{ old('school_phone', $settings['school_phone']) }}" placeholder="(021) xxxxxxx">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email Sekolah</label>
                            <input type="email" name="school_email" class="form-control"
                                value="{{ old('school_email', $settings['school_email']) }}" placeholder="info@sekolah.sch.id">
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label fw-semibold">Tahun Ajaran</label>
                            <input type="text" name="academic_year" class="form-control"
                                value="{{ old('academic_year', $settings['academic_year']) }}" placeholder="2025/2026">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-calendar-week me-1 text-primary"></i>
                                Hari Operasional Sekolah
                            </label>
                            @php $opDays = old('school_operational_days', $settings['school_operational_days'] ?? '5_days'); @endphp
                            <div class="d-flex gap-3 flex-wrap mt-1">
                                <div class="form-check form-check-inline border rounded-3 px-3 py-2 flex-fill {{ $opDays === '5_days' ? 'border-primary bg-primary bg-opacity-10' : 'border-secondary-subtle' }}" style="cursor:pointer" onclick="document.getElementById('op5').click()">
                                    <input class="form-check-input" type="radio" name="school_operational_days" id="op5" value="5_days" {{ $opDays === '5_days' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="op5" style="cursor:pointer">
                                        <i class="bi bi-calendar5 me-1 text-primary"></i>
                                        5 Hari Sekolah
                                        <small class="d-block text-muted fw-normal">Senin — Jumat &nbsp;|&nbsp; Libur: Sabtu &amp; Minggu</small>
                                    </label>
                                </div>
                                <div class="form-check form-check-inline border rounded-3 px-3 py-2 flex-fill {{ $opDays === '6_days' ? 'border-primary bg-primary bg-opacity-10' : 'border-secondary-subtle' }}" style="cursor:pointer" onclick="document.getElementById('op6').click()">
                                    <input class="form-check-input" type="radio" name="school_operational_days" id="op6" value="6_days" {{ $opDays === '6_days' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="op6" style="cursor:pointer">
                                        <i class="bi bi-calendar6 me-1 text-success"></i>
                                        6 Hari Sekolah
                                        <small class="d-block text-muted fw-normal">Senin — Sabtu &nbsp;|&nbsp; Libur: Hanya Minggu</small>
                                    </label>
                                </div>
                            </div>
                            <div class="form-text mt-1">
                                <i class="bi bi-info-circle me-1"></i>
                                Pengaturan ini menentukan hari pembuatan jadwal dan validasi presensi sistem.
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Simpan Informasi Sekolah
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Pengaturan Absensi & Info Sistem --}}
    <div class="col-lg-6">
        
        {{-- Pengaturan Absensi --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white fw-bold py-3">
                <i class="bi bi-clock me-2 text-primary"></i>Pengaturan Absensi
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('settings.attendance') }}">
                    @csrf

                    {{-- 1. Pengaturan Absensi Siswa --}}
                    <div class="rounded-3 p-3 bg-light border mb-4">
                        <h6 class="fw-bold text-primary mb-3">
                            <i class="bi bi-people me-2"></i>Pengaturan Absensi Siswa
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Batas Jam Terlambat (Pagi)</label>
                                <input type="time" name="late_limit" class="form-control @error('late_limit') is-invalid @enderror"
                                    value="{{ old('late_limit', substr($settings['late_limit'], 0, 5)) }}">
                                @error('late_limit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Siswa pagi masuk setelah waktu ini dianggap terlambat.</div>
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Batas Jam Terlambat (Siang)</label>
                                <input type="time" name="late_limit_siang" class="form-control @error('late_limit_siang') is-invalid @enderror"
                                    value="{{ old('late_limit_siang', substr($settings['late_limit_siang'], 0, 5)) }}">
                                @error('late_limit_siang')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Siswa siang masuk setelah waktu ini dianggap terlambat.</div>
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Min. Kehadiran Siswa (%)</label>
                                <div class="input-group">
                                    <input type="number" name="min_attendance" class="form-control @error('min_attendance') is-invalid @enderror"
                                        value="{{ old('min_attendance', $settings['min_attendance']) }}" min="1" max="100">
                                </div>
                                @error('min_attendance')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Batas persentase peringatan kehadiran siswa.</div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Pengaturan Absensi Guru --}}
                    <div class="rounded-3 p-3 bg-light border mb-4">
                        <h6 class="fw-bold text-success mb-3">
                            <i class="bi bi-person-badge me-2"></i>Pengaturan Absensi Guru (Masuk & Pulang)
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Batas Jam Terlambat Guru</label>
                                <input type="time" name="teacher_late_limit" class="form-control @error('teacher_late_limit') is-invalid @enderror"
                                    value="{{ old('teacher_late_limit', substr($settings['teacher_late_limit'], 0, 5)) }}">
                                @error('teacher_late_limit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Guru masuk setelah waktu ini dianggap terlambat.</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Jam Mulai Absen Pulang Guru</label>
                                <input type="time" name="teacher_checkout_start" class="form-control @error('teacher_checkout_start') is-invalid @enderror"
                                    value="{{ old('teacher_checkout_start', substr($settings['teacher_checkout_start'], 0, 5)) }}">
                                @error('teacher_checkout_start')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Guru dapat melakukan absen pulang mulai jam ini.</div>
                            </div>
                        </div>
                    </div>

                    {{-- 3. Pengaturan Geofence GPS & Deteksi Wajah --}}
                    <div class="rounded-3 p-3 bg-light border mb-3">
                        <h6 class="fw-bold text-dark mb-3">
                            <i class="bi bi-geo-alt me-2"></i>Pengaturan Geofence GPS & Deteksi Wajah
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Latitude</label>
                                <input type="number" step="any" id="geofence_lat" name="geofence_lat" class="form-control" 
                                    value="{{ old('geofence_lat', $settings['geofence_lat']) }}" required>
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Longitude</label>
                                <input type="number" step="any" id="geofence_lng" name="geofence_lng" class="form-control" 
                                    value="{{ old('geofence_lng', $settings['geofence_lng']) }}" required>
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Radius Sekolah (Meter)</label>
                                <input type="number" name="geofence_radius" class="form-control" 
                                    value="{{ old('geofence_radius', $settings['geofence_radius']) }}" min="10" max="5000" required>
                            </div>
                            
                            <div class="col-12 mt-2">
                                <button type="button" id="btnGetLocation" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-geo-alt"></i> Ambil Lokasi Saat Ini
                                </button>
                                <span id="locationStatus" class="ms-2 small text-muted"></span>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Min. Deteksi Wajah</label>
                                <input type="number" step="0.01" name="face_detection_min" class="form-control" 
                                    value="{{ old('face_detection_min', $settings['face_detection_min']) }}" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Min. Face Match</label>
                                <input type="number" step="0.01" name="face_match_min" class="form-control" 
                                    value="{{ old('face_match_min', $settings['face_match_min']) }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info mt-4 mb-0">
                        <h6 class="alert-heading fw-bold mb-1">
                            <i class="bi bi-info-circle me-1"></i> Pengaturan Aktif:
                        </h6>
                        <ul class="mb-0 small ps-3">
                            <li>Status terlambat siswa: Pagi > <strong>{{ substr($settings['late_limit'], 0, 5) }}</strong> | Siang > <strong>{{ substr($settings['late_limit_siang'], 0, 5) }}</strong></li>
                            <li>Batas kehadiran minimal: <strong>{{ $settings['min_attendance'] }}%</strong></li>
                            <li>Geofence: <strong>{{ $settings['geofence_lat'] }}, {{ $settings['geofence_lng'] }}</strong> (Radius: <strong>{{ $settings['geofence_radius'] }}m</strong>)</li>
                        </ul>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Simpan Pengaturan Absensi
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Info Sistem --}}
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-bold py-3">
                <i class="bi bi-cpu me-2 text-primary"></i>Info Sistem
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-5 text-muted small">Versi Laravel</dt>
                    <dd class="col-sm-7 fw-semibold mb-2">{{ app()->version() }}</dd>
                    
                    <dt class="col-sm-5 text-muted small">Versi PHP</dt>
                    <dd class="col-sm-7 fw-semibold mb-2">{{ PHP_VERSION }}</dd>
                    
                    <dt class="col-sm-5 text-muted small">Waktu Server</dt>
                    <dd class="col-sm-7 fw-semibold mb-2">{{ now()->translatedFormat('d F Y H:i') }}</dd>
                    
                    <dt class="col-sm-5 text-muted small">Zona Waktu</dt>
                    <dd class="col-sm-7 fw-semibold mb-0">{{ config('app.timezone') }}</dd>
                </dl>
            </div>
        </div>
        
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('btnGetLocation');
    const status = document.getElementById('locationStatus');
    const latInput = document.getElementById('geofence_lat');
    const lngInput = document.getElementById('geofence_lng');

    if (btn) {
        btn.addEventListener('click', function() {
            if (!navigator.geolocation) {
                status.textContent = 'Geolocation tidak didukung oleh browser Anda.';
                status.className = 'ms-2 small text-danger';
                return;
            }

            btn.disabled = true;
            status.textContent = 'Mendapatkan lokasi...';
            status.className = 'ms-2 small text-muted';

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    latInput.value = position.coords.latitude.toFixed(6);
                    lngInput.value = position.coords.longitude.toFixed(6);
                    status.textContent = 'Lokasi berhasil diambil!';
                    status.className = 'ms-2 small text-success';
                    btn.disabled = false;
                },
                function(error) {
                    console.error(error);
                    status.textContent = 'Gagal mendapatkan lokasi: ' + error.message;
                    status.className = 'ms-2 small text-danger';
                    btn.disabled = false;
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        });
    }
});
</script>
@endpush