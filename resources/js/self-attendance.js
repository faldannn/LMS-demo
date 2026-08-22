/**
 * Presensi Mandiri — Simplified Photo Capture + GPS Geolocation
 */
export class SelfAttendanceApp {
    constructor(root) {
        this.root = root;
        this.config = null;
        this.stream = null;
        this.trackingTask = null;
        this.state = {
            step: 'init',
            gps: null,
            photoBlob: null,
        };
        this.els = {
            stepIndicator: root.querySelector('[data-step-indicator]'),
            panels: root.querySelectorAll('[data-step-panel]'),
            video: root.querySelector('#attendanceVideo'),
            previewImg: root.querySelector('#attendancePreview'),
            faceOverlay: root.querySelector('#faceOverlay'),
            canvas: root.querySelector('#attendanceCanvas'),
            status: root.querySelector('[data-status]'),
            btnStart: root.querySelector('[data-btn-start]'),
            captureAction: root.querySelector('#captureActionContainer'),
            confirmAction: root.querySelector('#confirmActionContainer'),
            btnCapturePhoto: root.querySelector('#btnCapturePhoto'),
            btnRetake: root.querySelector('#btnRetakePhoto'),
            btnConfirm: root.querySelector('#btnConfirmPhoto'),
            successBox: root.querySelector('[data-success]'),
            cameraErrorContainer: root.querySelector('#cameraErrorContainer'),
            btnRetryCamera: root.querySelector('#btnRetryCamera'),
        };
        this.bindEvents();
    }

    bindEvents() {
        this.els.btnStart?.addEventListener('click', () => this.startFlow());
        this.els.btnCapturePhoto?.addEventListener('click', () => this.captureAndPreview());
        this.els.btnRetake?.addEventListener('click', () => this.retakePhoto());
        this.els.btnConfirm?.addEventListener('click', () => this.confirmPhoto());
        this.els.btnRetryCamera?.addEventListener('click', () => this.startFlow());
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
            this.showCameraError();
        } else {
            this.setStatus('Hadapkan wajah Anda ke kamera lalu klik tombol di bawah untuk mengambil foto.');
        }
    }

    async startCamera() {
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            console.warn('getUserMedia is not supported on this context (HTTP/non-secure)');
            return false;
        }

        try {
            this.stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } },
                audio: false,
            });
            this.els.video.srcObject = this.stream;
            await this.els.video.play();
            
            // Show video, hide camera error
            this.els.video.parentElement.classList.remove('hidden');
            if (this.els.captureAction) this.els.captureAction.classList.remove('hidden');
            if (this.els.confirmAction) this.els.confirmAction.classList.add('hidden');
            this.els.cameraErrorContainer?.classList.add('hidden');

            this.initFaceTracking();

            return true;
        } catch (e) {
            console.error('Camera access error:', e);
            return false;
        }
    }

    initFaceTracking() {
        if (!window.tracking || !window.tracking.ObjectTracker) {
            console.warn('tracking.js is not loaded. Face detection bypassed.');
            if (this.els.btnCapturePhoto) {
                this.els.btnCapturePhoto.removeAttribute('disabled');
            }
            this.setStatus('Kamera siap. Posisikan wajah Anda dan ambil foto.');
            return;
        }

        const tracker = new window.tracking.ObjectTracker('face');
        tracker.setInitialScale(4);
        tracker.setStepSize(2);
        tracker.setEdgesDensity(0.1);

        this.trackingTask = window.tracking.track(this.els.video, tracker);
        this.setStatus('Mencari wajah... Harap hadapkan wajah Anda ke kamera.', 'warning');

        tracker.on('track', (event) => {
            if (this.state.step !== 'camera' || this.state.photoBlob) {
                if (this.els.faceOverlay) this.els.faceOverlay.classList.add('hidden');
                return;
            }

            if (event.data.length > 0) {
                const rect = event.data[0];
                this.els.btnCapturePhoto.removeAttribute('disabled');
                this.setStatus('Wajah terdeteksi! Silakan ambil foto.', 'success');

                // Draw face overlay
                if (this.els.faceOverlay) {
                    const video = this.els.video;
                    const displayWidth = video.offsetWidth;
                    const displayHeight = video.offsetHeight;
                    const videoWidth = video.videoWidth || 640;
                    const videoHeight = video.videoHeight || 480;

                    const scaleX = displayWidth / videoWidth;
                    const scaleY = displayHeight / videoHeight;

                    // Since the video is mirrored (scaleX(-1)), the left coordinate must be mirrored!
                    const faceWidth = rect.width * scaleX;
                    const faceHeight = rect.height * scaleY;
                    const faceLeft = displayWidth - (rect.x + rect.width) * scaleX;
                    const faceTop = rect.y * scaleY;

                    this.els.faceOverlay.style.left = `${faceLeft}px`;
                    this.els.faceOverlay.style.top = `${faceTop}px`;
                    this.els.faceOverlay.style.width = `${faceWidth}px`;
                    this.els.faceOverlay.style.height = `${faceHeight}px`;
                    this.els.faceOverlay.classList.remove('hidden');
                }
            } else {
                this.els.btnCapturePhoto.setAttribute('disabled', 'true');
                this.setStatus('Wajah tidak terdeteksi. Silakan posisikan wajah Anda ke kamera.', 'danger');
                if (this.els.faceOverlay) {
                    this.els.faceOverlay.classList.add('hidden');
                }
            }
        });
    }

    showCameraError() {
        this.els.video.parentElement.classList.add('hidden');
        if (this.els.captureAction) this.els.captureAction.classList.add('hidden');
        if (this.els.confirmAction) this.els.confirmAction.classList.add('hidden');
        this.els.cameraErrorContainer?.classList.remove('hidden');
        this.setStatus('Akses kamera langsung wajib diizinkan untuk melakukan presensi mandiri.', 'danger');
    }

    compressImage(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload = (event) => {
                const img = new Image();
                img.onload = () => {
                    const canvas = document.createElement('canvas');
                    let width = img.width;
                    let height = img.height;
                    const maxDim = 800;
                    
                    if (width > maxDim || height > maxDim) {
                        if (width > height) {
                            height = Math.round((height * maxDim) / width);
                            width = maxDim;
                        } else {
                            width = Math.round((width * maxDim) / height);
                            height = maxDim;
                        }
                    }
                    
                    canvas.width = width;
                    canvas.height = height;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, width, height);
                    canvas.toBlob((blob) => {
                        if (blob) resolve(blob);
                        else reject(new Error('Canvas blob is null'));
                    }, 'image/jpeg', 0.8);
                };
                img.onerror = reject;
                img.src = event.target.result;
            };
            reader.onerror = reject;
            reader.readAsDataURL(file);
        });
    }

    stopCamera() {
        if (this.trackingTask) {
            this.trackingTask.stop();
            this.trackingTask = null;
        }
        if (this.els.faceOverlay) {
            this.els.faceOverlay.classList.add('hidden');
        }
        this.stream?.getTracks().forEach(t => t.stop());
        this.stream = null;
    }

    async captureAndPreview() {
        if (!this.state.photoBlob) {
            this.state.photoBlob = await this.capturePhoto();
        }

        if (!this.state.photoBlob) {
            alert('Gagal mengambil gambar. Silakan coba lagi atau unggah foto.');
            return;
        }

        // Hide video & overlay, show preview image
        const url = URL.createObjectURL(this.state.photoBlob);
        if (this.els.previewImg) {
            this.els.previewImg.src = url;
            this.els.previewImg.classList.remove('hidden');
        }
        this.els.video.classList.add('hidden');
        if (this.els.faceOverlay) {
            this.els.faceOverlay.classList.add('hidden');
        }

        this.stopCamera();

        // Switch buttons
        if (this.els.captureAction) this.els.captureAction.classList.add('hidden');
        if (this.els.confirmAction) this.els.confirmAction.classList.remove('hidden');

        this.setStatus('Foto berhasil diambil. Klik "Gunakan Foto & Lanjut" jika hasilnya bagus, atau "Foto Ulang" jika ingin mengambil gambar kembali.', 'success');
    }

    async retakePhoto() {
        this.state.photoBlob = null;

        // Reset elements visibility
        if (this.els.previewImg) {
            this.els.previewImg.src = '';
            this.els.previewImg.classList.add('hidden');
        }
        this.els.video.classList.remove('hidden');

        // Switch buttons back
        if (this.els.captureAction) this.els.captureAction.classList.remove('hidden');
        if (this.els.confirmAction) this.els.confirmAction.classList.add('hidden');

        // Restart camera
        this.setStatus('Hadapkan wajah Anda ke kamera lalu klik tombol di bawah untuk mengambil foto.');
        await this.startCamera();
    }

    async confirmPhoto() {
        this.goToStep('gps');
        await this.acquireGps();
    }

    capturePhoto() {
        const video = this.els.video;
        const canvas = this.els.canvas;
        if (!video?.videoWidth) return null;
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const ctx = canvas.getContext('2d');
        
        ctx.save();
        // Mirror the canvas drawing to match the scaleX(-1) CSS video preview!
        ctx.translate(canvas.width, 0);
        ctx.scale(-1, 1);
        
        ctx.drawImage(video, 0, 0);
        ctx.restore();
        
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
            this.state.photoBlob = null;
            this.goToStep('camera');
            if (this.els.previewImg) {
                this.els.previewImg.src = '';
                this.els.previewImg.classList.add('hidden');
            }
            this.els.video.classList.remove('hidden');
            if (this.els.captureAction) this.els.captureAction.classList.remove('hidden');
            if (this.els.confirmAction) this.els.confirmAction.classList.add('hidden');
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
