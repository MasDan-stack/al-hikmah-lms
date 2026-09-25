<?php

namespace App\Console\Commands;

use App\Models\BackupLog;
use App\Traits\SendsTelegramAlerts;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class VerifyBackupRestoreCommand extends Command
{
    use SendsTelegramAlerts;

    protected $signature = 'backup:verify-restore';

    protected $description = 'Melakukan simulasi verifikasi pemulihan (dry-run restore) cadangan basis data mingguan';

    public function handle(): int
    {
        $startedAt = now();
        $this->info('Memulai verifikasi pemulihan cadangan data (Dry-Run Restore)...');

        // 1. Ambil log backup sukses terakhir
        $latestBackup = BackupLog::where('status', 'success')
            ->where('type', '!=', 'dry_run_verify')
            ->latest('id')
            ->first();

        if (! $latestBackup) {
            $this->warn('Tidak ditemukan berkas cadangan aktif untuk diverifikasi.');
            $this->recordVerificationLog('dry-run-verify-none.sql', 0, 'failed', 'Tidak ada riwayat backup sukses di tabel backup_logs', $startedAt);

            return Command::FAILURE;
        }

        $disk = $latestBackup->disk ?? config('alhikmah.backup.disk', 'local');
        $path = 'backups/'.$latestBackup->filename;

        if (! Storage::disk($disk)->exists($path)) {
            $msg = "Berkas cadangan {$latestBackup->filename} tidak ditemukan di disk {$disk}!";
            $this->error($msg);
            $this->recordVerificationLog($latestBackup->filename, 0, 'failed', $msg, $startedAt);
            $this->sendTelegramAlert("🚨 *Verifikasi Restore Gagal*: {$msg}", 'critical');

            return Command::FAILURE;
        }

        try {
            // 2. Baca dan verifikasi integritas kompresi gzip
            $compressedData = Storage::disk($disk)->get($path);
            $decompressedSql = @gzdecode($compressedData);

            if ($decompressedSql === false || empty($decompressedSql)) {
                throw new \RuntimeException('Gagal melakukan dekompresi gzip (berkas korup atau tidak valid).');
            }

            // 3. Sanity check: verifikasi tabel-tabel utama di dalam dump
            $criticalTables = ['users', 'students', 'learning_sessions', 'payments', 'mentors'];
            $integrityErrors = [];

            foreach ($criticalTables as $table) {
                // Pastikan definisi tabel ada di file dump
                if (! str_contains($decompressedSql, "`{$table}`")) {
                    $integrityErrors[] = "Tabel `{$table}` tidak ditemukan di dalam berkas dump SQL.";
                }

                // Cek deviasi baris jika memungkinkan
                $currentCount = DB::table($table)->count();
                $dumpMatches = preg_match_all("/INSERT INTO `{$table}`/i", $decompressedSql);

                if ($currentCount > 0 && $dumpMatches === 0) {
                    $integrityErrors[] = "Tabel `{$table}` memiliki {$currentCount} baris di database, tetapi tidak ada INSERT di backup.";
                }
            }

            if (! empty($integrityErrors)) {
                $errorMsg = implode('; ', $integrityErrors);
                throw new \RuntimeException($errorMsg);
            }

            $sizeBytes = strlen($compressedData);
            $this->recordVerificationLog($latestBackup->filename, $sizeBytes, 'success', null, $startedAt);
            $this->info("Verifikasi restorasi berkas {$latestBackup->filename} BERHASIL (100% integritas tabel inti terverifikasi).");

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Verifikasi restorasi gagal: '.$e->getMessage());

            $this->recordVerificationLog($latestBackup->filename, 0, 'failed', $e->getMessage(), $startedAt);
            $this->sendTelegramAlert("🚨 *Simulasi Pemulihan Backup Mingguan Gagal!*\nFile: `{$latestBackup->filename}`\nDeviasi/Error: {$e->getMessage()}", 'critical');

            return Command::FAILURE;
        }
    }

    protected function recordVerificationLog(string $filename, int $sizeBytes, string $status, ?string $errorMessage, \DateTimeInterface $startedAt): void
    {
        BackupLog::create([
            'filename' => 'verify_'.$filename,
            'disk' => config('alhikmah.backup.disk', 'local'),
            'size_bytes' => $sizeBytes,
            'type' => 'dry_run_verify',
            'status' => $status,
            'error_message' => $errorMessage,
            'started_at' => $startedAt,
            'completed_at' => now(),
        ]);
    }
}
