<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ParentPaymentController extends Controller
{
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

        return view('parent.payments.index', compact('pendingPayments'));
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

    public function show(int $id): View
    {
        $parent = auth()->user()->parentProfile;
        $childIds = $parent ? $parent->students()->pluck('id')->toArray() : [];

        $payment = Payment::with(['student.user', 'program'])->findOrFail($id);

        if (! in_array($payment->student_id, $childIds)) {
            abort(403, 'Akses tagihan ditolak.');
        }

        return view('parent.payments.show', compact('payment'));
    }

    public function payOnline(Request $request, int $id): RedirectResponse
    {
        $parent = auth()->user()->parentProfile;
        $childIds = $parent ? $parent->students()->pluck('id')->toArray() : [];

        $payment = Payment::findOrFail($id);
        if (! in_array($payment->student_id, $childIds)) {
            abort(403, 'Akses tagihan ditolak.');
        }

        // Simulasi integrasi Midtrans Snap / Gateway
        $payment->update([
            'status' => 'paid',
            'payment_date' => now(),
            'payment_method' => $request->input('payment_method', 'Midtrans Transfer/QRIS'),
        ]);

        return redirect()
            ->route('parent.payments.history')
            ->with('success', 'Pembayaran berhasil dikonfirmasi dan lunas!');
    }

    public function downloadInvoice(int $id)
    {
        $parent = auth()->user()->parentProfile;
        $childIds = $parent ? $parent->students()->pluck('id')->toArray() : [];

        $payment = Payment::with(['student.user', 'program'])->findOrFail($id);

        if (! in_array($payment->student_id, $childIds)) {
            abort(403, 'Akses invoice ditolak.');
        }

        return view('parent.payments.invoice_pdf', compact('payment', 'parent'));
    }
}
