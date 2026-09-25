<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeatureUsage;
use App\Models\Mentor;
use App\Models\Payment;
use App\Models\Question;
use App\Models\Session;
use App\Models\SessionConfirmation;
use App\Models\Student;
use App\Models\TrialBooking;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RealityCheckController extends Controller
{
    public function index()
    {
        $metrics = Cache::remember('reality_check_metrics', 300, function () {
            // 1. Santri Aktif Riil = punya minimal 1 sesi completed dalam 30 hari terakhir
            $activeStudentsCount = Student::whereHas('sessions', function ($q) {
                $q->where('status', 'completed')
                    ->where('date', '>=', now()->subDays(30));
            })->count();

            // 2. Guru Aktif Riil = punya minimal 1 sesi completed dalam 30 hari terakhir
            $activeMentorsCount = Mentor::whereHas('sessions', function ($q) {
                $q->where('status', 'completed')
                    ->where('date', '>=', now()->subDays(30));
            })->count();

            // 3. Rata-rata Sesi Selesai / Hari dalam 30 hari terakhir
            $completedSessions30d = Session::where('status', 'completed')
                ->where('date', '>=', now()->subDays(30))
                ->count();
            $avgSessionsPerDay = round($completedSessions30d / 30, 1);

            // 4. Gross Revenue Riil Bulan Ini = pembayaran riil berstatus paid
            $grossRevenueMonth = Payment::where('status', 'paid')
                ->whereMonth('payment_date', now()->month)
                ->whereYear('payment_date', now()->year)
                ->sum('amount');

            // 5. Utilisasi 10 Fitur Utama (Hibrida Query & Usage Log)
            $featureUsage = $this->getFeatureUsageReport();

            return compact(
                'activeStudentsCount',
                'activeMentorsCount',
                'completedSessions30d',
                'avgSessionsPerDay',
                'grossRevenueMonth',
                'featureUsage'
            );
        });

        return view('admin.reality-check.index', $metrics);
    }

    public function exportCsv(): StreamedResponse
    {
        $filename = 'reality-check-report-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Kategori', 'Indikator', 'Nilai Riil', 'Definisi Validasi']);

            $metrics = Cache::get('reality_check_metrics');

            $activeStudentsCount = $metrics['activeStudentsCount'] ?? 0;
            $activeMentorsCount = $metrics['activeMentorsCount'] ?? 0;
            $avgSessionsPerDay = $metrics['avgSessionsPerDay'] ?? 0;
            $grossRevenueMonth = $metrics['grossRevenueMonth'] ?? 0;

            // Data Baris
            fputcsv($handle, ['Metrik Inti', 'Santri Aktif Riil', $activeStudentsCount, 'Memiliki >= 1 sesi completed dalam 30 hari']);
            fputcsv($handle, ['Metrik Inti', 'Guru Aktif Riil', $activeMentorsCount, 'Memandu >= 1 sesi completed dalam 30 hari']);
            fputcsv($handle, ['Metrik Inti', 'Sesi Selesai / Hari', $avgSessionsPerDay, 'Rata-rata sesi selesai per hari (30 hari terakhir)']);
            fputcsv($handle, ['Metrik Finansial', 'Pendapatan Riil Bulan Ini', $grossRevenueMonth, 'Total tabel payments berstatus paid']);

            fputcsv($handle, []);
            fputcsv($handle, ['Fitur Key', 'Nama Fitur', 'Akses 30 Hari', 'Terakhir Digunakan', 'Status Adopsi']);

            foreach ($this->getFeatureUsageReport() as $f) {
                fputcsv($handle, [$f['key'], $f['name'], $f['count_30d'], $f['last_used'], $f['status']]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    protected function formatFeature(string $key, string $name, int $count, string $lastUsed): array
    {
        $status = 'menganggur';
        if ($count > 20) {
            $status = 'aktif';
        } elseif ($count > 0) {
            $status = 'jarang';
        }

        return [
            'key' => $key,
            'name' => $name,
            'count_30d' => $count,
            'last_used' => $lastUsed,
            'status' => $status,
        ];
    }

    protected function getFeatureUsageReport(): array
    {
        $features = [];

        // Opsi C (Berdasarkan Record Database)
        $mutabaahCount = Session::whereNotNull('notes')
            ->where('date', '>=', now()->subDays(30))
            ->count();
        $features[] = $this->formatFeature('mutabaah', 'Rapor Mutaba\'ah Santri', $mutabaahCount, $mutabaahCount > 0 ? 'Dalam 30 hari' : 'Belum digunakan');

        $aiCount = Question::where('created_by_ai', true)
            ->where('created_at', '>=', now()->subDays(30))
            ->count();
        $features[] = $this->formatFeature('ai_exam', 'Generator Soal Ujian AI', $aiCount, $aiCount > 0 ? 'Dalam 30 hari' : 'Belum digunakan');

        $trialCount = TrialBooking::where('created_at', '>=', now()->subDays(30))->count();
        $features[] = $this->formatFeature('trial_booking', 'Booking Uji Coba Gratis', $trialCount, $trialCount > 0 ? 'Dalam 30 hari' : 'Belum digunakan');

        $attendanceCount = SessionConfirmation::where('created_at', '>=', now()->subDays(30))->count();
        $features[] = $this->formatFeature('attendance', 'Presensi Mengajar Guru', $attendanceCount, $attendanceCount > 0 ? 'Dalam 30 hari' : 'Belum digunakan');

        $paymentCount = Payment::where('created_at', '>=', now()->subDays(30))->count();
        $features[] = $this->formatFeature('payments', 'Pembayaran via Gateway', $paymentCount, $paymentCount > 0 ? 'Dalam 30 hari' : 'Belum digunakan');

        // Opsi B (Berdasarkan tabel feature_usages)
        $trackedFeatures = FeatureUsage::all();
        foreach ($trackedFeatures as $tf) {
            $features[] = $this->formatFeature(
                $tf->feature_key,
                $tf->feature_name,
                $tf->use_count_30d,
                $tf->last_used_at ? $tf->last_used_at->diffForHumans() : 'Belum digunakan'
            );
        }

        // Limit or sort by count
        usort($features, fn ($a, $b) => $b['count_30d'] <=> $a['count_30d']);

        return array_slice($features, 0, 10);
    }
}
