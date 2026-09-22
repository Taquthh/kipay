<?php

namespace App\Livewire\Auth;

use App\Jobs\SendWaNotification;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Throwable;

class LoginRegister extends Component
{
    // ── Langkah (step) ────────────────────────────────────────────────
    private const STEP_PHONE     = 1; // input nomor WhatsApp
    private const STEP_PIN       = 2; // login dengan PIN
    private const STEP_OTP       = 3; // verifikasi OTP (daftar / reset PIN)
    private const STEP_PROFILE   = 4; // lengkapi profil + buat PIN
    private const STEP_RESET_PIN = 5; // buat PIN baru (lupa PIN)

    // ── Aturan keamanan ───────────────────────────────────────────────
    private const OTP_TTL           = 300;  // OTP berlaku 5 menit
    private const OTP_MAX_ATTEMPTS  = 5;    // salah input OTP maksimal
    private const OTP_COOLDOWN      = 60;   // jeda kirim ulang (detik)
    private const OTP_MAX_PER_HOUR  = 5;    // maksimal kirim OTP / jam / nomor
    private const VERIFIED_TTL      = 900;  // waktu menyelesaikan langkah setelah OTP valid
    private const LOGIN_MAX_TRIES   = 5;    // salah PIN maksimal
    private const LOGIN_LOCK_SECOND = 300;  // durasi kunci setelah salah PIN

    // ── State yang tidak boleh diubah dari browser ────────────────────
    #[Locked] public int $step = self::STEP_PHONE;
    #[Locked] public bool $isLogin = false;
    #[Locked] public string $phone = '';          // nomor ternormalisasi (628xxx)
    #[Locked] public string $purpose = 'register'; // register | reset
    #[Locked] public int $resendAvailableAt = 0;
    #[Locked] public bool $showRegisterModal = false;
    #[Locked] public bool $showConfirmSubmitModal = false;

    // ── Input dari pengguna ───────────────────────────────────────────
    public string $whatsapp = '';
    public string $pin = '';
    public string $pinConfirmation = '';
    public string $otp = '';
    public string $name = '';

    public string $errorMessage = '';

    /* ====================================================================
     |  LANGKAH 1 — Cek nomor WhatsApp
     * ==================================================================*/
    public function checkWhatsapp(): void
    {
        $this->resetFeedback();

        // Bersihkan spasi, tanda hubung, dsb. sebelum divalidasi
        $this->whatsapp = preg_replace('/[^\d+]/', '', $this->whatsapp) ?? '';

        $this->validate(['whatsapp' => ['required', 'regex:/^(\+?62|0)8\d{8,12}$/']]);

        $key = 'wa-check|' . request()->ip();
        if ($this->isThrottled($key, 10)) {
            return;
        }
        RateLimiter::hit($key, 60);

        $this->phone = $this->normalizePhone($this->whatsapp);

        if ($this->findUser($this->phone)) {
            $this->isLogin = true;
            $this->step = self::STEP_PIN;
            return;
        }

        // Nomor baru → minta konfirmasi dulu (OTP belum dikirim)
        $this->isLogin = false;
        $this->showRegisterModal = true;
    }

    public function cancelRegister(): void
    {
        $this->showRegisterModal = false;
    }

    public function confirmRegister(): void
    {
        $this->showRegisterModal = false;

        if ($this->step !== self::STEP_PHONE || $this->phone === '') {
            return;
        }

        // Jaga-jaga jika nomor ternyata sudah terdaftar
        if ($this->findUser($this->phone)) {
            $this->isLogin = true;
            $this->step = self::STEP_PIN;
            return;
        }

        $this->purpose = 'register';

        if ($this->issueOtp()) {
            $this->otp = '';
            $this->step = self::STEP_OTP;
        }
    }

    /* ====================================================================
     |  LANGKAH 2 — Login dengan PIN
     * ==================================================================*/
    public function login()
    {
        $this->resetFeedback();

        if ($this->step !== self::STEP_PIN) {
            return;
        }

        $this->validate(['pin' => ['required', 'digits:6']]);

        $user = $this->findUser($this->phone);
        if (! $user) {
            $this->restart('Akun tidak ditemukan. Silakan periksa nomor Anda.');
            return;
        }

        $key = 'login|' . $this->phone . '|' . request()->ip();
        if ($this->isThrottled($key, self::LOGIN_MAX_TRIES)) {
            $this->pin = '';
            return;
        }

        if (! Hash::check($this->pin, (string) $user->pin)) {
            RateLimiter::hit($key, self::LOGIN_LOCK_SECOND);
            $left = max(0, self::LOGIN_MAX_TRIES - RateLimiter::attempts($key));

            $this->pin = '';
            $this->errorMessage = $left > 0
                ? "PIN salah. Sisa percobaan: {$left}."
                : 'PIN salah terlalu banyak. Coba lagi dalam ' . $this->humanizeSeconds(RateLimiter::availableIn($key)) . ' atau gunakan "Lupa PIN".';
            return;
        }

        RateLimiter::clear($key);

        return $this->loginAndRedirect($user);
    }

    /* ====================================================================
     |  LUPA PIN — kirim OTP ke WhatsApp
     * ==================================================================*/
    public function startPinReset(): void
    {
        $this->resetFeedback();

        if ($this->step !== self::STEP_PIN || ! $this->findUser($this->phone)) {
            return;
        }

        $this->purpose = 'reset';
        $this->pin = '';

        if ($this->issueOtp()) {
            $this->otp = '';
            $this->step = self::STEP_OTP;
        }
    }

    /* ====================================================================
     |  LANGKAH 3 — OTP
     * ==================================================================*/
    public function resendOtp(): void
    {
        $this->resetFeedback();

        if ($this->step !== self::STEP_OTP) {
            return;
        }

        $this->otp = '';
        $this->sendOtp();
    }

    public function verifyOtp(): void
    {
        $this->resetFeedback();

        if ($this->step !== self::STEP_OTP) {
            return;
        }

        $this->validate(['otp' => ['required', 'digits:6']]);

        $challenge = session('otp_challenge');

        if (! $this->isChallengeActive($challenge)) {
            session()->forget('otp_challenge');
            $this->otp = '';
            $this->errorMessage = 'Kode OTP sudah kedaluwarsa. Silakan kirim ulang kode baru.';
            return;
        }

        if (! hash_equals($challenge['hash'], $this->hashOtp($this->otp))) {
            $challenge['attempts']++;
            session(['otp_challenge' => $challenge]);

            $left = self::OTP_MAX_ATTEMPTS - $challenge['attempts'];
            $this->otp = '';
            $this->errorMessage = $left > 0
                ? "Kode OTP tidak sesuai. Sisa percobaan: {$left}."
                : 'Terlalu banyak kode salah. Silakan kirim ulang kode baru.';
            return;
        }

        // OTP benar → tandai terverifikasi di sisi server (bukan di properti browser)
        session()->forget('otp_challenge');
        session(['otp_verified' => [
            'phone'      => $this->phone,
            'purpose'    => $this->purpose,
            'expires_at' => now()->timestamp + self::VERIFIED_TTL,
        ]]);

        $this->otp = '';
        $this->step = $this->purpose === 'reset' ? self::STEP_RESET_PIN : self::STEP_PROFILE;
    }

    /* ====================================================================
     |  LANGKAH 4 — Profil & PIN (pendaftaran)
     * ==================================================================*/
    public function promptFinalRegister(): void
    {
        $this->resetFeedback();

        if ($this->step !== self::STEP_PROFILE) {
            return;
        }

        if (! $this->isVerified()) {
            $this->restart('Sesi verifikasi berakhir. Silakan verifikasi ulang nomor Anda.');
            return;
        }

        $this->name = trim(preg_replace('/\s+/', ' ', $this->name) ?? '');
        $this->validate($this->profileRules());

        $this->showConfirmSubmitModal = true;
    }

    public function cancelFinalRegister(): void
    {
        $this->showConfirmSubmitModal = false;
    }

    public function register()
    {
        $this->showConfirmSubmitModal = false;

        if ($this->step !== self::STEP_PROFILE) {
            return;
        }

        if (! $this->isVerified()) {
            $this->restart('Sesi verifikasi berakhir. Silakan verifikasi ulang nomor Anda.');
            return;
        }

        // Validasi ulang: nilai bisa berubah antara langkah konfirmasi & submit
        $this->validate($this->profileRules());

        if ($this->findUser($this->phone)) {
            $this->restart('Nomor ini sudah terdaftar. Silakan masuk dengan PIN Anda.');
            return;
        }

        try {
            $user = DB::transaction(function () {
                $user = User::create([
                    'name'     => $this->name,
                    'whatsapp' => $this->phone,
                    'pin'      => Hash::make($this->pin),
                ]);

                Wallet::create([
                    'user_id' => $user->id,
                    'balance' => 0,
                ]);

                return $user;
            });
        } catch (Throwable $e) {
            report($e);
            $this->errorMessage = 'Pendaftaran belum berhasil diproses. Silakan coba lagi.';
            return;
        }

        session()->forget(['otp_challenge', 'otp_verified']);

        return $this->loginAndRedirect($user);
    }

    /* ====================================================================
     |  LANGKAH 5 — PIN baru (lupa PIN)
     * ==================================================================*/
    public function resetPin()
    {
        $this->resetFeedback();

        if ($this->step !== self::STEP_RESET_PIN) {
            return;
        }

        if (! $this->isVerified()) {
            $this->restart('Sesi verifikasi berakhir. Silakan ulangi proses lupa PIN.');
            return;
        }

        $this->validate([
            'pin'             => $this->pinRules(),
            'pinConfirmation' => ['required', 'same:pin'],
        ]);

        $user = $this->findUser($this->phone);
        if (! $user) {
            $this->restart('Akun tidak ditemukan.');
            return;
        }

        $user->forceFill(['pin' => Hash::make($this->pin)])->save();

        RateLimiter::clear('login|' . $this->phone . '|' . request()->ip());
        session()->forget(['otp_challenge', 'otp_verified']);

        return $this->loginAndRedirect($user);
    }

    /* ====================================================================
     |  Navigasi
     * ==================================================================*/
    public function back(): void
    {
        if (! in_array($this->step, [self::STEP_PIN, self::STEP_OTP], true)) {
            return;
        }

        $this->resetFeedback();
        $this->reset(['pin', 'pinConfirmation', 'otp', 'phone', 'isLogin', 'purpose']);
        $this->step = self::STEP_PHONE;
    }

    public function updated(string $property): void
    {
        $this->errorMessage = '';
        $this->resetErrorBag($property);
    }

    public function render()
    {
        return view('livewire.auth.login-register', [
            'formattedPhone' => $this->formatPhone($this->phone),
            'resendIn'       => max(0, $this->resendAvailableAt - now()->timestamp),
        ]);
    }

    /* ====================================================================
     |  Helper: OTP
     * ==================================================================*/

    /** Pakai OTP yang masih berlaku, atau kirim yang baru. */
    protected function issueOtp(): bool
    {
        if ($this->isChallengeActive(session('otp_challenge'))) {
            $this->resendAvailableAt = now()->timestamp + RateLimiter::availableIn($this->cooldownKey());
            return true;
        }

        return $this->sendOtp();
    }

    protected function sendOtp(): bool
    {
        $cooldown = $this->cooldownKey();
        $hourly   = 'otp-hour|' . $this->phone;

        if (RateLimiter::tooManyAttempts($cooldown, 1)) {
            $this->errorMessage = 'Tunggu ' . RateLimiter::availableIn($cooldown) . ' detik sebelum meminta kode baru.';
            return false;
        }

        if ($this->isThrottled($hourly, self::OTP_MAX_PER_HOUR)) {
            return false;
        }

        RateLimiter::hit($cooldown, self::OTP_COOLDOWN);
        RateLimiter::hit($hourly, 3600);

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        session(['otp_challenge' => [
            'phone'      => $this->phone,
            'purpose'    => $this->purpose,
            'hash'       => $this->hashOtp($code),
            'attempts'   => 0,
            'expires_at' => now()->timestamp + self::OTP_TTL,
        ]]);

        $keperluan = $this->purpose === 'reset' ? 'mengatur ulang PIN' : 'pendaftaran akun baru';

        SendWaNotification::dispatch(
            $this->phone,
            "*KIPAY - Verifikasi*\n\nKode OTP Anda: *{$code}*\n\nBerlaku 5 menit dan digunakan untuk {$keperluan}. Jangan bagikan kode ini kepada siapa pun, termasuk yang mengaku dari Kipay."
        );

        $this->resendAvailableAt = now()->timestamp + self::OTP_COOLDOWN;

        return true;
    }

    protected function isChallengeActive(mixed $challenge): bool
    {
        return is_array($challenge)
            && ($challenge['phone'] ?? null) === $this->phone
            && ($challenge['purpose'] ?? null) === $this->purpose
            && ($challenge['expires_at'] ?? 0) > now()->timestamp
            && ($challenge['attempts'] ?? 0) < self::OTP_MAX_ATTEMPTS;
    }

    protected function isVerified(): bool
    {
        $verified = session('otp_verified');

        return is_array($verified)
            && ($verified['phone'] ?? null) === $this->phone
            && ($verified['purpose'] ?? null) === $this->purpose
            && ($verified['expires_at'] ?? 0) > now()->timestamp;
    }

    protected function hashOtp(string $code): string
    {
        return hash_hmac('sha256', $code, (string) config('app.key'));
    }

    protected function cooldownKey(): string
    {
        return 'otp-cooldown|' . $this->phone;
    }

    /* ====================================================================
     |  Helper: validasi
     * ==================================================================*/
    protected function profileRules(): array
    {
        return [
            'name'            => ['required', 'string', 'min:3', 'max:100', "regex:/^[\pL\s.'-]+$/u"],
            'pin'             => $this->pinRules(),
            'pinConfirmation' => ['required', 'same:pin'],
        ];
    }

    protected function pinRules(): array
    {
        return [
            'required',
            'digits:6',
            function (string $attribute, mixed $value, \Closure $fail) {
                $value = (string) $value;

                if (
                    preg_match('/^(\d)\1{5}$/', $value)
                    || str_contains('01234567890', $value)
                    || str_contains('9876543210', $value)
                ) {
                    $fail('PIN terlalu mudah ditebak. Hindari angka berulang atau berurutan.');
                }
            },
        ];
    }

    protected function messages(): array
    {
        return [
            'whatsapp.required'        => 'Nomor WhatsApp wajib diisi.',
            'whatsapp.regex'           => 'Gunakan nomor seluler Indonesia, contoh: 08123456789.',
            'otp.required'             => 'Masukkan 6 digit kode OTP.',
            'otp.digits'               => 'Kode OTP terdiri dari 6 digit angka.',
            'pin.required'             => 'PIN wajib diisi.',
            'pin.digits'               => 'PIN terdiri dari 6 digit angka.',
            'pinConfirmation.required' => 'Ulangi PIN Anda.',
            'pinConfirmation.same'     => 'Konfirmasi PIN tidak sama.',
            'name.required'            => 'Nama lengkap wajib diisi.',
            'name.min'                 => 'Nama minimal 3 karakter.',
            'name.regex'               => 'Nama hanya boleh berisi huruf dan spasi.',
        ];
    }

    /* ====================================================================
     |  Helper: umum
     * ==================================================================*/
    protected function loginAndRedirect(User $user)
    {
        Auth::login($user);
        session()->regenerate();

        return redirect()->intended('/dashboard');
    }

    /** 0812… / +62812… / 62812… / 812… → 62812… */
    protected function normalizePhone(string $number): string
    {
        $digits = preg_replace('/\D+/', '', $number) ?? '';

        if (str_starts_with($digits, '0')) {
            return '62' . substr($digits, 1);
        }

        if (str_starts_with($digits, '8')) {
            return '62' . $digits;
        }

        return $digits;
    }

    /** Cocokkan ke semua format yang mungkin tersimpan di database. */
    protected function findUser(string $phone): ?User
    {
        if ($phone === '') {
            return null;
        }

        return User::whereIn('whatsapp', [$phone, '0' . substr($phone, 2), '+' . $phone])->first();
    }

    protected function formatPhone(string $phone): string
    {
        if ($phone === '') {
            return '';
        }

        $local = substr($phone, 2);

        return '+62 ' . preg_replace('/^(\d{3})(\d{3,4})(\d+)$/', '$1-$2-$3', $local);
    }

    protected function isThrottled(string $key, int $max): bool
    {
        if (! RateLimiter::tooManyAttempts($key, $max)) {
            return false;
        }

        $this->errorMessage = 'Terlalu banyak percobaan. Coba lagi dalam '
            . $this->humanizeSeconds(RateLimiter::availableIn($key)) . '.';

        return true;
    }

    protected function humanizeSeconds(int $seconds): string
    {
        return $seconds >= 60 ? ceil($seconds / 60) . ' menit' : $seconds . ' detik';
    }

    protected function resetFeedback(): void
    {
        $this->errorMessage = '';
        $this->resetErrorBag();
    }

    /** Kembali ke awal dengan pesan (dipakai saat sesi verifikasi tidak valid). */
    protected function restart(string $message = ''): void
    {
        $this->reset([
            'pin',
            'pinConfirmation',
            'otp',
            'name',
            'phone',
            'isLogin',
            'purpose',
            'resendAvailableAt',
            'showRegisterModal',
            'showConfirmSubmitModal',
        ]);

        session()->forget(['otp_challenge', 'otp_verified']);

        $this->step = self::STEP_PHONE;
        $this->errorMessage = $message;
    }
}
