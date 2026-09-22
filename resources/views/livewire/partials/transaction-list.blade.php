@php
    $meta = [
        'topup'    => ['label' => 'Top Up',   'in' => true,  'bg' => 'bg-emerald-50',  'text' => 'text-emerald-600'],
        'receive'  => ['label' => 'Diterima', 'in' => true,  'bg' => 'bg-teal-50',     'text' => 'text-teal-600'],
        'transfer' => ['label' => 'Transfer', 'in' => false, 'bg' => 'bg-blue-50',     'text' => 'text-blue-600'],
        'payment'  => ['label' => 'Bayar QR', 'in' => false, 'bg' => 'bg-indigo-50',   'text' => 'text-indigo-600'],
    ];
@endphp

@if ($items->isEmpty())
    <div class="flex flex-col items-center gap-2 py-10 text-center">
        <span class="flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-slate-400">
            <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3M3.75 5.25h16.5c.83 0 1.5.67 1.5 1.5v10.5c0 .83-.67 1.5-1.5 1.5H3.75a1.5 1.5 0 01-1.5-1.5V6.75c0-.83.67-1.5 1.5-1.5z"/></svg>
        </span>
        <p class="text-sm font-medium text-slate-500">Belum ada transaksi</p>
        <p class="text-xs text-slate-400">Isi saldo atau terima transfer untuk memulai.</p>
    </div>
@else
    <ul class="divide-y divide-slate-100">
        @foreach ($items as $trx)
            @php $m = $meta[$trx->type] ?? ['label' => ucfirst($trx->type), 'in' => false, 'bg' => 'bg-slate-100', 'text' => 'text-slate-600']; @endphp
            <li class="flex items-center gap-3 py-3">
                <span class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full {{ $m['bg'] }} {{ $m['text'] }}">
                    @if ($m['in'])
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m0 0l6-6m-6 6l-6-6"/></svg>
                    @else
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19.5v-15m0 0l6 6m-6-6l-6 6"/></svg>
                    @endif
                </span>

                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-semibold text-slate-800">{{ $trx->description ?? $m['label'] }}</p>
                    <p class="text-xs text-slate-400">{{ $trx->created_at->format('d M Y • H:i') }}</p>
                </div>

                <div class="flex-shrink-0 text-right">
                    <p class="text-sm font-bold {{ $m['in'] ? 'text-emerald-600' : 'text-slate-800' }}">
                        {{ $m['in'] ? '+' : '-' }} Rp {{ number_format($trx->amount, 0, ',', '.') }}
                    </p>
                    <p class="text-[11px] text-slate-400">Sisa {{ number_format($trx->latest_balance, 0, ',', '.') }}</p>
                </div>
            </li>
        @endforeach
    </ul>
@endif
