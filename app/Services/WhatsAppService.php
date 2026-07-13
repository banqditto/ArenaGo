<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class WhatsAppService
{
    protected ?string $token;

    public function __construct()
    {
        $this->token = config('services.fonnte.token');
    }

    /**
     * Send a WhatsApp message via Fonnte API.
     *
     * @param string $to
     * @param string $message
     * @return bool
     */
    public function sendMessage(string $to, string $message): bool
    {
        if (empty($this->token)) {
            Log::warning('WhatsApp Notification skipped: FONNTE_TOKEN is not configured.');
            return false;
        }

        // Normalize phone number
        $to = preg_replace('/[^0-9]/', '', $to) ?? '';
        if (empty($to)) {
            Log::warning('WhatsApp Notification skipped: Recipient phone number is empty.');
            return false;
        }

        if (str_starts_with($to, '0')) {
            $to = '62' . substr($to, 1);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])->post('https://api.fonnte.com/send', [
                'target' => $to,
                'message' => $message,
                'countryCode' => '62', // Default country code
            ]);

            if ($response->successful()) {
                Log::info('WhatsApp Notification sent successfully', ['to' => $to]);
                return true;
            }

            Log::error('WhatsApp Notification failed', [
                'to' => $to,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return false;

        } catch (Exception $e) {
            Log::error('WhatsApp Notification exception', [
                'to' => $to,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
