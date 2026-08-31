<?php

namespace App\Services;

use App\Models\Payment;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PakasirService
{
    protected string $apiKey;

    protected string $projectSlug;

    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.pakasir.api_key', 'wakGifjocg8pjIxFjMQXXJcNjvjkGQd1');
        $this->projectSlug = config('services.pakasir.project_slug', 'al-hikmah');
        $this->baseUrl = rtrim(config('services.pakasir.base_url', 'https://app.pakasir.com/api'), '/');
    }

    /**
     * Format nama channel pembayaran yang ramah pengguna
     */
    public static function formatMethodName(string $method): string
    {
        $method = strtolower($method);

        return match ($method) {
            'qris' => 'QRIS Instant',
            'bri_va', 'va_bri' => 'BRI Virtual Account (BRIVA)',
            'bni_va', 'va_bni' => 'BNI Virtual Account',
            'bca_va', 'va_bca' => 'BCA Virtual Account',
            'mandiri_va', 'va_mandiri' => 'Mandiri Virtual Account',
            'permata_va', 'va_permata' => 'Permata Virtual Account',
            'sampoerna_va', 'va_sampoerna' => 'Bank Sampoerna VA',
            default => strtoupper(str_replace(['_', 'va'], [' ', 'VA'], $method)),
        };
    }

    /**
     * Hitung Biaya Admin Gateway secara otomatis
     */
    public function calculateFee(float $amount, string $method): int
    {
        $feePayer = config('services.pakasir.fee_payer', 'customer');
        if ($feePayer !== 'customer') {
            return 0; // Biaya admin disubsidi lembaga (Gratis bagi Orang Tua)
        }

        // Virtual Account (Flat Fee, misal Rp 3.500)
        if (str_contains(strtolower($method), 'va')) {
            return (int) config('services.pakasir.fee_va_flat', 3500);
        }

        // QRIS (Persentase 0.7%)
        $percent = (float) config('services.pakasir.fee_qris_percent', 0.7);

        return (int) ceil(($amount * $percent) / 100);
    }

    /**
     * Membuat transaksi pembayaran ke API Pakasir
     *
     * @return array<string, mixed>
     *
     * @throws Exception
     */
    public function createTransaction(Payment $payment, string $method = 'qris'): array
    {
        $orderId = $payment->invoice_number ?? ('INV-'.$payment->id.'-'.time());
        $baseAmount = (float) $payment->amount;
        $adminFee = $this->calculateFee($baseAmount, $method);
        $totalAmount = (int) round($baseAmount + $adminFee);

        // Normalisasi format nama method untuk API Pakasir
        $methodMap = [
            'va_bri' => 'bri_va',
            'va_bni' => 'bni_va',
            'va_permata' => 'permata_va',
            'va_sampoerna' => 'sampoerna_va',
            'va_bca' => 'bca_va',
            'va_mandiri' => 'mandiri_va',
            'bri' => 'bri_va',
            'bni' => 'bni_va',
            'permata' => 'permata_va',
        ];
        $apiMethod = $methodMap[strtolower($method)] ?? strtolower($method);

        $endpoint = "{$this->baseUrl}/transactioncreate/{$apiMethod}";

        $payload = [
            'project' => $this->projectSlug,
            'order_id' => $orderId,
            'amount' => $totalAmount,
            'api_key' => $this->apiKey,
        ];

        try {
            $response = Http::timeout(15)
                ->acceptJson()
                ->post($endpoint, $payload);

            if ($response->successful()) {
                $result = $response->json();
                $paymentData = $result['payment'] ?? $result['data'] ?? $result;

                // Format data response Pakasir
                $checkoutUrl = $paymentData['payment_url'] ?? $result['payment_url'] ?? $result['url'] ?? $result['checkout_url'] ?? "https://app.pakasir.com/pay/{$this->projectSlug}/{$orderId}";
                $paymentNumber = $paymentData['payment_number'] ?? $result['payment_number'] ?? $result['qr_string'] ?? $result['qr_content'] ?? $result['payment_code'] ?? $orderId;
                $expiredAt = isset($paymentData['expired_at']) ? now()->parse($paymentData['expired_at']) : (isset($result['expired_at']) ? now()->parse($result['expired_at']) : now()->addHours(24));

                $payment->update([
                    'pakasir_order_id' => $orderId,
                    'admin_fee' => $adminFee,
                    'total_amount' => $totalAmount,
                    'checkout_url' => $checkoutUrl,
                    'qr_content' => $paymentNumber,
                    'expired_at' => $expiredAt,
                    'payment_method' => self::formatMethodName($method),
                    'gateway_response' => array_merge($result, [
                        'base_amount' => $baseAmount,
                        'admin_fee' => $adminFee,
                        'total_amount' => $totalAmount,
                        'method' => $method,
                        'created_at' => now()->toIso8601String(),
                    ]),
                ]);

                return [
                    'success' => true,
                    'data' => $result,
                    'total_amount' => $totalAmount,
                    'admin_fee' => $adminFee,
                    'qr_content' => $paymentNumber,
                    'checkout_url' => $checkoutUrl,
                ];
            }

            // Jika API Pakasir mengembalikan error response
            $errorMsg = $response->json('message') ?? $response->body();
            Log::warning('Pakasir Create Transaction API Notice', [
                'status' => $response->status(),
                'error' => $errorMsg,
                'method' => $apiMethod,
                'payload' => $payload,
            ]);

            // Fallback simulasi Virtual Account di mode Local / Testing jika bank tertentu belum diaktifkan di sandbox
            if (app()->environment('local', 'testing')) {
                $mockVaNumber = match ($apiMethod) {
                    'bca_va', 'va_bca' => '8808'.substr(preg_replace('/\D/', '', (string) $payment->id).time(), -8),
                    'mandiri_va', 'va_mandiri' => '89022'.substr(preg_replace('/\D/', '', (string) $payment->id).time(), -7),
                    'bri_va', 'va_bri' => '12800'.substr(preg_replace('/\D/', '', (string) $payment->id).time(), -8),
                    'bni_va', 'va_bni' => '8810'.substr(preg_replace('/\D/', '', (string) $payment->id).time(), -8),
                    default => '8888'.substr(preg_replace('/\D/', '', (string) $payment->id).time(), -8),
                };

                $mockQr = "00020101021226670014ID.LINKAJA.WWW01189360091100216063460215{$orderId}5204581253033605406{$totalAmount}5802ID5914AL-HIKMAH LMS6007JAKARTA6304";
                $isQris = ($apiMethod === 'qris');

                $mockResult = [
                    'payment' => [
                        'project' => $this->projectSlug,
                        'order_id' => $orderId,
                        'amount' => $totalAmount,
                        'total_payment' => $totalAmount,
                        'fee' => $adminFee,
                        'payment_method' => $apiMethod,
                        'payment_number' => $isQris ? $mockQr : $mockVaNumber,
                        'expired_at' => now()->addHours(24)->toIso8601String(),
                        'payment_url' => "https://app.pakasir.com/pay/{$this->projectSlug}/{$orderId}",
                    ],
                    'payment_url' => "https://app.pakasir.com/pay/{$this->projectSlug}/{$orderId}",
                    'qr_string' => $isQris ? $mockQr : $mockVaNumber,
                    'status' => 'pending',
                    'order_id' => $orderId,
                    'amount' => $totalAmount,
                ];

                $payment->update([
                    'pakasir_order_id' => $orderId,
                    'admin_fee' => $adminFee,
                    'total_amount' => $totalAmount,
                    'checkout_url' => $mockResult['payment_url'],
                    'qr_content' => $isQris ? $mockQr : $mockVaNumber,
                    'expired_at' => now()->addHours(24),
                    'payment_method' => self::formatMethodName($method),
                    'gateway_response' => array_merge($mockResult, [
                        'base_amount' => $baseAmount,
                        'admin_fee' => $adminFee,
                        'total_amount' => $totalAmount,
                        'method' => $method,
                        'note' => 'Generated in Local Sandbox environment fallback',
                        'created_at' => now()->toIso8601String(),
                    ]),
                ]);

                return [
                    'success' => true,
                    'data' => $mockResult,
                    'total_amount' => $totalAmount,
                    'admin_fee' => $adminFee,
                    'qr_content' => $isQris ? $mockQr : $mockVaNumber,
                    'checkout_url' => $mockResult['payment_url'],
                ];
            }

            throw new Exception('Gagal membuat transaksi di Pakasir: '.$errorMsg);
        } catch (Exception $e) {
            Log::error('Pakasir Exception: '.$e->getMessage());
            throw $e;
        }
    }

    /**
     * Memeriksa status transaksi langsung ke server Pakasir
     *
     * @return array<string, mixed>
     */
    public function checkTransactionStatus(Payment $payment): array
    {
        $orderId = $payment->pakasir_order_id ?? $payment->invoice_number;
        $amount = (int) round($payment->total_amount ?? $payment->amount);

        $endpoint = "{$this->baseUrl}/transactiondetail";

        try {
            $response = Http::timeout(10)->get($endpoint, [
                'project' => $this->projectSlug,
                'amount' => $amount,
                'order_id' => $orderId,
                'api_key' => $this->apiKey,
            ]);

            if ($response->successful()) {
                return $response->json();
            }
        } catch (Exception $e) {
            Log::warning('Pakasir Check Status Exception: '.$e->getMessage());
        }

        return [
            'status' => $payment->status,
            'order_id' => $orderId,
        ];
    }

    /**
     * Batalkan transaksi aktif agar orang tua bisa memilih metode lain
     */
    public function cancelActiveTransaction(Payment $payment): bool
    {
        if ($payment->status === 'paid') {
            return false;
        }

        $payment->update([
            'checkout_url' => null,
            'qr_content' => null,
            'pakasir_order_id' => null,
            'admin_fee' => 0,
            'total_amount' => $payment->amount,
            'expired_at' => null,
            'payment_method' => null,
        ]);

        return true;
    }
}
