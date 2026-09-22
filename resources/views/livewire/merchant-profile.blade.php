<div class="max-w-xl mx-auto mt-10 p-6 bg-white rounded-lg shadow-md">

    <div class="flex justify-between items-center mb-6 border-b pb-4">
        <h1 class="text-2xl font-bold text-gray-800">Profil Merchant</h1>
        <a href="{{ route('dashboard') }}" class="text-blue-500 hover:underline">Kembali ke Dashboard</a>
    </div>

    @if(!$hasMerchant)
        <!-- Form Buat Merchant Baru -->
        <div class="text-center py-6">
            <h2 class="text-lg font-semibold mb-2">Anda belum memiliki toko.</h2>
            <p class="text-gray-500 mb-6">Daftarkan nama merchant Anda untuk mendapatkan QR Code Pembayaran.</p>

            <form wire:submit="createMerchant" class="max-w-sm mx-auto">
                <div class="mb-4">
                    <input type="text" wire:model="merchant_name" placeholder="Nama Toko / Merchant" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 font-bold">
                    Buat QR Code Toko
                </button>
            </form>
        </div>
    @else
        <!-- Tampilan QR Code -->
        <div class="text-center py-4">
            <h2 class="text-xl font-bold text-gray-800 mb-1">{{ $merchantData->merchant_name }}</h2>
            <p class="text-sm text-gray-500 mb-6">Tunjukkan QR Code ini kepada pelanggan untuk menerima pembayaran.</p>

            <!-- Render SVG QR Code -->
            <div class="flex justify-center mb-6">
                <div class="p-4 bg-white border-4 border-blue-100 rounded-xl inline-block shadow-sm">
                    {!! $qrCodeSvg !!}
                </div>
            </div>

            <div class="bg-gray-100 p-3 rounded text-sm text-gray-600 break-all">
                <strong>Payload Data:</strong><br>
                {{ $merchantData->qr_code_payload }}
            </div>
        </div>
    @endif

</div>
