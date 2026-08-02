{{-- resources/views/settings/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Pengaturan Sistem')
@section('page-title', 'Pengaturan Sistem')

@push('styles')
    <style>
        .settings-shell {
            display: grid;
            gap: 1.5rem;
        }

        .settings-hero {
            border: 0;
            border-radius: 1.5rem;
            overflow: hidden;
            background: linear-gradient(135deg, var(--color-brand-700), var(--color-brand-500));
            box-shadow: 0 20px 45px rgba(15, 23, 42, .12);
        }

        .settings-hero__inner {
            padding: 1.5rem;
        }

        @media (min-width: 992px) {
            .settings-hero__inner {
                padding: 2rem 2.25rem;
            }
        }

        .settings-hero__tag {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            padding: .35rem .8rem;
            border-radius: 999px;
            background: rgba(255, 255, 255, .12);
            border: 1px solid rgba(255, 255, 255, .12);
            color: rgba(255, 255, 255, .9);
            font-size: .76rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .settings-hero__title {
            margin: .85rem 0 .5rem;
            color: #fff;
            font-size: clamp(1.5rem, 2.2vw, 2.15rem);
            font-weight: 800;
            line-height: 1.2;
        }

        .settings-hero__text {
            max-width: 58rem;
            margin: 0;
            color: rgba(255, 255, 255, .84);
            line-height: 1.7;
        }

        .settings-hero__chips {
            display: flex;
            flex-wrap: wrap;
            gap: .75rem;
            margin-top: 1.25rem;
        }

        .settings-chip {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            padding: .7rem .9rem;
            border-radius: .95rem;
            background: rgba(255, 255, 255, .12);
            border: 1px solid rgba(255, 255, 255, .14);
            color: #fff;
            font-size: .82rem;
            font-weight: 600;
        }

        .settings-card {
            border: 0;
            border-radius: 1.25rem;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 12px 32px rgba(15, 23, 42, .08);
        }

        .settings-card .card-header {
            padding: 1rem 1.25rem;
            background: linear-gradient(180deg, #fff 0%, #f8fafc 100%);
            border-bottom: 1px solid #e2e8f0;
        }

        .settings-card .card-body {
            padding: 1.25rem;
        }

        .settings-card__head {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: .5rem .75rem;
        }

        .settings-card__title {
            display: inline-flex;
            align-items: center;
            gap: .55rem;
            font-weight: 800;
            color: #0f172a;
        }

        .settings-card__subtitle {
            color: #64748b;
            font-size: .8rem;
        }

        .settings-label {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            margin-bottom: .45rem;
            font-weight: 700;
            color: #0f172a;
        }

        .settings-control,
        .settings-control.form-control,
        .settings-control.form-select,
        .settings-control textarea {
            border-radius: .9rem;
            border-color: #dbe4f0;
            box-shadow: none;
            padding: .78rem .95rem;
        }

        .settings-control:focus {
            border-color: var(--color-brand-500);
            box-shadow: 0 0 0 .2rem rgba(59, 130, 246, .12);
        }

        .settings-help {
            margin-top: .4rem;
            color: #64748b;
            font-size: .8rem;
            line-height: 1.5;
        }

        .settings-action {
            display: flex;
            justify-content: flex-end;
            margin-top: 1.25rem;
        }

        .settings-action .btn {
            min-width: 220px;
            border-radius: .95rem;
            padding: .8rem 1.15rem;
            font-weight: 700;
        }

        .settings-summary {
            margin-top: 1.25rem;
            padding: 1rem 1.1rem;
            border-radius: 1rem;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }

        .settings-summary h6 {
            color: #0f172a;
        }

        .settings-summary ul {
            margin-bottom: 0;
            padding-left: 1.1rem;
            color: #475569;
        }

        .settings-system-list dt {
            color: #64748b;
            font-weight: 600;
        }

        .settings-system-list dd {
            color: #0f172a;
        }

        .settings-location {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: .75rem;
        }

        .settings-location .btn {
            border-radius: .85rem;
        }

        @media (max-width: 767.98px) {
            .settings-action {
                justify-content: stretch;
            }

            .settings-action .btn {
                width: 100%;
                min-width: 0;
            }
        }

        /* =========================================================
           MOBILE-ONLY ADDITIONS BELOW.
           Nothing above this line was changed — desktop (≥992px)
           renders exactly as before. Everything in this block is
           scoped to max-width: 991.98px, so desktop is untouched.
           ========================================================= */
        @media (max-width: 991.98px) {

            /* Stop any horizontal overflow at the root of the page,
               which is what actually causes fields like No. Telepon /
               Email to look "cut off" at the edge of the screen. */
            html, body {
                overflow-x: hidden;
            }

            .settings-shell {
                max-width: 100vw;
                overflow-x: hidden;
                box-sizing: border-box;
                padding-left: .75rem;
                padding-right: .75rem;
            }

            /* Neutralize Bootstrap's .row negative margins, which is
               the usual real cause of mobile overflow/cut-off when a
               row isn't wrapped by a matching .container. */
            .settings-shell .row {
                margin-left: 0 !important;
                margin-right: 0 !important;
            }

            /* Force every grid column inside the settings page to a
               single full-width stacked column on mobile — this
               covers Nama/Alamat/No. Telepon/Email, the attendance
               fields, and the Latitude/Longitude/Radius trio. */
            .settings-shell .row > [class*="col-"] {
                width: 100% !important;
                max-width: 100% !important;
                flex: 0 0 100% !important;
                padding-left: .5rem !important;
                padding-right: .5rem !important;
                box-sizing: border-box !important;
            }

            /* The right-hand stack (Pengaturan Absensi + Info Sistem)
               keeps its natural source order — Info Sistem stays
               below Pengaturan Absensi automatically. */
            .row.g-4.align-items-start > .col-xl-6 {
                width: 100% !important;
                max-width: 100% !important;
                flex: 0 0 100% !important;
            }
            .row.g-4.align-items-start > .col-xl-6.d-flex {
                display: flex !important;
                flex-direction: column !important;
            }

            .settings-hero__inner {
                padding: 1.25rem;
            }

            .settings-card {
                margin-bottom: 0;
            }

            .settings-card .card-body {
                padding: 1rem;
            }

            .settings-card .card-header {
                padding: .9rem 1rem;
            }

            .settings-action {
                justify-content: stretch;
            }

            .settings-action .btn {
                width: 100%;
                min-width: 0;
            }

            /* h-100 + overflow:hidden (from the original desktop CSS)
               can clip a card's content once the layout is forced to
               stack on mobile. Neutralize just the height constraint
               on small screens so every field inside the card
               (including Email Sekolah, Tahun Ajaran, and the submit
               button) is always fully visible. */
            .settings-card.h-100 {
                height: auto !important;
            }
            .settings-card {
                overflow: visible !important;
            }
            .settings-card .card-header {
                border-radius: 1.25rem 1.25rem 0 0;
            }
        }

        @media (max-width: 575.98px) {
            .settings-hero__title {
                font-size: 1.4rem;
            }

            .settings-hero__text {
                font-size: .92rem;
            }

            .settings-chip {
                font-size: .78rem;
                padding: .55rem .75rem;
            }

            .settings-card__title {
                font-size: .98rem;
            }
        }

        /* Prevent any input/select/textarea inside the settings page
           from ever exceeding its container width on small screens
           (fixes fields getting visually cut off on mobile). */
        @media (max-width: 991.98px) {
            .settings-shell input,
            .settings-shell select,
            .settings-shell textarea {
                width: 100% !important;
                max-width: 100% !important;
                box-sizing: border-box !important;
            }

            .settings-shell .input-group {
                width: 100%;
                flex-wrap: nowrap;
            }

            .settings-shell .input-group > .form-control {
                min-width: 0;
            }
        }
    </style>
@endpush

@section('content')
<div class="settings-shell">
    <div class="settings-hero">
        <div class="settings-hero__inner row g-4 align-items-center">
            <div class="col-xl-8">
                <span class="settings-hero__tag"><i class="bi bi-gear-fill"></i> Admin Panel</span>
                <h1 class="settings-hero__title">Pengaturan Sistem</h1>
                <p class="settings-hero__text">
                    Atur informasi sekolah, parameter absensi, dan detail sistem pada satu tempat dengan tampilan yang lebih terstruktur, mudah dibaca, dan nyaman digunakan.
                </p>
                <div class="settings-hero__chips">
                    <div class="settings-chip"><i class="bi bi-building"></i> Informasi Sekolah</div>
                    <div class="settings-chip"><i class="bi bi-clock-history"></i> Pengaturan Absensi</div>
                    <div class="settings-chip"><i class="bi bi-cpu"></i> Info Sistem</div>
                </div>
            </div>
            <div class="col-xl-4 d-none d-xl-block">
                <div class="rounded-4 bg-white bg-opacity-10 border border-white border-opacity-10 p-4 text-white shadow-sm">
                    <div class="d-flex align-items-center gap-3">
                        <div class="d-flex align-items-center justify-content-center rounded-4 bg-white bg-opacity-15" style="width:56px;height:56px;">
                            <i class="bi bi-sliders2-vertical fs-3"></i>
                        </div>
                        <div>
                            <div class="fw-bold">Panel pengelolaan</div>
                            <div class="small" style="opacity:.82">Semua pengaturan inti ada di sini</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 align-items-start">
        <div class="col-xl-6">
            <div class="card settings-card h-100">
                <div class="card-header">
                    <div class="settings-card__head">
                        <div class="settings-card__title"><i class="bi bi-building text-primary"></i>Informasi Sekolah</div>
                        <div class="settings-card__subtitle">Data dasar sekolah yang tampil di sistem</div>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('settings.school') }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="settings-label">Nama Sekolah <span class="text-danger">*</span></label>
                                <input type="text" name="school_name" class="form-control settings-control @error('school_name') is-invalid @enderror"
                                    value="{{ old('school_name', $settings['school_name']) }}" required>
                                @error('school_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="settings-label">Alamat Sekolah</label>
                                <textarea name="school_address" class="form-control settings-control" rows="2"
                                    placeholder="Alamat lengkap sekolah">{{ old('school_address', $settings['school_address']) }}</textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="settings-label">No. Telepon</label>
                                <input type="text" name="school_phone" class="form-control settings-control"
                                    value="{{ old('school_phone', $settings['school_phone']) }}" placeholder="(021) xxxxxxx">
                            </div>

                            <div class="col-md-6">
                                <label class="settings-label">Email Sekolah</label>
                                <input type="email" name="school_email" class="form-control settings-control"
                                    value="{{ old('school_email', $settings['school_email']) }}" placeholder="info@sekolah.sch.id">
                            </div>

                            <div class="col-12">
                                <label class="settings-label">Tahun Ajaran</label>
                                <input type="text" name="academic_year" class="form-control settings-control"
                                    value="{{ old('academic_year', $settings['academic_year']) }}" placeholder="2025/2026">
                            </div>
                        </div>

                        <div class="settings-action">
                            <button type="submit" class="btn btn-primary shadow-sm">
                                <i class="bi bi-save me-2"></i>Simpan Informasi Sekolah
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xl-6 d-flex flex-column gap-4">
            <div class="card settings-card">
                <div class="card-header">
                    <div class="settings-card__head">
                        <div class="settings-card__title"><i class="bi bi-clock text-primary"></i>Pengaturan Absensi</div>
                        <div class="settings-card__subtitle">Parameter presensi dan deteksi wajah</div>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('settings.attendance') }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="settings-label">Batas Jam Terlambat</label>
                                <input type="time" name="late_limit" class="form-control settings-control @error('late_limit') is-invalid @enderror"
                                    value="{{ old('late_limit', substr($settings['late_limit'], 0, 5)) }}">
                                @error('late_limit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="settings-help">Siswa masuk setelah waktu ini dianggap terlambat.</div>
                            </div>

                            <div class="col-md-6">
                                <label class="settings-label">Min. Kehadiran (%)</label>
                                <div class="input-group">
                                    <input type="number" name="min_attendance" class="form-control settings-control @error('min_attendance') is-invalid @enderror"
                                        value="{{ old('min_attendance', $settings['min_attendance']) }}" min="1" max="100">
                                    <span class="input-group-text">%</span>
                                </div>
                                @error('min_attendance')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="settings-help">Batas persentase peringatan kehadiran.</div>
                            </div>

                            <div class="col-md-4">
                                <label class="settings-label">Latitude</label>
                                <input type="number" step="any" id="geofence_lat" name="geofence_lat" class="form-control settings-control"
                                    value="{{ old('geofence_lat', $settings['geofence_lat']) }}" required>
                            </div>

                            <div class="col-md-4">
                                <label class="settings-label">Longitude</label>
                                <input type="number" step="any" id="geofence_lng" name="geofence_lng" class="form-control settings-control"
                                    value="{{ old('geofence_lng', $settings['geofence_lng']) }}" required>
                            </div>

                            <div class="col-md-4">
                                <label class="settings-label">Radius (m)</label>
                                <input type="number" name="geofence_radius" class="form-control settings-control"
                                    value="{{ old('geofence_radius', $settings['geofence_radius']) }}" min="10" max="5000" required>
                            </div>

                            <div class="col-12">
                                <div class="settings-location">
                                    <button type="button" id="btnGetLocation" class="btn btn-outline-secondary btn-sm">
                                        <i class="bi bi-geo-alt"></i> Ambil Lokasi Saat Ini
                                    </button>
                                    <span id="locationStatus" class="small text-muted"></span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="settings-label">Min. Deteksi Wajah</label>
                                <input type="number" step="0.01" name="face_detection_min" class="form-control settings-control"
                                    value="{{ old('face_detection_min', $settings['face_detection_min']) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="settings-label">Min. Face Match</label>
                                <input type="number" step="0.01" name="face_match_min" class="form-control settings-control"
                                    value="{{ old('face_match_min', $settings['face_match_min']) }}" required>
                            </div>
                        </div>

                        <div class="settings-summary">
                            <h6 class="fw-bold mb-2"><i class="bi bi-info-circle me-1"></i> Pengaturan Aktif:</h6>
                            <ul class="small ps-3">
                                <li>Status terlambat: setelah jam <strong>{{ substr($settings['late_limit'], 0, 5) }}</strong></li>
                                <li>Batas kehadiran minimal: <strong>{{ $settings['min_attendance'] }}%</strong></li>
                                <li>Geofence: <strong>{{ $settings['geofence_lat'] }}, {{ $settings['geofence_lng'] }}</strong> (Radius: <strong>{{ $settings['geofence_radius'] }}m</strong>)</li>
                            </ul>
                        </div>

                        <div class="settings-action">
                            <button type="submit" class="btn btn-primary shadow-sm">
                                <i class="bi bi-save me-2"></i>Simpan Pengaturan Absensi
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card settings-card">
                <div class="card-header">
                    <div class="settings-card__head">
                        <div class="settings-card__title"><i class="bi bi-cpu text-primary"></i>Info Sistem</div>
                        <div class="settings-card__subtitle">Ringkasan lingkungan aplikasi</div>
                    </div>
                </div>
                <div class="card-body">
                    <dl class="row mb-0 settings-system-list">
                        <dt class="col-sm-5 small">Versi Laravel</dt>
                        <dd class="col-sm-7 fw-semibold mb-2">{{ app()->version() }}</dd>

                        <dt class="col-sm-5 small">Versi PHP</dt>
                        <dd class="col-sm-7 fw-semibold mb-2">{{ PHP_VERSION }}</dd>

                        <dt class="col-sm-5 small">Waktu Server</dt>
                        <dd class="col-sm-7 fw-semibold mb-2">{{ now()->translatedFormat('d F Y H:i') }}</dd>

                        <dt class="col-sm-5 small">Zona Waktu</dt>
                        <dd class="col-sm-7 fw-semibold mb-0">{{ config('app.timezone') }}</dd>
                    </dl>
                </div>
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
