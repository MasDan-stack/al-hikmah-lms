<?php

namespace App\Enums;

enum EnrollmentStatus: string
{
    case WAITING_ADMIN = 'waiting_admin_confirmation';
    case WAITING_PARENT = 'waiting_parent_response';
    case CONFIRMED = 'schedule_confirmed';
    case ACTIVE = 'active';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::WAITING_ADMIN => 'Menunggu Konfirmasi Lembaga',
            self::WAITING_PARENT => 'Menunggu Respon Wali Santri',
            self::CONFIRMED => 'Jadwal Disepakati (Siap Bayar)',
            self::ACTIVE => 'Kelas Aktif',
            self::CANCELLED => 'Dibatalkan / Expired',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::WAITING_ADMIN => 'warning',
            self::WAITING_PARENT => 'info',
            self::CONFIRMED => 'primary',
            self::ACTIVE => 'success',
            self::CANCELLED => 'danger',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::WAITING_ADMIN => 'bi-hourglass-split',
            self::WAITING_PARENT => 'bi-chat-dots',
            self::CONFIRMED => 'bi-check-circle',
            self::ACTIVE => 'bi-award',
            self::CANCELLED => 'bi-x-circle',
        };
    }
}
