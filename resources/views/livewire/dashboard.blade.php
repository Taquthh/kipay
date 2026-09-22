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
                    <button wire:click="openScan"
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

        @if (session('success'))
            <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-800 shadow-sm">
                {{ session('success') }}
            </div>
        @endif

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

                    <div class="mt-5 grid grid-cols-4 gap-2 border-t border-slate-100 pt-5">
                        <button wire:click="openScan" class="group flex flex-col items-center gap-2">
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

                        <button wire:click="$set('showQr', true)" class="group flex flex-col items-center gap-2">
                            <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-teal-50 text-teal-600 transition group-hover:bg-teal-100">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 4.5h5v5h-5v-5zM4.5 14.5h5v5h-5v-5zM14.5 4.5h5v5h-5v-5zM14.5 14.5h2v2h-2v-2zM18.5 18.5h1v1h-1v-1z"/></svg>
                            </span>
                            <span class="text-[11px] font-medium text-slate-600">QR Saya</span>
                        </button>

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

            <button wire:click="openScan"
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
                        <button type="submit" class="flex-1 rounded-xl bg-gradient-to-r from-blue-600 to-emerald-500 py-3 text-sm font-bold text-white">Isi Saldo</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ================= SHEET: QR SAYA ================= --}}
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

    {{-- ================= SHEET: SCAN & BAYAR ================= --}}
    @if ($showScan)
        <div class="fixed inset-0 z-40 flex items-end justify-center bg-slate-900/60 md:items-center" wire:click.self="closeAll">
            <div class="max-h-[92vh] w-full max-w-md overflow-y-auto rounded-t-3xl bg-white p-6 md:rounded-2xl">
                <div class="mx-auto mb-4 h-1.5 w-12 rounded-full bg-slate-200 md:hidden"></div>

                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-800">
                        @if ($payStep === 0) Scan QR
                        @elseif ($payStep === 1) Konfirmasi Pembayaran
                        @else Transaksi Selesai @endif
                    </h3>
                    <button wire:click="closeAll" class="text-sm font-semibold text-slate-400 hover:text-slate-600">Tutup</button>
                </div>

                @if ($errorMessage)
                    <div class="mb-4 rounded-xl bg-red-50 p-3 text-sm font-semibold text-red-700">{{ $errorMessage }}</div>
                @endif

                {{-- STEP 0 — kamera --}}
                @if ($payStep === 0)
                    <div x-data="kipayScanner()" x-init="start()" x-on:destroy="stop()">
                        <div class="relative mb-3 aspect-square w-full overflow-hidden rounded-2xl bg-slate-900">
                            <div id="kipay-reader" class="h-full w-full [&_video]:h-full [&_video]:w-full [&_video]:object-cover"></div>

                            <div x-show="loading" x-cloak class="absolute inset-0 flex flex-col items-center justify-center gap-2 text-white/70">
                                <svg class="h-6 w-6 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                </svg>
                                <p class="text-xs">Membuka kamera...</p>
                            </div>
                        </div>

                        <p class="text-center text-xs text-slate-400">Arahkan kamera ke QR pembayaran</p>

                        <div x-show="failed" x-cloak class="mt-3 rounded-xl bg-amber-50 p-4 text-center text-sm text-amber-700">
                            <p class="mb-3">Kamera tidak bisa diakses. Pastikan kamu mengizinkan akses kamera di browser lalu coba lagi.</p>
                            <button type="button" x-on:click="failed = false; start()"
                                class="rounded-xl bg-amber-600 px-4 py-2 text-xs font-bold text-white">Coba Lagi</button>
                        </div>
                    </div>
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

@push('scripts')
        <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('kipayScanner', () => ({
                    instance: null,
                    failed: false,
                    loading: true,

                    // Tambahkan parameter withDelay
                    start(withDelay = true) {
                        this.loading = true;
                        this.failed = false;

                        const executeCamera = () => {
                            if (typeof Html5Qrcode === 'undefined') {
                                this.loading = false; this.failed = true; return;
                            }
                            const el = document.getElementById('kipay-reader');
                            if (!el) { this.loading = false; this.failed = true; return; }

                            this.instance = new Html5Qrcode('kipay-reader');
                            this.instance.start(
                                { facingMode: 'environment' },
                                { fps: 10, qrbox: { width: 230, height: 230 } },
                                (text) => {
                                    this.stop();
                                    @this.call('scan', text);
                                },
                                () => {}
                            ).then(() => {
                                this.loading = false;
                            }).catch((err) => {
                                console.error(err);
                                this.loading = false;
                                this.failed = true;
                            });
                        };

                        // Jika dibuka otomatis, gunakan delay agar modal iOS siap
                        // Jika diklik dari tombol "Coba Lagi", jalankan instan tanpa delay!
                        if (withDelay) {
                            setTimeout(executeCamera, 400);
                        } else {
                            executeCamera();
                        }
                    },
                    stop() {
                        if (this.instance) {
                            this.instance.stop().then(() => this.instance.clear()).catch(() => {});
                            this.instance = null;
                        }
                    }
                }));
            });
        </script>
    @endpush
</div>
