/**
 * Presensi Mandiri — Simplified Photo Capture + GPS Geolocation
 */
export class SelfAttendanceApp {
    constructor(root) {
        this.root = root;
        this.config = null;
        this.stream = null;
        this.state = {
            step: 'init',
            gps: null,
            photoBlob: null,
        };
        this.els = {
            stepIndicator: root.querySelector('[data-step-indicator]'),
            panels: root.querySelectorAll('[data-step-panel]'),
            video: root.querySelector('#attendanceVideo'),
            canvas: root.querySelector('#attendanceCanvas'),
            status: root.querySelector('[data-status]'),
            btnStart: root.querySelector('[data-btn-start]'),
            btnCapturePhoto: root.querySelector('#btnCapturePhoto'),
            successBox: root.querySelector('[data-success]'),
        };
        this.bindEvents();
    }

    bindEvents() {
        this.els.btnStart?.addEventListener('click', () => this.startFlow());
        this.els.btnCapturePhoto?.addEventListener('click', () => this.captureAndNext());
    }

    async init() {
        try {
            const res = await fetch('/api/attendance/self/config', {
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
            });
            if (!res.ok) {
                const text = await res.text();
                let errMsg = 'Gagal memuat konfigurasi';
                try {
                    const errJson = JSON.parse(text);
                    if (errJson.message) errMsg += `: ${errJson.message}`;
                } catch {
                    errMsg += ` (${res.status} ${res.statusText})`;
                }
                throw new Error(errMsg);
            }
            this.config = await res.json();
            this.setStatus('Siap memulai presensi. Pastikan wajah terlihat jelas dan GPS aktif.');
            this.goToStep('ready');
        } catch (e) {
            this.setStatus(e.message, 'danger');
        }
    }

    async startFlow() {
        this.goToStep('camera');
        const cameraOk = await this.startCamera();
        if (!cameraOk) {
            this.setStatus('Kamera tidak dapat diakses atau tidak ada izin.', 'danger');
            return;
        }
        this.setStatus('Hadapkan wajah Anda ke kamera lalu klik tombol di bawah untuk mengambil foto.');
    }

    async startCamera() {
        try {
            this.stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } },
                audio: false,
            });
            this.els.video.srcObject = this.stream;
            await this.els.video.play();
            return true;
        } catch (e) {
            console.error('Camera access error:', e);
            return false;
        }
    }

    stopCamera() {
        this.stream?.getTracks().forEach(t => t.stop());
        this.stream = null;
    }

    async captureAndNext() {
        this.state.photoBlob = await this.capturePhoto();
        if (!this.state.photoBlob) {
            alert('Gagal mengambil gambar. Pastikan kamera menyala.');
            return;
        }
        this.stopCamera();
        this.goToStep('gps');
        await this.acquireGps();
    }

    capturePhoto() {
        const video = this.els.video;
        const canvas = this.els.canvas;
        if (!video?.videoWidth) return null;
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0);
        return new Promise(resolve => canvas.toBlob(resolve, 'image/jpeg', 0.85));
    }

    async acquireGps() {
        this.setStatus('Mengambil lokasi GPS...');
        if (!navigator.geolocation) {
            this.setStatus('Perangkat Anda tidak mendukung GPS.', 'danger');
            return;
        }

        try {
            const pos = await new Promise((resolve, reject) => {
                navigator.geolocation.getCurrentPosition(resolve, reject, {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0,
                });
            });
            this.state.gps = {
                latitude: pos.coords.latitude,
                longitude: pos.coords.longitude,
                accuracy: pos.coords.accuracy,
            };
            await this.submitCheckIn();
        } catch (e) {
            console.error('GPS acquisition error:', e);
            this.setStatus('Gagal mengambil lokasi GPS. Pastikan izin lokasi diaktifkan.', 'danger');
        }
    }

    async submitCheckIn() {
        this.goToStep('submitting');
        this.setStatus('Mengirim data presensi...');

        const form = new FormData();
        form.append('check_in_mode', 'full');
        form.append('face_detection_confidence', '1');
        form.append('face_match_confidence', '1');
        form.append('face_matched', '1');
        form.append('device_type', this.detectDevice());
        form.append('browser', this.detectBrowser());
        if (this.state.gps) {
            form.append('latitude', this.state.gps.latitude);
            form.append('longitude', this.state.gps.longitude);
        }
        if (this.state.photoBlob) {
            form.append('photo', this.state.photoBlob, 'presensi.jpg');
        }

        try {
            const res = await fetch('/api/attendance/self/check-in', {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
                body: form,
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.message || 'Presensi gagal');

            this.goToStep('success');
            this.els.successBox.innerHTML = `
                <i class="bi bi-check-circle-fill text-6xl text-emerald-500 mb-4 d-block"></i>
                <h5 class="font-bold text-emerald-600 text-xl mb-2">Presensi Berhasil!</h5>
                <p class="mb-3 text-slate-700">${data.message}</p>
                <p class="m-0 text-sm text-slate-500">
                    ${data.within_radius ? '✓ Terverifikasi di radius sekolah' : '⚠ Lokasi di luar radius sekolah'}
                    ${data.distance_meters != null ? ` (${Math.round(data.distance_meters)} m)` : ''}
                </p>
                <a href="/siswa/my-attendance" class="mt-6 inline-block rounded-xl border border-brand-600 px-6 py-2.5 text-sm font-semibold text-brand-600 transition hover:bg-brand-50">Lihat Riwayat Absensi</a>`;
        } catch (e) {
            this.setStatus(e.message, 'danger');
            this.goToStep('camera');
            this.startCamera();
        }
    }

    detectDevice() {
        const ua = navigator.userAgent;
        if (/mobile/i.test(ua)) return 'mobile';
        if (/tablet/i.test(ua)) return 'tablet';
        return 'desktop';
    }

    detectBrowser() {
        const ua = navigator.userAgent;
        if (ua.includes('Chrome')) return 'Chrome';
        if (ua.includes('Firefox')) return 'Firefox';
        if (ua.includes('Safari')) return 'Safari';
        if (ua.includes('Edge')) return 'Edge';
        return 'Unknown';
    }

    goToStep(step) {
        this.state.step = step;
        this.els.panels.forEach(p => p.classList.toggle('hidden', p.dataset.stepPanel !== step));
        const labels = { ready: 1, camera: 2, gps: 3, submitting: 4, success: 4 };
        if (this.els.stepIndicator) {
            this.els.stepIndicator.querySelectorAll('[data-step]').forEach(el => {
                const n = parseInt(el.dataset.step, 10);
                el.classList.toggle('active', n <= (labels[step] || 1));
            });
        }
    }

    setStatus(msg, type = 'info') {
        if (!this.els.status) return;
        
        let classes = '';
        if (type === 'danger') {
            classes = 'mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800';
        } else if (type === 'warning') {
            classes = 'mb-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800';
        } else if (type === 'success') {
            classes = 'mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800';
        } else {
            classes = 'mb-4 rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-800';
        }
        
        this.els.status.className = classes;
        this.els.status.textContent = msg;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('selfAttendanceApp');
    if (root && !root.dataset.alreadyCheckedIn) {
        const app = new SelfAttendanceApp(root);
        app.init();
    }
});
