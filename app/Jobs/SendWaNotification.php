<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendWaNotification implements ShouldQueue
{
    use Queueable;

    public $targetNumber;
    public $message;

    // Menerima data saat job dipanggil (di-dispatch)
    public function __construct($targetNumber, $message)
    {
        $this->targetNumber = $targetNumber;
        $this->message = $message;
    }

    // Dieksekusi oleh Queue Worker di latar belakang
    public function handle(): void
    {
        // Menggunakan API Token Fonnte Anda secara langsung
        $apiToken = 'yhnEw7NnGTwbBTwMDrex';

        try {
            // Tembakan HTTP request ke layanan API Fonnte
            $response = Http::withHeaders([
                'Authorization' => $apiToken,
            ])->post('https://api.fonnte.com/send', [
                'target' => $this->targetNumber,
                'message' => $this->message,
                'delay' => '2', // Delay 2 detik standar aman API
            ]);

            // Catat respons dari Fonnte ke log jika terjadi error pada API-nya
            if (!$response->successful()) {
                Log::error('Fonnte API Error: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('Gagal mengirim WA: ' . $e->getMessage());
        }
    }
}
