<?php

namespace App\Enums;

enum NotificationType: string
{
    case INFO = 'info';
    case SUCCESS = 'success';
    case WARNING = 'warning';
    case DANGER = 'danger';

    public function label(): string
    {
        return match ($this) {
            self::INFO => 'Informasi',
            self::SUCCESS => 'Sukses',
            self::WARNING => 'Peringatan',
            self::DANGER => 'Penting / Galat',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::INFO => 'bg-info text-white',
            self::SUCCESS => 'bg-success text-white',
            self::WARNING => 'bg-warning text-dark',
            self::DANGER => 'bg-danger text-white',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::INFO => 'bi-info-circle-fill',
            self::SUCCESS => 'bi-check-circle-fill',
            self::WARNING => 'bi-exclamation-triangle-fill',
            self::DANGER => 'bi-x-circle-fill',
        };
    }
}
