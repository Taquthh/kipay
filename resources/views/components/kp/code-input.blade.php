{{--
    Input kode 6 kotak (OTP / PIN).
    Satu <input> asli transparan berada di atas kotak, sehingga:
    keyboard numerik muncul, paste & autofill OTP dari SMS/WA tetap bekerja.

    Contoh:
    <x-kp.code-input wire:model="otp" submit="verifyOtp" autocomplete="one-time-code" :autofocus="true" />
    <x-kp.code-input wire:model="pin" :masked="true" label="Buat PIN (6 digit)" />
--}}
@props([
    'masked' => false,
    'submit' => null,
    'length' => 6,
    'autocomplete' => 'off',
    'label' => null,
    'autofocus' => false,
])

@php
    $model = $attributes->wire('model')->value();
@endphp

<div
    class="w-full"
    x-data="{ code: $wire.$entangle('{{ $model }}'), focused: false, len: {{ $length }} }"
    @if ($autofocus) x-init="$nextTick(() => $refs.field.focus())" @endif
>
    @if ($label)
        <label class="mb-2 block text-sm font-semibold text-slate-700">{{ $label }}</label>
    @endif

    <div class="relative">
        <input
            x-ref="field"
            x-model="code"
            type="{{ $masked ? 'password' : 'text' }}"
            inputmode="numeric"
            pattern="[0-9]*"
            maxlength="{{ $length }}"
            autocomplete="{{ $autocomplete }}"
            enterkeyhint="done"
            aria-label="{{ $label ?? 'Kode ' . $length . ' digit' }}"
            @focus="focused = true"
            @blur="focused = false"
            @input="
                code = (code ?? '').replace(/\D/g, '').slice(0, len);
                @if ($submit) if (code.length === len) $nextTick(() => $wire.{{ $submit }}()); @endif
            "
            class="absolute inset-0 z-10 h-full w-full cursor-pointer opacity-0"
        >

        <div class="grid gap-2 sm:gap-3" style="grid-template-columns: repeat({{ $length }}, minmax(0, 1fr))" aria-hidden="true">
            @for ($i = 0; $i < $length; $i++)
                <div
                    class="flex h-14 items-center justify-center rounded-2xl border-2 text-2xl font-bold text-slate-900 transition sm:h-16"
                    :class="focused && (code || '').length === {{ $i }}
                        ? 'border-blue-500 bg-white ring-4 ring-blue-100'
                        : ((code || '').length > {{ $i }} ? 'border-emerald-400 bg-emerald-50' : 'border-slate-200 bg-slate-50')"
                >
                    @if ($masked)
                        <span x-show="(code || '').length > {{ $i }}" class="h-3 w-3 rounded-full bg-slate-800"></span>
                    @else
                        <span x-text="(code || '')[{{ $i }}] ?? ''"></span>
                    @endif
                </div>
            @endfor
        </div>
    </div>
</div>
