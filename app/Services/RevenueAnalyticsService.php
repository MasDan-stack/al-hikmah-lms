<?php

namespace App\Services;

use App\Models\FinancialAuditLog;
use App\Models\Payment;
use App\Models\Program;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class RevenueAnalyticsService
{
    /**
     * Cache duration in minutes
     */
    protected int $cacheTtl = 15;

    /**
     * Dapatkan ringkasan metrik finansial utama (Summary Metrics)
     */
    public function getSummaryMetrics(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $now = now();
        $thisMonthStart = $now->copy()->startOfMonth();
        $thisMonthEnd = $now->copy()->endOfMonth();
        $lastMonthStart = $now->copy()->subMonth()->startOfMonth();
        $lastMonthEnd = $now->copy()->subMonth()->endOfMonth();

        // 1. Total Pendapatan Seluruh Waktu / Berdasarkan Rentang Tanggal
        $totalRevenueQuery = Payment::where('status', 'paid');
        if ($startDate && $endDate) {
            $totalRevenueQuery->whereBetween('payment_date', [$startDate->startOfDay(), $endDate->endOfDay()]);
        }
        $totalRevenue = (float) $totalRevenueQuery->sum('amount');
        $totalPaidInvoices = (int) $totalRevenueQuery->count();

        // 2. Pendapatan Bulan Ini & Bulan Lalu
        $thisMonthRevenue = (float) Payment::where('status', 'paid')
            ->whereBetween('payment_date', [$thisMonthStart, $thisMonthEnd])
            ->sum('amount');

        $lastMonthRevenue = (float) Payment::where('status', 'paid')
            ->whereBetween('payment_date', [$lastMonthStart, $lastMonthEnd])
            ->sum('amount');

        // 3. Month-over-Month (MoM) Growth Percentage
        if ($lastMonthRevenue > 0) {
            $momGrowthPercent = round((($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1);
        } else {
            $momGrowthPercent = $thisMonthRevenue > 0 ? 100.0 : 0.0;
        }

        // 4. ARPU (Average Revenue Per User / Santri Aktif)
        $activeStudentsCount = Student::whereHas('programs', fn ($q) => $q->where('student_program.status', 'active'))->count();
        if ($activeStudentsCount === 0) {
            $activeStudentsCount = max(1, Student::count());
        }
        $arpu = round($thisMonthRevenue / max(1, $activeStudentsCount), 0);

        // 5. Tagihan Pending & Overdue
        $pendingPayments = Payment::where('status', 'pending');
        $pendingInvoicesCount = (int) $pendingPayments->count();
        $pendingInvoicesAmount = (float) $pendingPayments->sum('amount');

        $today = today();
        $overduePayments = Payment::where('status', 'pending')
            ->whereNotNull('due_date')
            ->where('due_date', '<', $today);
        $overdueInvoicesCount = (int) $overduePayments->count();
        $overdueInvoicesAmount = (float) $overduePayments->sum('amount');

        return [
            'total_revenue' => $totalRevenue,
            'total_paid_invoices' => $totalPaidInvoices,
            'this_month_revenue' => $thisMonthRevenue,
            'last_month_revenue' => $lastMonthRevenue,
            'mom_growth_percent' => $momGrowthPercent,
            'arpu' => $arpu,
            'active_students_count' => $activeStudentsCount,
            'pending_invoices_count' => $pendingInvoicesCount,
            'pending_invoices_amount' => $pendingInvoicesAmount,
            'overdue_invoices_count' => $overdueInvoicesCount,
            'overdue_invoices_amount' => $overdueInvoicesAmount,
        ];
    }

    /**
     * Dapatkan data tren pendapatan 12 bulan terakhir untuk ApexCharts
     */
    public function get12MonthsTrend(): array
    {
        return Cache::remember('revenue_analytics_12_months_trend', now()->addMinutes($this->cacheTtl), function () {
            $categories = [];
            $revenueSeries = [];
            $invoicesSeries = [];
            $lastYearSeries = [];

            $now = now();

            for ($i = 11; $i >= 0; $i--) {
                $monthDate = $now->copy()->subMonths($i);
                $monthStart = $monthDate->copy()->startOfMonth();
                $monthEnd = $monthDate->copy()->endOfMonth();

                $categories[] = $monthDate->translatedFormat('M Y');

                // Pendapatan bulan bersangkutan
                $rev = (float) Payment::where('status', 'paid')
                    ->whereBetween('payment_date', [$monthStart, $monthEnd])
                    ->sum('amount');
                $revenueSeries[] = $rev;

                // Jumlah transaksi lunas
                $count = (int) Payment::where('status', 'paid')
                    ->whereBetween('payment_date', [$monthStart, $monthEnd])
                    ->count();
                $invoicesSeries[] = $count;

                // Pendapatan bulan yang sama tahun lalu (YoY)
                $prevYearStart = $monthStart->copy()->subYear();
                $prevYearEnd = $monthEnd->copy()->subYear();
                $prevRev = (float) Payment::where('status', 'paid')
                    ->whereBetween('payment_date', [$prevYearStart, $prevYearEnd])
                    ->sum('amount');
                $lastYearSeries[] = $prevRev;
            }

            return [
                'categories' => $categories,
                'series' => [
                    [
                        'name' => 'Pendapatan (Rp)',
                        'type' => 'area',
                        'data' => $revenueSeries,
                    ],
                    [
                        'name' => 'Tahun Lalu (Rp)',
                        'type' => 'line',
                        'data' => $lastYearSeries,
                    ],
                ],
                'invoices_count_series' => $invoicesSeries,
            ];
        });
    }

    /**
     * Dapatkan breakdown pendapatan per program
     */
    public function getProgramBreakdown(): array
    {
        return Cache::remember('revenue_analytics_program_breakdown', now()->addMinutes($this->cacheTtl), function () {
            $programs = Program::withCount(['students' => function ($q) {
                $q->where('student_program.status', 'active');
            }])->get();

            $labels = [];
            $series = [];
            $details = [];
            $totalRevenueAll = (float) Payment::where('status', 'paid')->sum('amount');

            foreach ($programs as $program) {
                $rev = (float) Payment::where('status', 'paid')
                    ->where('program_id', $program->id)
                    ->sum('amount');

                $labels[] = $program->name;
                $series[] = $rev;

                $percentage = $totalRevenueAll > 0 ? round(($rev / $totalRevenueAll) * 100, 1) : 0;

                $details[] = [
                    'id' => $program->id,
                    'name' => $program->name,
                    'category' => $program->category ?? 'Umum',
                    'revenue' => $rev,
                    'percentage' => $percentage,
                    'active_students' => $program->students_count,
                    'price' => (float) $program->price,
                ];
            }

            // Sertakan transaksi tanpa program jika ada
            $noProgramRev = (float) Payment::where('status', 'paid')
                ->whereNull('program_id')
                ->sum('amount');

            if ($noProgramRev > 0) {
                $labels[] = 'Pendaftaran / Lainnya';
                $series[] = $noProgramRev;
                $percentage = $totalRevenueAll > 0 ? round(($noProgramRev / $totalRevenueAll) * 100, 1) : 0;
                $details[] = [
                    'id' => null,
                    'name' => 'Pendaftaran / Lainnya',
                    'category' => 'Registrasi',
                    'revenue' => $noProgramRev,
                    'percentage' => $percentage,
                    'active_students' => 0,
                    'price' => 0,
                ];
            }

            return [
                'labels' => $labels,
                'series' => $series,
                'details' => $details,
                'total_revenue' => $totalRevenueAll,
            ];
        });
    }

    /**
     * Dapatkan status distribusi pembayaran (Paid, Pending, Overdue, Cancelled)
     */
    public function getPaymentStatusDistribution(): array
    {
        $today = today();

        $paidCount = Payment::where('status', 'paid')->count();
        $paidAmount = (float) Payment::where('status', 'paid')->sum('amount');

        $pendingWithinDue = Payment::where('status', 'pending')
            ->where(function ($q) use ($today) {
                $q->whereNull('due_date')->orWhere('due_date', '>=', $today);
            })->count();
        $pendingAmount = (float) Payment::where('status', 'pending')
            ->where(function ($q) use ($today) {
                $q->whereNull('due_date')->orWhere('due_date', '>=', $today);
            })->sum('amount');

        $overdueCount = Payment::where('status', 'pending')
            ->whereNotNull('due_date')
            ->where('due_date', '<', $today)
            ->count();
        $overdueAmount = (float) Payment::where('status', 'pending')
            ->whereNotNull('due_date')
            ->where('due_date', '<', $today)
            ->sum('amount');

        $cancelledCount = Payment::where('status', 'cancelled')->count();
        $cancelledAmount = (float) Payment::where('status', 'cancelled')->sum('amount');

        $totalCount = $paidCount + $pendingWithinDue + $overdueCount + $cancelledCount;

        return [
            'paid' => ['count' => $paidCount, 'amount' => $paidAmount, 'percent' => $totalCount > 0 ? round(($paidCount / $totalCount) * 100, 1) : 0],
            'pending' => ['count' => $pendingWithinDue, 'amount' => $pendingAmount, 'percent' => $totalCount > 0 ? round(($pendingWithinDue / $totalCount) * 100, 1) : 0],
            'overdue' => ['count' => $overdueCount, 'amount' => $overdueAmount, 'percent' => $totalCount > 0 ? round(($overdueCount / $totalCount) * 100, 1) : 0],
            'cancelled' => ['count' => $cancelledCount, 'amount' => $cancelledAmount, 'percent' => $totalCount > 0 ? round(($cancelledCount / $totalCount) * 100, 1) : 0],
            'total_count' => $totalCount,
        ];
    }

    /**
     * Dapatkan log audit keuangan terbaru
     */
    public function getRecentFinancialAuditLogs(int $limit = 10): Collection
    {
        return FinancialAuditLog::with('user')
            ->latest('created_at')
            ->take($limit)
            ->get();
    }

    /**
     * Bersihkan cache analitik
     */
    public function clearCache(): void
    {
        Cache::forget('revenue_analytics_12_months_trend');
        Cache::forget('revenue_analytics_program_breakdown');
    }
}
