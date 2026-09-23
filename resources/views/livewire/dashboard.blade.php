<div class="min-h-screen bg-slate-100 pb-28 md:pb-10">

    {{-- ================= HEADER ================= --}}
    <header class="relative overflow-hidden bg-gradient-to-br from-blue-700 via-blue-600 to-emerald-500 pb- 6pt-6 text-white">
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

                    {{--
                        Grid aksi cepat sebelumnya berisi 4 tombol (Scan QRIS, Transfer, QR Saya, Merchant).
                        Tombol "QR Saya" dinonaktifkan bersama fitur Tampilkan QRIS — kode lama disimpan di bawah untuk rollback.
                    --}}
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

                        {{--
                        <button wire:click="$set('showQr', true)" class="group flex flex-col items-center gap-2">
                            <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-teal-50 text-teal-600 transition group-hover:bg-teal-100">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 4.5h5v5h-5v-5zM4.5 14.5h5v5h-5v-5zM14.5 4.5h5v5h-5v-5zM14.5 14.5h2v2h-2v-2zM18.5 18.5h1v1h-1v-1z"/></svg>
                            </span>
                            <span class="text-[11px] font-medium text-slate-600">QR Saya</span>
                        </button>
                        --}}

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

    {{--
        ================= SHEET: QR SAYA (DINONAKTIFKAN) =================
        Fitur "Tampilkan QRIS" tidak dibutuhkan di web KiPay. Blok ini disimpan sebagai
        komentar (bukan dihapus) agar mudah diaktifkan kembali jika dibutuhkan nanti.
        Perlu mengaktifkan kembali properti $showQr dan getMyPayloadProperty() di Dashboard.php.

    @if ($showQr)
        <div class="fixed inset-0 z-40 flex items-end justify-center bg-slate-900/50 md:items-center" wire:click.self="closeAll">
            <div class="w-full max-w-sm rounded-t-3xl bg-white p-6 text-center md:rounded-2xl">
                <div class="mx-auto mb-4 h-1.5 w-12 rounded-full bg-slate-200 md:hidden"></div>
                <h3 class="text-lg font-bold text-slate-800">QR Pribadi Saya</h3>
                <p class="mb-4 text-sm text-slate-500">Minta teman memindai kode ini untuk mengirim saldo.</p>

                <div class="mx-auto inline-block rounded-2xl border-4 border-emerald-500 p-3">
                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(200)->margin(1)->generate($this->myPayload) !!}
                </div>

                <p class="mt-4 text-sm font-semibold text-slate-700">{{ $user->name }}</p>
                <p class="text-xs text-slate-400">{{ $user->whatsapp }}</p>
                <p class="mt-2 break-all text-[10px] text-slate-300">{{ $this->myPayload }}</p>

                <button wire:click="closeAll" class="mt-5 w-full rounded-xl bg-slate-100 py-3 text-sm font-semibold text-slate-600">Tutup</button>
            </div>
        </div>
    @endif
    --}}

    {{-- ================= FULLSCREEN: SCAN QRIS (gaya GoPay, kamera JS murni via jsQR) ================= --}}
    @if ($showScan)
        <div class="fixed inset-0 z-50 bg-black" wire:key="scan-sheet">

            {{-- STEP 0 — kamera / upload, tampilan penuh layar (JS murni, TANPA Alpine sama sekali) --}}
            @if ($payStep === 0)
                <div id="kipay-scan-root" class="relative h-full w-full overflow-hidden">

                    {{-- video kamera memenuhi layar (diambil langsung via getUserMedia, didekode dengan jsQR) --}}
                    <video id="kipay-video" autoplay playsinline muted class="absolute inset-0 h-full w-full bg-black object-cover"></video>
                    <canvas id="kipay-canvas" class="hidden"></canvas>

                    {{-- overlay gelap tipis agar chrome tetap terbaca di atas kamera --}}
                    <div class="pointer-events-none absolute inset-0 bg-gradient-to-b from-black/60 via-transparent to-black/70"></div>

                    {{-- top bar --}}
                    <div class="absolute inset-x-0 top-0 flex items-center justify-between px-5 pt-[calc(env(safe-area-inset-top,0px)+1rem)]">
                        <button wire:click="closeAll" class="flex h-10 w-10 items-center justify-center rounded-full bg-white/15 text-white backdrop-blur">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <p class="text-sm font-semibold text-white">Scan QRIS</p>
                        <button type="button" id="kipay-torch-btn" onclick="KipayScanner.toggleTorch()" style="display:none"
                            class="flex h-10 w-10 items-center justify-center rounded-full bg-white/15 text-white backdrop-blur">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-6.364-2.386 1.591-1.591M3 12h2.25m.386-6.364 1.591 1.591M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </button>
                    </div>

                    {{-- kotak pemindai dengan efek scan digital (garis bergerak) --}}
                    <div class="pointer-events-none absolute left-1/2 top-1/2 h-64 w-64 -translate-x-1/2 -translate-y-1/2">
                        <div class="absolute inset-0 rounded-2xl border border-white/40"></div>
                        <div class="absolute -left-0.5 -top-0.5 h-9 w-9 rounded-tl-2xl border-l-4 border-t-4 border-emerald-400"></div>
                        <div class="absolute -right-0.5 -top-0.5 h-9 w-9 rounded-tr-2xl border-r-4 border-t-4 border-emerald-400"></div>
                        <div class="absolute -left-0.5 -bottom-0.5 h-9 w-9 rounded-bl-2xl border-l-4 border-b-4 border-emerald-400"></div>
                        <div class="absolute -right-0.5 -bottom-0.5 h-9 w-9 rounded-br-2xl border-r-4 border-b-4 border-emerald-400"></div>
                        <div class="kipay-scanline absolute inset-x-2 h-1 rounded-full bg-gradient-to-r from-transparent via-emerald-300 to-transparent" style="box-shadow:0 0 12px 2px rgba(52,211,153,0.85)"></div>
                    </div>

                    <p class="absolute inset-x-0 top-[calc(50%+9rem)] text-center text-xs text-white/80">Arahkan kamera ke kode QR pembayaran</p>

                    <div id="kipay-loading" class="absolute inset-0 flex flex-col items-center justify-center gap-3 bg-black/40 text-white">
                        <svg class="h-7 w-7 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                        <p class="text-xs">Membuka kamera...</p>
                    </div>

                    {{-- Panel gagal — pesan mengikuti alasan spesifik (izin ditolak / tidak ada kamera / http / tidak didukung / feed hitam) --}}
                    <div id="kipay-failed" style="display:none" class="absolute inset-x-6 top-1/2 -translate-y-1/2 rounded-2xl bg-white p-5 text-center shadow-xl">
                        <p id="kipay-fail-message" class="mb-1 text-sm text-slate-600"></p>
                        <p id="kipay-fail-detail" style="display:none" class="mb-4 text-[10px] text-slate-300"></p>
                        <div class="flex flex-col gap-2">
                            <button type="button" id="kipay-retry-btn" onclick="KipayScanner.retry()"
                                class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-bold text-white">Coba Lagi</button>
                            <button type="button" onclick="document.getElementById('kipay-file-input').click()"
                                class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600">Upload QR</button>
                        </div>

                        {{-- ============================================================================
                             TESTER: INPUT PAYLOAD MANUAL — versi ini tampil di panel gagal (kamera error).
                             Tombol pakai onclick="" native, bukan Alpine, jadi tidak bergantung directive
                             apa pun untuk berfungsi. HAPUS SELURUH BLOK INI (dari komentar TESTER START
                             sampai TESTER END) begitu alur scan kamera sudah dikonfirmasi aman & berjalan
                             di semua perangkat target.
                             ============================================================================ --}}
                        {{-- TESTER START --}}
                        <div class="mt-4 border-t border-slate-100 pt-4 text-left">
                            <p class="mb-2 text-xs font-semibold text-slate-400">Mode tester — masukkan payload QR manual</p>
                            <div class="flex gap-2">
                                <input type="text" id="kipay-tester-input-failed" placeholder="KIPAY-MCH-1-169..."
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
                                <button type="button" onclick="KipayScanner.submitTesterPayload('kipay-tester-input-failed')"
                                    class="shrink-0 rounded-xl bg-slate-800 px-3 py-2 text-xs font-semibold text-white">Kirim</button>
                            </div>
                        </div>
                        {{-- TESTER END --}}
                    </div>

                    {{-- bottom sheet: Upload QR + toggle mode tester --}}
                    <div class="absolute inset-x-0 bottom-0 rounded-t-3xl bg-white px-5 pb-[calc(env(safe-area-inset-bottom,0px)+1.25rem)] pt-4 shadow-2xl">
                        <div class="mx-auto mb-4 h-1.5 w-12 rounded-full bg-slate-200"></div>

                        <div class="mb-4 rounded-2xl bg-gradient-to-r from-blue-600 to-emerald-500 px-4 py-3 text-center text-xs font-semibold text-white">
                            Arahkan kamera ke kode QR atau unggah gambar QR untuk membayar
                        </div>

                        <button type="button" onclick="document.getElementById('kipay-file-input').click()"
                            class="flex w-full items-center justify-center gap-2 rounded-2xl border border-slate-200 py-4 transition hover:border-blue-400">
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5V18a2.25 2.25 0 002.25 2.25h13.5A2.25 2.25 0 0021 18v-1.5M7.5 7.5L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                            </span>
                            <span class="text-sm font-semibold text-slate-700">Upload QR dari Galeri</span>
                        </button>

                        <input type="file" accept="image/*" id="kipay-file-input" class="hidden"
                            onchange="KipayScanner.scanFromFile(this.files[0]); this.value = ''">

                        {{-- ============================================================================
                             TESTER: TOGGLE PAYLOAD MANUAL (tersedia juga saat kamera hidup normal, untuk
                             menguji payload tertentu tanpa scan fisik). Tombol pakai onclick="" native.
                             HAPUS SELURUH BLOK INI (dari komentar TESTER START sampai TESTER END) begitu
                             alur kamera sudah dikonfirmasi aman & berjalan di semua perangkat target.
                             ============================================================================ --}}
                        {{-- TESTER START --}}
                        <div class="mt-3">
                            <button type="button" onclick="KipayScanner.toggleTester()"
                                class="w-full text-center text-[11px] font-semibold text-slate-400 underline decoration-dotted">
                                Mode tester: masukkan payload manual
                            </button>
                            <div id="kipay-tester-panel" style="display:none" class="mt-2 gap-2">
                                <input type="text" id="kipay-tester-input-main" placeholder="KIPAY-MCH-1-169..."
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
                                <button type="button" onclick="KipayScanner.submitTesterPayload('kipay-tester-input-main')"
                                    class="shrink-0 rounded-xl bg-slate-800 px-3 py-2 text-xs font-semibold text-white">Kirim</button>
                            </div>
                        </div>
                        {{-- TESTER END --}}
                    </div>
                </div>
            @endif

            {{-- STEP 1 & 2 tetap sebagai bottom sheet biasa di atas layar gelap --}}
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

    {{-- ================= MODAL NOTIFIKASI SUKSES (mengambang, bukan tertanam) ================= --}}
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

    {{-- Animasi garis scan digital --}}
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
        {{--
            Metode scan diubah ke JavaScript murni (jsQR + getUserMedia/canvas) agar tidak bergantung
            pada worker/script eksternal html5-qrcode yang kadang bermasalah di hosting statis seperti Vercel,
            dan supaya efek "scan digital" bisa digambar bebas di atas video kamera.
            Library jsQR: hanya mendekode gambar/kanvas — tidak butuh backend PHP sama sekali.
        --}}
        <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
        <script>
            /* =====================================================================
             | KipayScanner — JS MURNI, TANPA ALPINE SAMA SEKALI
             | Sebelumnya bagian ini pakai Alpine.js (x-data/x-show/x-model dst).
             | Di beberapa environment, Alpine tidak konsisten memproses ulang
             | elemen yang baru dimasukkan Livewire lewat morph, sehingga seluruh
             | komponen (kamera + semua tombol di dalamnya) berhenti merespons.
             | Supaya tidak bergantung pada itu sama sekali, semua interaksi di
             | sini pakai onclick="" / onchange="" native (persis seperti tombol
             | "Scan QRIS" di luar, yang sudah terbukti selalu berfungsi), dan
             | status (loading/gagal) ditulis langsung ke DOM lewat getElementById,
             | tanpa reactivity framework apa pun.
             |
             | Kapan kamera mulai/berhenti dipantau pakai MutationObserver yang
             | mengawasi kapan elemen #kipay-scan-root muncul/hilang dari DOM —
             | ini juga tidak bergantung pada Alpine maupun hook Livewire tertentu,
             | jadi aman terhadap versi Livewire apa pun.
             |=====================================================================*/

            /* ---------- PREWARM KAMERA (dipanggil dari onclick tombol "Scan QRIS") ---------- */
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

                // Serap rejection supaya tidak muncul "Uncaught (in promise)" kalau sheet scan
                // ternyata tidak jadi dibuka setelah prewarm ini.
                window.kipayPendingStream.catch(() => {});
            };

            window.KipayScanner = (function () {
                let stream = null;
                let rafId = null;
                let watchdogId = null;
                let frameArrived = false;
                let track = null;

                function el(id) {
                    return document.getElementById(id);
                }

                function setLoading(isLoading) {
                    const node = el('kipay-loading');
                    if (node) node.style.display = isLoading ? 'flex' : 'none';
                }

                function failMessage(reason) {
                    switch (reason) {
                        case 'insecure':
                            return 'Kamera hanya bisa diakses lewat koneksi HTTPS (atau localhost). Buka halaman ini lewat HTTPS, lalu coba lagi — atau gunakan upload/mode tester di bawah.';
                        case 'unsupported':
                            return 'Browser ini tidak mendukung akses kamera. Coba gunakan Chrome/Safari versi terbaru, atau unggah gambar QR dari galeri.';
                        case 'no-device':
                            return 'Tidak ada kamera yang terdeteksi di perangkat ini. Silakan unggah gambar QR dari galeri, atau gunakan mode tester di bawah.';
                        case 'denied':
                            return 'Akses kamera ditolak. Izinkan akses kamera lewat pengaturan browser (ikon gembok di address bar), lalu tekan "Coba Lagi".';
                        case 'black-feed':
                            return 'Kamera terbuka tapi tidak mengirim gambar. Kemungkinan sedang dipakai aplikasi/tab lain, atau driver kamera bermasalah. Tutup aplikasi lain yang memakai kamera, lalu coba lagi.';
                        default:
                            return 'Kamera tidak bisa diakses. Pastikan browser diizinkan mengakses kamera, lalu coba lagi. Atau unggah gambar QR dari galeri.';
                    }
                }

                function setFailed(show, reason, detail) {
                    const panel = el('kipay-failed');
                    if (!panel) return;
                    panel.style.display = show ? 'block' : 'none';
                    if (!show) return;

                    const msgNode = el('kipay-fail-message');
                    if (msgNode) msgNode.textContent = failMessage(reason);

                    const detailNode = el('kipay-fail-detail');
                    if (detailNode) {
                        if (detail) {
                            detailNode.textContent = 'Detail teknis: ' + detail;
                            detailNode.style.display = 'block';
                        } else {
                            detailNode.style.display = 'none';
                        }
                    }

                    const retryBtn = el('kipay-retry-btn');
                    if (retryBtn) {
                        retryBtn.style.display = (reason === 'insecure' || reason === 'unsupported') ? 'none' : 'block';
                    }
                }

                async function checkAvailability() {
                    if (!window.isSecureContext) {
                        return { ok: false, reason: 'insecure' };
                    }
                    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                        return { ok: false, reason: 'unsupported' };
                    }
                    try {
                        const devices = await navigator.mediaDevices.enumerateDevices();
                        const hasVideoInput = devices.some((d) => d.kind === 'videoinput');
                        if (!hasVideoInput) {
                            return { ok: false, reason: 'no-device' };
                        }
                    } catch (e) {
                        // Sebagian browser membatasi enumerateDevices() sebelum izin diberikan —
                        // kalau gagal total, tetap lanjut coba getUserMedia langsung.
                    }
                    return { ok: true };
                }

                async function openCamera() {
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
                                video: { facingMode: { exact: 'environment' } },
                                audio: false,
                            });
                        } catch (e1) {
                            stream = await navigator.mediaDevices.getUserMedia({
                                video: true,
                                audio: false,
                            });
                        }
                    }

                    const video = el('kipay-video');
                    if (!video) throw new Error('Elemen video tidak ditemukan');
                    video.srcObject = stream;
                    await video.play();
                }

                function armWatchdog() {
                    if (watchdogId) clearTimeout(watchdogId);
                    watchdogId = setTimeout(() => {
                        if (!frameArrived && stream) {
                            console.warn('[KipayScanner] tidak ada frame video setelah 4 detik — kemungkinan feed hitam.');
                            stopCamera();
                            setFailed(true, 'black-feed');
                        }
                    }, 4000);
                }

                function checkTorchSupport() {
                    const btn = el('kipay-torch-btn');
                    try {
                        track = stream.getVideoTracks()[0];
                        const caps = track.getCapabilities ? track.getCapabilities() : null;
                        const has = !!(caps && caps.torch);
                        if (btn) btn.style.display = has ? 'flex' : 'none';
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
                        if (!stream) return; // kamera sudah ditutup, hentikan loop
                        if (video.readyState === video.HAVE_ENOUGH_DATA && video.videoWidth > 0) {
                            frameArrived = true;
                            canvas.width = video.videoWidth;
                            canvas.height = video.videoHeight;
                            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                            const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                            const code = jsQR(imageData.data, imageData.width, imageData.height, {
                                inversionAttempts: 'dontInvert',
                            });
                            if (code && code.data) {
                                stopCamera();
                                @this.call('scan', code.data);
                                return;
                            }
                        }
                        rafId = requestAnimationFrame(tick);
                    }
                    rafId = requestAnimationFrame(tick);
                }

                function stopCamera() {
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
                    setLoading(true);
                    setFailed(false);
                    frameArrived = false;

                    try {
                        if (typeof jsQR === 'undefined') {
                            console.error('[KipayScanner] jsQR tidak termuat (kemungkinan CDN diblokir jaringan).');
                            setLoading(false);
                            setFailed(true, 'other', 'jsQR belum termuat');
                            return;
                        }

                        const check = await checkAvailability();
                        if (!check.ok) {
                            console.warn('[KipayScanner] kamera tidak tersedia:', check.reason);
                            setLoading(false);
                            setFailed(true, check.reason);
                            return;
                        }

                        await openCamera();
                        setLoading(false);
                        checkTorchSupport();
                        armWatchdog();
                        loopScan();
                    } catch (err) {
                        console.error('[KipayScanner] gagal membuka kamera:', err);
                        setLoading(false);
                        let reason = 'other';
                        if (err && (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError')) {
                            reason = 'denied';
                        } else if (err && (err.name === 'NotFoundError' || err.name === 'DevicesNotFoundError')) {
                            reason = 'no-device';
                        }
                        setFailed(true, reason, err && err.name ? err.name : String(err));
                    }
                }

                function stop() {
                    stopCamera();
                }

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

                // Fallback: scan dari gambar galeri (mis. kamera tidak tersedia/diizinkan).
                async function scanFromFile(file) {
                    if (!file) return;
                    setLoading(true);
                    setFailed(false);
                    try {
                        stopCamera();

                        const img = await new Promise((resolve, reject) => {
                            const image = new Image();
                            image.onload = () => resolve(image);
                            image.onerror = reject;
                            image.src = URL.createObjectURL(file);
                        });

                        const canvas = el('kipay-canvas');
                        canvas.width = img.naturalWidth;
                        canvas.height = img.naturalHeight;
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0);
                        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                        const code = jsQR(imageData.data, imageData.width, imageData.height);

                        setLoading(false);
                        @this.call('scan', code && code.data ? code.data : '');
                    } catch (e) {
                        console.error('[KipayScanner] gagal membaca gambar:', e);
                        setLoading(false);
                        @this.call('scan', '');
                    }
                }

                // ========================================================================
                // TESTER: INPUT PAYLOAD MANUAL
                // Dipakai dari blok HTML "TESTER START/END" di dashboard.blade.php. Hapus
                // dua fungsi ini bersamaan dengan blok HTML tester begitu scan kamera sudah
                // dikonfirmasi jalan di semua perangkat target.
                // ========================================================================
                function toggleTester() {
                    const panel = el('kipay-tester-panel');
                    if (!panel) return;
                    panel.style.display = (panel.style.display === 'flex') ? 'none' : 'flex';
                }

                function submitTesterPayload(inputId) {
                    const input = el(inputId);
                    if (!input) return;
                    const value = (input.value || '').trim();
                    if (!value) return;
                    stopCamera();
                    @this.call('scan', value);
                }
                // ==================== END TESTER ====================

                return {
                    start: start,
                    stop: stop,
                    retry: retry,
                    toggleTorch: toggleTorch,
                    scanFromFile: scanFromFile,
                    toggleTester: toggleTester,
                    submitTesterPayload: submitTesterPayload,
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
                    // Kalau sheet scan kebetulan sudah ada di DOM saat script ini pertama
                    // kali jalan (mis. setelah full page reload dengan $showScan true dari
                    // server), langsung mulai juga di sini.
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
