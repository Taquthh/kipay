<div class="max-w-md mx-auto mt-10 p-6 bg-white rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6 border-b pb-4">
        <h1 class="text-xl font-bold text-gray-800">Bayar via QR</h1>
        <a href="{{ route('dashboard') }}" class="text-blue-500 hover:underline text-sm">Batal</a>
    </div>

    @if($errorMessage)
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm font-bold">
            {{ $errorMessage }}
        </div>
    @endif

    <!-- STEP 1: Simulasi Scan QR -->
    @if($step == 1)
        <div class="text-center">
            <div class="bg-gray-100 p-8 rounded-lg mb-4 border-2 border-dashed border-gray-300">
                <p class="text-gray-500 text-sm mb-2">Simulasi Kamera Scanner</p>
                <p class="text-xs text-gray-400">Masukkan teks Payload Data dari QR Merchant di sini</p>
            </div>

            <form wire:submit="scan">
                <input type="text" wire:model="payload" placeholder="Contoh: KIPAY-MCH-1-..." class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 mb-4" required>
                <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 font-bold">
                    Proses Scan
                </button>
            </form>
        </div>
    @endif

    <!-- STEP 2: Input Nominal & PIN -->
    @if($step == 2)
        <div>
            <div class="bg-blue-50 border border-blue-200 p-4 rounded-lg mb-6 text-center">
                <p class="text-sm text-gray-500 mb-1">Membayar ke Merchant:</p>
                <h2 class="text-2xl font-bold text-blue-700">{{ $merchant->merchant_name }}</h2>
            </div>

            <form wire:submit="pay">
                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2">Nominal Bayar (Rp)</label>
                    <input type="number" wire:model="amount" placeholder="Min. 1000" class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-xl font-bold" required>
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700 font-bold mb-2">PIN Transaksi</label>
                    <input type="password" wire:model="pin" maxlength="6" class="w-full px-4 py-3 border rounded-lg text-center tracking-widest text-2xl focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <button type="submit" class="w-full bg-green-500 text-white py-3 rounded-lg hover:bg-green-600 font-bold text-lg shadow-lg">
                    Konfirmasi Pembayaran
                </button>
            </form>
        </div>
    @endif

    <!-- STEP 3: Sukses -->
    @if($step == 3)
        <div class="text-center py-6">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 mb-4">
                <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Pembayaran Berhasil!</h2>
            <p class="text-gray-500 mb-6">Dana telah diteruskan ke merchant <strong>{{ $merchant->merchant_name }}</strong>.</p>
            <a href="{{ route('dashboard') }}" class="inline-block w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 font-bold">
                Kembali ke Dashboard
            </a>
        </div>
    @endif
</div>
