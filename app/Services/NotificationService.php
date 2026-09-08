<?php

namespace App\Services;

use App\Enums\NotificationType;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Kirim notifikasi ke 1 user atau ID user
     */
    public static function send(
        User|int $recipient,
        string $title,
        string $message,
        NotificationType|string $type = NotificationType::INFO,
        ?string $actionUrl = null,
        string $category = 'general',
        bool $sendWhatsApp = false
    ): ?Notification {
        $userId = $recipient instanceof User ? $recipient->id : $recipient;
        $user = $recipient instanceof User ? $recipient : User::find($recipient);

        if (! $user) {
            return null;
        }

        $typeString = $type instanceof NotificationType ? $type->value : $type;

        $notification = Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $typeString,
            'category' => $category,
            'action_url' => $actionUrl,
            'is_read' => false,
        ]);

        // Opsional: Kirim WhatsApp jika nomor HP tersedia dan parameter aktif
        if ($sendWhatsApp && $user->phone) {
            try {
                $waService = app(WhatsAppService::class);
                $waMsg = "*{$title}*\n\n{$message}";
                if ($actionUrl) {
                    $waMsg .= "\n\nLihat detail: ".url($actionUrl);
                }
                $waService->sendMessage($user->phone, $waMsg);
            } catch (\Throwable $e) {
                Log::warning("Gagal mengirim WhatsApp ke {$user->phone}: ".$e->getMessage());
            }
        }

        return $notification;
    }

    /**
     * Kirim notifikasi broadcast ke seluruh Administrator
     */
    public static function notifyAdmins(
        string $title,
        string $message,
        NotificationType|string $type = NotificationType::INFO,
        ?string $actionUrl = null,
        string $category = 'admin'
    ): Collection {
        $admins = User::whereHas('role', fn ($q) => $q->where('name', 'admin'))->get();
        $notifications = collect();

        foreach ($admins as $admin) {
            $notif = self::send($admin, $title, $message, $type, $actionUrl, $category, false);
            if ($notif) {
                $notifications->push($notif);
            }
        }

        return $notifications;
    }

    /**
     * Kirim notifikasi broadcast ke seluruh user berdasar role name
     */
    public static function notifyRole(
        string $roleName,
        string $title,
        string $message,
        NotificationType|string $type = NotificationType::INFO,
        ?string $actionUrl = null,
        string $category = 'general'
    ): Collection {
        $users = User::whereHas('role', fn ($q) => $q->where('name', $roleName))->get();
        $notifications = collect();

        foreach ($users as $user) {
            $notif = self::send($user, $title, $message, $type, $actionUrl, $category, false);
            if ($notif) {
                $notifications->push($notif);
            }
        }

        return $notifications;
    }

    /**
     * Kirim notifikasi pembayaran sukses ke wali santri dan admin
     */
    public static function notifyPaymentSuccess(Payment $payment, User $parentUser): void
    {
        $amountFmt = 'Rp '.number_format($payment->total_amount ?? $payment->amount, 0, ',', '.');
        $invoiceNo = $payment->invoice_number ?? ('INV-'.$payment->id);

        // Notifikasi ke Orang Tua
        self::send(
            $parentUser,
            'Pembayaran Berhasil Dikonfirmasi 🎉',
            "Alhamdulillah, pembayaran invoice #{$invoiceNo} sebesar {$amountFmt} telah berhasil diverifikasi. Kelas belajar ananda kini telah aktif.",
            NotificationType::SUCCESS,
            route('parent.payments.show', $payment->id),
            'payment',
            true
        );

        // Notifikasi ke Admin
        self::notifyAdmins(
            'Pembayaran Masuk (Online)',
            "Pembayaran invoice #{$invoiceNo} sebesar {$amountFmt} dari wali santri {$parentUser->name} telah berhasil diterima secara online.",
            NotificationType::SUCCESS,
            route('admin.payments.index'),
            'payment'
        );
    }
}
