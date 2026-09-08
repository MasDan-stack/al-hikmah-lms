<?php

namespace App\Exports;

use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RevenueReportExport
{
    protected ?Carbon $startDate;

    protected ?Carbon $endDate;

    protected ?int $programId;

    protected ?string $status;

    public function __construct(
        ?Carbon $startDate = null,
        ?Carbon $endDate = null,
        ?int $programId = null,
        ?string $status = null
    ) {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->programId = $programId;
        $this->status = $status;
    }

    /**
     * Build base query for export
     */
    public function query(): Builder
    {
        $query = Payment::with(['student.parent.user', 'program'])->latest('payment_date');

        if ($this->startDate && $this->endDate) {
            $query->whereBetween('payment_date', [
                $this->startDate->copy()->startOfDay(),
                $this->endDate->copy()->endOfDay(),
            ]);
        }

        if ($this->programId) {
            $query->where('program_id', $this->programId);
        }

        if ($this->status && $this->status !== 'all') {
            $query->where('status', $this->status);
        }

        return $query;
    }

    /**
     * Download as CSV / Excel compatible spreadsheet
     */
    public function download(string $filename = 'Laporan_Keuangan_ALHIKMAH.csv'): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM untuk kompatibilitas Microsoft Excel
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header Judul Laporan
            fputcsv($handle, ['LAPORAN KEUANGAN & REKAPITULASI TRANSAKSI AL-HIKMAH LMS']);
            fputcsv($handle, ['Tanggal Cetak', now()->translatedFormat('d F Y H:i').' WIB']);
            fputcsv($handle, [
                'Periode',
                ($this->startDate ? $this->startDate->format('d/m/Y') : 'Semua').' s/d '.($this->endDate ? $this->endDate->format('d/m/Y') : 'Semua'),
            ]);
            fputcsv($handle, []); // Baris Kosong

            // Header Kolom Tabel
            fputcsv($handle, [
                'No',
                'Nomor Invoice',
                'Tanggal Transaksi',
                'Nama Santri',
                'Nama Wali Santri',
                'No. WhatsApp',
                'Program Belajar',
                'Tipe Pembayaran',
                'Metode Pembayaran',
                'Status',
                'Biaya Pendaftaran (Rp)',
                'Biaya Program (Rp)',
                'Total Nominal (Rp)',
            ]);

            $no = 1;
            $totalRegistration = 0;
            $totalProgram = 0;
            $totalOverall = 0;

            // Chunked query untuk hemat memori
            $this->query()->chunk(250, function ($payments) use (&$no, &$totalRegistration, &$totalProgram, &$totalOverall, $handle) {
                foreach ($payments as $payment) {
                    $regFee = (float) ($payment->registration_fee ?? 0);
                    $progFee = (float) ($payment->program_fee ?? $payment->amount);
                    $total = (float) $payment->amount;

                    if ($payment->status === 'paid') {
                        $totalRegistration += $regFee;
                        $totalProgram += $progFee;
                        $totalOverall += $total;
                    }

                    fputcsv($handle, [
                        $no++,
                        $payment->invoice_number,
                        $payment->payment_date ? $payment->payment_date->format('d/m/Y H:i') : '-',
                        $payment->student?->getDisplayName() ?? 'Santri',
                        $payment->student?->parent_name ?? '-',
                        $payment->student?->getParentPhone() ?? '-',
                        $payment->program?->name ?? 'Pendaftaran / Umum',
                        ucfirst($payment->payment_purpose ?? 'SPP'),
                        strtoupper($payment->payment_method ?? 'Online'),
                        strtoupper($payment->status),
                        $regFee,
                        $progFee,
                        $total,
                    ]);
                }
            });

            // Summary Totals
            fputcsv($handle, []);
            fputcsv($handle, [
                '', '', '', '', '', '', '', '', '',
                'TOTAL TRANSAKSI LUNAS:',
                $totalRegistration,
                $totalProgram,
                $totalOverall,
            ]);

            fclose($handle);
        }, 200, $headers);
    }
}
