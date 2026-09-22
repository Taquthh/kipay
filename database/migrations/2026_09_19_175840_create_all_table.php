<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Users

        // 2. Tabel Wallets (Dompet Saldo)
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('balance', 15, 2)->default(0.00); // Presisi nilai angka saldo
            $table->timestamps();
        });

        // 3. Tabel Merchants (Toko Pembuat Static QR)
        Schema::create('merchants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Pemilik merchant
            $table->string('merchant_name'); // Nama Toko / Merchant
            $table->string('qr_code_payload')->unique(); // Payload Unik Static QR (cth: MERCHANTPAY-MCH-001)
            $table->timestamps();
        });

        // 4. Tabel Bills (Tagihan QR Code Dinamis - Opsional jika ada nominal khusus)
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained()->onDelete('cascade');
            $table->string('bill_code')->unique();
            $table->decimal('amount', 15, 2);
            $table->string('description')->nullable();
            $table->enum('status', ['unpaid', 'paid', 'expired'])->default('unpaid');
            $table->timestamps();
        });

        // 5. Tabel Transactions (Riwayat Transaksi)
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('reference_id')->unique(); // Kode transaksi (cth: TRX-20260920-001)
            $table->foreignId('user_id')->constrained(); // Pembayar / Penerima topup
            $table->foreignId('merchant_id')->nullable()->constrained(); // Merchant penerima dana
            $table->foreignId('bill_id')->nullable()->constrained(); // Nullable jika menggunakan Static QR
            // Tambahkan 'receive' dan 'transfer' ke dalam enum
            $table->enum('type', ['topup', 'payment', 'receive', 'transfer']);
            $table->decimal('amount', 15, 2);
            $table->decimal('latest_balance', 15, 2); // Catatan saldo terakhir pengguna setelah transaksi
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('bills');
        Schema::dropIfExists('merchants');
        Schema::dropIfExists('wallets');
        Schema::dropIfExists('users');
    }
};
