<?php

use App\Models\BackupLog;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

test('it can record a backup log', function () {
    $log = BackupLog::create([
        'filename' => 'backup-2026.sql.gz',
        'disk' => 'local',
        'size_bytes' => 1024,
        'type' => 'daily',
        'status' => 'success',
        'started_at' => now(),
        'completed_at' => now(),
    ]);

    expect($log->id)->not->toBeNull();
    expect($log->type)->toBe('daily');
});

test('artisan backup:run generates compressed backup and records success log', function () {
    Storage::fake('local');
    config(['alhikmah.backup.disk' => 'local']);

    $exitCode = Artisan::call('backup:run', ['--type' => 'daily']);

    expect($exitCode)->toBe(0);

    $latestLog = BackupLog::where('type', 'daily')->latest('id')->first();
    expect($latestLog)->not->toBeNull();
    expect($latestLog->status)->toBe('success');
    expect($latestLog->size_bytes)->toBeGreaterThan(0);

    // Pastikan berkas tersimpan di storage disk
    expect(Storage::disk('local')->exists('backups/'.$latestLog->filename))->toBeTrue();
});

test('artisan backup:verify-restore validates integrity of the latest backup', function () {
    Storage::fake('local');
    config(['alhikmah.backup.disk' => 'local']);

    // 1. Buat backup terlebih dahulu
    Artisan::call('backup:run', ['--type' => 'daily']);

    // 2. Jalankan verifikasi restorasi
    $verifyExitCode = Artisan::call('backup:verify-restore');
    expect($verifyExitCode)->toBe(0);

    $verifyLog = BackupLog::where('type', 'dry_run_verify')->latest('id')->first();
    expect($verifyLog)->not->toBeNull();
    expect($verifyLog->status)->toBe('success');
});
