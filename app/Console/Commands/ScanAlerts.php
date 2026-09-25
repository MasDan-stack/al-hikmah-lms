<?php

namespace App\Console\Commands;

use App\Services\AlertService;
use App\Services\WhatsAppService;
use Illuminate\Console\Command;

class ScanAlerts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alerts:scan {--notify : Kirim notifikasi WA/Email otomatis untuk alert berstatus kritis}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Memindai seluruh anomali operasional, finansial, dan SDM guru (3-Tier Alert System)';

    /**
     * Execute the console command.
     */
    public function handle(AlertService $alertService, WhatsAppService $whatsAppService): int
    {
        $this->info('🔍 Memulai pemindaian anomali operasional Al-Hikmah LMS...');

        $alerts = $alertService->getAllAlerts();
        $criticalCount = $alerts['critical_count'];
        $warningCount = $alerts['warning_count'];
        $infoCount = $alerts['info_count'];

        $this->table(
            ['Tier Tingkat Urgensi', 'Jumlah Anomali', 'Status'],
            [
                ['🔴 Kritis (Critical)', $criticalCount, $criticalCount > 0 ? 'Perlu Ditangani Segera' : 'Aman'],
                ['🟡 Perhatian (Warning)', $warningCount, $warningCount > 0 ? 'Perlu Monitoring' : 'Aman'],
                ['🟢 Info (Notice)', $infoCount, 'Informasi Sistem'],
            ]
        );

        if ($criticalCount > 0) {
            $this->warn("⚠️ Ditemukan {$criticalCount} anomali berstatus KRITIS:");
            foreach ($alerts['critical'] as $crit) {
                $this->line(" - [{$crit['category']}] {$crit['title']}: {$crit['description']}");
            }

            if ($this->option('notify')) {
                $this->info('Mengirim notifikasi alert ke admin sistem...');
                // Notifikasi ke nomor WhatsApp admin lembaga jika ada di konfigurasi
                $adminPhone = config('services.admin.phone', env('ADMIN_PHONE'));
                if ($adminPhone) {
                    $msg = "🔴 *PERINGATAN OPERASIONAL AL-HIKMAH LMS*\n\n"
                        ."Ditemukan *{$criticalCount} alert KRITIS* yang memerlukan tindakan manajemen segera.\n\n"
                        ."Silakan periksa dashboard pimpinan:\n".route('admin.alerts.index');
                    $whatsAppService->sendMessage($adminPhone, $msg);
                }
            }
        } else {
            $this->info('✅ Sistem berjalan optimal. Tidak ditemukan anomali kritis.');
        }

        return Command::SUCCESS;
    }
}
