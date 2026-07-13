<?php

namespace App\Services;

use App\Models\Booking;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Exception;

class MidtransService
{
    protected string $serverKey;
    protected string $clientKey;
    protected bool $isProduction;
    protected string $snapUrl;
    protected string $coreUrl;

    public function __construct()
    {
        $this->serverKey = config('services.midtrans.server_key') ?? '';
        $this->clientKey = config('services.midtrans.client_key') ?? '';
        $this->isProduction = (bool) config('services.midtrans.is_production', false);

        if ($this->isProduction) {
            $this->snapUrl = 'https://app.midtrans.com/snap/v1';
            $this->coreUrl = 'https://api.midtrans.com/v2';
        } else {
            $this->snapUrl = 'https://app.sandbox.midtrans.com/snap/v1';
            $this->coreUrl = 'https://api.sandbox.midtrans.com/v2';
        }
    }

    /**
     * Get request headers for Midtrans API.
     *
     * @return array<string, string>
     */
    protected function getHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Basic ' . base64_encode($this->serverKey . ':'),
        ];
    }

    /**
     * Create a new HTTP request with authorization and optional SSL verification bypass.
     *
     * @return \Illuminate\Http\Client\PendingRequest
     */
    protected function newRequest(): \Illuminate\Http\Client\PendingRequest
    {
        $request = Http::withHeaders($this->getHeaders());

        if (config('app.env') === 'local') {
            $request->withoutVerifying();
        }

        return $request;
    }

    /**
     * Generate Midtrans Snap payment token and redirect URL.
     *
     * @param Booking $booking
     * @return array<string, mixed>
     * @throws Exception
     */
    public function createSnapTransaction(Booking $booking): array
    {
        $orderId = 'ARENAGO-' . $booking->id . '-' . Str::random(5);

        $payload = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $booking->total_price,
            ],
            'customer_details' => [
                'first_name' => $booking->user->name,
                'email' => $booking->user->email,
            ],
            'item_details' => [
                [
                    'id' => (string) $booking->field_id,
                    'price' => (int) $booking->field->price_per_hour,
                    'quantity' => 1,
                    'name' => 'Field Booking: ' . $booking->field->name . ' (' . $booking->start_time . ' - ' . $booking->end_time . ')',
                ]
            ],
            'expiry' => [
                'start_time' => Carbon::now()->format('Y-m-d H:i:s O'),
                'duration' => 10,
                'unit' => 'minute',
            ],
        ];

        $response = $this->newRequest()
            ->post($this->snapUrl . '/transactions', $payload);

        if ($response->failed()) {
            throw new Exception('Midtrans Snap Error (HTTP ' . $response->status() . '): ' . $response->body());
        }

        $data = $response->json();
        if (isset($data['error_messages'])) {
            $msg = is_array($data['error_messages']) ? implode(', ', $data['error_messages']) : $data['error_messages'];
            throw new Exception('Midtrans Snap Error: ' . $msg);
        }

        return $data;
    }

    /**
     * Charge transaction directly using Core API with QRIS payment type.
     *
     * @param Booking $booking
     * @return array<string, mixed>
     * @throws Exception
     */
    public function createQrisTransaction(Booking $booking): array
    {
        $orderId = 'ARENAGO-' . $booking->id . '-' . Str::random(5);

        $payload = [
            'payment_type' => 'qris',
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $booking->total_price,
            ],
            'customer_details' => [
                'first_name' => $booking->user->name,
                'email' => $booking->user->email,
            ],
            'qris' => [
                'acquirer' => 'gopay',
            ],
            'custom_expiry' => [
                'expiry_duration' => 10,
                'unit' => 'minute',
            ]
        ];

        $response = $this->newRequest()
            ->post($this->coreUrl . '/charge', $payload);

        if ($response->failed()) {
            throw new Exception('Midtrans API Request Failed (HTTP ' . $response->status() . '): ' . $response->body());
        }

        $data = $response->json();

        if (isset($data['status_code']) && !in_array((string)$data['status_code'], ['200', '201', '202'])) {
            $message = $data['status_message'] ?? 'Unknown Midtrans error';
            throw new Exception("Midtrans Core QRIS Error (Code {$data['status_code']}): {$message}");
        }

        return $data;
    }

    /**
     * Cancel a pending Midtrans transaction.
     *
     * @param string $orderId
     * @return array<string, mixed>
     * @throws Exception
     */
    public function cancelTransaction(string $orderId): array
    {
        $response = $this->newRequest()
            ->post($this->coreUrl . '/' . $orderId . '/cancel');

        if ($response->failed()) {
            throw new Exception('Midtrans Cancel Error: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Get transaction status from Midtrans.
     *
     * @param string $id
     * @return array<string, mixed>
     * @throws Exception
     */
    public function getTransactionStatus(string $id): array
    {
        $response = $this->newRequest()
            ->get($this->coreUrl . '/' . $id . '/status');

        if ($response->failed()) {
            throw new Exception('Midtrans Status Error: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Extract Booking ID from Midtrans Order ID.
     *
     * @param string $orderId
     * @return int|null
     */
    public function getBookingIdFromOrderId(string $orderId): ?int
    {
        $parts = explode('-', $orderId);
        if (count($parts) >= 2 && $parts[0] === 'ARENAGO') {
            return (int) $parts[1];
        }
        return null;
    }
}
