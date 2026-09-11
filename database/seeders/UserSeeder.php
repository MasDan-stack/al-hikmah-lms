<?php

namespace Database\Seeders;

use App\Enums\Role as RoleEnum;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan role sistem tersedia
        foreach (RoleEnum::cases() as $role) {
            Role::firstOrCreate(
                ['name' => $role->value],
                ['label' => $role->label()]
            );
        }

        $adminRole = Role::where('name', RoleEnum::ADMIN->value)->first();

        // 1. Admin: Hikmatul Hasanah
        User::updateOrCreate(
            ['email' => 'hikmah@gmail.com'],
            [
                'name' => 'Hikmatul Hasanah',
                'phone' => '0857-8668-9008',
                'password' => Hash::make('password'),
                'role_id' => $adminRole?->id,
                'email_verified_at' => now(),
            ]
        );

        // 2. Admin: Dandi Hermawan
        User::updateOrCreate(
            ['email' => 'dandihermawan87@gmail.com'],
            [
                'name' => 'Dandi Hermawan',
                'phone' => '089699451818',
                'password' => Hash::make('password'),
                'role_id' => $adminRole?->id,
                'email_verified_at' => now(),
            ]
        );
    }
}
