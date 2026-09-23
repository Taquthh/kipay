<div
    x-data="window.qrisScanner()"
    x-init="init()"
    x-on:livewire:navigated.window="init()"
    class="fixed inset-0 z-50 overflow-hidden bg-slate-950 text-white"
    style="padding-top:env(safe-area-inset-top,0px); padding-bottom:env(safe-area-inset-bottom,0px);"
>
    {{-- ================= STEP 0 — KAMERA FULLSCREEN ================= --}}
    @if ($payStep === 0)
        <div class="relative h-full w-full">
            <video x-ref="video" autoplay muted playsinline class="absolute inset-0 h-full w-full object-cover" aria-label="Kamera pemindai QRIS"></video>
            <canvas x-ref="canvas" class="hidden"></canvas>

            {{-- placeholder saat kamera belum aktif --}}
            <div x-show="!active" class="absolute inset-0 grid place-items-center bg-gradient-to-b from-slate-900 to-slate-950 px-10 text-center">
                <div>
                    <div class="mx-auto mb-5 grid h-16 w-16 place-items-center rounded-full bg-white/10">
                        <svg viewBox="0 0 24 24" class="h-8 w-8 text-emerald-400" fill="none" stroke="currentColor" stroke-width="1.6"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7V5a1 1 0 0 1 1-1h2M4 17v2a1 1 0 0 0 1 1h2M20 7V5a1 1 0 0 0-1-1h-2M20 17v2a1 1 0 0 1-1 1h-2M4 12h16" /></svg>
                    </div>
                    <p class="text-sm text-slate-300" x-text="status"></p>
                </div>
            </div>

            {{-- gelap di luar bingkai + bingkai pemindai dengan efek scan digital --}}
            <div x-show="active" class="pointer-events-none absolute inset-0 grid place-items-center">
                <div class="relative h-64 w-64" style="animation: kipay-frame-glow 2.6s ease-in-out infinite;">
                    <div class="absolute inset-0 rounded-[28px] shadow-[0_0_0_999px_rgba(2,6,23,0.6)]"></div>
                    <div class="absolute inset-0 rounded-[28px] ring-1 ring-white/10"></div>

                    {{-- sudut bingkai --}}
                    <div class="absolute -left-0.5 -top-0.5 h-10 w-10 rounded-tl-[20px] border-l-[3px] border-t-[3px] border-emerald-400 drop-shadow-[0_0_6px_rgba(52,211,153,0.7)]"></div>
                    <div class="absolute -right-0.5 -top-0.5 h-10 w-10 rounded-tr-[20px] border-r-[3px] border-t-[3px] border-emerald-400 drop-shadow-[0_0_6px_rgba(52,211,153,0.7)]"></div>
                    <div class="absolute -bottom-0.5 -left-0.5 h-10 w-10 rounded-bl-[20px] border-b-[3px] border-l-[3px] border-emerald-400 drop-shadow-[0_0_6px_rgba(52,211,153,0.7)]"></div>
                    <div class="absolute -bottom-0.5 -right-0.5 h-10 w-10 rounded-br-[20px] border-b-[3px] border-r-[3px] border-emerald-400 drop-shadow-[0_0_6px_rgba(52,211,153,0.7)]"></div>

                    {{-- garis pindai digital --}}
                    <div x-show="scanning && !payload" class="absolute inset-x-3 top-2 h-[3px] rounded-full bg-gradient-to-r from-transparent via-emerald-300 to-transparent shadow-[0_0_16px_3px_rgba(52,211,153,0.9)]" style="animation: kipay-scanline 2.1s ease-in-out infinite;"></div>
                </div>
                <p x-show="active && scanning" class="absolute mt-80 text-xs font-medium text-slate-300/90">Posisikan kode QRIS di dalam kotak</p>
            </div>

            {{-- transisi instan begitu QR terbaca, langsung menuju halaman bayar --}}
            <div x-show="loadingPay" x-cloak class="absolute inset-0 z-20 grid place-items-center bg-slate-950/90 backdrop-blur-sm">
                <div class="flex flex-col items-center gap-4">
                    <div class="relative h-14 w-14">
                        <div class="absolute inset-0 rounded-full border-4 border-white/10"></div>
                        <div class="absolute inset-0 rounded-full border-4 border-emerald-400 border-t-transparent animate-spin"></div>
                    </div>
                    <p class="text-sm font-medium text-slate-200">Menyiapkan pembayaran...</p>
                </div>
            </div>

            {{-- app bar --}}
            <div class="absolute inset-x-0 top-0 flex items-center justify-between px-4 pt-4">
                <button type="button" onclick="history.back()" class="grid h-10 w-10 place-items-center rounded-full bg-black/35 backdrop-blur transition active:scale-90">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                </button>
                <p class="text-sm font-semibold tracking-wide">Scan QRIS</p>
                <button type="button" x-show="hasTorch" x-on:click="toggleTorch()" class="grid h-10 w-10 place-items-center rounded-full backdrop-blur transition active:scale-90" :class="torch ? 'bg-emerald-400 text-slate-900 shadow-[0_0_14px_2px_rgba(52,211,153,0.6)]' : 'bg-black/35 text-white'">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="currentColor"><path d="M13 2 3 14h6l-1 8 11-13h-6l1-7z" /></svg>
                </button>
                <div x-show="!hasTorch" class="h-10 w-10"></div>
            </div>

            {{-- status & error --}}
            <div class="absolute inset-x-0 bottom-0 space-y-3 bg-gradient-to-t from-slate-950/95 via-slate-950/60 to-transparent px-5 pb-6 pt-14">
                <p x-text="status" class="text-center text-sm text-slate-200"></p>
                <div x-show="error" x-text="error" x-cloak role="alert" class="rounded-xl bg-red-500/15 p-3 text-center text-xs text-red-300"></div>
                @if ($errorMessage)
                    <div class="rounded-xl bg-red-500/15 p-3 text-center text-xs text-red-300">{{ $errorMessage }}</div>
                @endif

                <div class="flex gap-3">
                    <button type="button" x-show="!active" x-cloak x-on:click="startCamera()" class="flex-1 rounded-2xl bg-gradient-to-br from-blue-600 to-emerald-500 py-3.5 text-center text-sm font-bold shadow-lg shadow-emerald-900/30 transition active:scale-[0.98]">
                        <span x-text="error ? 'Coba lagi' : 'Aktifkan kamera'"></span>
                    </button>
                    <label class="grid cursor-pointer place-items-center rounded-2xl border border-white/15 bg-white/5 transition active:scale-[0.98]" :class="active ? 'flex-1 flex-row gap-2 py-3.5' : 'w-14'">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.6-4.6a2 2 0 0 1 2.8 0L16 16m-2-2 1.6-1.6a2 2 0 0 1 2.8 0L20 14M4 8h.01M6 4h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z" /></svg>
                        <span x-show="active" x-cloak class="text-sm font-semibold">Pilih dari galeri</span>
                        <input type="file" accept="image/*" capture="environment" class="sr-only" x-on:change="readImage($event.target.files[0])">
                    </label>
                </div>

                <button type="button" x-on:click="$dispatch('toggle-manual')" x-data="{}" class="w-full text-center text-xs font-medium text-slate-400 underline decoration-slate-600 underline-offset-4">
                    Masukkan kode QRIS manual
                </button>
                <div x-data="{ open: false }" x-on:toggle-manual.window="open = !open" x-show="open" x-cloak class="flex gap-2 pt-1">
                    <input type="text" wire:model="manualPayload" placeholder="KIPAY-MCH-..." class="flex-1 rounded-xl border border-white/15 bg-white/5 px-3 py-2 text-xs text-white placeholder:text-slate-500">
                    <button type="button" wire:click="quickManualScan" class="rounded-xl bg-white/10 px-4 text-xs font-semibold">Cek</button>
                </div>
            </div>
        </div>
    @endif

    {{-- ================= STEP 1 — KONFIRMASI PEMBAYARAN ================= --}}
    @if ($payStep === 1 && $payTarget)
        <div class="relative flex h-full flex-col bg-gradient-to-b from-blue-800 via-blue-700 to-slate-950">
            <div class="absolute -top-16 -right-16 h-56 w-56 rounded-full bg-emerald-400/20 blur-3xl"></div>
            <div class="absolute -bottom-24 -left-10 h-64 w-64 rounded-full bg-blue-400/10 blur-3xl"></div>

            <div class="relative flex items-center justify-between px-4 pt-4">
                <button type="button" wire:click="resetScan" class="grid h-10 w-10 place-items-center rounded-full bg-white/10 backdrop-blur transition active:scale-90">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                </button>
                <p class="text-sm font-semibold tracking-wide">{{ $payMode === 'merchant' ? 'Bayar ke Toko' : 'Transfer' }}</p>
                <div class="h-10 w-10"></div>
            </div>

            <div class="relative mt-3 flex items-center gap-3 px-6">
                <div class="grid h-14 w-14 shrink-0 place-items-center rounded-full bg-white text-lg font-bold text-blue-700 shadow-lg ring-4 ring-white/25">{{ $payTarget['initial'] }}</div>
                <div class="min-w-0">
                    <p class="truncate text-lg font-bold">{{ $payTarget['title'] }}</p>
                    <p class="truncate text-xs text-blue-100/80">{{ $payTarget['subtitle'] }}</p>
                </div>
            </div>

            <form wire:submit.prevent="pay" class="relative mt-4 flex flex-1 flex-col overflow-hidden rounded-t-[32px] bg-white px-6 pb-6 pt-7 text-slate-900 shadow-[0_-20px_50px_rgba(2,6,23,0.35)]">
                <div class="mx-auto mb-5 h-1.5 w-10 rounded-full bg-slate-200"></div>

                <div wire:loading.flex wire:target="pay" class="absolute inset-0 z-20 hidden flex-col items-center justify-center gap-4 rounded-t-[32px] bg-white/95 backdrop-blur-sm">
                    <div class="relative h-16 w-16">
                        <div class="absolute inset-0 rounded-full border-4 border-slate-100"></div>
                        <div class="absolute inset-0 rounded-full border-4 border-emerald-500 border-t-transparent animate-spin"></div>
                        <div class="absolute inset-0 grid place-items-center">
                            <svg viewBox="0 0 24 24" class="h-6 w-6 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3" /></svg>
                        </div>
                    </div>
                    <div class="text-center">
                        <p class="text-sm font-semibold text-slate-800">Memproses pembayaran...</p>
                        <p class="mt-0.5 text-xs text-slate-400">Mohon tunggu, jangan tutup halaman ini</p>
                    </div>
                </div>

                @if ($errorMessage)
                    <div class="mb-4 rounded-xl bg-red-50 p-3 text-center text-xs font-medium text-red-600">{{ $errorMessage }}</div>
                @endif

                <div class="flex-1 space-y-5 overflow-y-auto">
                    <div>
                        <label class="text-xs font-semibold text-slate-400">Nominal</label>
                        <div class="mt-1.5 flex items-end gap-1.5 rounded-2xl border-2 border-slate-100 bg-slate-50/60 px-3 py-2.5 transition focus-within:border-emerald-400 focus-within:bg-emerald-50/40">
                            <span class="pb-1 text-xl font-bold text-slate-400">Rp</span>
                            <input type="number" min="1000" inputmode="numeric" wire:model="payAmount" placeholder="0" class="w-full border-0 bg-transparent p-0 text-3xl font-extrabold tracking-tight text-slate-900 outline-none placeholder:text-slate-300 tabular-nums" autofocus>
                        </div>
                        @error('payAmount') <p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-slate-400">Catatan (opsional)</label>
                        <input type="text" wire:model="note" maxlength="60" placeholder="Untuk apa transaksi ini?" class="mt-1.5 w-full rounded-2xl border-2 border-slate-100 bg-slate-50/60 px-4 py-3 text-sm outline-none transition focus:border-emerald-400 focus:bg-emerald-50/40">
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-slate-400">PIN Transaksi</label>
                        <div
                            x-data="window.kipayPinPad()"
                            x-init="$watch('pin', v => $wire.set('pin', v))"
                            class="mt-2 flex justify-center gap-2.5"
                        >
                            <template x-for="(d, i) in digits" :key="i">
                                <input
                                    type="tel" inputmode="numeric" maxlength="1" autocomplete="off"
                                    style="-webkit-text-security: disc; text-security: disc;"
                                    :value="digits[i]"
                                    x-on:input="onInput(i, $event)"
                                    x-on:keydown.backspace="onBackspace(i, $event)"
                                    class="h-14 w-11 rounded-2xl border-2 bg-slate-50/60 text-center text-2xl font-bold text-slate-900 outline-none transition focus:border-emerald-400 focus:bg-emerald-50/40 focus:shadow-[0_0_0_4px_rgba(16,185,129,0.12)]"
                                    :class="digits[i] ? 'border-emerald-300 bg-emerald-50/50' : 'border-slate-100'"
                                >
                            </template>
                        </div>
                        @error('pin') <p class="mt-2 text-center text-xs font-medium text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                <button type="submit" class="mt-5 w-full rounded-2xl bg-gradient-to-br from-blue-600 to-emerald-500 py-4 text-center text-sm font-bold text-white shadow-lg shadow-emerald-900/20 transition active:scale-[0.98] disabled:opacity-50" wire:loading.attr="disabled" wire:target="pay">
                    Bayar Sekarang
                </button>
            </form>
        </div>
    @endif

    {{-- ================= STEP 2 — BERHASIL ================= --}}
    @if ($payStep === 2)
        <div class="relative flex h-full flex-col items-center justify-center overflow-hidden bg-slate-950 px-8 text-center">
            <div class="absolute -top-24 left-1/2 h-72 w-72 -translate-x-1/2 rounded-full bg-emerald-500/10 blur-3xl"></div>

            <div class="relative grid h-24 w-24 place-items-center">
                <span class="absolute inset-0 rounded-full bg-emerald-400/20" style="animation: kipay-ping 1.8s cubic-bezier(0,0,0.2,1) infinite;"></span>
                <span class="absolute inset-2 rounded-full bg-emerald-400/20" style="animation: kipay-ping 1.8s cubic-bezier(0,0,0.2,1) infinite; animation-delay: .3s;"></span>
                <div class="relative grid h-16 w-16 place-items-center rounded-full bg-gradient-to-br from-emerald-400 to-emerald-500 shadow-lg shadow-emerald-900/40" style="animation: kipay-pop .45s ease-out;">
                    <svg viewBox="0 0 24 24" class="h-8 w-8 text-white" fill="none" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                </div>
            </div>

            <p class="relative mt-6 text-sm text-slate-400">{{ $payMode === 'merchant' ? 'Pembayaran berhasil' : 'Transfer berhasil' }}</p>
            <p class="relative mt-1 text-4xl font-extrabold tabular-nums">Rp {{ number_format((int) $payAmount, 0, ',', '.') }}</p>
            <p class="relative mt-1 text-sm text-slate-400">{{ $payMode === 'merchant' ? 'ke' : 'kepada' }} <span class="font-semibold text-slate-200">{{ $payTarget['title'] ?? '' }}</span></p>

            <div class="relative mt-7 w-full rounded-2xl border border-white/10 bg-white/[0.04] p-4 text-left text-xs backdrop-blur">
                <div class="flex justify-between py-1.5">
                    <span class="text-slate-400">No. Referensi</span>
                    <span class="font-semibold tracking-wide">{{ $lastRef }}</span>
                </div>
                <div class="my-1 border-t border-dashed border-white/10"></div>
                <div class="flex justify-between py-1.5">
                    <span class="text-slate-400">Status</span>
                    <span class="flex items-center gap-1.5 font-semibold text-emerald-400">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                        Sukses
                    </span>
                </div>
            </div>

            <a href="/" wire:navigate class="relative mt-8 block w-full rounded-2xl bg-gradient-to-br from-blue-600 to-emerald-500 py-4 text-sm font-bold shadow-lg shadow-emerald-900/20 transition active:scale-[0.98]">
                Selesai
            </a>
        </div>
    @endif

    <style>
        @keyframes kipay-scanline {
            0%   { top: 6px; opacity: .2; }
            50%  { top: calc(100% - 10px); opacity: 1; }
            100% { top: 6px; opacity: .2; }
        }
        @keyframes kipay-frame-glow {
            0%, 100% { filter: drop-shadow(0 0 0 rgba(52,211,153,0)); }
            50%      { filter: drop-shadow(0 0 14px rgba(52,211,153,0.35)); }
        }
        @keyframes kipay-pop {
            0%   { transform: scale(0.6); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }
        @keyframes kipay-ping {
            0%   { transform: scale(1); opacity: .55; }
            100% { transform: scale(1.9); opacity: 0; }
        }
        [x-cloak] { display: none !important; }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js" defer></script>

    @script
        <script>
            window.kipayPinPad = function kipayPinPad() {
                return {
                    digits: ['', '', '', '', '', ''],
                    get pin() { return this.digits.join(''); },
                    onInput(index, event) {
                        const value = event.target.value.replace(/\D/g, '').slice(-1);
                        this.digits[index] = value;
                        event.target.value = value;
                        if (value) {
                            const next = event.target.nextElementSibling;
                            if (next) next.focus();
                        }
                    },
                    onBackspace(index, event) {
                        if (this.digits[index]) return;
                        const prev = event.target.previousElementSibling;
                        if (prev) {
                            this.digits[index - 1] = '';
                            prev.focus();
                        }
                    }
                };
            };

            window.qrisScanner = function qrisScanner() {
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
                    loadingPay: false,

                    init() {
                        if (this.initialized) return;
                        this.initialized = true;
                        this.$watch('payload', (value) => {
                            if (value) this.stopCamera();
                        });
                        this.startCamera();
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
                                this.scanning = false;
                                this.payload = code.data;
                                this.loadingPay = true;
                                this.stopCamera();
                                this.$wire.scan(code.data);
                                Livewire.dispatch('scan', { raw: code.data });
                                Livewire.dispatch('qris-scanned', { payload: code.data });
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
                                this.loadingPay = true;
                                this.stopCamera();
                                this.$wire.scan(code.data);
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
