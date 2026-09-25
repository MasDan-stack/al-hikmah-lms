<?php

namespace App\Services;

use App\Models\JuzProgress;
use App\Models\Progress;
use App\Models\Student;

class HifzProgressService
{
    /**
     * Jumlah ayat standar untuk tiap-tiap Juz (1-30) dalam Al-Qur'an
     */
    protected array $juzAyatCounts = [
        1 => 148, 2 => 111, 3 => 126, 4 => 131, 5 => 124,
        6 => 110, 7 => 149, 8 => 142, 9 => 159, 10 => 127,
        11 => 151, 12 => 170, 13 => 154, 14 => 227, 15 => 185,
        16 => 269, 17 => 190, 18 => 202, 19 => 339, 20 => 171,
        21 => 178, 22 => 169, 23 => 357, 24 => 175, 25 => 246,
        26 => 195, 27 => 399, 28 => 137, 29 => 431, 30 => 564,
    ];

    /**
     * Inisialisasi data progress 30 Juz untuk santri baru
     */
    public function initializeJuzProgress(Student $student): void
    {
        for ($juz = 1; $juz <= 30; $juz++) {
            JuzProgress::firstOrCreate(
                [
                    'student_id' => $student->id,
                    'juz_number' => $juz,
                ],
                [
                    'total_ayat' => $this->juzAyatCounts[$juz] ?? 200,
                    'ayat_hafal' => 0,
                    'percentage' => 0.00,
                    'status' => 'not_started',
                ]
            );
        }
    }

    public function initializeStudentJuzProgress(Student $student): void
    {
        $this->initializeJuzProgress($student);
    }

    /**
     * Hitung ulang progres Juz berdasarkan rekaman di tabel progress
     */
    public function calculateJuzProgress(Student $student, int $juzNumber): JuzProgress
    {
        $juzRecord = JuzProgress::firstOrCreate(
            ['student_id' => $student->id, 'juz_number' => $juzNumber],
            ['total_ayat' => $this->juzAyatCounts[$juzNumber] ?? 200]
        );

        $progresses = Progress::where('student_id', $student->id)
            ->where(function ($q) use ($juzNumber) {
                $q->where('juz', $juzNumber)
                    ->orWhere('juz_number', $juzNumber);
            })
            ->get();

        $totalAyatHafal = 0;
        $hasMutqinExam = false;

        foreach ($progresses as $prog) {
            $ayatCount = 0;
            if ($prog->ayat_start && $prog->ayat_end) {
                $ayatCount = max(1, abs($prog->ayat_end - $prog->ayat_start) + 1);
            } elseif ($prog->kategori === 'Tahfidz') {
                $ayatCount = 5; // Default estimasi setoran
            }
            $totalAyatHafal += $ayatCount;

            if ($prog->is_mutqin_test && ($prog->nilai_fluent >= 85 || $prog->nilai_tajwid >= 85)) {
                $hasMutqinExam = true;
            }
        }

        $totalAyat = $juzRecord->total_ayat > 0 ? $juzRecord->total_ayat : ($this->juzAyatCounts[$juzNumber] ?? 200);
        $totalAyatHafal = min($totalAyat, $totalAyatHafal);
        $percentage = $totalAyat > 0 ? round(($totalAyatHafal / $totalAyat) * 100, 2) : 0.00;

        $status = 'not_started';
        if ($hasMutqinExam || ($percentage >= 100 && $progresses->where('is_mutqin_test', true)->count() > 0)) {
            $status = 'mutqin';
            $juzRecord->mutqin_at = $juzRecord->mutqin_at ?: now();
            $juzRecord->completed_at = $juzRecord->completed_at ?: now();
        } elseif ($percentage >= 100) {
            $status = 'completed';
            $juzRecord->completed_at = $juzRecord->completed_at ?: now();
        } elseif ($totalAyatHafal > 0) {
            $status = 'in_progress';
            $juzRecord->started_at = $juzRecord->started_at ?: now();
        }

        $juzRecord->ayat_hafal = $totalAyatHafal;
        $juzRecord->percentage = $percentage;
        $juzRecord->status = $status;
        $juzRecord->save();

        return $juzRecord;
    }

    /**
     * Mengambil ringkasan progres santri di seluruh Juz
     */
    public function getSummary(Student $student): array
    {
        $allJuz = JuzProgress::where('student_id', $student->id)->orderBy('juz_number')->get();

        if ($allJuz->isEmpty()) {
            $this->initializeJuzProgress($student);
            $allJuz = JuzProgress::where('student_id', $student->id)->orderBy('juz_number')->get();
        }

        $totalMutqin = $allJuz->where('status', 'mutqin')->count();
        $totalCompleted = $allJuz->whereIn('status', ['completed', 'mutqin'])->count();
        $totalActive = $allJuz->where('status', 'in_progress')->count();
        $totalAyatHafal = $allJuz->sum('ayat_hafal');
        $totalAyatAll = $allJuz->sum('total_ayat');
        $avgPercentage = $totalAyatAll > 0 ? round(($totalAyatHafal / $totalAyatAll) * 100, 1) : 0;

        return [
            'total_mutqin' => $totalMutqin,
            'total_completed' => $totalCompleted,
            'total_active' => $totalActive,
            'total_ayat_hafal' => $totalAyatHafal,
            'total_ayat_all' => $totalAyatAll,
            'overall_percentage' => $avgPercentage,
            'juz_list' => $allJuz,
        ];
    }

    public function getProgressSummary(Student $student): array
    {
        return $this->getSummary($student);
    }
}
