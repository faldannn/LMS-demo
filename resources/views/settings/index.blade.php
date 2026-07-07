{{-- resources/views/settings/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Pengaturan Sistem')
@section('page-title', 'Pengaturan Sistem')

@section('content')
<div class="row g-4">

    {{-- Pengaturan Sekolah --}}
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">
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
                            @error('school_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
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

    {{-- Pengaturan Absensi --}}
    <div class="col-lg-6">
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-clock me-2 text-primary"></i>Pengaturan Absensi
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('settings.attendance') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Batas Jam Terlambat</label>
                            <input type="time" name="late_limit" class="form-control @error('late_limit') is-invalid @enderror"
                                value="{{ old('late_limit', substr($settings['late_limit'], 0, 5)) }}">
                            @error('late_limit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-text">Siswa masuk setelah waktu ini dianggap terlambat</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Min. Kehadiran (%)</label>
                            <div class="input-group">
                                <input type="number" name="min_attendance" class="form-control @error('min_attendance') is-invalid @enderror"
                                    value="{{ old('min_attendance', $settings['min_attendance']) }}" min="1" max="100">
                                <span class="input-group-text">%</span>
                            </div>
                            @error('min_attendance')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-text">Siswa di bawah persentase ini dianggap perlu perhatian</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Latitude Sekolah</label>
                            <input type="number" step="any" id="geofence_lat" name="geofence_lat" class="form-control" value="{{ old('geofence_lat', $settings['geofence_lat']) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Longitude Sekolah</label>
                            <input type="number" step="any" id="geofence_lng" name="geofence_lng" class="form-control" value="{{ old('geofence_lng', $settings['geofence_lng']) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Radius (meter)</label>
                            <input type="number" name="geofence_radius" class="form-control" value="{{ old('geofence_radius', $settings['geofence_radius']) }}" min="10" max="5000" required>
                        </div>
                        <div class="col-12 mt-2">
                            <button type="button" id="btnGetLocation" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-geo-alt"></i> Ambil Lokasi Saat Ini
                            </button>
                            <span id="locationStatus" class="ms-2 small text-muted"></span>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Min. Deteksi Wajah</label>
                            <input type="number" step="0.01" name="face_detection_min" class="form-control" value="{{ old('face_detection_min', $settings['face_detection_min']) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Min. Face Match</label>
                            <input type="number" step="0.01" name="face_match_min" class="form-control" value="{{ old('face_match_min', $settings['face_match_min']) }}" required>
                        </div>
                    </div>

                    <div class="alert alert-info mt-3">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Pengaturan saat ini:</strong><br>
                        Terlambat jika masuk setelah <code>{{ substr($settings['late_limit'],0,5) }}</code> ·
                        Kehadiran minimal <code>{{ $settings['min_attendance'] }}%</code><br>
                        Geofence: <code>{{ $settings['geofence_lat'] }}, {{ $settings['geofence_lng'] }}</code> · Radius <code>{{ $settings['geofence_radius'] }}m</code>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Simpan Pengaturan Absensi
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Info Sistem --}}
        <div class="card">
            <div class="card-header"><i class="bi bi-info-circle me-2 text-primary"></i>Info Sistem</div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-5 text-muted small">Versi Laravel</dt>
                    <dd class="col-sm-7 fw-semibold mb-2">{{ app()->version() }}</dd>
                    <dt class="col-sm-5 text-muted small">Versi PHP</dt>
                    <dd class="col-sm-7 fw-semibold mb-2">{{ PHP_VERSION }}</dd>
                    <dt class="col-sm-5 text-muted small">Server Time</dt>
                    <dd class="col-sm-7 fw-semibold mb-2">{{ now()->translatedFormat('d F Y H:i') }}</dd>
                    <dt class="col-sm-5 text-muted small">Timezone</dt>
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
