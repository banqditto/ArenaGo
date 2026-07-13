<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Services\MidtransService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MidtransWebhookController extends Controller
{
    protected MidtransService $midtransService;
    protected \App\Services\WhatsAppService $whatsAppService;

    public function __construct(MidtransService $midtransService, \App\Services\WhatsAppService $whatsAppService)
    {
        $this->midtransService = $midtransService;
        $this->whatsAppService = $whatsAppService;
    }

    /**
     * Handle Midtrans HTTP post notifications (webhooks).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function handle(Request $request): JsonResponse
    {
        if ($request->isMethod('get')) {
            return response()->json(['message' => 'Midtrans Webhook is ready!'], 200);
        }

        $orderId = $request->input('order_id');
        $statusCode = $request->input('status_code');
        $grossAmount = $request->input('gross_amount');
        $signatureKey = $request->input('signature_key');
        
        $serverKey = config('services.midtrans.server_key');
        
        // 1. Verify Midtrans Signature Key to prevent spoofing
        $calculatedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
        
        if ($calculatedSignature !== $signatureKey) {
            Log::warning('Midtrans Webhook: Invalid signature', [
                'order_id' => $orderId,
                'received_signature' => $signatureKey,
                'calculated_signature' => $calculatedSignature,
            ]);
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        // 2. Parse booking ID from composite order ID
        $bookingId = $this->midtransService->getBookingIdFromOrderId($orderId);
        
        if (!$bookingId) {
            Log::error('Midtrans Webhook: Invalid order ID format', ['order_id' => $orderId]);
            return response()->json(['message' => 'Invalid order ID format'], 400);
        }

        // 3. Find the target booking
        $booking = Booking::find($bookingId);
        
        if (!$booking) {
            Log::error('Midtrans Webhook: Booking not found', ['booking_id' => $bookingId]);
            return response()->json(['message' => 'Booking not found'], 404);
        }

        // 4. Map Midtrans transaction status to booking status
        $transactionStatus = $request->input('transaction_status');
        $fraudStatus = $request->input('fraud_status');
        
        $mappedStatus = null;
        
        if ($transactionStatus === 'settlement') {
            $mappedStatus = 'paid';
        } elseif ($transactionStatus === 'capture') {
            if ($fraudStatus === 'challenge') {
                $mappedStatus = 'pending';
            } else {
                $mappedStatus = 'paid'; // Accept status
            }
        } elseif (in_array($transactionStatus, ['deny', 'cancel'])) {
            $mappedStatus = 'cancelled';
        } elseif ($transactionStatus === 'expire') {
            $mappedStatus = 'expired';
        } else {
            $mappedStatus = 'pending';
        }

        // 5. Update database status using database transaction for consistency
        try {
            DB::transaction(function () use ($booking, $request, $mappedStatus) {
                // Update booking status
                $booking->update([
                    'status' => $mappedStatus,
                ]);

                // Create or update payment transaction record
                Payment::updateOrCreate(
                    ['transaction_id' => $request->input('transaction_id')],
                    [
                        'booking_id' => $booking->id,
                        'payment_type' => $request->input('payment_type', 'qris'),
                        'gross_amount' => $request->input('gross_amount'),
                        'transaction_status' => $request->input('transaction_status'),
                    ]
                );
            });

            Log::info('Midtrans Webhook: Successfully processed transaction status update', [
                'order_id' => $orderId,
                'booking_id' => $booking->id,
                'transaction_status' => $transactionStatus,
                'mapped_status' => $mappedStatus,
            ]);

            // Trigger WhatsApp notification on successful payment
            if ($mappedStatus === 'paid') {
                $userPhone = $booking->user->phone;
                if ($userPhone) {
                    $fieldName = $booking->field->name;
                    $bookingDate = $booking->booking_date->format('d M Y');
                    $timeSlot = $booking->start_time . ' - ' . $booking->end_time;
                    $totalPrice = number_format($booking->total_price, 0, ',', '.');
                    
                    $message = "Halo {$booking->user->name},\n\n" .
                        "Pembayaran booking lapangan Anda di ArenaGo telah berhasil diverifikasi!\n\n" .
                        "Rincian Booking:\n" .
                        "- Lapangan: {$fieldName}\n" .
                        "- Tanggal: {$bookingDate}\n" .
                        "- Waktu: {$timeSlot}\n" .
                        "- Total Bayar: Rp {$totalPrice}\n\n" .
                        "Silakan tunjukkan pesan konfirmasi ini kepada petugas lapangan saat tiba. Terima kasih atas kepercayaan Anda!";
                        
                    $this->whatsAppService->sendMessage($userPhone, $message);
                } else {
                    Log::warning('WhatsApp Notification skipped: User does not have a phone number.', ['booking_id' => $booking->id]);
                }
            }

            return response()->json(['message' => 'Webhook processed successfully']);
        } catch (Exception $e) {
            Log::error('Midtrans Webhook Error: Database update failed', [
                'error' => $e->getMessage(),
                'order_id' => $orderId,
            ]);
            return response()->json(['message' => 'Database update failed'], 500);
        }
    }

    /**
     * Fallback invokable action routing to handle method.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        return $this->handle($request);
    }
}
