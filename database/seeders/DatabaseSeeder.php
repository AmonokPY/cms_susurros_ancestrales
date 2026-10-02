<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
        ]);

        User::query()->updateOrCreate(
            ['email' => 'usuario@secureapp.test'],
            [
                'name' => 'Usuario de Prueba',
                'password' => Hash::make('Segura#2026!'),
                'role' => User::ROLE_ADMIN,
                'is_active' => true,
                'cms_access' => true,
                'email_verified_at' => now(),
            ]
        );

        $this->call([
            AuthorizedEmailSeeder::class,
            SiteContentSeeder::class,
        ]);
    }
}
