<div class="relative flex min-h-[100dvh] flex-col overflow-hidden bg-gradient-to-br from-blue-700 via-blue-600 to-emerald-500 sm:items-center sm:justify-center sm:p-6">

    {{-- Dekorasi latar --}}
    <div class="pointer-events-none absolute -left-24 -top-24 h-72 w-72 rounded-full bg-white/10 blur-2xl"></div>
    <div class="pointer-events-none absolute -bottom-32 -right-20 h-80 w-80 rounded-full bg-emerald-300/25 blur-3xl"></div>

    <div class="relative flex w-full flex-1 flex-col sm:max-w-md sm:flex-none">

        {{-- Brand --}}
        <header class="px-6 pb-8 pt-[max(2.5rem,env(safe-area-inset-top))] text-center text-white sm:px-0 sm:pt-0">
            <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-white/15 ring-1 ring-white/30 backdrop-blur">
                <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 00-2.25-2.25H15a3 3 0 11-6 0H5.25A2.25 2.25 0 003 12m18 0v6a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 18v-6m18 0V9M3 12V9m18 0a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 9m18 0V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v3"/>
                </svg>
            </div>
            <h1 class="text-3xl font-extrabold tracking-tight">Kipay</h1>
            <p class="mt-1 text-sm text-blue-50/90">Dompet Digital Masa Depan</p>
        </header>

        {{-- Kartu utama: bottom-sheet penuh di mobile, kartu di tengah pada layar lebar --}}
        <main class="flex flex-1 flex-col rounded-t-[2rem] bg-white px-6 pb-[max(1.5rem,env(safe-area-inset-bottom))] pt-7 shadow-2xl shadow-blue-950/30 sm:flex-none sm:rounded-3xl sm:px-8 sm:pb-8">

            {{-- Tombol kembali (langkah PIN & OTP) --}}
            @if (in_array($step, [2, 3], true))
                <button type="button" wire:click="back"
                        class="-ml-1 mb-4 inline-flex items-center gap-1 self-start rounded-lg px-1 py-1 text-sm font-semibold text-slate-500 transition hover:text-blue-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-300">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                    Ganti nomor
                </button>
            @endif

            {{-- Indikator progres (pendaftaran / lupa PIN) --}}
            @php $progress = match (true) { $step === 3 => 2, $step >= 4 => 3, default => 0 }; @endphp
            @if ($progress)
                <div class="mb-6" role="progressbar" aria-valuemin="1" aria-valuemax="3" aria-valuenow="{{ $progress }}">
                    <div class="mb-2 flex items-center justify-between text-xs font-semibold text-slate-500">
                        <span>{{ $purpose === 'reset' ? 'Atur ulang PIN' : 'Pendaftaran akun' }}</span>
                        <span>Langkah {{ $progress }} dari 3</span>
                    </div>
                    <div class="grid grid-cols-3 gap-1.5">
                        @for ($i = 1; $i <= 3; $i++)
                            <div class="h-1.5 rounded-full {{ $i <= $progress ? 'bg-gradient-to-r from-blue-500 to-emerald-500' : 'bg-slate-200' }}"></div>
                        @endfor
                    </div>
                </div>
            @endif

            {{-- Pesan error --}}
            @if ($errorMessage)
                <div role="alert" class="kp-step mb-5 flex items-start gap-2.5 rounded-2xl border border-red-100 bg-red-50 p-4 text-sm text-red-700">
                    <svg class="mt-0.5 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ $errorMessage }}</span>
                </div>
            @endif

            {{-- ═════════════ LANGKAH 1: NOMOR WHATSAPP ═════════════ --}}
            @if ($step === 1)
                <div wire:key="step-1" class="kp-step flex flex-col">
                    <div class="mb-6">
                        <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">Masuk atau daftar</h2>
                        <p class="mt-1 text-sm text-slate-500">Masukkan nomor WhatsApp aktif Anda untuk melanjutkan.</p>
                    </div>

                    <form wire:submit="checkWhatsapp" class="flex flex-col gap-5">
                        <div>
                            <label for="whatsapp" class="mb-2 block text-sm font-semibold text-slate-700">Nomor WhatsApp</label>
                            <div class="relative">
                                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                </span>
                                <input id="whatsapp" type="tel" inputmode="tel" autocomplete="tel" wire:model="whatsapp"
                                       placeholder="08123456789"
                                       class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 pl-12 pr-4 text-base font-medium text-slate-900 placeholder:text-slate-400 transition focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-100">
                            </div>
                            @error('whatsapp') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <x-kp.button target="checkWhatsapp">Lanjutkan</x-kp.button>
                    </form>
                </div>
            @endif

            {{-- ═════════════ LANGKAH 2: LOGIN PIN ═════════════ --}}
            @if ($step === 2)
                <div wire:key="step-2" class="kp-step flex flex-col">
                    <div class="mb-7">
                        <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">Selamat datang kembali</h2>
                        <p class="mt-1 text-sm text-slate-500">Masukkan PIN untuk masuk sebagai <span class="font-bold text-slate-800">{{ $formattedPhone }}</span></p>
                    </div>

                    <form wire:submit="login" class="flex flex-col gap-6">
                        <div>
                            <x-kp.code-input wire:model="pin" :masked="true" submit="login" label="PIN" :autofocus="true" />
                            @error('pin') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <x-kp.button target="login">Masuk</x-kp.button>
                    </form>

                    <button type="button" wire:click="startPinReset"
                            class="mt-5 self-center rounded-lg px-2 py-1 text-sm font-semibold text-blue-600 transition hover:text-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-300">
                        Lupa PIN?
                    </button>
                </div>
            @endif

            {{-- ═════════════ LANGKAH 3: OTP ═════════════ --}}
            @if ($step === 3)
                <div wire:key="step-3" class="kp-step flex flex-col">
                    <div class="mb-7 text-center">
                        <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        </div>
                        <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">Verifikasi WhatsApp</h2>
                        <p class="mt-1 text-sm text-slate-500">Kode 6 digit dikirim ke</p>
                        <p class="font-bold text-slate-900">{{ $formattedPhone }}</p>
                    </div>

                    <form wire:submit="verifyOtp" class="flex flex-col gap-6">
                        <div>
                            <x-kp.code-input wire:model="otp" submit="verifyOtp" autocomplete="one-time-code" label="Kode OTP" :autofocus="true" />
                            @error('otp') <p class="mt-2 text-center text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <x-kp.button target="verifyOtp">Verifikasi</x-kp.button>
                    </form>

                    {{-- Hitung mundur kirim ulang (elemen dibuat ulang tiap OTP baru lewat wire:key) --}}
                    <div wire:key="resend-{{ $resendAvailableAt }}"
                         x-data="{ s: {{ $resendIn }}, t: null,
                                   init() { this.t = setInterval(() => { if (this.s > 0) this.s--; else clearInterval(this.t) }, 1000) },
                                   destroy() { clearInterval(this.t) } }"
                         class="mt-6 text-center text-sm text-slate-500">
                        <p x-show="s > 0">Kirim ulang kode dalam <span class="font-bold text-slate-700" x-text="s"></span> detik</p>
                        <button type="button" x-show="s <= 0" x-cloak wire:click="resendOtp"
                                class="rounded-lg px-2 py-1 font-semibold text-blue-600 transition hover:text-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-300">
                            Kirim ulang kode
                        </button>
                    </div>
                </div>
            @endif

            {{-- ═════════════ LANGKAH 4: PROFIL & PIN ═════════════ --}}
            @if ($step === 4)
                <div wire:key="step-4" class="kp-step flex flex-col">
                    <div class="mb-6">
                        <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">Lengkapi profil</h2>
                        <p class="mt-1 text-sm text-slate-500">Langkah terakhir untuk mulai menggunakan Kipay.</p>
                    </div>

                    <form wire:submit="promptFinalRegister" class="flex flex-col gap-6">
                        <div>
                            <label for="name" class="mb-2 block text-sm font-semibold text-slate-700">Nama lengkap</label>
                            <input id="name" type="text" wire:model="name" autocomplete="name" autocapitalize="words" placeholder="Sesuai identitas Anda"
                                   class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-base font-medium text-slate-900 placeholder:text-slate-400 transition focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-100">
                            @error('name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <x-kp.code-input wire:model="pin" :masked="true" label="Buat PIN (6 digit)" />
                            @error('pin') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <x-kp.code-input wire:model="pinConfirmation" :masked="true" label="Ulangi PIN" />
                            @error('pinConfirmation') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <p class="text-xs leading-relaxed text-slate-500">PIN dipakai untuk masuk dan mengamankan transaksi. Jangan bagikan ke siapa pun.</p>

                        <x-kp.button target="promptFinalRegister">Lanjutkan</x-kp.button>
                    </form>
                </div>
            @endif

            {{-- ═════════════ LANGKAH 5: PIN BARU (LUPA PIN) ═════════════ --}}
            @if ($step === 5)
                <div wire:key="step-5" class="kp-step flex flex-col">
                    <div class="mb-6">
                        <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">Buat PIN baru</h2>
                        <p class="mt-1 text-sm text-slate-500">Nomor Anda sudah terverifikasi. Tentukan PIN baru untuk akun ini.</p>
                    </div>

                    <form wire:submit="resetPin" class="flex flex-col gap-6">
                        <div>
                            <x-kp.code-input wire:model="pin" :masked="true" label="PIN baru (6 digit)" :autofocus="true" />
                            @error('pin') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <x-kp.code-input wire:model="pinConfirmation" :masked="true" label="Ulangi PIN baru" />
                            @error('pinConfirmation') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <x-kp.button target="resetPin" variant="success">Simpan PIN &amp; masuk</x-kp.button>
                    </form>
                </div>
            @endif

            {{-- Catatan keamanan --}}
            <p class="mt-auto flex items-center justify-center gap-1.5 pt-8 text-center text-xs text-slate-400">
                <svg class="h-4 w-4 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                Akun diamankan dengan PIN dan verifikasi WhatsApp
            </p>
        </main>
    </div>

    {{-- ═════════════ MODAL 1: NOMOR BELUM TERDAFTAR ═════════════ --}}
    @if ($showRegisterModal)
        <div class="fixed inset-0 z-50 flex items-end justify-center sm:items-center sm:p-4"
             role="dialog" aria-modal="true" aria-labelledby="modal-register-title"
             x-data @keydown.escape.window="$wire.cancelRegister()">
            <div class="kp-fade absolute inset-0 bg-slate-900/50 backdrop-blur-sm" wire:click="cancelRegister"></div>

            <div class="kp-sheet relative w-full rounded-t-3xl bg-white px-6 pb-[max(1.5rem,env(safe-area-inset-bottom))] pt-3 shadow-2xl sm:max-w-sm sm:rounded-3xl sm:pb-6">
                <div class="mx-auto mb-5 h-1.5 w-10 rounded-full bg-slate-200 sm:hidden"></div>

                <div class="mb-4 flex justify-center">
                    <div class="rounded-full border border-blue-100 bg-blue-50 p-4 text-blue-600">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>

                <h3 id="modal-register-title" class="mb-2 text-center text-xl font-bold text-slate-900">Nomor belum terdaftar</h3>
                <p class="mb-6 text-center text-sm leading-relaxed text-slate-500">
                    <span class="font-bold text-slate-800">{{ $formattedPhone }}</span> belum terdaftar di Kipay. Buat akun baru dengan nomor ini? Kami akan mengirim kode OTP lewat WhatsApp.
                </p>

                <div class="flex flex-col gap-3 sm:flex-row-reverse">
                    <x-kp.button type="button" target="confirmRegister" wire:click="confirmRegister" class="sm:w-1/2">Ya, daftar</x-kp.button>
                    <x-kp.button type="button" target="cancelRegister" variant="ghost" wire:click="cancelRegister" class="sm:w-1/2">Batal</x-kp.button>
                </div>
            </div>
        </div>
    @endif

    {{-- ═════════════ MODAL 2: KONFIRMASI PENDAFTARAN ═════════════ --}}
    @if ($showConfirmSubmitModal)
        <div class="fixed inset-0 z-50 flex items-end justify-center sm:items-center sm:p-4"
             role="dialog" aria-modal="true" aria-labelledby="modal-confirm-title"
             x-data @keydown.escape.window="$wire.cancelFinalRegister()">
            <div class="kp-fade absolute inset-0 bg-slate-900/50 backdrop-blur-sm" wire:click="cancelFinalRegister"></div>

            <div class="kp-sheet relative w-full rounded-t-3xl bg-white px-6 pb-[max(1.5rem,env(safe-area-inset-bottom))] pt-3 shadow-2xl sm:max-w-sm sm:rounded-3xl sm:pb-6">
                <div class="mx-auto mb-5 h-1.5 w-10 rounded-full bg-slate-200 sm:hidden"></div>

                <div class="mb-4 flex justify-center">
                    <div class="rounded-full border border-emerald-100 bg-emerald-50 p-4 text-emerald-600">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>

                <h3 id="modal-confirm-title" class="mb-2 text-center text-xl font-bold text-slate-900">Buat akun sekarang?</h3>
                <p class="mb-4 text-center text-sm leading-relaxed text-slate-500">Pastikan data berikut sudah benar. PIN tidak bisa dilihat kembali.</p>

                <dl class="mb-6 divide-y divide-slate-100 rounded-2xl bg-slate-50 px-4 text-sm">
                    <div class="flex justify-between gap-4 py-3"><dt class="text-slate-500">Nama</dt><dd class="text-right font-semibold text-slate-900">{{ $name }}</dd></div>
                    <div class="flex justify-between gap-4 py-3"><dt class="text-slate-500">WhatsApp</dt><dd class="text-right font-semibold text-slate-900">{{ $formattedPhone }}</dd></div>
                </dl>

                <div class="flex flex-col gap-3 sm:flex-row-reverse">
                    <x-kp.button type="button" target="register" variant="success" wire:click="register" class="sm:w-1/2">Buat akun</x-kp.button>
                    <x-kp.button type="button" target="cancelFinalRegister" variant="ghost" wire:click="cancelFinalRegister" class="sm:w-1/2">Periksa lagi</x-kp.button>
                </div>
            </div>
        </div>
    @endif

    <style>
        [x-cloak] { display: none !important; }
        @keyframes kp-fade  { from { opacity: 0 } to { opacity: 1 } }
        @keyframes kp-rise  { from { opacity: 0; transform: translateY(8px) } to { opacity: 1; transform: none } }
        @keyframes kp-sheet { from { transform: translateY(100%) } to { transform: none } }
        @keyframes kp-pop   { from { opacity: 0; transform: scale(.96) } to { opacity: 1; transform: none } }
        .kp-step  { animation: kp-rise .22s ease-out }
        .kp-fade  { animation: kp-fade .2s ease-out }
        .kp-sheet { animation: kp-sheet .28s cubic-bezier(.2, .8, .2, 1) }
        @media (min-width: 640px) { .kp-sheet { animation-name: kp-pop; animation-duration: .2s } }
        @media (prefers-reduced-motion: reduce) { .kp-step, .kp-fade, .kp-sheet { animation: none } }
    </style>
</div>
