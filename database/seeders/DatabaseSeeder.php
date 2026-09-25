<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SettingSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            ProgramSeeder::class,
            GalleryCategorySeeder::class,
            GallerySeeder::class,
            BlogCategorySeeder::class,
            BlogTagSeeder::class,
            ArticleSeeder::class,
        ]);
    }
}
