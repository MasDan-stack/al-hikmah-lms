<?php

namespace App\Services;

use App\Models\FinancialAuditLog;
use App\Models\ParentProfile;
use Illuminate\Support\Collection;

class BroadcastService
{
    public function __construct(
        protected WhatsAppService $whatsAppService
    ) {}

    /**
     * Dapatkan daftar calon penerima pesan berdasarkan filter target
     *
     * @return Collection<int, array{parent_id: int, user_id: int, parent_name: string, phone: string, children_names: string, program_names: string}>
     */
    public function resolveRecipients(string $targetType, ?int $targetId = null): Collection
    {
        $query = ParentProfile::with(['user', 'students.programs', 'students.mentors']);

        if ($targetType === 'program' && $targetId) {
            $query->whereHas('students.programs', function ($q) use ($targetId) {
                $q->where('programs.id', $targetId);
            });
        } elseif ($targetType === 'mentor' && $targetId) {
            $query->whereHas('students.mentors', function ($q) use ($targetId) {
                $q->where('mentors.id', $targetId);
            });
        }

        return $query->get()->map(function ($parent) {
            $user = $parent->user;
            $phone = $parent->emergency_phone ?? $user?->phone ?? '';
            $students = $parent->students;

            $childrenNames = $students->map(fn ($s) => $s->getDisplayName())->join(', ');
            $programNames = $students->flatMap(fn ($s) => $s->programs->pluck('name'))->unique()->join(', ');

            return [
                'parent_id' => $parent->id,
                'user_id' => $user?->id ?? 0,
                'parent_name' => $user?->name ?? 'Wali Santri',
                'phone' => $phone,
                'children_names' => $childrenNames ?: 'Ananda',
                'program_names' => $programNames ?: 'Program Al-Hikmah',
            ];
        })->filter(fn ($r) => ! empty($r['phone']))->values();
    }

    /**
     * Parse template variabel dinamis
     */
    public function parseTemplate(string $template, array $recipientData): string
    {
        $replacements = [
            '{nama_ortu}' => $recipientData['parent_name'] ?? 'Bapak/Ibu',
            '{nama_anak}' => $recipientData['children_names'] ?? 'Ananda',
            '{program}' => $recipientData['program_names'] ?? 'Program Al-Qur\'an',
            '{tanggal}' => now()->translatedFormat('d F Y'),
            '{lembaga}' => 'AL-HIKMAH LMS',
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }

    /**
     * Kirim broadcast massal ke seluruh penerima
     */
    public function dispatchBroadcast(string $template, string $targetType, ?int $targetId = null, ?string $title = null): array
    {
        $recipients = $this->resolveRecipients($targetType, $targetId);
        $total = $recipients->count();
        $successCount = 0;
        $failedCount = 0;

        foreach ($recipients as $recipient) {
            $message = $this->parseTemplate($template, $recipient);

            // Kirim via WhatsAppService
            $sent = $this->whatsAppService->sendMessage($recipient['phone'], $message);

            if ($sent) {
                $successCount++;
            } else {
                $failedCount++;
            }
        }

        // Catat jejak audit
        FinancialAuditLog::log(
            auth()->id() ?? 1,
            'whatsapp_broadcast',
            'broadcast',
            $total,
            null,
            [
                'target_type' => $targetType,
                'target_id' => $targetId,
                'total_recipients' => $total,
                'success_count' => $successCount,
                'failed_count' => $failedCount,
                'title' => $title ?? 'Broadcast Massal',
            ]
        );

        return [
            'total' => $total,
            'success_count' => $successCount,
            'failed_count' => $failedCount,
        ];
    }

    /**
     * Template pesan siap pakai (Preset Templates)
     */
    public function getPresetTemplates(): array
    {
        return [
            [
                'id' => 'holiday',
                'name' => 'Pengumuman Libur / Cuti Bersama',
                'template' => "Assalamu'alaikum Warahmatullahi Wabarakatuh.\n\nYth. Bapak/Ibu {nama_ortu},\n\nKami menginformasikan bahwa kegiatan bimbingan {program} untuk ananda {nama_anak} diliburkan sementara pada tanggal {tanggal} dalam rangka libur nasional.\n\nKegiatan pembelajaran akan aktif kembali sesuai jadwal masing-masing. Terima kasih atas kerja sama dan perhatiannya.\n\nWassalamu'alaikum Wr. Wb.\n*{lembaga}*",
            ],
            [
                'id' => 'exam',
                'name' => 'Jadwal Ujian Tasmi\' & Mutqin',
                'template' => "Assalamu'alaikum Warahmatullahi Wabarakatuh.\n\nYth. Bapak/Ibu {nama_ortu},\n\nAlhamdulillah, ananda {nama_anak} telah menyelesaikan target materi pada {program}. Ujian evaluasi hafalan (Tasmi' / Mutqin) dijadwalkan pada pekan ini.\n\nMohon bantu membimbing dan mendampingi ananda di rumah agar semakin mutqin dan istiqomah.\n\nWassalamu'alaikum Wr. Wb.\n*{lembaga}*",
            ],
            [
                'id' => 'tuition_reminder',
                'name' => 'Pengingat Pembayaran SPP Bulanan',
                'template' => "Assalamu'alaikum Warahmatullahi Wabarakatuh.\n\nYth. Bapak/Ibu {nama_ortu},\n\nSemoga Bapak/Ibu dan ananda {nama_anak} senantiasa dalam lindungan Allah SWT.\n\nKami menginformasikan tagihan SPP bimbingan {program} untuk periode ini telah terbit. Pembayaran dapat dilakukan dengan mudah via QRIS / Virtual Account di dashboard orang tua.\n\nTerima kasih atas kepercayaannya bersama {lembaga}.\n\nWassalamu'alaikum Wr. Wb.",
            ],
        ];
    }
}
