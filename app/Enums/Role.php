<?php

namespace App\Enums;

enum Role: string
{
    case ADMIN = 'admin';
    case MENTOR = 'mentor';
    case PARENT = 'parent';
    case STUDENT = 'student';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Administrator',
            self::MENTOR => 'Pendamping / Guru',
            self::PARENT => 'Orang Tua / Wali',
            self::STUDENT => 'Murid / Santri',
        };
    }
}
