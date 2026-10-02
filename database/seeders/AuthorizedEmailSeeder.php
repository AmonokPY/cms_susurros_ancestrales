<?php

namespace Database\Seeders;

use App\Models\AuthorizedEmail;
use Illuminate\Database\Seeder;

class AuthorizedEmailSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['admin@example.com', 'Lista blanca inicial'],
            ['usuario@secureapp.test', 'Cuenta de prueba del curso'],
        ] as [$email, $reason]) {
            AuthorizedEmail::query()->updateOrCreate(
                ['email' => $email],
                ['reason' => $reason, 'is_active' => true]
            );
        }
    }
}
