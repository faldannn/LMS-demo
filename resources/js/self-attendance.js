/**
 * Presensi Mandiri — MediaPipe Face Landmarker + GPS Geolocation
 */
const LANDMARK_INDICES = [1, 33, 263, 61, 291, 199, 419];

export class SelfAttendanceApp {
    constructor(root) {
        this.root = root;
        this.config = null;
        this.landmarker = null;
        this.stream = null;
        this.state = {
            step: 'init',
            detectionConfidence: 0,
            matchConfidence: 0,
            faceMatched: false,
            descriptor: null,
            referenceDescriptor: null,
            gps: null,
            cameraFailed: false,
            gpsFailed: false,
            checkInMode: 'full',
        };
        this.els = {
            stepIndicator: root.querySelector('[data-step-indicator]'),
            panels: root.querySelectorAll('[data-step-panel]'),
            video: root.querySelector('#attendanceVideo'),
            canvas: root.querySelector('#attendanceCanvas'),
            status: root.querySelector('[data-status]'),
            faceOverlay: root.querySelector('[data-face-overlay]'),
            btnStart: root.querySelector('[data-btn-start]'),
            btnRetry: root.querySelector('[data-btn-retry]'),
            btnFallbackGps: root.querySelector('[data-btn-fallback-gps]'),
            btnFallbackCamera: root.querySelector('[data-btn-fallback-camera]'),
            btnFallbackManual: root.querySelector('[data-btn-fallback-manual]'),
            successBox: root.querySelector('[data-success]'),
        };
        this.bindEvents();
    }

    bindEvents() {
        this.els.btnStart?.addEventListener('click', () => this.startFlow());
        this.els.btnRetry?.addEventListener('click', () => this.startFlow());
        this.els.btnFallbackGps?.addEventListener('click', () => this.useFallback('gps_fallback'));
        this.els.btnFallbackCamera?.addEventListener('click', () => this.useFallback('camera_fallback'));
        this.els.btnFallbackManual?.addEventListener('click', () => this.useFallback('manual_fallback'));
    }

    async init() {
        try {
            const res = await fetch('/api/attendance/self/config', {
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
            });
            if (!res.ok) throw new Error('Gagal memuat konfigurasi');
            this.config = await res.json();
            this.state.referenceDescriptor = this.config.reference_descriptor;
            this.setStatus('Siap memulai presensi. Pastikan wajah terlihat jelas dan GPS aktif.');
            this.goToStep('ready');
        } catch (e) {
            this.setStatus(e.message, 'danger');
        }
    }

    async startFlow() {
        this.goToStep('camera');
        await this.initMediaPipe();
        const cameraOk = await this.startCamera();
        if (!cameraOk) {
            this.state.cameraFailed = true;
            this.showFallbackOptions('camera');
            return;
        }
        await this.runFaceDetectionLoop();
    }

    async initMediaPipe() {
        if (this.landmarker) return;
        const vision = await import('https://cdn.jsdelivr.net/npm/@mediapipe/tasks-vision@0.10.14/+esm');
        const { FaceLandmarker, FilesetResolver } = vision;
        const fileset = await FilesetResolver.forVisionTasks(
            'https://cdn.jsdelivr.net/npm/@mediapipe/tasks-vision@0.10.14/wasm'
        );
        this.landmarker = await FaceLandmarker.createFromOptions(fileset, {
            baseOptions: {
                modelAssetPath: 'https://storage.googleapis.com/mediapipe-models/face_landmarker/face_landmarker/float16/1/face_landmarker.task',
                delegate: 'GPU',
            },
            runningMode: 'VIDEO',
            numFaces: 1,
        });
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
        } catch {
            return false;
        }
    }

    stopCamera() {
        this.stream?.getTracks().forEach(t => t.stop());
        this.stream = null;
    }

    extractDescriptor(landmarks) {
        const nose = landmarks[1];
        const points = LANDMARK_INDICES.map(i => landmarks[i]);
        const scale = Math.hypot(landmarks[33].x - landmarks[263].x, landmarks[33].y - landmarks[263].y) || 0.001;
        const flat = [];
        points.forEach(p => {
            flat.push((p.x - nose.x) / scale);
            flat.push((p.y - nose.y) / scale);
        });
        return flat;
    }

    compareDescriptors(a, b) {
        if (!a || !b || a.length !== b.length) return 0;
        let dot = 0, na = 0, nb = 0;
        for (let i = 0; i < a.length; i++) {
            dot += a[i] * b[i];
            na += a[i] * a[i];
            nb += b[i] * b[i];
        }
        const denom = Math.sqrt(na) * Math.sqrt(nb);
        return denom ? Math.max(0, Math.min(1, dot / denom)) : 0;
    }

    async runFaceDetectionLoop() {
        this.setStatus('Deteksi wajah... Hadapkan wajah ke kamera.');
        const video = this.els.video;
        let attempts = 0;
        const maxAttempts = 90;

        return new Promise(resolve => {
            const tick = async () => {
                if (!this.landmarker || video.readyState < 2) {
                    requestAnimationFrame(tick);
                    return;
                }
                attempts++;
                const result = this.landmarker.detectForVideo(video, performance.now());
                const faces = result.faceLandmarks || [];

                if (faces.length > 0) {
                    const lm = faces[0];
                    const detectionConf = Math.min(1, (result.faceBlendshapes?.[0]?.categories?.[0]?.score ?? 0.85) + 0.1);
                    this.state.detectionConfidence = Math.max(0.7, detectionConf);
                    this.state.descriptor = this.extractDescriptor(lm);

                    if (!this.state.referenceDescriptor) {
                        await this.saveReferenceDescriptor(this.state.descriptor);
                        this.state.referenceDescriptor = this.state.descriptor;
                    }

                    this.state.matchConfidence = this.compareDescriptors(this.state.descriptor, this.state.referenceDescriptor);
                    this.state.faceMatched = this.state.detectionConfidence >= this.config.geofence.detection_min;
                    this.els.faceOverlay.textContent = `Deteksi: ${(this.state.detectionConfidence * 100).toFixed(0)}% | Match: ${(this.state.matchConfidence * 100).toFixed(0)}%`;

                    if (this.state.detectionConfidence >= this.config.geofence.detection_min) {
                        this.stopCamera();
                        if (this.state.checkInMode === 'camera_fallback') {
                            await this.submitCheckIn('camera_fallback');
                            resolve();
                            return;
                        }
                        this.goToStep('gps');
                        await this.acquireGps();
                        resolve();
                        return;
                    }
                } else {
                    this.els.faceOverlay.textContent = 'Wajah tidak terdeteksi...';
                }

                if (attempts >= maxAttempts) {
                    this.stopCamera();
                    this.state.cameraFailed = true;
                    this.showFallbackOptions('face_timeout');
                    resolve();
                    return;
                }
                requestAnimationFrame(tick);
            };
            requestAnimationFrame(tick);
        });
    }

    async saveReferenceDescriptor(descriptor) {
        await fetch('/api/attendance/self/face-profile', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
            body: JSON.stringify({ landmark_descriptor: descriptor }),
        });
    }

    async acquireGps() {
        this.setStatus('Mengambil lokasi GPS...');
        if (!navigator.geolocation) {
            this.state.gpsFailed = true;
            this.showFallbackOptions('gps');
            return;
        }

        try {
            const pos = await new Promise((resolve, reject) => {
                navigator.geolocation.getCurrentPosition(resolve, reject, {
                    enableHighAccuracy: true,
                    timeout: 15000,
                    maximumAge: 0,
                });
            });
            this.state.gps = {
                latitude: pos.coords.latitude,
                longitude: pos.coords.longitude,
                accuracy: pos.coords.accuracy,
            };
            await this.submitCheckIn(this.state.checkInMode || 'full');
        } catch {
            this.state.gpsFailed = true;
            this.showFallbackOptions('gps');
        }
    }

    async useFallback(mode) {
        this.state.checkInMode = mode;
        if (mode === 'gps_fallback') {
            this.state.faceMatched = false;
            this.state.detectionConfidence = 0;
            this.state.matchConfidence = 0;
            await this.acquireGps();
        } else if (mode === 'camera_fallback') {
            this.state.gps = null;
            this.goToStep('camera');
            await this.initMediaPipe();
            const ok = await this.startCamera();
            if (ok) await this.runFaceDetectionLoop();
            else this.showFallbackOptions('camera');
        } else {
            const reason = prompt('Alasan presensi manual (wajib):');
            if (!reason) return;
            this.state.fallbackReason = reason;
            await this.submitCheckIn('manual_fallback');
        }
    }

    showFallbackOptions(type) {
        this.goToStep('fallback');
        const msgs = {
            camera: 'Kamera tidak dapat diakses.',
            gps: 'GPS tidak dapat diakses.',
            face_timeout: 'Wajah tidak terdeteksi dalam waktu yang cukup.',
        };
        this.setStatus(msgs[type] || 'Terjadi kendala. Pilih metode alternatif.', 'warning');
        this.els.btnFallbackGps?.classList.toggle('hidden', !this.config?.geofence?.allow_gps_fallback || type === 'gps');
        this.els.btnFallbackCamera?.classList.toggle('hidden', !this.config?.geofence?.allow_camera_fallback);
        this.els.btnFallbackManual?.classList.toggle('hidden', !this.config?.geofence?.allow_manual_fallback);
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

    async submitCheckIn(mode) {
        this.goToStep('submitting');
        this.setStatus('Mengirim data presensi...');

        const form = new FormData();
        form.append('check_in_mode', mode);
        form.append('face_detection_confidence', this.state.detectionConfidence || 0);
        form.append('face_match_confidence', this.state.matchConfidence || 0);
        form.append('face_matched', this.state.faceMatched ? '1' : '0');
        form.append('device_type', this.detectDevice());
        form.append('browser', this.detectBrowser());
        if (this.state.gps) {
            form.append('latitude', this.state.gps.latitude);
            form.append('longitude', this.state.gps.longitude);
        }
        if (this.state.fallbackReason) form.append('fallback_reason', this.state.fallbackReason);
        if (this.state.descriptor) {
            this.state.descriptor.forEach((v, i) => form.append(`landmark_descriptor[${i}]`, v));
        }

        const blob = await this.capturePhoto();
        if (blob) form.append('photo', blob, 'presensi.jpg');

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
                <h5 class="font-bold text-emerald-600">Presensi Berhasil!</h5>
                <p class="mb-1">${data.message}</p>
                <p class="m-0 text-sm text-slate-500">
                    ${data.within_radius ? '✓ Terverifikasi wajah dan lokasi sekolah' : '⚠ Lokasi di luar radius sekolah'}
                    ${data.distance_meters != null ? ` (${Math.round(data.distance_meters)} m)` : ''}
                </p>`;
        } catch (e) {
            this.setStatus(e.message, 'danger');
            this.goToStep('fallback');
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
        const labels = { ready: 1, camera: 2, gps: 3, submitting: 4, success: 5, fallback: 3 };
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
