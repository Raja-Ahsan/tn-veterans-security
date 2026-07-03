<?php

namespace App\Services;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    public function isEnabled(): bool
    {
        $settings = SiteSetting::first();

        return $settings
            && $settings->sms_enabled
            && $settings->twilio_sid
            && $settings->twilio_token
            && $settings->twilio_from_number;
    }

    /**
     * @return array{success: bool, message?: string, sid?: string}
     */
    public function send(string $to, string $message): array
    {
        if (! $this->isEnabled()) {
            Log::info('SMS skipped — Twilio not configured', ['to' => $to]);

            return ['success' => false, 'message' => 'SMS provider not configured'];
        }

        $settings = SiteSetting::first();
        $to = $this->normalizePhone($to);

        if ($to === null) {
            return ['success' => false, 'message' => 'Invalid phone number'];
        }

        try {
            $response = Http::withBasicAuth($settings->twilio_sid, $settings->twilio_token)
                ->asForm()
                ->post("https://api.twilio.com/2010-04-01/Accounts/{$settings->twilio_sid}/Messages.json", [
                    'From' => $settings->twilio_from_number,
                    'To' => $to,
                    'Body' => $message,
                ]);

            if ($response->successful()) {
                return ['success' => true, 'sid' => $response->json('sid')];
            }

            $error = $response->json('message') ?? $response->body();
            Log::error('Twilio SMS failed', ['to' => $to, 'error' => $error]);

            return ['success' => false, 'message' => $error];
        } catch (\Throwable $e) {
            Log::error('Twilio SMS exception', ['to' => $to, 'error' => $e->getMessage()]);

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    private function normalizePhone(string $phone): ?string
    {
        $digits = preg_replace('/\D/', '', $phone);

        if (strlen($digits) === 10) {
            return '+1'.$digits;
        }

        if (strlen($digits) === 11 && str_starts_with($digits, '1')) {
            return '+'.$digits;
        }

        if (str_starts_with($phone, '+') && strlen($digits) >= 10) {
            return '+'.$digits;
        }

        return null;
    }
}
