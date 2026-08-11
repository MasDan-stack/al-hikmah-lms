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
            ProgramSeeder::class,
            UserSeeder::class,
            LearningSessionSeeder::class,
            ProgressSeeder::class,
            PaymentSeeder::class,
            GallerySeeder::class,
            NotificationSeeder::class,
        ]);
    }
}
