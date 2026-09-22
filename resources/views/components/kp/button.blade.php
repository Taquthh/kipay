@props([
    'target',
    'variant' => 'primary',
    'type' => 'submit',
])

@php
    $styles = match ($variant) {
        'success' => 'bg-gradient-to-r from-emerald-500 to-green-500 text-white shadow-lg shadow-emerald-500/30 hover:from-emerald-600 hover:to-green-600 focus-visible:ring-emerald-300',
        'ghost'   => 'bg-slate-100 text-slate-700 hover:bg-slate-200 focus-visible:ring-slate-300',
        default   => 'bg-gradient-to-r from-blue-600 to-blue-500 text-white shadow-lg shadow-blue-500/30 hover:from-blue-700 hover:to-blue-600 focus-visible:ring-blue-300',
    };
@endphp

<button
    type="{{ $type }}"
    wire:loading.attr="disabled"
    wire:target="{{ $target }}"
    {{ $attributes->class([
        'flex h-14 w-full items-center justify-center gap-2 rounded-2xl text-base font-bold transition',
        'focus:outline-none focus-visible:ring-4 active:scale-[0.98]',
        'disabled:cursor-not-allowed disabled:opacity-70',
        $styles,
    ]) }}
>
    <svg wire:loading wire:target="{{ $target }}" class="h-5 w-5 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
    </svg>
    <span>{{ $slot }}</span>
</button>
