<?php

namespace Database\Seeders;

use App\Models\BlogTag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogTagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            'Tahsin',
            'Tahfidz',
            'Tajwid',
            'Iqra',
            'Parenting',
            'Adab',
            'Tips Mengaji',
            'Keluarga Qurani',
            'Murajaah',
            'Makhraj',
            'Anak Sholeh',
            'Bimbingan Privat',
            'Home Visit',
            'Online Learning',
        ];

        foreach ($tags as $tagName) {
            BlogTag::firstOrCreate(
                ['slug' => Str::slug($tagName)],
                ['name' => $tagName]
            );
        }
    }
}
