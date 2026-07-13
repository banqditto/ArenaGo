<?php

use App\Models\Booking;
use App\Services\MidtransService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * Scheduled job to check and expire pending bookings that have passed their 10-minute lock window,
 * and cancel their corresponding transactions in Midtrans.
 */
Schedule::call(function () {
    $expiredBookings = Booking::where('status', 'pending')
        ->where('expires_at', '<', \Illuminate\Support\Carbon::now('Asia/Jakarta'))
        ->with('payment')
        ->get();

    if ($expiredBookings->isEmpty()) {
        return;
    }

    $midtransService = app(MidtransService::class);

    foreach ($expiredBookings as $booking) {
        try {
            DB::transaction(function () use ($booking) {
                $booking->update(['status' => 'expired']);
            });

            // If there's an associated payment transaction, cancel it on Midtrans
            $payment = $booking->payment;
            if ($payment && !empty($payment->transaction_id)) {
                try {
                    $status = $midtransService->getTransactionStatus($payment->transaction_id);
                    $orderId = $status['order_id'] ?? null;
                    
                    if ($orderId) {
                        $midtransService->cancelTransaction($orderId);
                        
                        DB::transaction(function () use ($payment) {
                            $payment->update([
                                'transaction_status' => 'expire',
                            ]);
                        });
                    }
                } catch (Exception $e) {
                    Log::warning("Failed to cancel Midtrans transaction for expired booking ID {$booking->id}: " . $e->getMessage());
                }
            }

            Log::info("Successfully expired booking ID {$booking->id} via scheduler.");

        } catch (Exception $e) {
            Log::error("Error processing expired booking ID {$booking->id} in scheduler: " . $e->getMessage());
        }
    }
})->everyMinute();
