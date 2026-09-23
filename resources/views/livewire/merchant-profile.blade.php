<div class="mx-auto mt-6 max-w-xl px-4 pb-10 sm:mt-10">

    {{-- header --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">Profil Merchant</h1>
            <p class="text-sm text-slate-500">Kelola QR Code pembayaran toko Anda</p>
        </div>
        <a href="{{ route('dashboard') }}" class="flex items-center gap-1.5 rounded-full border border-slate-200 px-3.5 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-50 active:scale-[0.97]">
            <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
            Dashboard
        </a>
    </div>

    @if (!$hasMerchant)
        {{-- ================= BELUM PUNYA TOKO ================= --}}
        <div class="overflow-hidden rounded-3xl bg-white shadow-xl shadow-slate-200/60">
            <div class="relative bg-gradient-to-br from-blue-700 via-blue-600 to-emerald-500 px-8 pb-12 pt-10 text-center text-white">
                <div class="pointer-events-none absolute -top-10 -right-10 h-40 w-40 rounded-full bg-white/10 blur-2xl"></div>
                <div class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-white/15 shadow-inner">
                    <svg viewBox="0 0 24 24" class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="1.6"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9.5 12 4l9 5.5M4.5 10.5V19a1 1 0 0 0 1 1h13a1 1 0 0 0 1-1v-8.5M9 20v-6h6v6" /></svg>
                </div>
                <h2 class="relative mt-4 text-lg font-bold">Anda belum memiliki toko</h2>
                <p class="relative mx-auto mt-1 max-w-xs text-sm text-blue-100/90">Daftarkan nama merchant untuk mendapatkan QR Code pembayaran sendiri</p>
            </div>

            <form wire:submit="createMerchant" class="relative -mt-6 space-y-4 rounded-t-3xl bg-white px-6 pb-7 pt-7 sm:px-8">
                <div wire:loading.flex wire:target="createMerchant" class="absolute inset-0 z-10 hidden flex-col items-center justify-center gap-3 rounded-t-3xl bg-white/95 backdrop-blur-sm">
                    <div class="relative h-12 w-12">
                        <div class="absolute inset-0 rounded-full border-4 border-slate-100"></div>
                        <div class="absolute inset-0 rounded-full border-4 border-emerald-500 border-t-transparent animate-spin"></div>
                    </div>
                    <p class="text-sm font-semibold text-slate-700">Membuat QR Code toko...</p>
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-400">Nama Toko / Merchant</label>
                    <div class="mt-1.5 flex items-center gap-2 rounded-2xl border-2 border-slate-100 bg-slate-50/60 px-4 py-3.5 transition focus-within:border-emerald-400 focus-within:bg-emerald-50/40">
                        <svg viewBox="0 0 24 24" class="h-5 w-5 shrink-0 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.6"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9.5 12 4l9 5.5M4.5 10.5V19a1 1 0 0 0 1 1h13a1 1 0 0 0 1-1v-8.5" /></svg>
                        <input type="text" wire:model="merchant_name" placeholder="Contoh: Kopi Senja" class="w-full border-0 bg-transparent p-0 text-base font-semibold text-slate-900 outline-none placeholder:text-slate-300" required>
                    </div>
                    @error('merchant_name') <p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p> @enderror
                </div>

                <button type="submit" class="w-full rounded-2xl bg-gradient-to-br from-blue-600 to-emerald-500 py-3.5 text-sm font-bold text-white shadow-lg shadow-emerald-900/20 transition active:scale-[0.98] disabled:opacity-50" wire:loading.attr="disabled" wire:target="createMerchant">
                    Buat QR Code Toko
                </button>
            </form>
        </div>
    @else
        {{-- ================= QR CODE TOKO ================= --}}
        <div x-data="{ showPayload: false }">

            <div id="print-area" class="overflow-hidden rounded-3xl bg-white shadow-xl shadow-slate-200/60">
                <div class="relative bg-gradient-to-br from-blue-700 via-blue-600 to-emerald-500 px-6 py-6 text-center text-white print:bg-white print:text-slate-900 print:py-3">
                    <div class="pointer-events-none absolute -top-10 -right-10 h-40 w-40 rounded-full bg-white/10 blur-2xl print:hidden"></div>
                    <p class="relative text-xs font-semibold uppercase tracking-widest text-blue-100/80 print:text-slate-400">KiPay Merchant</p>
                    <h2 class="relative mt-1 text-xl font-extrabold">{{ $merchantData->merchant_name }}</h2>
                </div>

                <div class="px-6 py-8 text-center sm:px-10">
                    <p class="mx-auto max-w-xs text-sm text-slate-500 print:hidden">Tunjukkan QR Code ini kepada pelanggan untuk menerima pembayaran</p>

                    <div class="mt-6 flex justify-center">
                        <div class="relative rounded-2xl border-4 border-blue-50 bg-white p-4 shadow-sm">
                            <span class="absolute -left-1.5 -top-1.5 h-5 w-5 rounded-tl-xl border-l-2 border-t-2 border-emerald-400 print:hidden"></span>
                            <span class="absolute -right-1.5 -top-1.5 h-5 w-5 rounded-tr-xl border-r-2 border-t-2 border-emerald-400 print:hidden"></span>
                            <span class="absolute -bottom-1.5 -left-1.5 h-5 w-5 rounded-bl-xl border-b-2 border-l-2 border-emerald-400 print:hidden"></span>
                            <span class="absolute -bottom-1.5 -right-1.5 h-5 w-5 rounded-br-xl border-b-2 border-r-2 border-emerald-400 print:hidden"></span>
                            {!! $qrCodeSvg !!}
                        </div>
                    </div>

                    <p class="mt-4 text-xs font-medium text-slate-400 print:mt-3">Scan menggunakan aplikasi KiPay untuk membayar</p>
                </div>
            </div>

            {{-- data teknis: disembunyikan secara default --}}
            <div class="mt-4 print:hidden">
                <button type="button" x-on:click="showPayload = !showPayload" class="flex w-full items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 py-3 text-left text-xs font-semibold text-slate-500 transition hover:bg-slate-50">
                    <span class="flex items-center gap-1.5">
                        <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12s3.5-7 9-7 9 7 9 7-3.5 7-9 7-9-7-9-7Z" /><circle cx="12" cy="12" r="2.5" /></svg>
                        Detail teknis QR Code
                    </span>
                    <svg viewBox="0 0 24 24" class="h-4 w-4 transition" :class="showPayload && 'rotate-180'" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" /></svg>
                </button>
                <div x-show="showPayload" x-cloak x-transition class="mt-2 space-y-2 rounded-2xl bg-slate-50 p-4 text-xs text-slate-500">
                    <p class="font-semibold text-slate-400">Payload data (jangan dibagikan sembarangan)</p>
                    <div class="flex items-center gap-2">
                        <code class="flex-1 break-all rounded-lg bg-white px-3 py-2 font-mono text-[11px] text-slate-600 ring-1 ring-slate-200">{{ $merchantData->qr_code_payload }}</code>
                        <button type="button" x-on:click="navigator.clipboard.writeText(@js($merchantData->qr_code_payload)); $el.dataset.copied = 1; setTimeout(() => $el.dataset.copied = '', 1500)" class="relative grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-white text-slate-500 ring-1 ring-slate-200 transition active:scale-90">
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 9h10v10H9zM5 15V5h10" /></svg>
                        </button>
                    </div>
                </div>
            </div>

            {{-- aksi --}}
            <div class="mt-4 grid grid-cols-2 gap-3 print:hidden">
                <button type="button" onclick="window.print()" class="flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-br from-blue-600 to-emerald-500 py-3.5 text-sm font-bold text-white shadow-lg shadow-emerald-900/20 transition active:scale-[0.98]">
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 9V3h12v6M6 18H4a1 1 0 0 1-1-1v-6a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1v6a1 1 0 0 1-1 1h-2M6 14h12v7H6z" /></svg>
                    Cetak QR Code
                </button>
                <a href="{{ route('dashboard') }}" class="flex items-center justify-center gap-2 rounded-2xl border border-slate-200 py-3.5 text-sm font-bold text-slate-600 transition hover:bg-slate-50 active:scale-[0.98]">
                    Selesai
                </a>
            </div>
        </div>
    @endif

    <style>
        @media print {
            body * { visibility: hidden; }
            #print-area, #print-area * { visibility: visible; }
            #print-area {
                position: fixed;
                inset: 0;
                margin: auto;
                width: 320px;
                height: fit-content;
                box-shadow: none !important;
                border: 1px solid #e2e8f0;
            }
        }
        [x-cloak] { display: none !important; }
    </style>
</div>
