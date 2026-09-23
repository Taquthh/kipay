<div class="min-h-screen bg-slate-100 pb-28 md:pb-10">

    {{-- ================= HEADER ================= --}}
    <header class="relative overflow-hidden bg-gradient-to-br from-blue-700 via-blue-600 to-emerald-500 pb-6 pt-6 text-white">
        <div class="pointer-events-none absolute -right-16 -top-16 h-56 w-56 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute -bottom-24 -left-10 h-56 w-56 rounded-full bg-emerald-300/20"></div>

        <div class="relative mx-auto flex max-w-5xl items-center justify-between px-5">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-full bg-white/20 text-lg font-bold ring-2 ring-white/40">
                    {{ strtoupper(mb_substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <p class="text-xs text-white/70">Selamat datang,</p>
                    <h1 class="text-base font-semibold leading-tight">{{ $user->name }}</h1>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <nav class="mr-2 hidden items-center gap-1 md:flex">
                    <button wire:click="setTab('beranda')"
                        class="rounded-full px-4 py-2 text-sm font-medium transition {{ $tab === 'beranda' ? 'bg-white text-blue-700' : 'text-white/80 hover:bg-white/10' }}">Beranda</button>
                    <button wire:click="openScan" onclick="window.kipayPrewarmCamera()"
                        class="rounded-full px-4 py-2 text-sm font-medium text-white/80 transition hover:bg-white/10">Scan QRIS</button>
                    <button wire:click="setTab('riwayat')"
                        class="rounded-full px-4 py-2 text-sm font-medium transition {{ $tab === 'riwayat' ? 'bg-white text-blue-700' : 'text-white/80 hover:bg-white/10' }}">Riwayat</button>
                </nav>

                <button wire:click="logout" class="rounded-full bg-white/15 p-2.5 transition hover:bg-white/25" title="Keluar">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6A2.25 2.25 0 005.25 5.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M18 15l3-3m0 0l-3-3m3 3H9"/></svg>
                </button>
            </div>
        </div>
    </header>

    <main class="mx-auto -mt-20 max-w-5xl px-4 pt-24">

        {{-- ================= TAB BERANDA ================= --}}
        @if ($tab === 'beranda')
            <div class="grid gap-4 md:grid-cols-3">

                {{-- Kartu saldo + aksi cepat --}}
                <section class="rounded-2xl bg-white p-5 shadow-lg shadow-slate-200/70 md:col-span-2">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Saldo KiPay</p>
                            <div class="mt-1 flex items-center gap-2">
                                <p class="text-3xl font-bold text-slate-800">
                                    @if ($hideBalance) Rp •••••• @else Rp {{ number_format($wallet->balance, 0, ',', '.') }} @endif
                                </p>
                                <button wire:click="$toggle('hideBalance')" class="text-slate-400 hover:text-slate-600">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.04 12.32a1.01 1.01 0 010-.64C3.42 7.51 7.36 4.5 12 4.5c4.64 0 8.57 3.01 9.96 7.18.07.21.07.43 0 .64C20.58 16.49 16.64 19.5 12 19.5c-4.64 0-8.57-3.01-9.96-7.18z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </button>
                            </div>
                            <p class="mt-1 text-xs text-slate-400">{{ $user->whatsapp }}</p>
                        </div>

                        <button wire:click="openTopUp"
                            class="flex items-center gap-1 rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-200 transition hover:bg-emerald-100">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
                            Isi Saldo
                        </button>
                    </div>

                    <div class="mt-5 grid grid-cols-3 gap-2 border-t border-slate-100 pt-5">
                        <button wire:click="openScan" onclick="window.kipayPrewarmCamera()" class="group flex flex-col items-center gap-2">
                            <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 transition group-hover:bg-emerald-100">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5zM13.5 13.5h3v3h-3v-3zM18 18h2.25v2.25H18V18z"/></svg>
                            </span>
                            <span class="text-[11px] font-medium text-slate-600">Scan QRIS</span>
                        </button>

                        <a href="{{ route('transfer') }}" class="group flex flex-col items-center gap-2">
                            <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 transition group-hover:bg-blue-100">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5M16.5 3L21 7.5m0 0L16.5 12M21 7.5H7.5"/></svg>
                            </span>
                            <span class="text-[11px] font-medium text-slate-600">Transfer</span>
                        </a>

                        <a href="{{ route('merchant') }}" class="group flex flex-col items-center gap-2">
                            <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600 transition group-hover:bg-indigo-100">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5h-3V21M3 13.5V21h18v-7.5M3.75 3h16.5l1.5 5.25a3 3 0 11-6 0 3 3 0 11-6 0 3 3 0 11-6 0L3.75 3z"/></svg>
                            </span>
                            <span class="text-[11px] font-medium text-slate-600">Merchant</span>
                        </a>
                    </div>
                </section>

                {{-- Kartu merchant --}}
                <section class="rounded-2xl bg-gradient-to-br from-emerald-500 to-blue-600 p-5 text-white shadow-lg shadow-emerald-200/60">
                    @if ($merchant)
                        <p class="text-xs font-medium uppercase tracking-wide text-white/70">Toko Kamu</p>
                        <h3 class="mt-1 text-lg font-bold leading-snug">{{ $merchant->merchant_name }}</h3>
                        <p class="mt-2 text-sm text-white/80">QR merchant aktif. Tunjukkan ke pembeli untuk menerima pembayaran.</p>
                        <a href="{{ route('merchant') }}"
                            class="mt-4 block w-full rounded-xl bg-white/20 py-2.5 text-center text-sm font-semibold backdrop-blur transition hover:bg-white/30">
                            Lihat QR Merchant
                        </a>
                    @else
                        <p class="text-xs font-medium uppercase tracking-wide text-white/70">KiPay Merchant</p>
                        <h3 class="mt-1 text-lg font-bold leading-snug">Terima pembayaran pakai QR</h3>
                        <p class="mt-2 text-sm text-white/80">Daftarkan tokomu sekali, lalu pembeli tinggal scan untuk membayar.</p>
                        <a href="{{ route('merchant') }}"
                            class="mt-4 block w-full rounded-xl bg-white/20 py-2.5 text-center text-sm font-semibold backdrop-blur transition hover:bg-white/30">
                            Buat Merchant
                        </a>
                    @endif
                </section>
            </div>

            {{-- Input QR Manual (di luar menu Scan QRIS) --}}
            <section class="mt-4 rounded-2xl bg-white p-5 shadow-lg shadow-slate-200/70">
                <div class="mb-1 flex items-center gap-2">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 text-slate-500">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 9V4.5M9 9H4.5M9 9L3.75 3.75M9 15v4.5M9 15H4.5M9 15l-5.25 5.25M15 9h4.5M15 9V4.5M15 9l5.25-5.25M15 15h4.5M15 15v4.5m0-4.5l5.25 5.25"/></svg>
                    </span>
                    <h2 class="text-sm font-bold text-slate-800">Punya Kode QR / Payload? Masukkan Manual</h2>
                </div>
                <p class="mb-3 text-xs text-slate-400">Tidak perlu buka kamera. Tempel atau ketik payload QR merchant/teman di sini, mis. <code>KIPAY-MCH-1-1732012345</code>.</p>

                @if ($errorMessage && ! $showScan)
                    <div class="mb-3 rounded-xl bg-red-50 p-3 text-sm font-semibold text-red-700">{{ $errorMessage }}</div>
                @endif

                <form wire:submit="quickManualScan" class="flex gap-2">
                    <input type="text" wire:model="manualPayload" placeholder="KIPAY-MCH-1-169..."
                        class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
                    <button type="submit" wire:loading.attr="disabled" wire:target="quickManualScan"
                        class="shrink-0 rounded-xl bg-slate-800 px-4 py-2.5 text-sm font-semibold text-white disabled:opacity-60">
                        <span wire:loading.remove wire:target="quickManualScan">Proses</span>
                        <span wire:loading wire:target="quickManualScan">...</span>
                    </button>
                </form>
            </section>

            <section class="mt-4 rounded-2xl bg-white p-5 shadow-lg shadow-slate-200/70">
                <div class="mb-3 flex items-center justify-between">
                    <h2 class="text-sm font-bold text-slate-800">Transaksi Terakhir</h2>
                    <button wire:click="setTab('riwayat')" class="text-xs font-semibold text-blue-600 hover:underline">Lihat semua</button>
                </div>
                @include('livewire.partials.transaction-list', ['items' => $recent_transactions])
            </section>
        @endif

        {{-- ================= TAB RIWAYAT ================= --}}
        @if ($tab === 'riwayat')
            <section class="rounded-2xl bg-white p-5 shadow-lg shadow-slate-200/70">
                <h2 class="mb-3 text-sm font-bold text-slate-800">Riwayat Transaksi</h2>
                @include('livewire.partials.transaction-list', ['items' => $all_transactions])
            </section>
        @endif
    </main>

    {{-- ================= BOTTOM NAV (mobile) ================= --}}
    <nav class="fixed inset-x-0 bottom-0 z-30 border-t border-slate-200 bg-white/95 backdrop-blur md:hidden">
        <div class="relative mx-auto flex h-16 max-w-md items-center justify-around px-6">
            <button wire:click="setTab('beranda')" class="flex flex-col items-center gap-1 {{ $tab === 'beranda' ? 'text-blue-600' : 'text-slate-400' }}">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955a1.125 1.125 0 011.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75"/></svg>
                <span class="text-[10px] font-medium">Beranda</span>
            </button>

            <div class="w-16"></div>

            <button wire:click="setTab('riwayat')" class="flex flex-col items-center gap-1 {{ $tab === 'riwayat' ? 'text-blue-600' : 'text-slate-400' }}">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.008v.008H3.75V6.75zm.375 5.25h.008v.008h-.008V12zm-.375 5.25h.008v.008H3.75v-.008z"/></svg>
                <span class="text-[10px] font-medium">Riwayat</span>
            </button>

            <button wire:click="openScan" onclick="window.kipayPrewarmCamera()"
                class="absolute -top-6 left-1/2 flex h-16 w-16 -translate-x-1/2 items-center justify-center rounded-full bg-gradient-to-br from-blue-600 to-emerald-500 text-white shadow-xl shadow-blue-500/40 ring-4 ring-slate-100">
                <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5zM13.5 13.5h3v3h-3v-3zM18 18h2.25v2.25H18V18z"/></svg>
            </button>
        </div>
    </nav>

    {{-- ================= SHEET: TOP UP ================= --}}
    @if ($showTopUp)
        <div class="fixed inset-0 z-40 flex items-end justify-center bg-slate-900/50 md:items-center" wire:click.self="closeAll">
            <div class="w-full max-w-md rounded-t-3xl bg-white p-6 md:rounded-2xl">
                <div class="mx-auto mb-4 h-1.5 w-12 rounded-full bg-slate-200 md:hidden"></div>
                <h3 class="text-lg font-bold text-slate-800">Isi Saldo (Dummy)</h3>
                <p class="mb-4 text-sm text-slate-500">Minimal Rp 10.000.</p>

                <form wire:submit="topUp">
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-semibold text-slate-400">Rp</span>
                        <input type="number" wire:model="amount" placeholder="10000"
                            class="w-full rounded-xl border border-slate-200 py-3 pl-11 pr-4 text-lg font-semibold focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
                    </div>
                    @error('amount') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror

                    <div class="mt-3 grid grid-cols-3 gap-2">
                        @foreach ([20000, 50000, 100000] as $nominal)
                            <button type="button" wire:click="$set('amount', {{ $nominal }})"
                                class="rounded-xl border border-slate-200 py-2 text-sm font-semibold text-slate-600 transition hover:border-blue-400 hover:text-blue-600">
                                {{ number_format($nominal, 0, ',', '.') }}
                            </button>
                        @endforeach
                    </div>

                    <div class="mt-5 flex gap-2">
                        <button type="button" wire:click="closeAll" class="flex-1 rounded-xl border border-slate-200 py-3 text-sm font-semibold text-slate-600">Batal</button>
                        <button type="submit" wire:loading.attr="disabled"
                            class="flex-1 rounded-xl bg-gradient-to-r from-blue-600 to-emerald-500 py-3 text-sm font-bold text-white disabled:opacity-60">
                            <span wire:loading.remove wire:target="topUp">Isi Saldo</span>
                            <span wire:loading wire:target="topUp">Memproses...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ================= FULLSCREEN: SCAN QRIS (kamera JS murni via jsQR) ================= --}}
    @if ($showScan)
        <div class="fixed inset-0 z-50 bg-black" wire:key="scan-sheet">

            {{-- STEP 0 — kamera / upload / manual --}}
            @if ($payStep === 0)
                <div id="kipay-scan-root" class="relative h-full w-full overflow-hidden">

                    <video id="kipay-video" autoplay playsinline muted class="absolute inset-0 h-full w-full bg-black object-cover"></video>
                    <canvas id="kipay-canvas" class="hidden"></canvas>

                    <div class="pointer-events-none absolute inset-0 bg-gradient-to-b from-black/60 via-transparent to-black/70"></div>

                    {{-- top bar --}}
                    <div class="absolute inset-x-0 top-0 z-20 flex items-center justify-between px-5 pt-[calc(env(safe-area-inset-top,0px)+1rem)]">
                        <button wire:click="closeAll" class="flex h-10 w-10 items-center justify-center rounded-full bg-white/15 text-white backdrop-blur">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <p class="text-sm font-semibold text-white">Scan QRIS</p>
                        <button type="button" id="kipay-torch-btn" onclick="KipayScanner.toggleTorch()" style="display:none"
                            class="flex h-10 w-10 items-center justify-center rounded-full bg-white/15 text-white backdrop-blur">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-6.364-2.386 1.591-1.591M3 12h2.25m.386-6.364 1.591 1.591M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </button>
                    </div>

                    {{-- kotak pemindai dengan efek scan digital --}}
                    <div class="pointer-events-none absolute left-1/2 top-1/2 z-10 h-64 w-64 -translate-x-1/2 -translate-y-1/2">
                        <div class="absolute inset-0 rounded-2xl border border-white/40"></div>
                        <div class="absolute -left-0.5 -top-0.5 h-9 w-9 rounded-tl-2xl border-l-4 border-t-4 border-emerald-400"></div>
                        <div class="absolute -right-0.5 -top-0.5 h-9 w-9 rounded-tr-2xl border-r-4 border-t-4 border-emerald-400"></div>
                        <div class="absolute -left-0.5 -bottom-0.5 h-9 w-9 rounded-bl-2xl border-l-4 border-b-4 border-emerald-400"></div>
                        <div class="absolute -right-0.5 -bottom-0.5 h-9 w-9 rounded-br-2xl border-r-4 border-b-4 border-emerald-400"></div>
                        <div class="kipay-scanline absolute inset-x-2 h-1 rounded-full bg-gradient-to-r from-transparent via-emerald-300 to-transparent" style="box-shadow:0 0 12px 2px rgba(52,211,153,0.85)"></div>
                    </div>

                    <p class="pointer-events-none absolute inset-x-0 top-[calc(50%+9rem)] z-10 text-center text-xs text-white/80">Arahkan kamera ke kode QR pembayaran</p>

                    {{-- FIX: pointer-events-none + z-10 supaya layer ini TIDAK PERNAH memblokir klik --}}
                    <div id="kipay-loading" class="pointer-events-none absolute inset-0 z-10 flex flex-col items-center justify-center gap-3 bg-black/40 text-white">
                        <svg class="h-7 w-7 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                        <p class="text-xs">Membuka kamera...</p>
                    </div>

                    {{-- FIX: sebelumnya $errorMessage dari hasil scan (payload salah / merchant sendiri /
                         merchant tidak ditemukan) TIDAK PERNAH ditampilkan di step 0. Sekarang ditampilkan
                         sebagai status bar tipis di bawah, bukan popup. --}}
                    @if ($errorMessage)
                        <div class="absolute inset-x-4 bottom-[calc(env(safe-area-inset-bottom,0px)+1rem)] z-20 rounded-xl bg-red-500/90 px-4 py-3 text-center text-xs font-semibold text-white backdrop-blur">
                            {{ $errorMessage }}
                        </div>
                    @endif

                    {{-- Kontrol bawah: kamera, pilih foto, dan pesan status --}}
                    <div class="absolute inset-x-4 bottom-[calc(env(safe-area-inset-bottom,0px)+1rem)] z-20 space-y-2">
                        <div id="kipay-status-bar" style="display:none"
                            class="rounded-xl bg-black/70 px-4 py-3 text-center text-xs text-white backdrop-blur">
                            <p id="kipay-status-message" class="mb-1"></p>
                            <button type="button" id="kipay-retry-btn" onclick="KipayScanner.retry()"
                                style="display:none" class="text-xs font-bold text-emerald-300 underline">Coba lagi</button>
                        </div>
                        <div class="flex gap-2">
                            <button type="button" onclick="KipayScanner.retry()"
                                class="flex-1 rounded-xl bg-white px-4 py-3 text-sm font-bold text-slate-800 shadow-lg">
                                Buka kamera
                            </button>
                            <label class="flex flex-1 cursor-pointer items-center justify-center rounded-xl bg-white/15 px-4 py-3 text-sm font-bold text-white backdrop-blur">
                                Pilih foto
                                <input id="kipay-image-input" type="file" accept="image/*" capture="environment" class="sr-only"
                                    onchange="KipayScanner.readImage(this.files && this.files[0])">
                            </label>
                        </div>
                    </div>
                </div>
            @endif

            {{-- STEP 1 & 2 --}}
            @if ($payStep === 1 || $payStep === 2)
                <div class="flex h-full items-end justify-center bg-slate-900/60 md:items-center" wire:click.self="closeAll">
                    <div class="max-h-[92vh] w-full max-w-md overflow-y-auto rounded-t-3xl bg-white p-6 md:rounded-2xl">
                        <div class="mx-auto mb-4 h-1.5 w-12 rounded-full bg-slate-200 md:hidden"></div>

                        <div class="mb-4 flex items-center justify-between">
                            <h3 class="text-lg font-bold text-slate-800">
                                @if ($payStep === 1) Konfirmasi Pembayaran
                                @else Transaksi Selesai @endif
                            </h3>
                            <button wire:click="closeAll" class="text-sm font-semibold text-slate-400 hover:text-slate-600">Tutup</button>
                        </div>

                        @if ($errorMessage)
                            <div class="mb-4 rounded-xl bg-red-50 p-3 text-sm font-semibold text-red-700">{{ $errorMessage }}</div>
                        @endif

                        {{-- STEP 1 — nominal + PIN --}}
                        @if ($payStep === 1)
                            <div class="mb-5 flex items-center gap-3 rounded-2xl bg-gradient-to-r from-blue-600 to-emerald-500 p-4 text-white">
                                <span class="flex h-12 w-12 items-center justify-center rounded-full bg-white/25 text-lg font-bold">{{ $payTarget['initial'] }}</span>
                                <div>
                                    <p class="text-xs text-white/70">{{ $payMode === 'merchant' ? 'Membayar ke merchant' : 'Transfer ke teman' }}</p>
                                    <p class="text-base font-bold leading-tight">{{ $payTarget['title'] }}</p>
                                    <p class="text-xs text-white/70">{{ $payTarget['subtitle'] }}</p>
                                </div>
                            </div>

                            <form wire:submit="pay" class="space-y-3">
                                <div>
                                    <label class="mb-1 block text-xs font-semibold text-slate-500">Nominal</label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-semibold text-slate-400">Rp</span>
                                        <input type="number" wire:model="payAmount" placeholder="1000"
                                            class="w-full rounded-xl border border-slate-200 py-3 pl-11 pr-4 text-lg font-bold focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
                                    </div>
                                    @error('payAmount') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                    <div class="mt-2 grid grid-cols-3 gap-2">
                                        @foreach ([10000, 25000, 50000] as $nominal)
                                            <button type="button" wire:click="$set('payAmount', {{ $nominal }})"
                                                class="rounded-lg border border-slate-200 py-1.5 text-xs font-semibold text-slate-600 hover:border-blue-400 hover:text-blue-600">
                                                {{ number_format($nominal, 0, ',', '.') }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>

                                <div>
                                    <label class="mb-1 block text-xs font-semibold text-slate-500">Catatan (opsional)</label>
                                    <input type="text" wire:model="note" placeholder="Bayar kopi"
                                        class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
                                    @error('note') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="mb-1 block text-xs font-semibold text-slate-500">PIN Transaksi (6 digit)</label>
                                    <input type="password" inputmode="numeric" maxlength="6" wire:model="pin"
                                        class="w-full rounded-xl border border-slate-200 px-4 py-3 text-center text-2xl tracking-[0.5em] focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
                                    @error('pin') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div class="flex gap-2 pt-2">
                                    <button type="button" wire:click="resetScan" class="flex-1 rounded-xl border border-slate-200 py-3 text-sm font-semibold text-slate-600">Ulangi Scan</button>
                                    <button type="submit" wire:loading.attr="disabled"
                                        class="flex-1 rounded-xl bg-gradient-to-r from-blue-600 to-emerald-500 py-3 text-sm font-bold text-white disabled:opacity-60">
                                        <span wire:loading.remove wire:target="pay">Konfirmasi</span>
                                        <span wire:loading wire:target="pay">Memproses...</span>
                                    </button>
                                </div>
                            </form>
                        @endif

                        {{-- STEP 2 — sukses --}}
                        @if ($payStep === 2)
                            <div class="py-4 text-center">
                                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100">
                                    <svg class="h-8 w-8 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <h4 class="text-xl font-bold text-slate-800">Transaksi Berhasil</h4>
                                <p class="mt-1 text-sm text-slate-500">
                                    Rp {{ number_format((int) $payAmount, 0, ',', '.') }} terkirim ke <strong>{{ $payTarget['title'] }}</strong>.
                                </p>
                                <p class="mt-2 text-xs text-slate-400">Ref: {{ $lastRef }}</p>

                                <div class="mt-6 flex gap-2">
                                    <button wire:click="resetScan" class="flex-1 rounded-xl border border-slate-200 py-3 text-sm font-semibold text-slate-600">Scan Lagi</button>
                                    <button wire:click="closeAll" class="flex-1 rounded-xl bg-gradient-to-r from-blue-600 to-emerald-500 py-3 text-sm font-bold text-white">Selesai</button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    @endif

    {{-- ================= MODAL NOTIFIKASI SUKSES ================= --}}
    @if ($showSuccessModal)
        <div class="fixed inset-0 z-[60] flex items-start justify-center px-4 pt-[calc(env(safe-area-inset-top,0px)+1.5rem)]"
            x-data x-init="setTimeout(() => $wire.closeSuccessModal(), 3000)">
            <div class="pointer-events-none fixed inset-0 bg-slate-900/10"></div>

            <div class="pointer-events-auto relative flex w-full max-w-sm items-center gap-3 rounded-2xl bg-white p-4 shadow-2xl ring-1 ring-black/5"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 -translate-y-4"
                x-transition:enter-end="opacity-100 translate-y-0">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-blue-600 to-emerald-500 text-white">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-bold text-slate-800">Berhasil</p>
                    <p class="text-xs text-slate-500">{{ $successMessage }}</p>
                </div>
                <button wire:click="closeSuccessModal" class="text-slate-300 hover:text-slate-500">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
    @endif

    <style>
        @keyframes kipay-scanline-move {
            0%   { top: 6%; opacity: .2; }
            50%  { top: 92%; opacity: 1; }
            100% { top: 6%; opacity: .2; }
        }
        .kipay-scanline {
            animation: kipay-scanline-move 2.2s ease-in-out infinite;
        }
    </style>

@push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
        <script>
            /* =====================================================================
             | KipayScanner — FOKUS KAMERA SAJA
             | Sheet Scan QRIS ini sudah tidak punya upload galeri, input manual,
             | atau panel popup lagi. Input manual/tester sekarang ada di halaman
             | Beranda (di luar sheet ini). Di sini hanya:
             | - video kamera + kotak pemindai
             | - status bar tipis di bawah (bukan popup) untuk pesan gagal kamera
             |   + tombol "Coba lagi" inline
             | - $errorMessage dari hasil scan QR (misal merchant tidak ditemukan)
             |   ditampilkan sebagai status bar tipis yang sama
             |
             | Hasil scan tetap dikirim lewat sendScanPayload(), yang mencoba
             | @this.call('scan', value) lalu fallback ke
             | Livewire.dispatch('kipay-scan-result', {payload}) kalau @this gagal.
             |=====================================================================*/

            window.kipayPendingStream = null;
            window.kipayPrewarmCamera = function () {
                if (!window.isSecureContext) return;
                if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) return;

                window.kipayPendingStream = navigator.mediaDevices
                    .getUserMedia({ video: { facingMode: { exact: 'environment' } }, audio: false })
                    .catch(() => navigator.mediaDevices.getUserMedia({ video: true, audio: false }))
                    .catch((err) => {
                        window.kipayPendingStream = null;
                        throw err;
                    });

                window.kipayPendingStream.catch(() => {});
            };

            // Satu pintu untuk mengirim hasil scan ke Livewire, dengan fallback.
            function sendScanPayload(value) {
                try {
                    if (typeof window.$wire !== 'undefined' && window.$wire && window.$wire.call) {
                        window.$wire.call('scan', value);
                        return;
                    }
                    // @this dikompilasi Blade menjadi referensi ke instance komponen ini.
                    @this.call('scan', value);
                } catch (e) {
                    console.warn('[KipayScanner] @this.call gagal, fallback ke Livewire.dispatch', e);
                    if (window.Livewire && typeof window.Livewire.dispatch === 'function') {
                        window.Livewire.dispatch('kipay-scan-result', { payload: value });
                    } else {
                        console.error('[KipayScanner] Livewire tidak ditemukan, tidak bisa mengirim hasil scan.');
                    }
                }
            }

            window.KipayScanner = (function () {
                let stream = null;
                let rafId = null;
                let watchdogId = null;
                let frameArrived = false;
                let track = null;
                let isStarting = false; // guard: cegah start() dipanggil dobel/bertumpuk
                const CAMERA_OPEN_TIMEOUT_MS = 8000;

                function el(id) { return document.getElementById(id); }

                function setLoading(isLoading) {
                    const node = el('kipay-loading');
                    if (node) node.style.display = isLoading ? 'flex' : 'none';
                }

                function failMessage(reason) {
                    switch (reason) {
                        case 'insecure':
                            return 'Kamera hanya bisa diakses lewat HTTPS (atau localhost).';
                        case 'unsupported':
                            return 'Browser ini tidak mendukung akses kamera.';
                        case 'no-device':
                            return 'Tidak ada kamera terdeteksi di perangkat ini.';
                        case 'denied':
                            return 'Akses kamera ditolak. Cek ikon gembok di address bar untuk mengizinkan.';
                        case 'black-feed':
                            return 'Kamera terbuka tapi tidak mengirim gambar. Tutup aplikasi lain yang memakai kamera lalu coba lagi.';
                        case 'timeout':
                            return 'Kamera tidak merespons. Coba lagi, atau pakai kode manual di halaman Beranda.';
                        default:
                            return 'Kamera tidak bisa diakses. Coba lagi, atau pakai kode manual di halaman Beranda.';
                    }
                }

                // Status bar tipis di bawah layar kamera (bukan popup) — hanya untuk status kamera.
                function setStatus(show, reason) {
                    const bar = el('kipay-status-bar');
                    if (!bar) return;
                    bar.style.display = show ? 'block' : 'none';
                    if (!show) return;

                    const msgNode = el('kipay-status-message');
                    if (msgNode) msgNode.textContent = failMessage(reason);

                    const retryBtn = el('kipay-retry-btn');
                    if (retryBtn) retryBtn.style.display = (reason === 'insecure' || reason === 'unsupported') ? 'none' : 'inline-block';
                }

                async function checkAvailability() {
                    if (!window.isSecureContext) return { ok: false, reason: 'insecure' };
                    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) return { ok: false, reason: 'unsupported' };
                    try {
                        const devices = await navigator.mediaDevices.enumerateDevices();
                        const hasVideoInput = devices.some((d) => d.kind === 'videoinput');
                        if (!hasVideoInput) return { ok: false, reason: 'no-device' };
                    } catch (e) {
                        // sebagian browser membatasi enumerateDevices() sebelum izin diberikan
                    }
                    return { ok: true };
                }

                async function openCameraRaw() {
                    if (window.kipayPendingStream) {
                        const pending = window.kipayPendingStream;
                        window.kipayPendingStream = null;
                        try {
                            stream = await pending;
                        } catch (e) {
                            stream = null;
                        }
                    }

                    if (!stream) {
                        try {
                            stream = await navigator.mediaDevices.getUserMedia({
                                video: { facingMode: { exact: 'environment' } }, audio: false,
                            });
                        } catch (e1) {
                            stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
                        }
                    }

                    const video = el('kipay-video');
                    if (!video) throw new Error('Elemen video tidak ditemukan');
                    video.srcObject = stream;
                    await video.play();
                }

                function openCameraWithTimeout() {
                    return new Promise((resolve, reject) => {
                        let settled = false;
                        const timer = setTimeout(() => {
                            if (settled) return;
                            settled = true;
                            const err = new Error('Timeout membuka kamera');
                            err.name = 'TimeoutError';
                            reject(err);
                        }, CAMERA_OPEN_TIMEOUT_MS);

                        openCameraRaw().then(
                            () => { if (settled) return; settled = true; clearTimeout(timer); resolve(); },
                            (err) => { if (settled) return; settled = true; clearTimeout(timer); reject(err); }
                        );
                    });
                }

                function armWatchdog() {
                    if (watchdogId) clearTimeout(watchdogId);
                    watchdogId = setTimeout(() => {
                        if (!frameArrived && stream) {
                            stopCamera();
                            setStatus(true, 'black-feed');
                        }
                    }, 4000);
                }

                function checkTorchSupport() {
                    const btn = el('kipay-torch-btn');
                    try {
                        track = stream.getVideoTracks()[0];
                        const caps = track.getCapabilities ? track.getCapabilities() : null;
                        if (btn) btn.style.display = (caps && caps.torch) ? 'flex' : 'none';
                    } catch (e) {
                        if (btn) btn.style.display = 'none';
                    }
                }

                function loopScan() {
                    const video = el('kipay-video');
                    const canvas = el('kipay-canvas');
                    if (!video || !canvas) return;
                    const ctx = canvas.getContext('2d', { willReadFrequently: true });

                    function tick() {
                        if (!stream) return;
                        if (video.readyState === video.HAVE_ENOUGH_DATA && video.videoWidth > 0) {
                            frameArrived = true;
                            const scale = Math.min(1, 1280 / video.videoWidth);
                            canvas.width = Math.max(1, Math.round(video.videoWidth * scale));
                            canvas.height = Math.max(1, Math.round(video.videoHeight * scale));
                            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                            const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                            const code = jsQR(imageData.data, imageData.width, imageData.height, { inversionAttempts: 'attemptBoth' });
                            if (code && code.data) {
                                stopCamera();
                                sendScanPayload(code.data);
                                return;
                            }
                        }
                        rafId = requestAnimationFrame(tick);
                    }
                    rafId = requestAnimationFrame(tick);
                }

                function readImage(file) {
                    if (!file || !file.type.startsWith('image/')) return;
                    stopCamera();
                    setLoading(true);
                    setStatus(false);

                    const image = new Image();
                    const url = URL.createObjectURL(file);
                    image.onload = function () {
                        try {
                            const canvas = el('kipay-canvas');
                            if (!canvas) return;
                            const scale = Math.min(1, 1600 / image.naturalWidth);
                            canvas.width = Math.max(1, Math.round(image.naturalWidth * scale));
                            canvas.height = Math.max(1, Math.round(image.naturalHeight * scale));
                            const ctx = canvas.getContext('2d', { willReadFrequently: true });
                            ctx.drawImage(image, 0, 0, canvas.width, canvas.height);
                            const data = ctx.getImageData(0, 0, canvas.width, canvas.height);
                            const code = jsQR(data.data, data.width, data.height, { inversionAttempts: 'attemptBoth' });
                            if (code && code.data) {
                                sendScanPayload(code.data);
                            } else {
                                setStatus(true, 'other');
                                const message = el('kipay-status-message');
                                if (message) message.textContent = 'QR tidak terbaca. Gunakan foto yang lebih jelas dan coba lagi.';
                            }
                        } finally {
                            URL.revokeObjectURL(url);
                            setLoading(false);
                        }
                    };
                    image.onerror = function () {
                        URL.revokeObjectURL(url);
                        setLoading(false);
                        setStatus(true, 'other');
                    };
                    image.src = url;
                }

                function stopCamera() {
                    isStarting = false;
                    if (watchdogId) { clearTimeout(watchdogId); watchdogId = null; }
                    if (rafId) cancelAnimationFrame(rafId);
                    rafId = null;
                    if (stream) {
                        stream.getTracks().forEach((t) => t.stop());
                        stream = null;
                    }
                    track = null;
                    const torchBtn = el('kipay-torch-btn');
                    if (torchBtn) torchBtn.style.display = 'none';
                }

                async function start() {
                    // Kalau sedang proses membuka kamera atau kamera sudah aktif,
                    // JANGAN mulai lagi — request getUserMedia yang tumpang tindih
                    // di beberapa browser bisa saling mengganjal & menggantung
                    // selamanya di "Membuka kamera...".
                    if (isStarting || stream) {
                        console.log('[KipayScanner] start() diabaikan, sudah berjalan.');
                        return;
                    }
                    isStarting = true;

                    setLoading(true);
                    setStatus(false);
                    frameArrived = false;
                    console.log('[KipayScanner] Mulai membuka kamera...');

                    try {
                        if (typeof jsQR === 'undefined') {
                            console.error('[KipayScanner] jsQR tidak termuat (CDN mungkin diblokir).');
                            isStarting = false;
                            setLoading(false);
                            setStatus(true, 'other');
                            return;
                        }

                        const check = await checkAvailability();
                        if (!check.ok) {
                            console.warn('[KipayScanner] Kamera tidak tersedia:', check.reason);
                            isStarting = false;
                            setLoading(false);
                            setStatus(true, check.reason);
                            return;
                        }

                        await openCameraWithTimeout();

                        console.log('[KipayScanner] Kamera aktif.');
                        isStarting = false;
                        setLoading(false);
                        checkTorchSupport();
                        armWatchdog();
                        loopScan();
                    } catch (err) {
                        isStarting = false;
                        setLoading(false);
                        let reason = 'other';
                        if (err && err.name === 'TimeoutError') reason = 'timeout';
                        else if (err && (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError')) reason = 'denied';
                        else if (err && (err.name === 'NotFoundError' || err.name === 'DevicesNotFoundError')) reason = 'no-device';

                        console.error('[KipayScanner] Gagal membuka kamera:', err && err.name, err && err.message);
                        setStatus(true, reason);
                        stopCamera();
                    }
                }

                function stop() { stopCamera(); }

                function retry() {
                    if (window.kipayPrewarmCamera) window.kipayPrewarmCamera();
                    start();
                }

                function toggleTorch() {
                    if (!track) return;
                    const btn = el('kipay-torch-btn');
                    const isOn = btn && btn.classList.contains('kipay-torch-on');
                    track.applyConstraints({ advanced: [{ torch: !isOn }] }).then(() => {
                        if (btn) {
                            btn.classList.toggle('kipay-torch-on', !isOn);
                            btn.classList.toggle('bg-emerald-400/80', !isOn);
                            btn.classList.toggle('bg-white/15', isOn);
                        }
                    }).catch(() => {});
                }

                return {
                    start: start,
                    stop: stop,
                    retry: retry,
                    readImage: readImage,
                    toggleTorch: toggleTorch,
                };
            })();

            /* ---------- LIFECYCLE: mulai/hentikan kamera saat #kipay-scan-root muncul/hilang ---------- */
            (function () {
                function containsRoot(node) {
                    if (!node || node.nodeType !== 1) return false;
                    if (node.id === 'kipay-scan-root') return true;
                    return typeof node.querySelector === 'function' && !!node.querySelector('#kipay-scan-root');
                }

                const observer = new MutationObserver(function (mutations) {
                    for (const mutation of mutations) {
                        mutation.addedNodes.forEach(function (node) {
                            if (containsRoot(node)) window.KipayScanner.start();
                        });
                        mutation.removedNodes.forEach(function (node) {
                            if (containsRoot(node)) window.KipayScanner.stop();
                        });
                    }
                });

                function boot() {
                    observer.observe(document.body, { childList: true, subtree: true });
                    if (document.getElementById('kipay-scan-root')) {
                        window.KipayScanner.start();
                    }
                }

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', boot);
                } else {
                    boot();
                }
            })();
        </script>
    @endpush
</div>
