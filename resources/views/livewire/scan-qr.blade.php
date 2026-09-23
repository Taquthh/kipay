<div
    x-data="qrisScanner()"
    x-init="init()"
    x-on:livewire:navigated.window="init()"
    class="min-h-screen bg-slate-100 px-4 py-8 text-slate-900"
>
    <section class="mx-auto max-w-md overflow-hidden rounded-3xl bg-white shadow-xl">
        <header class="bg-gradient-to-br from-blue-700 via-blue-600 to-emerald-500 p-6 text-white">
            <p class="text-sm text-white/75">Kipay</p>
            <h1 class="mt-1 text-2xl font-bold">Scan QRIS</h1>
            <p class="mt-1 text-sm text-white/80">Pembaca QR aman untuk Android dan iOS</p>
        </header>

        <div class="p-5">
            <div class="relative aspect-[4/5] overflow-hidden rounded-2xl bg-slate-950">
                <video x-ref="video" autoplay muted playsinline class="h-full w-full object-cover" aria-label="Kamera pemindai QRIS"></video>
                <canvas x-ref="canvas" class="hidden"></canvas>
                <div class="pointer-events-none absolute inset-0 grid place-items-center">
                    <div class="h-56 w-56 rounded-2xl border-2 border-emerald-400 shadow-[0_0_0_999px_rgb(0_0_0/0.38)]"></div>
                </div>
                <div x-show="!active" class="absolute inset-0 grid place-items-center p-8 text-center text-white">
                    <p>Tekan tombol di bawah untuk mengaktifkan kamera</p>
                </div>
                <p x-show="active && !payload" x-text="status" class="absolute inset-x-4 bottom-4 rounded-xl bg-black/60 p-3 text-center text-xs text-white backdrop-blur"></p>
            </div>

            <p x-text="status" class="mt-3 text-center text-sm text-slate-500"></p>

            <div x-show="error" x-text="error" role="alert" class="mt-3 rounded-xl bg-red-50 p-3 text-sm text-red-700"></div>

            <div x-show="payload" class="mt-4 rounded-xl bg-emerald-50 p-4">
                <p class="text-xs font-semibold text-emerald-700">Payload QRIS</p>
                <p x-text="payload" class="mt-1 break-all text-sm text-emerald-950"></p>
            </div>

            <div class="mt-4 grid grid-cols-2 gap-2">
                <button type="button" x-on:click="startCamera()" class="rounded-xl bg-blue-600 px-4 py-3 text-sm font-bold text-white">
                    <span>Buka kamera</span>
                </button>
                <label class="cursor-pointer rounded-xl border border-slate-200 px-4 py-3 text-center text-sm font-bold text-slate-700">
                    Pilih foto
                    <input type="file" accept="image/*" capture="environment" class="sr-only" x-on:change="readImage($event.target.files[0])">
                </label>
            </div>

            <button x-show="hasTorch" type="button" x-on:click="toggleTorch()" class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold">
                <span x-text="torch ? 'Matikan flash' : 'Nyalakan flash'"></span>
            </button>

            <button x-show="payload" type="button" x-on:click="reset()" class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold">
                Scan lagi
            </button>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js" defer></script>

    @script
        <script>
            function qrisScanner() {
                return {
                    active: false,
                    status: @js($this->status ?? 'Siap memindai QRIS'),
                    error: @js($this->error ?? ''),
                    payload: @js($this->payload ?? ''),
                    torch: false,
                    hasTorch: false,
                    stream: null,
                    frame: null,
                    scanning: false,
                    resultSent: false,
                    initialized: false,

                    init() {
                        if (this.initialized) return;
                        this.initialized = true;
                        this.$watch('payload', (value) => {
                            if (value) this.stopCamera();
                        });
                    },

                    stopCamera() {
                        if (this.frame) cancelAnimationFrame(this.frame);
                        this.frame = null;
                        this.scanning = false;
                        this.stream?.getTracks().forEach((track) => track.stop());
                        this.stream = null;
                        this.active = false;
                        this.torch = false;
                        this.hasTorch = false;
                        if (this.$refs.video) this.$refs.video.srcObject = null;
                    },

                    async startCamera() {
                        this.stopCamera();
                        this.resultSent = false;
                        this.payload = '';
                        this.error = '';
                        this.status = 'Meminta izin kamera...';

                        if (!window.isSecureContext && window.location.hostname !== 'localhost') {
                            this.error = 'Kamera hanya dapat digunakan melalui HTTPS.';
                            this.status = 'Kamera belum tersedia';
                            return;
                        }
                        if (!navigator.mediaDevices?.getUserMedia) {
                            this.error = 'Browser ini tidak mendukung kamera. Gunakan Safari atau Chrome terbaru.';
                            this.status = 'Kamera tidak didukung';
                            return;
                        }

                        try {
                            let stream;
                            try {
                                stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: { ideal: 'environment' }, width: { ideal: 1280 }, height: { ideal: 720 } }, audio: false });
                            } catch {
                                stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
                            }
                            this.stream = stream;
                            const video = this.$refs.video;
                            video.srcObject = stream;
                            await new Promise((resolve) => {
                                if (video.readyState >= HTMLMediaElement.HAVE_METADATA) resolve();
                                else video.onloadedmetadata = resolve;
                            });
                            await video.play();
                            this.hasTorch = Boolean(stream.getVideoTracks()[0]?.getCapabilities?.().torch);
                            this.active = true;
                            this.scanning = true;
                            this.status = 'Arahkan kamera ke kode QRIS';
                            this.scanFrame();
                        } catch (reason) {
                            const name = reason?.name;
                            this.error = name === 'NotAllowedError' || name === 'PermissionDeniedError'
                                ? 'Izin kamera ditolak. Izinkan Camera di pengaturan browser, lalu tekan Coba lagi.'
                                : name === 'NotFoundError' ? 'Kamera tidak ditemukan pada perangkat ini.'
                                : 'Kamera tidak dapat dibuka. Pastikan HTTPS aktif dan kamera tidak dipakai aplikasi lain.';
                            this.status = 'Kamera belum aktif';
                            this.stopCamera();
                        }
                    },

                    scanFrame() {
                        if (!this.scanning || !this.stream) return;
                        const video = this.$refs.video;
                        const canvas = this.$refs.canvas;
                        if (video.readyState >= HTMLMediaElement.HAVE_CURRENT_DATA && video.videoWidth) {
                            const width = Math.min(video.videoWidth, 1280);
                            canvas.width = width;
                            canvas.height = Math.round((width / video.videoWidth) * video.videoHeight);
                            const context = canvas.getContext('2d', { willReadFrequently: true });
                            context.drawImage(video, 0, 0, canvas.width, canvas.height);
                            const image = context.getImageData(0, 0, canvas.width, canvas.height);
                            const code = window.jsQR?.(image.data, image.width, image.height, { inversionAttempts: 'attemptBoth' });
                            if (code?.data && !this.resultSent) {
                                this.resultSent = true;
                                this.payload = code.data;
                                this.status = 'QRIS berhasil terbaca';
                                Livewire.dispatch('scan', { raw: code.data });
                                Livewire.dispatch('qris-scanned', { payload: code.data });
                                this.stopCamera();
                                return;
                            }
                        }
                        this.frame = requestAnimationFrame(() => this.scanFrame());
                    },

                    async toggleTorch() {
                        const track = this.stream?.getVideoTracks()[0];
                        if (!track || !this.hasTorch) return;
                        try {
                            this.torch = !this.torch;
                            await track.applyConstraints({ advanced: [{ torch: this.torch }] });
                        } catch {
                            this.error = 'Flash tidak tersedia di perangkat ini.';
                        }
                    },

                    readImage(file) {
                        if (!file) return;
                        const image = new Image();
                        const url = URL.createObjectURL(file);
                        image.onload = () => {
                            const canvas = this.$refs.canvas;
                            canvas.width = image.naturalWidth;
                            canvas.height = image.naturalHeight;
                            const context = canvas.getContext('2d', { willReadFrequently: true });
                            context.drawImage(image, 0, 0);
                            const data = context.getImageData(0, 0, canvas.width, canvas.height);
                            const code = window.jsQR?.(data.data, data.width, data.height, { inversionAttempts: 'attemptBoth' });
                            URL.revokeObjectURL(url);
                            if (code?.data) {
                                this.payload = code.data;
                                this.status = 'QRIS berhasil terbaca';
                                Livewire.dispatch('scan', { raw: code.data });
                                Livewire.dispatch('qris-scanned', { payload: code.data });
                            } else this.error = 'QR tidak terbaca. Gunakan foto yang lebih jelas dan coba lagi.';
                        };
                        image.src = url;
                    },

                    reset() {
                        this.stopCamera();
                        this.payload = '';
                        this.error = '';
                        this.status = 'Siap memindai QRIS';
                        this.resultSent = false;
                    }
                };
            }
        </script>
    @endscript
</div>
