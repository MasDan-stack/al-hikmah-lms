<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\Program;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(): View
    {
        $allPayments = Payment::with(['student.user', 'program', 'student.parent.user'])
            ->latest()
            ->paginate(15);

        $pendingPayments = Payment::with(['student.user', 'program', 'student.parent.user'])
            ->where('status', 'pending')
            ->latest()
            ->get();

        $students = Student::with(['user', 'parent.user'])->get();
        $programs = Program::all();

        $totalPendingAmount = Payment::where('status', 'pending')->sum('amount');
        $totalPaidAmount = Payment::where('status', 'paid')->sum('amount');

        return view('admin.payments.index', compact(
            'allPayments',
            'pendingPayments',
            'students',
            'programs',
            'totalPendingAmount',
            'totalPaidAmount'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'program_id' => 'nullable|exists:programs,id',
            'amount' => 'required|numeric|min:1000',
            'due_date' => 'required|date',
            'invoice_number' => 'nullable|string|max:50',
            'status' => 'required|in:pending,paid',
        ]);

        $invoiceNumber = $request->invoice_number ?: ('INV-'.date('Ym').'-'.Str::random(4));

        $payment = Payment::create([
            'student_id' => $request->student_id,
            'program_id' => $request->program_id,
            'amount' => $request->amount,
            'due_date' => $request->due_date,
            'invoice_number' => strtoupper($invoiceNumber),
            'status' => $request->status,
            'payment_date' => $request->status === 'paid' ? now() : null,
        ]);

        // Kirim Notifikasi otomatis ke Orang Tua Santri
        $student = Student::with('parent.user')->find($request->student_id);
        if ($student?->parent?->user_id) {
            Notification::create([
                'user_id' => $student->parent->user_id,
                'type' => 'payment_reminder',
                'title' => 'Tagihan SPP Baru Terbit',
                'message' => 'Tagihan SPP '.($student->user?->name ?? $student->full_name).' sebesar Rp '.number_format($request->amount, 0, ',', '.').' telah terbit (Jatuh Tempo: '.date('d/m/Y', strtotime($request->due_date)).').',
                'is_read' => false,
            ]);
        }

        return redirect()->back()->with('success', 'Tagihan SPP sebesar Rp '.number_format($request->amount, 0, ',', '.').' berhasil diterbitkan!');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $payment = Payment::findOrFail($id);

        $request->validate([
            'amount' => 'required|numeric|min:1000',
            'due_date' => 'required|date',
            'status' => 'required|in:pending,paid',
        ]);

        $payment->update([
            'amount' => $request->amount,
            'due_date' => $request->due_date,
            'status' => $request->status,
            'payment_date' => $request->status === 'paid' ? ($payment->payment_date ?? now()) : null,
        ]);

        return redirect()->back()->with('success', 'Data tagihan SPP #'.$payment->invoice_number.' berhasil diperbarui!');
    }

    public function destroy(int $id): RedirectResponse
    {
        $payment = Payment::findOrFail($id);
        $payment->delete();

        return redirect()->back()->with('success', 'Tagihan SPP berhasil dihapus!');
    }

    public function sendReminder(Request $request): RedirectResponse
    {
        $pendingPayments = Payment::with('student.parent')
            ->where('status', 'pending')
            ->where(function ($q) {
                $q->whereNull('due_date')
                    ->orWhereDate('due_date', '<=', now()->addDays(3));
            })
            ->get();

        $parentUserIds = $pendingPayments->map(fn ($p) => $p->student?->parent?->user_id)->filter()->unique();

        foreach ($parentUserIds as $userId) {
            Notification::create([
                'user_id' => $userId,
                'type' => 'payment_reminder',
                'title' => 'Pengingat Pembayaran SPP',
                'message' => 'Tagihan SPP anak Anda telah terbit atau mendekati jatuh tempo. Mohon segera lakukan pembayaran.',
                'is_read' => false,
            ]);
        }

        return redirect()->back()->with('success', 'Notifikasi pengingat SPP berhasil dikirim ke '.$parentUserIds->count().' Orang Tua Wali Santri.');
    }
}
