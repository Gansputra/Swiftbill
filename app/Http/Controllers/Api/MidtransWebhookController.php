<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Transaction;

class MidtransWebhookController extends Controller
{
    /**
     * Handle incoming Midtrans payment notification (Webhook).
     */
    public function handle(Request $request)
    {
        $payload = $request->all();
        Log::info('Midtrans Webhook Received:', $payload);

        $serverKey = config('midtrans.server_key');
        $orderId = $payload['order_id'] ?? null;
        $statusCode = $payload['status_code'] ?? null;
        $grossAmount = $payload['gross_amount'] ?? null;
        $signatureKey = $payload['signature_key'] ?? null;
        $transactionStatus = $payload['transaction_status'] ?? null;
        $fraudStatus = $payload['fraud_status'] ?? null;

        if (!$orderId || !$statusCode || !$grossAmount || !$signatureKey) {
            return response()->json(['message' => 'Invalid payload format'], 400);
        }

        // Verify SHA512 signature key
        $calculatedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
        if ($signatureKey !== $calculatedSignature) {
            Log::warning('Midtrans Webhook Signature Mismatch for Order: ' . $orderId);
            return response()->json(['message' => 'Invalid signature key'], 403);
        }

        // Check transaction status from Midtrans
        if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
            Log::info("Midtrans Payment Success for Order: {$orderId}");
            
            // Optional: If invoice_number is stored or matched with orderId
            $transaction = Transaction::where('invoice_number', $orderId)->first();
            if ($transaction) {
                Log::info("Transaction {$orderId} verified & logged.");
            }
        } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
            Log::info("Midtrans Payment Cancelled/Failed for Order: {$orderId}");
        } elseif ($transactionStatus == 'pending') {
            Log::info("Midtrans Payment Pending for Order: {$orderId}");
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Notification processed successfully'
        ], 200);
    }
}
