<div class="max-w-md mx-auto mt-10 p-6 bg-white rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6 border-b pb-4">
        <h1 class="text-xl font-bold text-gray-800">Transfer Saldo</h1>
        <a href="{{ route('dashboard') }}" class="text-blue-500 hover:underline text-sm">Batal</a>
    </div>

    @if($errorMessage)
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm font-bold">
            {{ $errorMessage }}
        </div>
    @endif

    @if($step == 1)
        <form wire:submit="checkUser">
            <label class="block text-gray-700 font-bold mb-2">Nomor WA Tujuan</label>
            <input type="text" wire:model="whatsapp" placeholder="Contoh: 081234..." class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 mb-4" required>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 font-bold">Cari Pengguna</button>
        </form>
    @endif

    @if($step == 2)
        <div class="bg-blue-50 border border-blue-200 p-4 rounded-lg mb-6 text-center">
            <p class="text-sm text-gray-500 mb-1">Transfer kepada:</p>
            <h2 class="text-2xl font-bold text-blue-700">{{ $targetUser->name }}</h2>
            <p class="text-xs text-gray-400">{{ $targetUser->whatsapp }}</p>
        </div>
        <form wire:submit="processTransfer">
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Nominal (Rp)</label>
                <input type="number" wire:model="amount" placeholder="Min. 1000" class="w-full px-4 py-3 border rounded-lg text-xl font-bold focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 font-bold mb-2">PIN Transaksi</label>
                <input type="password" wire:model="pin" maxlength="6" class="w-full px-4 py-3 border rounded-lg text-center tracking-widest text-2xl focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <button type="submit" class="w-full bg-green-500 text-white py-3 rounded-lg hover:bg-green-600 font-bold">Kirim Sekarang</button>
        </form>
    @endif

    @if($step == 3)
        <div class="text-center py-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Transfer Berhasil!</h2>
            <p class="text-gray-500 mb-6">Dana terkirim ke <strong>{{ $targetUser->name }}</strong>.</p>
            <a href="{{ route('dashboard') }}" class="inline-block w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 font-bold">Kembali ke Dashboard</a>
        </div>
    @endif
</div>
