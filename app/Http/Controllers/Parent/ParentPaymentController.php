<?php

namespace App\Http\Controllers\Parent;

use App\Enums\EnrollmentStatus;
use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Services\NotificationService;
use App\Services\PakasirService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ParentPaymentController extends Controller
{
    public function __construct(
        protected PakasirService $pakasirService
    ) {}

    public function index(): View
    {
        $parent = auth()->user()->parentProfile;
        $childIds = $parent ? $parent->students()->pluck('id')->toArray() : [];

        $pendingPayments = count($childIds) > 0
            ? Payment::with(['student.user', 'program'])
                ->whereIn('student_id', $childIds)
                ->where('status', 'pending')
                ->latest()
                ->get()
            : collect();

        $activeEnrollments = count($childIds) > 0
            ? Enrollment::with(['student.user', 'program', 'mentor.user'])
                ->whereIn('student_id', $childIds)
                ->where('status', EnrollmentStatus::ACTIVE->value)
                ->get()
            : collect();

        return view('parent.payments.index', compact('pendingPayments', 'activeEnrollments'));
    }

    public function history(): View
    {
        $parent = auth()->user()->parentProfile;
        $childIds = $parent ? $parent->students()->pluck('id')->toArray() : [];

        $paidPayments = count($childIds) > 0
            ? Payment::with(['student.user', 'program'])
                ->whereIn('student_id', $childIds)
                ->where('status', 'paid')
                ->latest()
                ->paginate(10)
            : collect();

        return view('parent.payments.history', compact('paidPayments'));
    }

    public function show(Payment $payment): View
    {
        $this->authorize('view', $payment);

        $payment->loadMissing(['student.user', 'student.parent.user', 'program', 'enrollment.mentor.user']);

        // Active Real-time Sync ke Pakasir jika masih pending
        if ($payment->status === 'pending' && ! empty($payment->pakasir_order_id)) {
            $this->syncPaymentWithPakasir($payment);
            $payment->refresh();
        }

        return view('parent.payments.show', compact('payment'));
    }

    /**
     * Memproses / Menginisialisasi pembayaran online via Pakasir
     */
    public function payOnline(Request $request, Payment $payment): RedirectResponse
    {
        $this->authorize('pay', $payment);

        $payment->loadMissing(['enrollment', 'student.user', 'program']);

        if ($payment->status === 'paid') {
            return redirect()->route('parent.payments.show', $payment->id)
                ->with('warning', 'Tagihan ini sudah berstatus lunas.');
        }

        $method = $request->input('payment_method', 'qris');

        try {
            $this->pakasirService->createTransaction($payment, $method);

            return redirect()->route('parent.payments.show', $payment->id)
                ->with('success', 'Instruksi pembayaran berhasil dibuat. Silakan selesaikan pembayaran Anda.');
        } catch (Exception $e) {
            return redirect()->route('parent.payments.show', $payment->id)
                ->with('error', 'Gagal memproses gateway pembayaran: '.$e->getMessage());
        }
    }

    /**
     * Polling status pembayaran (AJAX Real-time endpoint)
     */
    public function checkStatus(Payment $payment): JsonResponse
    {
        $this->authorize('view', $payment);

        $payment->loadMissing(['student.parent.user', 'enrollment']);

        // Active Real-time Sync ke Pakasir jika masih pending
        if ($payment->status === 'pending' && ! empty($payment->pakasir_order_id)) {
            $this->syncPaymentWithPakasir($payment);
            $payment->refresh();
        }

        $isPaid = ($payment->status === 'paid');

        return response()->json([
            'is_paid' => $isPaid,
            'status' => $payment->status,
            'payment_date' => $payment->payment_date ? $payment->payment_date->format('d/m/Y H:i') : null,
            'redirect_url' => route('parent.schedules.list'),
        ]);
    }

    /**
     * Sinkronisasi aktif status pembayaran langsung ke server Pakasir
     */
    protected function syncPaymentWithPakasir(Payment $payment): bool
    {
        if ($payment->status === 'paid' || empty($payment->pakasir_order_id)) {
            return $payment->status === 'paid';
        }

        try {
            $statusData = $this->pakasirService->checkTransactionStatus($payment);
            $txStatus = strtolower(
                $statusData['transaction']['status']
                ?? $statusData['status']
                ?? $statusData['payment_status']
                ?? ''
            );

            if (in_array($txStatus, ['completed', 'success', 'paid', 'settlement', '1'])) {
                DB::transaction(function () use ($payment, $statusData) {
                    $payment->update([
                        'status' => 'paid',
                        'payment_date' => now(),
                        'gateway_response' => array_merge($payment->gateway_response ?? [], [
                            'synced_at' => now()->toIso8601String(),
                            'pakasir_detail' => $statusData,
                        ]),
                    ]);

                    if ($payment->enrollment) {
                        $payment->enrollment->markAsPaidAndActive();
                    }

                    $parentUser = $payment->student?->parent?->user;
                    if ($parentUser) {
                        NotificationService::notifyPaymentSuccess($payment, $parentUser);
                    }
                });

                return true;
            }
        } catch (\Throwable $e) {
            Log::warning("Gagal sync status transaksi Pakasir ID {$payment->id}: ".$e->getMessage());
        }

        return false;
    }

    /**
     * Membatalkan transaksi aktif agar orang tua dapat mengganti metode pembayaran
     */
    public function cancelPayment(Payment $payment): RedirectResponse
    {
        $this->authorize('pay', $payment);

        if ($payment->status === 'paid') {
            return redirect()->route('parent.payments.show', $payment->id)
                ->with('warning', 'Tagihan ini sudah lunas.');
        }

        $this->pakasirService->cancelActiveTransaction($payment);

        return redirect()->route('parent.payments.show', $payment->id)
            ->with('info', 'Metode pembayaran telah direset. Silakan pilih kembali metode yang diinginkan.');
    }

    public function downloadInvoice(Payment $payment)
    {
        $this->authorize('view', $payment);

        $parent = auth()->user()->parentProfile;
        $payment->loadMissing(['student.user', 'program']);

        if ($payment->status !== 'paid') {
            return redirect()->route('parent.payments.show', $payment->id)
                ->with('warning', 'Invoice resmi (kuitansi pelunasan) hanya dapat diunduh setelah pembayaran berhasil diselesaikan.');
        }

        return view('parent.payments.invoice_pdf', compact('payment', 'parent'));
    }
}
