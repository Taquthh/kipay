<div
    class="fixed inset-0 z-50 overflow-hidden bg-slate-950 text-white"
    style="padding-top:env(safe-area-inset-top,0px); padding-bottom:env(safe-area-inset-bottom,0px);"
>
    {{-- ================= STEP 1 & 2 — HEADER + FORM ================= --}}
    @if ($step === 1 || $step === 2)
        <div class="relative flex h-full flex-col bg-gradient-to-b from-blue-800 via-blue-700 to-slate-950">
            <div class="pointer-events-none absolute -top-16 -right-16 h-56 w-56 rounded-full bg-emerald-400/20 blur-3xl"></div>
            <div class="pointer-events-none absolute -bottom-24 -left-10 h-64 w-64 rounded-full bg-blue-400/10 blur-3xl"></div>

            <div class="relative flex items-center justify-between px-4 pt-4">
                @if ($step === 2)
                    <button type="button" wire:click="$set('step', 1)" class="grid h-10 w-10 place-items-center rounded-full bg-white/10 backdrop-blur transition active:scale-90">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                    </button>
                @else
                    <a href="{{ route('dashboard') }}" wire:navigate class="grid h-10 w-10 place-items-center rounded-full bg-white/10 backdrop-blur transition active:scale-90">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                    </a>
                @endif
                <p class="text-sm font-semibold tracking-wide">Transfer Saldo</p>
                <div class="h-10 w-10"></div>
            </div>

            {{-- ---- STEP 1: cari nomor WA ---- --}}
            @if ($step === 1)
                <div class="relative mt-8 flex flex-1 flex-col items-center px-6 text-center">
                    <div class="grid h-16 w-16 place-items-center rounded-full bg-white/10 shadow-inner">
                        <svg viewBox="0 0 24 24" class="h-8 w-8 text-emerald-300" fill="none" stroke="currentColor" stroke-width="1.6"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0ZM4 21a8 8 0 0 1 16 0" /></svg>
                    </div>
                    <h1 class="mt-4 text-lg font-bold">Kirim ke siapa?</h1>
                    <p class="mt-1 text-sm text-blue-100/80">Masukkan nomor WhatsApp tujuan transfer</p>
                </div>

                <form wire:submit="checkUser" class="relative flex flex-1 flex-col overflow-hidden rounded-t-[32px] bg-white px-6 pb-6 pt-7 text-slate-900 shadow-[0_-20px_50px_rgba(2,6,23,0.35)]">
                    <div class="mx-auto mb-5 h-1.5 w-10 rounded-full bg-slate-200"></div>

                    <div wire:loading.flex wire:target="checkUser" class="absolute inset-0 z-20 hidden flex-col items-center justify-center gap-4 rounded-t-[32px] bg-white/95 backdrop-blur-sm">
                        <div class="relative h-14 w-14">
                            <div class="absolute inset-0 rounded-full border-4 border-slate-100"></div>
                            <div class="absolute inset-0 rounded-full border-4 border-emerald-500 border-t-transparent animate-spin"></div>
                        </div>
                        <p class="text-sm font-semibold text-slate-700">Mencari pengguna...</p>
                    </div>

                    @if ($errorMessage)
                        <div class="mb-4 rounded-xl bg-red-50 p-3 text-center text-xs font-medium text-red-600" style="animation: kipay-shake .4s ease-in-out;">{{ $errorMessage }}</div>
                    @endif

                    <label class="text-xs font-semibold text-slate-400">Nomor WhatsApp</label>
                    <div class="mt-1.5 flex items-center gap-2 rounded-2xl border-2 border-slate-100 bg-slate-50/60 px-4 py-3.5 transition focus-within:border-emerald-400 focus-within:bg-emerald-50/40">
                        <svg viewBox="0 0 24 24" class="h-5 w-5 shrink-0 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.6"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 0 1 2-2h2.28a1 1 0 0 1 .97.76l1 4a1 1 0 0 1-.5 1.11L7 10.5a11 11 0 0 0 6.5 6.5l1.63-1.75a1 1 0 0 1 1.11-.5l4 1a1 1 0 0 1 .76.97V19a2 2 0 0 1-2 2h-1C10.4 21 3 13.6 3 4.5Z" /></svg>
                        <input type="text" inputmode="numeric" wire:model="whatsapp" placeholder="0812xxxxxxx" class="w-full border-0 bg-transparent p-0 text-lg font-bold text-slate-900 outline-none placeholder:text-slate-300" autofocus required>
                    </div>
                    @error('whatsapp') <p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p> @enderror

                    <div class="flex-1"></div>

                    <button type="submit" class="mt-6 w-full rounded-2xl bg-gradient-to-br from-blue-600 to-emerald-500 py-4 text-center text-sm font-bold text-white shadow-lg shadow-emerald-900/20 transition active:scale-[0.98] disabled:opacity-50" wire:loading.attr="disabled" wire:target="checkUser">
                        Cari Pengguna
                    </button>
                </form>
            @endif

            {{-- ---- STEP 2: konfirmasi nominal & PIN ---- --}}
            @if ($step === 2 && $targetUser)
                <div class="relative mt-3 flex items-center gap-3 px-6">
                    <div class="grid h-14 w-14 shrink-0 place-items-center rounded-full bg-white text-lg font-bold text-blue-700 shadow-lg ring-4 ring-white/25">
                        {{ strtoupper(mb_substr($targetUser->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="truncate text-lg font-bold">{{ $targetUser->name }}</p>
                        <p class="truncate text-xs text-blue-100/80">{{ $targetUser->whatsapp }}</p>
                    </div>
                </div>

                <form wire:submit="processTransfer" class="relative mt-4 flex flex-1 flex-col overflow-hidden rounded-t-[32px] bg-white px-6 pb-6 pt-7 text-slate-900 shadow-[0_-20px_50px_rgba(2,6,23,0.35)]">
                    <div class="mx-auto mb-5 h-1.5 w-10 rounded-full bg-slate-200"></div>

                    <div wire:loading.flex wire:target="processTransfer" class="absolute inset-0 z-20 hidden flex-col items-center justify-center gap-4 rounded-t-[32px] bg-white/95 backdrop-blur-sm">
                        <div class="relative h-16 w-16">
                            <div class="absolute inset-0 rounded-full border-4 border-slate-100"></div>
                            <div class="absolute inset-0 rounded-full border-4 border-emerald-500 border-t-transparent animate-spin"></div>
                            <div class="absolute inset-0 grid place-items-center">
                                <svg viewBox="0 0 24 24" class="h-6 w-6 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3" /></svg>
                            </div>
                        </div>
                        <div class="text-center">
                            <p class="text-sm font-semibold text-slate-800">Memproses transfer...</p>
                            <p class="mt-0.5 text-xs text-slate-400">Mohon tunggu, jangan tutup halaman ini</p>
                        </div>
                    </div>

                    @if ($errorMessage)
                        <div class="mb-4 rounded-xl bg-red-50 p-3 text-center text-xs font-medium text-red-600" style="animation: kipay-shake .4s ease-in-out;">{{ $errorMessage }}</div>
                    @endif

                    <div class="flex-1 space-y-5 overflow-y-auto">
                        <div>
                            <label class="text-xs font-semibold text-slate-400">Nominal</label>
                            <div class="mt-1.5 flex items-end gap-1.5 rounded-2xl border-2 border-slate-100 bg-slate-50/60 px-3 py-2.5 transition focus-within:border-emerald-400 focus-within:bg-emerald-50/40">
                                <span class="pb-1 text-xl font-bold text-slate-400">Rp</span>
                                <input type="number" min="1000" inputmode="numeric" wire:model="amount" placeholder="0" class="w-full border-0 bg-transparent p-0 text-3xl font-extrabold tracking-tight text-slate-900 outline-none placeholder:text-slate-300 tabular-nums" autofocus>
                            </div>
                            @error('amount') <p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="text-xs font-semibold text-slate-400">PIN Transaksi</label>
                            <div x-data="window.kipayPinPad()">
                                <div class="mt-2 flex justify-center gap-2.5">
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
                                <input type="hidden" wire:model="pin" x-ref="pinField">
                            </div>
                            @error('pin') <p class="mt-2 text-center text-xs font-medium text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <button type="submit" class="mt-5 w-full rounded-2xl bg-gradient-to-br from-blue-600 to-emerald-500 py-4 text-center text-sm font-bold text-white shadow-lg shadow-emerald-900/20 transition active:scale-[0.98] disabled:opacity-50" wire:loading.attr="disabled" wire:target="processTransfer">
                        Kirim Sekarang
                    </button>
                </form>
            @endif
        </div>
    @endif

    {{-- ================= STEP 3 — BERHASIL ================= --}}
    @if ($step === 3)
        <div
            x-data="{ seconds: 5 }"
            x-init="const t = setInterval(() => { seconds--; if (seconds <= 0) { clearInterval(t); window.location.href = @js(route('dashboard')); } }, 1000)"
            class="relative flex h-full flex-col items-center justify-center overflow-hidden bg-slate-950 px-8 text-center"
        >
            <div class="absolute -top-24 left-1/2 h-72 w-72 -translate-x-1/2 rounded-full bg-emerald-500/10 blur-3xl"></div>

            <div class="relative grid h-24 w-24 place-items-center">
                <span class="absolute inset-0 rounded-full bg-emerald-400/20" style="animation: kipay-ping 1.8s cubic-bezier(0,0,0.2,1) infinite;"></span>
                <span class="absolute inset-2 rounded-full bg-emerald-400/20" style="animation: kipay-ping 1.8s cubic-bezier(0,0,0.2,1) infinite; animation-delay: .3s;"></span>
                <div class="relative grid h-16 w-16 place-items-center rounded-full bg-gradient-to-br from-emerald-400 to-emerald-500 shadow-lg shadow-emerald-900/40" style="animation: kipay-pop .45s ease-out;">
                    <svg viewBox="0 0 24 24" class="h-8 w-8 text-white" fill="none" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                </div>
            </div>

            <p class="relative mt-6 text-sm text-slate-400">Transfer berhasil</p>
            <p class="relative mt-1 text-4xl font-extrabold tabular-nums">Rp {{ number_format((int) $amount, 0, ',', '.') }}</p>
            <p class="relative mt-1 text-sm text-slate-400">kepada <span class="font-semibold text-slate-200">{{ $targetUser->name ?? '' }}</span></p>

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

            <a href="{{ route('dashboard') }}" wire:navigate class="relative mt-8 block w-full rounded-2xl bg-gradient-to-br from-blue-600 to-emerald-500 py-4 text-sm font-bold shadow-lg shadow-emerald-900/20 transition active:scale-[0.98]">
                Kembali ke Dashboard
            </a>
            <p class="relative mt-3 text-xs text-slate-500">Otomatis kembali dalam <span x-text="seconds"></span> detik</p>
        </div>
    @endif

    <style>
        @keyframes kipay-pop {
            0%   { transform: scale(0.6); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }
        @keyframes kipay-ping {
            0%   { transform: scale(1); opacity: .55; }
            100% { transform: scale(1.9); opacity: 0; }
        }
        @keyframes kipay-shake {
            0%, 100% { transform: translateX(0); }
            20%      { transform: translateX(-6px); }
            40%      { transform: translateX(6px); }
            60%      { transform: translateX(-4px); }
            80%      { transform: translateX(4px); }
        }
        [x-cloak] { display: none !important; }
    </style>

    @script
        <script>
            if (!window.kipayPinPad) {
                window.kipayPinPad = function kipayPinPad() {
                    return {
                        digits: Array(6).fill(''),
                        syncHidden() {
                            const field = this.$refs.pinField;
                            if (!field) return;
                            field.value = this.digits.join('');
                            field.dispatchEvent(new Event('input', { bubbles: true }));
                        },
                        onInput(index, event) {
                            const value = event.target.value.replace(/\D/g, '').slice(-1);
                            this.digits[index] = value;
                            event.target.value = value;
                            this.syncHidden();
                            if (value) {
                                const next = event.target.nextElementSibling;
                                if (next && next.tagName === 'INPUT') next.focus();
                            }
                        },
                        onBackspace(index, event) {
                            if (this.digits[index]) return;
                            const prev = event.target.previousElementSibling;
                            if (prev && prev.tagName === 'INPUT') {
                                this.digits[index - 1] = '';
                                prev.focus();
                            }
                            this.syncHidden();
                        }
                    };
                };
            }
        </script>
    @endscript
</div>
