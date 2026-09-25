<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'message',
        'status',
        'admin_notes',
        'contacted_at',
    ];

    protected function casts(): array
    {
        return [
            'contacted_at' => 'datetime',
        ];
    }

    /**
     * Scope untuk pesan yang belum dibaca
     */
    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('status', 'unread');
    }

    /**
     * Scope untuk pesan yang sudah dihubungi
     */
    public function scopeContacted(Builder $query): Builder
    {
        return $query->where('status', 'contacted');
    }

    /**
     * Format nomor telepon ke standar internasional (628xxx)
     */
    public function getCleanPhoneAttribute(): string
    {
        $clean = preg_replace('/[^0-9]/', '', $this->phone);
        if (str_starts_with($clean, '0')) {
            $clean = '62'.substr($clean, 1);
        }

        return $clean;
    }

    /**
     * URL WhatsApp direct link untuk mem-follow up calon wali santri
     */
    public function getWhatsAppUrlAttribute(): string
    {
        $phone = $this->clean_phone;
        $greeting = "Assalamu'alaikum Warahmatullahi Wabarakatuh Ayah/Bunda *{$this->name}*,\n\n"
            ."Terima kasih telah menghubungi Lembaga Belajar Al-Qur'an *AL-HIKMAH* melalui formulir kontak kami.\n\n"
            ."Terkait pesan Anda:\n"
            .'_"'.trim($this->message)."\"_\n\n"
            ."Kami siap membantu dan mendampingi kebutuhan belajar Al-Qur'an ananda/keluarga. Apakah saat ini berkenan untuk berbincang mengenai jadwal dan program yang paling cocok?\n\n"
            .'_Salam hangat, Tim Layanan AL-HIKMAH_';

        return 'https://wa.me/'.$phone.'?text='.urlencode($greeting);
    }

    /**
     * Label status bahasa Indonesia
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'unread' => 'Belum Dibaca',
            'read' => 'Sudah Dibaca',
            'contacted' => 'Sudah Dihubungi',
            default => ucfirst($this->status),
        };
    }

    /**
     * Badge CSS class Bootstrap 5
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'unread' => 'bg-danger-subtle text-danger border border-danger-subtle',
            'read' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
            'contacted' => 'bg-success-subtle text-success border border-success-subtle',
            default => 'bg-secondary-subtle text-secondary',
        };
    }
}
