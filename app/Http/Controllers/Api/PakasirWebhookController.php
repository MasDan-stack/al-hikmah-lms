<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PakasirWebhookController extends Controller
{
    /**
     * Menerima notifikasi status pembayaran otomatis dari server Pakasir
     */
    public function handle(Request $request, NotificationService $notificationService): JsonResponse
    {
        $orderId = $request->input('order_id')
            ?? $request->input('transaction.order_id')
            ?? $request->input('payment.order_id')
            ?? $request->input('transaction_id');

        $amount = (float) ($request->input('amount')
            ?? $request->input('transaction.amount')
            ?? $request->input('payment.amount', 0));

        $status = strtolower((string) (
            $request->input('status')
            ?? $request->input('transaction.status')
            ?? $request->input('payment.status')
            ?? $request->input('payment_status', '')
        ));

        $paymentMethod = $request->input('payment_method')
            ?? $request->input('transaction.payment_method')
            ?? $request->input('payment.payment_method')
            ?? $request->input('method')
            ?? 'QRIS Instant';

        if (! $orderId) {
            return response()->json([
                'success' => false,
                'message' => 'Missing order_id or transaction_id in webhook payload',
            ], 400);
        }

        // Cari transaksi berdasarkan invoice_number atau pakasir_order_id
        $payment = Payment::with(['enrollment', 'student.user', 'student.parent.user', 'program'])
            ->where('invoice_number', $orderId)
            ->orWhere('pakasir_order_id', $orderId)
            ->first();

        if (! $payment) {
            Log::warning("Pakasir Webhook: Payment not found for order_id: {$orderId}");

            return response()->json([
                'success' => false,
                'message' => 'Payment record not found',
            ], 404);
        }

        // Idempotency: Jika pembayaran sudah lunas sebelumnya
        if ($payment->status === 'paid') {
            return response()->json([
                'success' => true,
                'message' => 'Payment already processed and marked as PAID',
            ], 200);
        }

        // Status sukses dari Pakasir: completed / success / paid / 1
        if (in_array($status, ['completed', 'success', 'paid', '1', 'settlement'])) {
            DB::transaction(function () use ($payment, $paymentMethod, $request, $notificationService) {
                // 1. Update status pembayaran menjadi lunas
                $payment->update([
                    'status' => 'paid',
                    'payment_date' => now(),
                    'payment_method' => strtoupper(str_replace('_', ' ', str_ireplace('pakasir ', '', $paymentMethod))),
                    'gateway_response' => array_merge($payment->gateway_response ?? [], [
                        'webhook_received_at' => now()->toIso8601String(),
                        'webhook_payload' => $request->all(),
                    ]),
                ]);

                // 2. Aktifkan enrollment & bentuk 4 minggu sesi belajar otomatis
                if ($payment->enrollment) {
                    $payment->enrollment->markAsPaidAndActive();
                }

                // 3. Kirim notifikasi konfirmasi pembayaran sukses ke wali santri
                $parentUser = $payment->student?->parent?->user;
                if ($parentUser) {
                    $notificationService->notifyPaymentSuccess($payment, $parentUser);
                }
            });

            Log::info("Pakasir Webhook: Payment {$orderId} successfully marked as PAID and enrollment activated.");

            return response()->json([
                'success' => true,
                'status' => 'success',
                'message' => 'Payment processed and enrollment activated successfully',
            ], 200);
        }

        return response()->json([
            'success' => true,
            'status' => 'acknowledged',
            'message' => 'Webhook received with status: '.$status,
        ], 200);
    }
}
