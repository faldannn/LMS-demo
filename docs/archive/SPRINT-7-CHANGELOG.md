# Sprint 7 — Changelog: Presensi Modern (Face + GPS)

**Tanggal:** 26 Juni 2026  
**Tujuan:** Presensi mandiri siswa dengan MediaPipe Face Detection, face matching, validasi GPS Haversine, dan audit log lengkap.

---

## Flow Presensi

```
Login (session)
    ↓
Kamera (getUserMedia)
    ↓
Face Detection (MediaPipe Face Landmarker)
    ↓
Face Matching (landmark descriptor vs profil siswa)
    ↓
GPS (HTML5 Geolocation)
    ↓
Validasi Radius (Haversine)
    ↓
Presensi Berhasil → attendance_details + self_attendance_logs
```

---

## Teknologi

| Layer | Teknologi |
|-------|-----------|
| Frontend | MediaPipe Tasks Vision (`FaceLandmarker`) via CDN |
| Geolocation | HTML5 `navigator.geolocation` |
| Backend API | Laravel `routes/api.php` + session auth |
| Penyimpanan foto | Laravel Storage disk `public` |
| Geofence | `GeofenceService` — rumus **Haversine** |

### Rumus Haversine

```
a = sin²(Δφ/2) + cos(φ1)·cos(φ2)·sin²(Δλ/2)
c = 2·atan2(√a, √(1−a))
d = R · c   (R = 6.371.000 meter)
```

---

## Data yang Disimpan

Tabel `self_attendance_logs`:

| Field | Keterangan |
|-------|------------|
| `latitude`, `longitude` | Koordinat GPS |
| `distance_meters` | Jarak ke titik sekolah |
| `within_radius` | Apakah dalam radius |
| `face_detection_confidence` | Skor deteksi wajah |
| `face_match_confidence` | Skor kecocokan wajah |
| `face_matched` | Boolean verifikasi |
| `photo_path` | Foto saat presensi (Storage) |
| `device_type` | mobile / tablet / desktop |
| `browser` | Chrome, Safari, dll. |
| `ip_address` | IP request |
| `user_agent` | User agent lengkap |
| `check_in_mode` | full / gps_fallback / camera_fallback / manual_fallback |
| `checked_in_at` | Timestamp presensi |

Tabel `student_face_profiles` — descriptor landmark untuk face matching.

---

## Mode Fallback

| Mode | Kondisi | Validasi |
|------|---------|----------|
| `full` | Kamera + GPS OK | Wajah + radius |
| `gps_fallback` | Kamera gagal | GPS + radius saja |
| `camera_fallback` | GPS gagal | Wajah saja (tanpa radius) |
| `manual_fallback` | Keduanya gagal | Alasan wajib, flag audit |

Diatur via `config/attendance.php` → `self_checkin.allow_*_fallback`.

---

## API Endpoints

| Method | URL | Fungsi |
|--------|-----|--------|
| GET | `/api/attendance/self/config` | Config geofence + profil siswa |
| POST | `/api/attendance/self/face-profile` | Simpan descriptor wajah |
| POST | `/api/attendance/self/check-in` | Submit presensi |
| GET | `/api/attendance/self/history` | Riwayat presensi mandiri |

Auth: session cookie (`web` middleware) — same-origin dari aplikasi Laravel.

---

## Halaman Web

| Route | Role | View |
|-------|------|------|
| `attendance.self-checkin` | Siswa | Wizard kamera + GPS |
| `attendance.self-logs` | Guru/Kepsek | Audit log presensi |

---

## Pengaturan Geofence

**Pengaturan Sistem** → Absensi:
- Latitude / Longitude sekolah
- Radius (meter)
- Min. deteksi wajah
- Min. face match

Atau via `.env`:
```env
SCHOOL_LATITUDE=-6.2349
SCHOOL_LONGITUDE=106.9896
SCHOOL_RADIUS_METERS=150
FACE_DETECTION_MIN=0.65
FACE_MATCH_MIN=0.72
```

---

## File Utama

| Path | Peran |
|------|-------|
| `app/Services/GeofenceService.php` | Haversine distance |
| `app/Services/SelfAttendanceService.php` | Business logic presensi |
| `app/Http/Controllers/Api/SelfAttendanceApiController.php` | REST API |
| `resources/js/self-attendance.js` | MediaPipe + GPS wizard |
| `resources/views/attendance/self-checkin.blade.php` | UI siswa |
| `database/migrations/2026_06_30_100000_create_modern_attendance_tables.php` | Schema |

---

## Testing

```bash
php artisan test --filter=GeofenceServiceTest
php artisan test --filter=ModernAttendanceTest
# 49 tests total
```

---

## Build Frontend

```bash
npm run build   # wajib setelah pull / deploy
php artisan storage:link
```

---

## Catatan Keamanan

- Face matching dijalankan di browser; server memvalidasi threshold minimum.
- Foto presensi disimpan sebagai bukti audit.
- Presensi manual fallback dicatat dengan `fallback_reason` untuk review guru.
- HTTPS disarankan agar geolocation berfungsi optimal di browser modern.
