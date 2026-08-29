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
        $parentName = $recipientData['parent_name'] ?? 'Bapak/Ibu';
        $childName = $recipientData['children_names'] ?? 'Ananda';
        $programName = $recipientData['program_names'] ?? 'Program Al-Qur\'an';
        $dateFormatted = now()->translatedFormat('d F Y');
        $dueDateFormatted = now()->addDays(7)->translatedFormat('d F Y');
        $amount = '250.000';
        $paymentUrl = url('/parent/payments');
        $institution = 'AL-HIKMAH LMS';

        $replacements = [
            '{nama_ortu}' => $parentName,
            '{{nama_ortu}}' => $parentName,
            '{{name}}' => $parentName,
            '{nama_anak}' => $childName,
            '{{nama_anak}}' => $childName,
            '{{child_name}}' => $childName,
            '{program}' => $programName,
            '{{program}}' => $programName,
            '{tanggal}' => $dateFormatted,
            '{{tanggal}}' => $dateFormatted,
            '{{date}}' => $dateFormatted,
            '{{due_date}}' => $dueDateFormatted,
            '{due_date}' => $dueDateFormatted,
            '{{amount}}' => $amount,
            '{amount}' => $amount,
            '{{payment_url}}' => $paymentUrl,
            '{payment_url}' => $paymentUrl,
            '{lembaga}' => $institution,
            '{{lembaga}}' => $institution,
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
                'id' => 'tuition_spp_official',
                'name' => 'Pengingat Tagihan SPP & Link Pakasir (Resmi)',
                'template' => "Assalamu'alaikum Warahmatullahi Wabarakatuh, Ayah/Bunda {{name}},\n\nKami menginformasikan bahwa tagihan administrasi bimbingan Al-Qur'an untuk ananda {{child_name}} pada program {{program}} sebesar Rp {{amount}} akan jatuh tempo pada tanggal {{due_date}}.\n\nPembayaran dapat dilakukan secara praktis melalui tautan resmi Pakasir berikut:\n{{payment_url}}\n\nSemoga Allah SWT senantiasa memberkahi ikhtiar kita dalam mendidik putra-putri pecinta Al-Qur'an.\n\nJazakumullahu Khairan Katsiran,\nManajemen Lembaga {{lembaga}}",
            ],
            [
                'id' => 'holiday',
                'name' => 'Pengumuman Libur / Cuti Bersama',
                'template' => "Assalamu'alaikum Warahmatullahi Wabarakatuh.\n\nYth. Bapak/Ibu {{name}},\n\nKami menginformasikan bahwa kegiatan bimbingan {{program}} untuk ananda {{child_name}} diliburkan sementara pada tanggal {{date}} dalam rangka libur nasional.\n\nKegiatan pembelajaran akan aktif kembali sesuai jadwal masing-masing. Terima kasih atas kerja sama dan perhatiannya.\n\nWassalamu'alaikum Wr. Wb.\n*{{lembaga}}*",
            ],
            [
                'id' => 'exam',
                'name' => 'Jadwal Ujian Tasmi\' & Mutqin',
                'template' => "Assalamu'alaikum Warahmatullahi Wabarakatuh.\n\nYth. Bapak/Ibu {{name}},\n\nAlhamdulillah, ananda {{child_name}} telah menyelesaikan target materi pada {{program}}. Ujian evaluasi hafalan (Tasmi' / Mutqin) dijadwalkan pada pekan ini.\n\nMohon bantu membimbing dan mendampingi ananda di rumah agar semakin mutqin dan istiqomah.\n\nWassalamu'alaikum Wr. Wb.\n*{{lembaga}}*",
            ],
        ];
    }
}
