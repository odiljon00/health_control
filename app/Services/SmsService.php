<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService {
    protected $provider;
    protected $login;
    protected $password;
    protected $sender;
    protected $enabled;
    protected $testMode;
    protected $token = null;
    protected $tokenExpiresAt = null;

    public function __construct() {
        $this->provider = config('services.sms.provider', 'eskiz');
        $this->login    = config('services.sms.login');
        $this->password = config('services.sms.password');
        $this->sender   = config('services.sms.sender', '4546');
        $this->enabled  = config('services.sms.enabled', true);
        $this->testMode = config('services.sms.test_mode', true);
    }

    /**
     * Bitta raqamga SMS yuborish
     */
    public function send(string $phone, string $message): bool {
        // Test rejim
        if ($this->testMode || !$this->enabled) {
            Log::info("📱 [SMS TEST] To: {$phone} | Text: {$message}");
            return true;
        }

        $phone = $this->normalizePhone($phone);
        if (!$phone) {
            Log::warning("❌ SMS: Noto'g'ri telefon raqam");
            return false;
        }

        try {
            // Provayderga qarab
            if ($this->provider === 'eskiz') {
                return $this->sendViaEskiz($phone, $message);
            }
            if ($this->provider === 'getsms') {
                return $this->sendViaGetsms($phone, $message);
            }
            if ($this->provider === 'playmobile') {
                return $this->sendViaPlaymobile($phone, $message);
            }

            Log::error("❌ Noma'lum SMS provayder: {$this->provider}");
            return false;

        } catch (\Exception $e) {
            Log::error("❌ SMS Exception: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Bir nechta raqamga yuborish
     */
    public function sendBulk(array $phones, string $message): array {
        $results = [];
        foreach ($phones as $phone) {
            $results[$phone] = $this->send($phone, $message);
        }
        return $results;
    }

    /* ==================== ESKIZ.UZ ==================== */
    protected function sendViaEskiz(string $phone, string $message): bool {
        // Token olish (har safar emas — cache)
        if (!$this->token) {
            $this->eskizLogin();
        }

        $response = Http::withToken($this->token)
            ->asForm()
            ->post('https://notify.eskiz.uz/api/message/sms/send', [
                'mobile_phone' => $phone,
                'message'      => $message,
                'from'         => $this->sender,
            ]);

        $result = $response->json();

        if ($response->successful() && isset($result['id'])) {
            Log::info("✅ Eskiz SMS: {$phone} | ID: {$result['id']}");
            return true;
        }

        // Token muddati o'tgan bo'lsa — qayta login
        if (isset($result['message']) && str_contains($result['message'], 'token')) {
            $this->token = null;
            $this->eskizLogin();
            return $this->sendViaEskiz($phone, $message);
        }

        Log::error("❌ Eskiz xato: " . json_encode($result));
        return false;
    }

    protected function eskizLogin(): void {
        $response = Http::asForm()->post('https://notify.eskiz.uz/api/auth/login', [
            'email'    => $this->login,
            'password' => $this->password,
        ]);

        $result = $response->json();

        if (isset($result['data']['token'])) {
            $this->token = $result['data']['token'];
            Log::info("🔐 Eskiz: token olindi");
        } else {
            Log::error("❌ Eskiz login xato: " . json_encode($result));
            throw new \Exception("Eskiz login muvaffaqiyatsiz");
        }
    }

    /* ==================== GETSMS.UZ ==================== */
    protected function sendViaGetsms(string $phone, string $message): bool {
        $response = Http::asForm()->post('http://185.8.212.184/smsgateway/', [
            'login'    => $this->login,
            'password' => $this->password,
            'nickname' => $this->sender,
            'data'     => json_encode([
                ['phone' => $phone, 'text' => $message]
            ]),
        ]);

        $result = $response->json();

        if ($response->successful() && isset($result[0]['recipient'])) {
            Log::info("✅ GETSMS: {$phone}");
            return true;
        }

        Log::error("❌ GETSMS xato: " . json_encode($result));
        return false;
    }

    /* ==================== PLAYMOBILE.UZ ==================== */
    protected function sendViaPlaymobile(string $phone, string $message): bool {
        $response = Http::withBasicAuth($this->login, $this->password)
            ->post('https://send.smsxabar.uz/broker-api/send', [
                'messages' => [
                    [
                        'recipient' => $phone,
                        'message-id' => uniqid(),
                        'sms' => [
                            'originator' => $this->sender,
                            'content'    => ['text' => $message],
                        ],
                    ],
                ],
            ]);

        if ($response->successful()) {
            Log::info("✅ Playmobile: {$phone}");
            return true;
        }

        Log::error("❌ Playmobile xato: " . json_encode($response->json()));
        return false;
    }

    /* ==================== PHONE NORMALIZE ==================== */
    protected function normalizePhone(string $phone): ?string {
        $digits = preg_replace('/\D/', '', $phone);

        if (strlen($digits) === 12 && str_starts_with($digits, '998')) {
            return $digits;
        }
        if (strlen($digits) === 9) {
            return '998' . $digits;
        }
        if (strlen($digits) === 13 && str_starts_with($digits, '8998')) {
            return substr($digits, 1);
        }
        return null;
    }
}