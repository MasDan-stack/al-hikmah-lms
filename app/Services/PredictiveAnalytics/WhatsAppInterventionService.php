<?php

namespace App\Services\PredictiveAnalytics;

use App\Models\PredictiveAnalyticsAuditLog;
use App\Models\Student;
use App\Models\StudentDropoutPrediction;
use App\Services\WhatsAppService;

class WhatsAppInterventionService
{
    public function __construct(
        protected WhatsAppService $whatsappService
    ) {}

    public function buildInterventionMessage(Student $student, array $riskData): string
    {
        $parentName = $student->parent?->user?->name ?? 'Ayah/Bunda';
        $factors = $riskData['risk_factors'] ?? [];

        $message = "Assalamu'alaikum Warahmatullahi Wabarakatuh, Ayah/Bunda {$parentName}. 🌸\n\n";
        $message .= "Semoga Ayah/Bunda dan ananda {$student->full_name} senantiasa dalam lindungan dan keberkahan Allah SWT.\n\n";
        $message .= "Kami dari Tim Akademik AL-HIKMAH LMS ingin bersilaturahmi dan memastikan kelancaran bimbingan ananda.\n\n";

        // Paragraf dinamis berbasis faktor pemicu
        if (in_array('Presensi rendah (< 70%)', $factors) || in_array('Kehadiran rendah', $factors)) {
            $message .= "📅 *Kehadiran Belajar Ananda:*\n";
            $message .= "Kami melihat ananda sempat berhalangan hadir pada beberapa sesi terakhir. Apakah ada kendala waktu bimbingan yang bisa kami bantu sesuaikan bersama mentor?\n\n";
        }

        if (in_array('Pembayaran menunggak > 30 hari', $factors) || in_array('Pembayaran tidak rutin', $factors)) {
            $message .= "💳 *Administrasi Bimbingan:*\n";
            $message .= "Berikut kami sampaikan pengingat tagihan bimbingan ananda. Pembayaran dapat diselesaikan dengan mudah melalui portal wali santri.\n\n";
        }

        if (in_array('Progres hafalan stagnan > 14 hari', $factors) || in_array('Progres hafalan stagnan', $factors)) {
            $message .= "📖 *Motivasi Hafalan Ananda:*\n";
            $message .= "Kami ingin memastikan ananda tetap bersemangat dan nyaman dalam menambah hafalan. Guru pembimbing siap menyesuaikan metode belajar agar ananda kembali ceria.\n\n";
        }

        $message .= "Semoga Allah SWT senantiasa memudahkan langkah ananda dalam mempelajari Al-Qur'an.\n";
        $message .= "Jazakumullah khairan katsiran. 🙏\n\n";
        $message .= "*AL-HIKMAH LMS* - Sistem Pendampingan Al-Qur'an Terpadu";

        return $message;
    }

    public function sendIntervention(Student $student, array $riskData, int $userId): array
    {
        $message = $this->buildInterventionMessage($student, $riskData);
        $phone = $student->parent?->emergency_phone ?? $student->parent?->user?->phone ?? $student->user?->phone;

        if (! $phone || $phone === '-') {
            return ['success' => false, 'reason' => 'Nomor WhatsApp wali santri tidak ditemukan.'];
        }

        $result = $this->whatsappService->sendMessage($phone, $message);

        // Update status is_alerted di snapshot prediksi
        StudentDropoutPrediction::where('student_id', $student->id)
            ->whereDate('prediction_date', today())
            ->update([
                'is_alerted' => true,
                'alerted_at' => now(),
            ]);

        // Update flag di tabel students
        $student->update([
            'last_dropout_prediction_at' => today(),
            'dropout_risk_level' => $riskData['risk_level'] ?? 'low',
            'dropout_risk_score' => $riskData['risk_score'] ?? 0.00,
        ]);

        // Catat Audit Trail
        PredictiveAnalyticsAuditLog::create([
            'user_id' => $userId,
            'action_type' => 'intervention_wa',
            'target_type' => 'student',
            'target_id' => $student->id,
            'metadata' => [
                'phone' => $phone,
                'risk_level' => $riskData['risk_level'] ?? 'low',
                'risk_score' => $riskData['risk_score'] ?? 0.00,
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return [
            'success' => true,
            'phone' => $phone,
            'message' => $message,
            'result' => $result,
        ];
    }

    public function sendInterventionByPrediction(StudentDropoutPrediction $prediction, ?int $userId = null): array
    {
        $student = $prediction->student;
        if (! $student) {
            return ['success' => false, 'reason' => 'Data santri tidak ditemukan.'];
        }

        $userId = $userId ?? auth()->id() ?? 1;

        $riskData = [
            'risk_level' => $prediction->risk_level,
            'risk_score' => $prediction->risk_score,
            'risk_factors' => (array) $prediction->risk_factors,
        ];

        $res = $this->sendIntervention($student, $riskData, $userId);

        if ($res['success']) {
            $prediction->update([
                'is_alerted' => true,
                'alerted_at' => now(),
            ]);
        }

        return $res;
    }
}
