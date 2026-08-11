<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Group: contact
            [
                'key' => 'whatsapp_number',
                'value' => '6285786689008',
                'label' => 'Nomor WhatsApp CS',
                'group' => 'contact',
            ],
            [
                'key' => 'instagram_handle',
                'value' => 'houseofalhikmah',
                'label' => 'Username Instagram',
                'group' => 'social',
            ],
            [
                'key' => 'email_contact',
                'value' => 'belajarquranalhikmah@gmail.com',
                'label' => 'Email Kontak',
                'group' => 'contact',
            ],
            [
                'key' => 'office_address',
                'value' => 'Jabodetabek & Sekitarnya (Home Visit & Online)',
                'label' => 'Alamat / Area Layanan',
                'group' => 'contact',
            ],
            // Group: general
            [
                'key' => 'site_name',
                'value' => 'AL-HIKMAH',
                'label' => 'Nama Lembaga',
                'group' => 'general',
            ],
            [
                'key' => 'site_tagline',
                'value' => 'Menemani Generasi Qur\'ani Indonesia',
                'label' => 'Tagline Website',
                'group' => 'general',
            ],
            [
                'key' => 'registration_fee',
                'value' => '150000',
                'label' => 'Biaya Pendaftaran (Rp)',
                'group' => 'general',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
