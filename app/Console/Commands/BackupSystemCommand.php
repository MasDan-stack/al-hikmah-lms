<?php

namespace App\Console\Commands;

use App\Models\BackupLog;
use App\Traits\SendsTelegramAlerts;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BackupSystemCommand extends Command
{
    use SendsTelegramAlerts;

    protected $signature = 'backup:run {--type=daily : Tipe cadangan (daily, weekly, monthly, yearly, manual)}';

    protected $description = 'Menjalankan pencadangan basis data Al-Hikmah LMS otomatis dengan kompresi gzip & audit log';

    public function handle(): int
    {
        $type = $this->option('type') ?: 'daily';
        $disk = config('alhikmah.backup.disk', 'local');
        $startedAt = now();

        $this->info("Memulai proses backup Al-Hikmah LMS [Tipe: {$type}]...");

        $filename = 'alhikmah-backup-'.$type.'-'.$startedAt->format('Y-m-d-His').'.sql.gz';
        $relativeDir = 'backups';
        $relativePath = $relativeDir.'/'.$filename;

        try {
            // Generate SQL Dump via PDO export
            $sqlContent = $this->generateSqlDump();
            $compressedContent = gzencode($sqlContent, 9);

            // Simpan ke storage disk terkonfigurasi
            Storage::disk($disk)->put($relativePath, $compressedContent);
            $sizeBytes = strlen($compressedContent);

            $completedAt = now();

            // Catat log audit pencadangan
            $log = BackupLog::create([
                'filename' => $filename,
                'disk' => $disk,
                'size_bytes' => $sizeBytes,
                'type' => $type,
                'status' => 'success',
                'started_at' => $startedAt,
                'completed_at' => $completedAt,
            ]);

            $this->info("Backup berhasil disimpan: {$relativePath} (".round($sizeBytes / 1024, 2).' KB)');

            // Terapkan kebijakan retensi GFS
            $this->applyRetentionPolicy($type, $disk);

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Gagal melakukan backup: '.$e->getMessage());

            BackupLog::create([
                'filename' => $filename,
                'disk' => $disk,
                'size_bytes' => 0,
                'type' => $type,
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'started_at' => $startedAt,
                'completed_at' => now(),
            ]);

            $this->sendTelegramAlert("🚨 *Pencadangan Sistem Gagal*\nTipe: `{$type}`\nError: {$e->getMessage()}", 'critical');

            return Command::FAILURE;
        }
    }

    /**
     * Menghasilkan dump SQL dari seluruh tabel basis data secara portabel (native PDO).
     */
    protected function generateSqlDump(): string
    {
        $tableRows = DB::select('SHOW FULL TABLES WHERE Table_type = "BASE TABLE"');
        $tables = array_map(function ($row) {
            return array_values((array) $row)[0];
        }, $tableRows);

        $output = "-- AL-HIKMAH LMS DATABASE DUMP\n";
        $output .= '-- Generated: '.now()->toIso8601String()."\n";
        $output .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $table) {
            $createTable = DB::select("SHOW CREATE TABLE `{$table}`");
            $createStatement = $createTable[0]->{'Create Table'} ?? null;

            if ($createStatement) {
                $output .= "DROP TABLE IF EXISTS `{$table}`;\n";
                $output .= $createStatement.";\n\n";
            }

            // Dump Data
            $rows = DB::table($table)->get();
            if ($rows->isNotEmpty()) {
                foreach ($rows as $row) {
                    $values = array_map(function ($val) {
                        if (is_null($val)) {
                            return 'NULL';
                        }

                        return "'".addslashes((string) $val)."'";
                    }, (array) $row);

                    $output .= "INSERT INTO `{$table}` VALUES (".implode(', ', $values).");\n";
                }
                $output .= "\n";
            }
        }

        $output .= "SET FOREIGN_KEY_CHECKS=1;\n";

        return $output;
    }

    /**
     * Menerapkan batas retensi GFS berdasarkan konfigurasi alhikmah.backup.retention.
     */
    protected function applyRetentionPolicy(string $type, string $disk): void
    {
        $maxRetained = config("alhikmah.backup.retention.{$type}", 7);

        $successfulBackups = BackupLog::where('type', $type)
            ->where('status', 'success')
            ->orderBy('id', 'desc')
            ->get();

        if ($successfulBackups->count() > $maxRetained) {
            $toDelete = $successfulBackups->slice($maxRetained);

            foreach ($toDelete as $oldBackup) {
                $oldPath = 'backups/'.$oldBackup->filename;
                if (Storage::disk($disk)->exists($oldPath)) {
                    Storage::disk($disk)->delete($oldPath);
                }
                $oldBackup->delete();
            }

            $this->info("Retensi GFS diterapkan: {$toDelete->count()} cadangan usang dibersihkan.");
        }
    }
}
