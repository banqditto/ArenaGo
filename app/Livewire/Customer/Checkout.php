<?php

namespace App\Livewire\Customer;

use App\Models\Booking;
use App\Models\Payment;
use App\Services\MidtransService;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;
use Flux\Flux;
use Illuminate\Support\Carbon;

#[Title('Checkout Booking')]
class Checkout extends Component
{
    public Booking $booking;
    public string $qrisUrl = '';
    public string $transactionId = '';
    public string $orderId = '';
    public int $timeLeft = 0;
    public string $errorMessage = '';

    protected MidtransService $midtransService;

    /**
     * Inject dependencies.
     *
     * @param MidtransService $midtransService
     */
    public function boot(MidtransService $midtransService): void
    {
        $this->midtransService = $midtransService;
    }

    /**
     * Mount the component.
     *
     * @param Booking $booking
     * @return mixed
     */
    public function mount(Booking $booking): mixed
    {
        $this->booking = $booking;

        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        $timezone = 'Asia/Jakarta';
        $expiresAt = Carbon::parse($booking->expires_at->format('Y-m-d H:i:s'), $timezone);
        $now = Carbon::now($timezone);

        if ($booking->status !== 'pending' || $expiresAt->isPast()) {
            if ($booking->status === 'paid') {
                return $this->redirect(route('bookings.history'), navigate: true);
            }
            return $this->redirect(route('dashboard'), navigate: true);
        }

        $this->timeLeft = (int) max(0, $now->diffInSeconds($expiresAt, false));

        try {
            /** @var array{qris_url: string, transaction_id: string, order_id: string} $paymentData */
            $paymentData = Cache::remember('booking_payment_' . $booking->id, $booking->expires_at, function () {
                $response = $this->midtransService->createQrisTransaction($this->booking);
                
                $qrisUrl = '';
                if (isset($response['actions'])) {
                    foreach ($response['actions'] as $action) {
                        if ($action['name'] === 'generate-qr-code') {
                            $qrisUrl = $action['url'];
                            break;
                        }
                    }
                }

                Payment::updateOrCreate(
                    ['transaction_id' => $response['transaction_id']],
                    [
                        'booking_id' => $this->booking->id,
                        'payment_type' => 'qris',
                        'gross_amount' => $this->booking->total_price,
                        'transaction_status' => 'pending',
                    ]
                );

                return [
                    'qris_url' => $qrisUrl,
                    'transaction_id' => $response['transaction_id'],
                    'order_id' => $response['order_id'],
                ];
            });

            $this->qrisUrl = $paymentData['qris_url'];
            $this->transactionId = $paymentData['transaction_id'];
            $this->orderId = $paymentData['order_id'];

        } catch (Exception $e) {
            Log::error('Midtrans QRIS Generation failed in Checkout page: ' . $e->getMessage());
            $this->errorMessage = $e->getMessage();
        }

        return null;
    }

    /**
     * Update countdown timer and handle lock expiration.
     *
     * @return mixed
     */
    public function updateTimer(): mixed
    {
        if (!empty($this->errorMessage)) {
            return null;
        }

        $this->booking->refresh();
        
        if ($this->booking->status === 'paid') {
            return $this->redirect(route('bookings.history'), navigate: true);
        }

        if ($this->booking->status === 'expired') {
            return $this->redirect(route('dashboard'), navigate: true);
        }

        $timezone = 'Asia/Jakarta';
        $expiresAt = Carbon::parse($this->booking->expires_at->format('Y-m-d H:i:s'), $timezone);
        $now = Carbon::now($timezone);

        $this->timeLeft = (int) max(0, $now->diffInSeconds($expiresAt, false));

        if ($this->timeLeft <= 0) {
            if ($this->booking->status === 'pending') {
                DB::transaction(function () {
                    $this->booking->update(['status' => 'expired']);
                });

                if (!empty($this->orderId)) {
                    try {
                        $this->midtransService->cancelTransaction($this->orderId);
                    } catch (Exception $e) {
                        Log::warning('Midtrans cancel failed on booking expiration: ' . $e->getMessage());
                    }
                }
            }

            return $this->redirect(route('dashboard'), navigate: true);
        }

        return null;
    }

    /**
     * Simulate a successful payment for local testing.
     *
     * @return mixed
     */
    public function simulatePayment(): mixed
    {
        if (!app()->environment('local')) {
            return null;
        }

        try {
            DB::transaction(function () {
                $this->booking->update(['status' => 'paid']);

                Payment::updateOrCreate(
                    ['booking_id' => $this->booking->id],
                    [
                        'transaction_id' => $this->transactionId ?: 'SIMULATED-' . \Illuminate\Support\Str::uuid(),
                        'payment_type' => 'qris',
                        'gross_amount' => $this->booking->total_price,
                        'transaction_status' => 'settlement',
                    ]
                );
            });

            // Trigger WhatsApp notification for simulation
            try {
                $userPhone = $this->booking->user->phone;
                if ($userPhone) {
                    $whatsAppService = app(\App\Services\WhatsAppService::class);
                    $fieldName = $this->booking->field->name;
                    $bookingDate = $this->booking->booking_date->format('d M Y');
                    $timeSlot = $this->booking->start_time . ' - ' . $this->booking->end_time;
                    $totalPrice = number_format($this->booking->total_price, 0, ',', '.');
                    
                    $message = "Halo {$this->booking->user->name},\n\n" .
                        "[SIMULASI] Pembayaran booking lapangan Anda di ArenaGo telah berhasil diverifikasi!\n\n" .
                        "Rincian Booking:\n" .
                        "- Lapangan: {$fieldName}\n" .
                        "- Tanggal: {$bookingDate}\n" .
                        "- Waktu: {$timeSlot}\n" .
                        "- Total Bayar: Rp {$totalPrice}\n\n" .
                        "Silakan tunjukkan pesan konfirmasi ini kepada petugas lapangan saat tiba. Terima kasih atas kepercayaan Anda!";
                        
                    $whatsAppService->sendMessage($userPhone, $message);
                }
            } catch (Exception $ex) {
                Log::warning('WhatsApp Simulation notification failed: ' . $ex->getMessage());
            }

            Flux::toast(variant: 'success', text: __('Payment simulated successfully!'));

            return $this->redirect(route('bookings.history'), navigate: true);

        } catch (Exception $e) {
            Log::error('Simulated payment failed: ' . $e->getMessage());
            $this->errorMessage = $e->getMessage();
            return null;
        }
    }

    /**
     * Render the component view.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.customer.checkout')->layout('layouts.app');
    }
}
