<div class="min-h-screen bg-slate-100 pb-28 md:pb-10">

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
                    <button wire:click="setTab('beranda')" class="rounded-full px-4 py-2 text-sm font-medium transition {{ $tab === 'beranda' ? 'bg-white text-blue-700' : 'text-white/80 hover:bg-white/10' }}">Beranda</button>
                    <a href="{{ route('scan') }}" class="rounded-full px-4 py-2 text-sm font-medium text-white/80 transition hover:bg-white/10">Scan QRIS</a>
                    <button wire:click="setTab('riwayat')" class="rounded-full px-4 py-2 text-sm font-medium transition {{ $tab === 'riwayat' ? 'bg-white text-blue-700' : 'text-white/80 hover:bg-white/10' }}">Riwayat</button>
                </nav>
                <button wire:click="logout" class="rounded-full bg-white/15 p-2.5 transition hover:bg-white/25">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6A2.25 2.25 0 005.25 5.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M18 15l3-3m0 0l-3-3m3 3H9"/></svg>
                </button>
            </div>
        </div>
    </header>

    <main class="mx-auto -mt-20 max-w-5xl px-4 pt-24">
        @if ($tab === 'beranda')
            <div class="grid gap-4 md:grid-cols-3">
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
                        <button wire:click="openTopUp" class="flex items-center gap-1 rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-200">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg> Isi Saldo
                        </button>
                    </div>

                    <div class="mt-5 grid grid-cols-3 gap-2 border-t border-slate-100 pt-5">
                        <a href="{{ route('scan') }}" class="group flex flex-col items-center gap-2">
                            <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5zM13.5 13.5h3v3h-3v-3zM18 18h2.25v2.25H18V18z"/></svg>
                            </span>
                            <span class="text-[11px] font-medium text-slate-600">Scan QRIS</span>
                        </a>
                        <a href="{{ route('transfer') }}" class="group flex flex-col items-center gap-2">
                            <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 text-blue-600"><svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5M16.5 3L21 7.5m0 0L16.5 12M21 7.5H7.5"/></svg></span>
                            <span class="text-[11px] font-medium text-slate-600">Transfer</span>
                        </a>
                        <a href="{{ route('merchant') }}" class="group flex flex-col items-center gap-2">
                            <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600"><svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5h-3V21M3 13.5V21h18v-7.5M3.75 3h16.5l1.5 5.25a3 3 0 11-6 0 3 3 0 11-6 0 3 3 0 11-6 0L3.75 3z"/></svg></span>
                            <span class="text-[11px] font-medium text-slate-600">Merchant</span>
                        </a>
                    </div>
                </section>

                <section class="rounded-2xl bg-gradient-to-br from-emerald-500 to-blue-600 p-5 text-white shadow-lg shadow-emerald-200/60">
                    @if ($merchant)
                        <p class="text-xs font-medium uppercase tracking-wide text-white/70">Toko Kamu</p>
                        <h3 class="mt-1 text-lg font-bold leading-snug">{{ $merchant->merchant_name }}</h3>
                        <a href="{{ route('merchant') }}" class="mt-4 block w-full rounded-xl bg-white/20 py-2.5 text-center text-sm font-semibold backdrop-blur transition hover:bg-white/30">Lihat QR Merchant</a>
                    @else
                        <p class="text-xs font-medium uppercase tracking-wide text-white/70">KiPay Merchant</p>
                        <h3 class="mt-1 text-lg font-bold leading-snug">Terima pembayaran pakai QR</h3>
                        <a href="{{ route('merchant') }}" class="mt-4 block w-full rounded-xl bg-white/20 py-2.5 text-center text-sm font-semibold backdrop-blur transition hover:bg-white/30">Buat Merchant</a>
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

        @if ($tab === 'riwayat')
            <section class="rounded-2xl bg-white p-5 shadow-lg shadow-slate-200/70">
                <h2 class="mb-3 text-sm font-bold text-slate-800">Riwayat Transaksi</h2>
                @include('livewire.partials.transaction-list', ['items' => $all_transactions])
            </section>
        @endif
    </main>

    {{-- BOTTOM NAV (mobile) --}}
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
            <a href="{{ route('scan') }}" class="absolute -top-6 left-1/2 flex h-16 w-16 -translate-x-1/2 items-center justify-center rounded-full bg-gradient-to-br from-blue-600 to-emerald-500 text-white shadow-xl shadow-blue-500/40 ring-4 ring-slate-100">
                <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5zM13.5 13.5h3v3h-3v-3zM18 18h2.25v2.25H18V18z"/></svg>
            </a>
        </div>
    </nav>

    {{-- MODAL TOP UP --}}
    @if ($showTopUp)
        <div class="fixed inset-0 z-40 flex items-end justify-center bg-slate-900/50 md:items-center" wire:click.self="closeAll">
            <div class="w-full max-w-md rounded-t-3xl bg-white p-6 md:rounded-2xl">
                <div class="mx-auto mb-4 h-1.5 w-12 rounded-full bg-slate-200 md:hidden"></div>
                <h3 class="text-lg font-bold text-slate-800">Isi Saldo (Dummy)</h3>
                <form wire:submit="topUp">
                    <div class="relative mt-4">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-semibold text-slate-400">Rp</span>
                        <input type="number" wire:model="amount" class="w-full rounded-xl border border-slate-200 py-3 pl-11 pr-4 text-lg font-semibold focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
                    </div>
                    <div class="mt-5 flex gap-2">
                        <button type="button" wire:click="closeAll" class="flex-1 rounded-xl border border-slate-200 py-3 text-sm font-semibold text-slate-600">Batal</button>
                        <button type="submit" class="flex-1 rounded-xl bg-gradient-to-r from-blue-600 to-emerald-500 py-3 text-sm font-bold text-white">Isi Saldo</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- MODAL SUKSES --}}
    @if ($showSuccessModal)
        <div class="fixed inset-0 z-[60] flex items-start justify-center px-4 pt-10" x-data x-init="setTimeout(() => $wire.closeSuccessModal(), 3000)">
            <div class="pointer-events-none fixed inset-0 bg-slate-900/10"></div>
            <div class="pointer-events-auto relative flex w-full max-w-sm items-center gap-3 rounded-2xl bg-white p-4 shadow-2xl ring-1 ring-black/5">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-blue-600 to-emerald-500 text-white"><svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></div>
                <div class="flex-1">
                    <p class="text-sm font-bold text-slate-800">Berhasil</p>
                    <p class="text-xs text-slate-500">{{ $successMessage }}</p>
                </div>
            </div>
        </div>
    @endif
</div>
